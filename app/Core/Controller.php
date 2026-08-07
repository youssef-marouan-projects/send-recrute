<?php

class Controller
{
    public function model($model)
    {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = [])
    {
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    public function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    // Blocks the request unless a user is logged in
    protected function requireLogin()
    {
        if (!Auth::check()) {
            $this->redirect('/auth/login');
        }
    }

    // Blocks the request unless the logged-in user is an admin
    protected function requireAdmin()
    {
        $this->requireLogin();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo "403 - Admins only.";
            exit;
        }
    }
}