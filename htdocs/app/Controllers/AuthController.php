<?php

class AuthController extends Controller
{
    // Show register form
    // URL: /auth/register
    public function register()
    {
        if (Auth::check()) {
            $this->redirect('/email');
        }

        $this->view('auth/register', [
            'title' => 'Create Account',
            'error' => ''
        ]);
    }

    // Handle register form submit (POST)
    // URL: /auth/store  (kept separate from /auth/register so a
    // failed submit can re-render the form with old input + error)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/register');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $error    = '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        }

        $userModel = $this->model('User');

        if (empty($error) && $userModel->findByEmail($email)) {
            $error = 'An account with this email already exists.';
        }

        if (!empty($error)) {
            $this->view('auth/register', [
                'title' => 'Create Account',
                'error' => $error,
                'name'  => $name,
                'email' => $email
            ]);
            return;
        }

        // Every public registration is forced to role 'user' and the
        // Free plan (id 1). Roles/plans are only changed by an admin.
        $userId = $userModel->create($name, $email, $password, 'user', 1);
        $user   = $userModel->find($userId);

        Auth::login($user);
        $this->redirect('/email');
    }

    // Show login form
    // URL: /auth/login
    public function login()
    {
        if (Auth::check()) {
            $this->redirect('/email');
        }

        $this->view('auth/login', [
            'title' => 'Log In',
            'error' => ''
        ]);
    }

    // Handle login form submit (POST)
    // URL: /auth/authenticate
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword($user, $password)) {
            $this->view('auth/login', [
                'title' => 'Log In',
                'error' => 'Invalid email or password.',
                'email' => $email
            ]);
            return;
        }

        Auth::login($user);

        if (Auth::isAdmin()) {
            $this->redirect('/user');
        }
        $this->redirect('/email');
    }

    // URL: /auth/logout
    public function logout()
    {
        Auth::logout();
        $this->redirect('/auth/login');
    }
}
