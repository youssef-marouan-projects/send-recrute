<?php

class Auth
{
    protected static function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // Store the logged-in user's data in the session
    public static function login($user)
    {
        self::startSession();
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
    }

    public static function logout()
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check()
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function id()
    {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role()
    {
        self::startSession();
        return $_SESSION['user_role'] ?? null;
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    // Always re-reads from DB so name/role/plan changes reflect immediately
    public static function user()
    {
        self::startSession();
        if (!self::check()) {
            return null;
        }
        require_once __DIR__ . '/../Models/User.php';
        $userModel = new User();
        return $userModel->find(self::id());
    }
}
