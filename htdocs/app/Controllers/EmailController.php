<?php

class EmailController extends Controller
{
    public function index()
    {
        $result = '';
        $error  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name     = trim($_POST['candidate_name'] ?? 'Candidate');
            $job      = trim($_POST['job_post'] ?? '');
            $language = $_POST['language'] ?? 'English';

            // Load helper
            require_once __DIR__ . '/../Helpers/CvHelper.php';

            // Handle file upload
            if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cv_file'];
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $tmp  = $file['tmp_name'];

                if ($ext === 'docx') {
                    $cvText = CvHelper::extractTextFromDocx($tmp);
                    if ($cvText === false) {
                        $error = 'Could not read the DOCX file.';
                    }
                } elseif ($ext === 'pdf') {
                    $cvText = CvHelper::extractTextFromPdf($tmp);
                    if ($cvText === false) {
                        $error = 'Could not read the PDF. Make sure the pdfparser folder is correct.';
                    }
                } else {
                    $error = 'Only PDF and DOCX files are allowed.';
                }
            } else {
                $error = 'Please upload a CV file (PDF or DOCX).';
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
            }
        }

        $this->view('email/index', [
            'title'  => 'AI Job Email Generator',
            'result' => $result,
            'error'  => $error
        ]);
    }
}