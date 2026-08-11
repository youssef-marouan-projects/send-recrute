<?php

require_once __DIR__ . '/../Helpers/CryptoHelper.php';

class ProfileController extends Controller
{
    // GET /profile
    public function index()
    {
        $this->requireLogin();
        $userModel = $this->model('User');

        $this->view('profile/index', [
            'title'   => 'My Profile',
            'user'    => $userModel->find(Auth::id()),
            'error'   => $_SESSION['profile_error'] ?? '',
            'success' => $_SESSION['profile_success'] ?? '',
        ]);
        unset($_SESSION['profile_error'], $_SESSION['profile_success']);
    }

    // POST /profile/update — basic identity (name/email + sender display name)
    public function update()
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $userId = Auth::id();
        $userModel = $this->model('User');

        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $senderName = trim($_POST['sender_name'] ?? $name);

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['profile_error'] = 'Please provide a valid name and email.';
            $this->redirect('/profile');
        }

        $userModel->update($userId, $name, $email);
        $userModel->updateSenderIdentity($userId, $senderName, $userModel->find($userId)['sender_email'] ?? null);

        // Keep the session name in sync so the nav/header reflects the change
        $_SESSION['user_name'] = $name;

        $_SESSION['profile_success'] = 'Profile updated.';
        $this->redirect('/profile');
    }

    // POST /profile/mailSettings — the Gmail address + App Password used to
    // actually send mass emails via SMTP.
    public function mailSettings()
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $userId = Auth::id();
        $userModel = $this->model('User');
        $user = $userModel->find($userId);

        $senderEmail = trim($_POST['sender_email'] ?? '');
        $appPassword = trim($_POST['gmail_app_password'] ?? '');

        if ($senderEmail !== '' && !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['profile_error'] = 'Please enter a valid Gmail address.';
            $this->redirect('/profile');
        }

        $userModel->updateSenderIdentity($userId, $user['sender_name'] ?? $user['name'], $senderEmail);

        // Only overwrite the stored app password if the user actually typed
        // a new one (the field is left blank on reload for security).
        if ($appPassword !== '') {
            // Gmail App Passwords are shown as "abcd efgh ijkl mnop" — strip
            // spaces since Gmail's SMTP accepts it either way.
            $clean = str_replace(' ', '', $appPassword);
            $userModel->updateGmailAppPassword($userId, CryptoHelper::encrypt($clean));
        }

        $_SESSION['profile_success'] = 'Mail sending settings saved.';
        $this->redirect('/profile');
    }

    // POST /profile/clearAppPassword
    public function clearAppPassword()
    {
        $this->requireLogin();
        $userModel = $this->model('User');
        $userModel->updateGmailAppPassword(Auth::id(), null);
        $_SESSION['profile_success'] = 'App Password removed.';
        $this->redirect('/profile');
    }
}
