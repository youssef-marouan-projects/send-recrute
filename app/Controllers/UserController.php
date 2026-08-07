<?php

// Admin-only user management panel.
// Public registration lives in AuthController (/auth/register).
class UserController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    // List all users
    // URL: /user  or  /user/index
    public function index()
    {
        $userModel = $this->model('User');
        $users = $userModel->getAll();

        $this->view('users/index', [
            'title' => 'All Users',
            'users' => $users
        ]);
    }

    // Show one user
    // URL: /user/show/5
    public function show($id = null)
    {
        if (!$id) {
            echo "User ID is required";
            return;
        }

        $userModel = $this->model('User');
        $user = $userModel->find($id);

        if (!$user) {
            echo "User not found";
            return;
        }

        $this->view('users/show', [
            'title' => 'User Details',
            'user'  => $user
        ]);
    }

    // Show create form
    // URL: /user/create
    public function create()
    {
        $planModel = $this->model('Plan');

        $this->view('users/create', [
            'title' => 'Create User',
            'plans' => $planModel->getAll()
        ]);
    }

    // Save new user (POST) — admin creating a user, role/plan selectable
    // URL: /user/store
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/user/create');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'user';
        $planId   = (int) ($_POST['plan_id'] ?? 1);

        if (empty($name) || empty($email) || empty($password)) {
            echo "All fields are required";
            return;
        }

        if (!in_array($role, ['admin', 'user'], true)) {
            $role = 'user';
        }

        $userModel = $this->model('User');
        $userModel->create($name, $email, $password, $role, $planId);

        $this->redirect('/user');
    }

    // Show edit form
    // URL: /user/edit/5
    public function edit($id = null)
    {
        if (!$id) {
            echo "User ID is required";
            return;
        }

        $userModel = $this->model('User');
        $planModel = $this->model('Plan');
        $user = $userModel->find($id);

        if (!$user) {
            echo "User not found";
            return;
        }

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user'  => $user,
            'plans' => $planModel->getAll()
        ]);
    }

    // Update user (POST) — profile, role, plan
    // URL: /user/update/5
    public function update($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/user');
        }

        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $role   = $_POST['role'] ?? 'user';
        $planId = (int) ($_POST['plan_id'] ?? 1);

        $userModel = $this->model('User');
        $userModel->update($id, $name, $email);
        $userModel->updateRole($id, $role);
        $userModel->updatePlan($id, $planId);

        $this->redirect('/user/show/' . $id);
    }

    // Delete user
    // URL: /user/delete/5
    public function delete($id = null)
    {
        if (!$id) {
            echo "User ID is required";
            return;
        }

        $userModel = $this->model('User');
        $userModel->delete($id);

        $this->redirect('/user');
    }

    // Admin view: every CV uploaded by every user
    // URL: /user/cvs
    public function cvs()
    {
        $cvModel = $this->model('CvUpload');

        $this->view('users/cvs', [
            'title' => 'All Uploaded CVs',
            'cvs'   => $cvModel->getAll()
        ]);
    }

    // Admin view: every AI-generated email from every user
    // URL: /user/emails
    public function emails()
    {
        $emailModel = $this->model('EmailGeneration');

        $this->view('users/emails', [
            'title'  => 'All Generated Emails',
            'emails' => $emailModel->getAll()
        ]);
    }
}