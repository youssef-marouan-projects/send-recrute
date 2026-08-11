<?php

require_once __DIR__ . '/../Helpers/XlsxHelper.php';
require_once __DIR__ . '/../Helpers/CryptoHelper.php';
require_once __DIR__ . '/../Helpers/MailerHelper.php';
require_once __DIR__ . '/../Helpers/SignatureHelper.php';
require_once __DIR__ . '/../Helpers/CvHelper.php';

// Ported from send_multi_email_to_recruters: bulk-load recipients from an
// Excel file, generate a personalized body per row with Groq, and really
// send the emails through the user's own Gmail account (App Password).
class CampaignController extends Controller
{
    // GET/POST /campaign — upload the Excel, then review the loaded rows
    // before sending.
    public function index()
    {
        $this->requireLogin();
        $userId = Auth::id();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['campaign_error'] = 'Upload failed.';
            } elseif (!in_array($ext, ['xlsx', 'xls'], true)) {
                $_SESSION['campaign_error'] = 'Only .xlsx or .xls files are allowed.';
            } else {
                $parsed = XlsxHelper::readFirstSheet($file['tmp_name']);
                if (!$parsed || empty(array_filter($parsed['headers'], fn($h) => $h === 'email'))) {
                    $_SESSION['campaign_error'] = "Excel must contain a column named 'email'.";
                } else {
                    $rows = array_map(function ($r) {
                        return ['email' => $r['email'] ?? '', 'post' => $r['post'] ?? ''];
                    }, array_filter($parsed['rows'], fn($r) => !empty($r['email'])));

                    $_SESSION['campaign_rows']     = array_values($rows);
                    $_SESSION['campaign_has_post'] = !empty($parsed['rows'][0]['post'] ?? null) || in_array('post', $parsed['headers'], true);
                    $_SESSION['campaign_success']  = 'Successfully loaded ' . count($rows) . ' recipients!';
                }
            }
            $this->redirect('/campaign');
        }

        $userModel = $this->model('User');
        $cvModel   = $this->model('CvUpload');
        $sigModel  = $this->model('Signature');

        $this->view('campaign/index', [
            'title'    => 'Mass Send Campaign',
            'user'     => $userModel->find($userId),
            'rows'     => $_SESSION['campaign_rows'] ?? [],
            'hasPost'  => $_SESSION['campaign_has_post'] ?? false,
            'myCvs'    => $cvModel->getByUser($userId),
            'sigs'     => $sigModel->getByUser($userId),
            'error'    => $_SESSION['campaign_error'] ?? '',
            'success'  => $_SESSION['campaign_success'] ?? '',
        ]);
        unset($_SESSION['campaign_error'], $_SESSION['campaign_success']);
    }

    // POST /campaign/deleteRow/{index}
    public function deleteRow($index = null)
    {
        $this->requireLogin();
        $rows = $_SESSION['campaign_rows'] ?? [];
        if ($index !== null && isset($rows[$index])) {
            array_splice($rows, (int) $index, 1);
            $_SESSION['campaign_rows'] = $rows;
        }
        $this->redirect('/campaign');
    }

    // POST /campaign/clear
    public function clear()
    {
        $this->requireLogin();
        unset($_SESSION['campaign_rows'], $_SESSION['campaign_has_post']);
        $this->redirect('/campaign');
    }

    // POST /campaign/send — creates the campaign + recipient rows, then
    // hands off to the live-status page which drives the actual sending.
    public function send()
    {
        $this->requireLogin();
        $userId = Auth::id();

        $rows = $_SESSION['campaign_rows'] ?? [];
        if (empty($rows)) {
            $_SESSION['campaign_error'] = 'No recipients to send to. Upload an Excel file first.';
            $this->redirect('/campaign');
        }

        $userModel = $this->model('User');
        if (!$userModel->hasMailSendingConfigured($userId)) {
            $_SESSION['campaign_error'] = 'Add your Gmail address and App Password in your Profile before sending.';
            $this->redirect('/campaign');
        }

        $subject     = trim($_POST['subject'] ?? 'Hello');
        $message     = trim($_POST['message'] ?? '');
        $cvUploadId  = !empty($_POST['existing_cv_id']) ? (int) $_POST['existing_cv_id'] : null;
        $signatureId = !empty($_POST['signature_id']) ? (int) $_POST['signature_id'] : null;

        $campaignModel = $this->model('Campaign');
        $campaignId = $campaignModel->create($userId, $cvUploadId, $signatureId, $subject, $message, count($rows));

        $recipientModel = $this->model('CampaignRecipient');
        $recipientModel->bulkCreate($campaignId, $rows);

        unset($_SESSION['campaign_rows'], $_SESSION['campaign_has_post']);

        $this->redirect('/campaign/status/' . $campaignId);
    }

    // GET /campaign/status/{id}
    public function status($id = null)
    {
        $this->requireLogin();
        $campaignModel = $this->model('Campaign');
        $campaign = $campaignModel->findForUser($id, Auth::id());
        if (!$campaign) {
            http_response_code(404);
            echo "Campaign not found.";
            return;
        }
        $this->view('campaign/status', [
            'title'    => 'Sending Campaign',
            'campaign' => $campaign,
        ]);
    }

    // GET /campaign/statusJson/{id} — polled by the status page's JS
    public function statusJson($id = null)
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $campaignModel = $this->model('Campaign');
        $campaign = $campaignModel->findForUser($id, Auth::id());
        if (!$campaign) {
            http_response_code(404);
            echo json_encode(['error' => 'not found']);
            return;
        }
        echo json_encode([
            'total'    => (int) $campaign['total'],
            'sent'     => (int) $campaign['sent_count'],
            'failed'   => (int) $campaign['failed_count'],
            'finished' => $campaign['status'] === 'finished',
        ]);
    }

    // POST /campaign/processBatch/{id} — called repeatedly by the status
    // page's JS. Sends a small batch of pending recipients per call so a
    // single HTTP request stays fast, and returns fresh progress numbers.
    public function processBatch($id = null)
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId = Auth::id();
        $campaignModel   = $this->model('Campaign');
        $recipientModel  = $this->model('CampaignRecipient');
        $userModel       = $this->model('User');
        $cvModel         = $this->model('CvUpload');
        $sigModel        = $this->model('Signature');

        $campaign = $campaignModel->findForUser($id, $userId);
        if (!$campaign) {
            http_response_code(404);
            echo json_encode(['error' => 'not found']);
            return;
        }

        if ($campaign['status'] === 'pending') {
            $campaignModel->setStatus($id, 'sending');
        }

        $user = $userModel->find($userId);
        $appPassword = CryptoHelper::decrypt($user['gmail_app_password'] ?? null);
        $senderEmail = $user['sender_email'] ?? '';
        $senderName  = $user['sender_name'] ?? $user['name'];

        // CV attachment (optional)
        $cvBytes = null; $cvName = null; $cvMime = null; $cvText = '';
        if (!empty($campaign['cv_upload_id'])) {
            $cv = $cvModel->find($campaign['cv_upload_id']);
            if ($cv) {
                $filePath = __DIR__ . '/../../' . $cv['path'];
                if (file_exists($filePath)) {
                    $cvBytes = file_get_contents($filePath);
                    $cvName  = $cv['original_name'];
                    $cvMime  = $cv['extension'] === 'pdf'
                        ? 'application/pdf'
                        : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    $cvText = $cv['extension'] === 'docx'
                        ? CvHelper::extractTextFromDocx($filePath)
                        : CvHelper::extractTextFromPdf($filePath);
                    $cvText = $cvText === false ? '' : $cvText;
                }
            }
        }

        // Signature (optional)
        $signatureHtml = '';
        $sigImageB64 = null;
        if (!empty($campaign['signature_id'])) {
            $sig = $sigModel->find($campaign['signature_id']);
            $hasImage = $sig && !empty($sig['image_base64']);
            $signatureHtml = SignatureHelper::build($sig, $hasImage);
            if ($hasImage) $sigImageB64 = $sig['image_base64'];
        }

        $batch = $recipientModel->getPendingBatch($id, 3);
        $generatedCache = [];

        foreach ($batch as $recipient) {
            $postText = trim($recipient['post'] ?? '');
            $body = $campaign['message'];

            if ($postText !== '' && $cvText !== '') {
                $cacheKey = md5($postText);
                if (!isset($generatedCache[$cacheKey])) {
                    $prompt = "You are an expert career coach and professional email writer.\n\n"
                        . "Write a short, professional job-application email body (max 130 words).\n\n"
                        . "Rules:\n"
                        . "- Same language as the job post below.\n"
                        . "- 2 short paragraphs max, no greeting and no closing/sign-off line "
                        . "(e.g. \"Sincerely,\" \"Best regards,\") — a signature is appended separately.\n"
                        . "- Mention 1-2 relevant skills from the CV matching the post.\n"
                        . "- Do NOT invent skills that aren't in the CV.\n"
                        . "- Output ONLY the email body text, nothing else.\n\n"
                        . "JOB POST:\n" . mb_substr($postText, 0, 1200) . "\n\n"
                        . "CV:\n" . mb_substr($cvText, 0, 1500);

                    $generated = CvHelper::callGroq($prompt);
                    $generatedCache[$cacheKey] = $generated ?: $campaign['message'];
                }
                $body = $generatedCache[$cacheKey];
            }

            $recipientModel->saveBody($recipient['id'], $body);

            $bodyHtml = nl2br(htmlspecialchars($body));
            $fullHtml = '<div style="font-family: Arial, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.6;">'
                . $bodyHtml . $signatureHtml . '</div>';

            $attachments = [];
            if ($sigImageB64) {
                if (preg_match('/^data:(.*?);base64,(.*)$/', $sigImageB64, $m)) {
                    $attachments[] = [
                        'data' => base64_decode($m[2]),
                        'name' => 'signature_photo.png',
                        'mime' => $m[1],
                        'inline_cid' => 'signature_photo',
                    ];
                }
            }
            if ($cvBytes) {
                $attachments[] = ['data' => $cvBytes, 'name' => $cvName, 'mime' => $cvMime];
            }

            $subject = $postText !== '' ? $postText : $campaign['subject'];

            $result = MailerHelper::send(
                $senderEmail, $appPassword, $senderName,
                $recipient['email'], $subject, $fullHtml, $body, $attachments
            );

            if ($result['ok']) {
                $recipientModel->markSent($recipient['id']);
                $campaignModel->incrementCounters($id, 1, 0);
            } else {
                $recipientModel->markFailed($recipient['id'], $result['error']);
                $campaignModel->incrementCounters($id, 0, 1);
            }
        }

        $remaining = $recipientModel->countRemaining($id);
        if ($remaining === 0) {
            $campaignModel->setStatus($id, 'finished');
        }

        $fresh = $campaignModel->find($id);
        echo json_encode([
            'total'    => (int) $fresh['total'],
            'sent'     => (int) $fresh['sent_count'],
            'failed'   => (int) $fresh['failed_count'],
            'finished' => $fresh['status'] === 'finished',
        ]);
    }
}
