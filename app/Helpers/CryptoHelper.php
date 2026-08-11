<?php

// Small reversible-encryption helper for secrets we must be able to read
// back and use (e.g. the Gmail App Password used to send mail via SMTP).
// This is NOT for passwords the app itself authenticates users with —
// those stay one-way hashed in User::create() via password_hash().
class CryptoHelper
{
    private static function key()
    {
        // Falls back to a derived key if APP_ENCRYPTION_KEY isn't set,
        // but you should always define a real one in config/config.php:
        //   define('APP_ENCRYPTION_KEY', bin2hex(random_bytes(32)));
        $key = defined('APP_ENCRYPTION_KEY') ? APP_ENCRYPTION_KEY : 'insecure-default-change-me';
        return hash('sha256', $key, true);
    }

    public static function encrypt($plaintext)
    {
        if ($plaintext === null || $plaintext === '') {
            return null;
        }
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $cipher = openssl_encrypt($plaintext, 'aes-256-cbc', self::key(), OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher);
    }

    public static function decrypt($stored)
    {
        if (!$stored) {
            return null;
        }
        $raw = base64_decode($stored);
        $ivLen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($raw, 0, $ivLen);
        $cipher = substr($raw, $ivLen);
        $plain = openssl_decrypt($cipher, 'aes-256-cbc', self::key(), OPENSSL_RAW_DATA, $iv);
        return $plain === false ? null : $plain;
    }
}
