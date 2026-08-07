<?php

class EmailController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $userModel     = $this->model('User');
        $cvUploadModel = $this->model('CvUpload');
        $userId        = Auth::id();
        $user          = $userModel->find($userId);

        $result = '';
        $error  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name     = trim($_POST['candidate_name'] ?? 'Candidate');
            $job      = trim($_POST['job_post'] ?? '');
            $language = $_POST['language'] ?? 'English';
            $cvSource = $_POST['cv_source'] ?? 'new'; // 'new' or 'existing'
            $cvText   = '';
            $cvUploadId = null;

            require_once __DIR__ . '/../Helpers/CvHelper.php';

            if ($cvSource === 'existing') {
                // ---- Reusing a previously uploaded CV: no new file, no upload-limit hit ----
                if (!$userModel->canGenerateEmail($userId)) {
                    $error = 'You have reached your plan\'s email generation limit. Please upgrade your plan.';
                } else {
                    $existingId = (int) ($_POST['existing_cv_id'] ?? 0);
                    $cv = $cvUploadModel->find($existingId);

                    if (!$cv || (int) $cv['user_id'] !== $userId) {
                        $error = 'Please choose a valid CV from your uploads.';
                    } else {
                        $filePath = __DIR__ . '/../../' . $cv['path'];
                        if (!file_exists($filePath)) {
                            $error = 'That CV file could not be found on the server anymore. Please upload it again.';
                        } else {
                            $cvUploadId = $cv['id'];
                            if ($cv['extension'] === 'docx') {
                                $cvText = CvHelper::extractTextFromDocx($filePath);
                                if ($cvText === false) {
                                    $error = 'Could not read the saved DOCX file.';
                                }
                            } else {
                                $cvText = CvHelper::extractTextFromPdf($filePath);
                                if ($cvText === false) {
                                    $error = 'Could not read the saved PDF file.';
                                }
                            }
                        }
                    }
                }
            } else {
                // ---- Uploading a brand new CV: enforce both limits ----
                if (!$userModel->canUploadCv($userId)) {
                    $error = 'You have reached your plan\'s CV upload limit. Please upgrade your plan.';
                } elseif (!$userModel->canGenerateEmail($userId)) {
                    $error = 'You have reached your plan\'s email generation limit. Please upgrade your plan.';
                } elseif (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['cv_file'];
                    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $tmp  = $file['tmp_name'];

                    if (!in_array($ext, ['pdf', 'docx'], true)) {
                        $error = 'Only PDF and DOCX files are allowed.';
                    } else {
                        $uploadDir = __DIR__ . '/../../uploads/cv/' . $userId . '/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $storedName = uniqid('cv_', true) . '.' . $ext;
                        $destPath   = $uploadDir . $storedName;

                        if (!move_uploaded_file($tmp, $destPath)) {
                            $error = 'Could not save the uploaded file.';
                        } else {
                            $relativePath = 'uploads/cv/' . $userId . '/' . $storedName;

                            $cvUploadId = $cvUploadModel->create(
                                $userId,
                                $file['name'],
                                $storedName,
                                $relativePath,
                                $ext,
                                $file['size'] ?? null
                            );
                            $userModel->incrementCvUploads($userId);

                            if ($ext === 'docx') {
                                $cvText = CvHelper::extractTextFromDocx($destPath);
                                if ($cvText === false) {
                                    $error = 'Could not read the DOCX file.';
                                }
                            } else {
                                $cvText = CvHelper::extractTextFromPdf($destPath);
                                if ($cvText === false) {
                                    $error = 'Could not read the PDF. Make sure the pdfparser folder is correct.';
                                }
                            }
                        }
                    }
                } else {
                    $error = 'Please upload a CV file (PDF or DOCX).';
                }
            }

            if (empty($error) && (empty($cvText) || empty($job))) {
                $error = 'CV text could not be extracted or Job Post is empty.';
            }

            if (empty($error)) {
                $prompt = "You are an expert career coach and professional email writer.

CV:
$cvText

JOB POST:
$job

CANDIDATE NAME: $name
LANGUAGE: $language

Write a professional and personalized job application email.

Return ONLY in this exact format:

SUBJECT: [strong subject line]

BODY:
[full email body]

Rules:
- Write everything in $language
- Be professional but warm and human
- Highlight only relevant experience from the CV
- Do NOT invent any skills or experience
- Keep the body between 160–220 words
- Start with a proper greeting
- End with a polite call to action + candidate name";

                $result = CvHelper::callGroq($prompt);

                $emailGenModel = $this->model('EmailGeneration');
                $emailGenModel->create($userId, $cvUploadId, $job, $language, $result);
                $userModel->incrementEmailsGenerated($userId);

                // Refresh user data so the view shows updated usage counts
                $user = $userModel->find($userId);
            }
        }

        $this->view('email/index', [
            'title'   => 'AI Job Email Generator',
            'result'  => $result,
            'error'   => $error,
            'user'    => $user,
            'myCvs'   => $cvUploadModel->getByUser($userId)
        ]);
    }

    // Serve a previously uploaded CV for viewing/downloading — only to its owner (or an admin)
    // URL: /email/viewCv/5
    public function viewCv($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            http_response_code(404);
            echo "CV not found.";
            return;
        }

        $cvUploadModel = $this->model('CvUpload');
        $cv = $cvUploadModel->find($id);

        if (!$cv || ((int) $cv['user_id'] !== Auth::id() && !Auth::isAdmin())) {
            http_response_code(403);
            echo "You don't have access to this file.";
            return;
        }

        $filePath = __DIR__ . '/../../' . $cv['path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "File not found on server.";
            return;
        }

        $mime = $cv['extension'] === 'pdf'
            ? 'application/pdf'
            : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($cv['original_name']) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}