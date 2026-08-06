<?php

class EmailController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $userModel = $this->model('User');
        $userId    = Auth::id();
        $user      = $userModel->find($userId);

        $result = '';
        $error  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Enforce plan limits BEFORE touching the file system or the AI API
            if (!$userModel->canUploadCv($userId)) {
                $error = 'You have reached your plan\'s CV upload limit. Please upgrade your plan.';
            } elseif (!$userModel->canGenerateEmail($userId)) {
                $error = 'You have reached your plan\'s email generation limit. Please upgrade your plan.';
            }

            $name     = trim($_POST['candidate_name'] ?? 'Candidate');
            $job      = trim($_POST['job_post'] ?? '');
            $language = $_POST['language'] ?? 'English';
            $cvText   = '';
            $cvUploadId = null;

            // Load helper
            require_once __DIR__ . '/../Helpers/CvHelper.php';

            if (empty($error)) {
                if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['cv_file'];
                    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $tmp  = $file['tmp_name'];

                    if (!in_array($ext, ['pdf', 'docx'], true)) {
                        $error = 'Only PDF and DOCX files are allowed.';
                    } else {
                        // Save the uploaded CV to disk under uploads/cv/{user_id}/
                        $uploadDir = __DIR__ . '/../../uploads/cv/' . $userId . '/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $storedName = uniqid('cv_', true) . '.' . $ext;
                        $destPath   = $uploadDir . $storedName;

                        if (!move_uploaded_file($tmp, $destPath)) {
                            $error = 'Could not save the uploaded file.';
                        } else {
                            // Relative path stored in DB (relative to htdocs/)
                            $relativePath = 'uploads/cv/' . $userId . '/' . $storedName;

                            $cvUploadModel = $this->model('CvUpload');
                            $cvUploadId = $cvUploadModel->create(
                                $userId,
                                $file['name'],
                                $storedName,
                                $relativePath,
                                $ext,
                                $file['size'] ?? null
                            );
                            $userModel->incrementCvUploads($userId);

                            // Extract text from the file we just saved on disk
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

                // Log the generation and bump the usage counter
                $emailGenModel = $this->model('EmailGeneration');
                $emailGenModel->create($userId, $cvUploadId, $job, $language, $result);
                $userModel->incrementEmailsGenerated($userId);

                // Refresh user data so the view shows updated usage counts
                $user = $userModel->find($userId);
            }
        }

        $this->view('email/index', [
            'title'  => 'AI Job Email Generator',
            'result' => $result,
            'error'  => $error,
            'user'   => $user
        ]);
    }
}
