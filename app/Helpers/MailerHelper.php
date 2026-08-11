<?php

require_once __DIR__ . '/../../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Sends real emails through the logged-in user's own Gmail account,
// authenticated with a Gmail "App Password" (not their normal password).
// See ProfileController for where that app password is collected.
class MailerHelper
{
    /**
     * @param string $senderEmail   Gmail address the mail is sent FROM
     * @param string $appPassword   16-char Gmail App Password (already decrypted)
     * @param string $senderName    Display name
     * @param string $toEmail
     * @param string $subject
     * @param string $htmlBody
     * @param string $plainBody
     * @param array  $attachments   [['path' => ..., 'name' => ...], ...] or
     *                              [['data' => ..., 'name' => ..., 'mime' => ..., 'inline_cid' => ...]]
     * @return array ['ok' => bool, 'error' => string|null]
     */
    public static function send($senderEmail, $appPassword, $senderName, $toEmail, $subject, $htmlBody, $plainBody, $attachments = [])
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $senderEmail;
            $mail->Password   = $appPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($senderEmail, $senderName ?: $senderEmail);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $plainBody;

            foreach ($attachments as $att) {
                if (isset($att['path'])) {
                    $mail->addAttachment($att['path'], $att['name'] ?? basename($att['path']));
                } elseif (isset($att['data'])) {
                    if (!empty($att['inline_cid'])) {
                        $mail->addStringEmbeddedImage($att['data'], $att['inline_cid'], $att['name'] ?? 'image', 'base64', $att['mime'] ?? 'image/png');
                    } else {
                        $mail->addStringAttachment($att['data'], $att['name'] ?? 'attachment', 'base64', $att['mime'] ?? 'application/octet-stream');
                    }
                }
            }

            $mail->send();
            return ['ok' => true, 'error' => null];
        } catch (PHPMailerException $e) {
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
