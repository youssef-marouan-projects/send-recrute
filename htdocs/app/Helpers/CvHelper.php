<?php

class CvHelper
{
    public static function extractTextFromDocx($filePath)
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();
                $xml = new DOMDocument();
                $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $text = strip_tags($xml->saveXML());
                $text = preg_replace("/\s+/", " ", $text);
                return trim($text);
            }
            $zip->close();
        }
        return false;
    }

    public static function extractTextFromPdf($filePath)
    {
        $autoload = __DIR__ . '/../../pdfparser/alt_autoload.php';
        if (!file_exists($autoload)) {
            return false;
        }
        require_once $autoload;

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            return trim($pdf->getText());
        } catch (Exception $e) {
            return false;
        }
    }

    public static function callGroq($prompt)
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $data = [
            'model' => GROQ_MODEL,
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert career coach and professional email writer. Always follow the requested format strictly.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.65,
            'max_tokens' => 1100
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . GROQ_API_KEY,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 50
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return "API Error ($httpCode): " . $response;
        }

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'No response from AI';
    }
}