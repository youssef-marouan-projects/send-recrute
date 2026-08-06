<?php

class UserController extends Controller
{
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
        $this->view('users/create', [
            'title' => 'Create User'
        ]);
    }

    // Save new user (POST)
    // URL: /user/store
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user/create');
            exit;
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo "All fields are required";
            return;
        }

        $userModel = $this->model('User');
        $userModel->create($name, $email, $password);

        header('Location: /user');
        exit;
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
        $user = $userModel->find($id);

        if (!$user) {
            echo "User not found";
            return;
        }

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user'  => $user
        ]);
    }

    // Update user (POST)
    // URL: /user/update/5
    public function update($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user');
            exit;
        }

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $userModel = $this->model('User');
        $userModel->update($id, $name, $email);

        header('Location: /user/show/' . $id);
        exit;
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

        header('Location: /user');
        exit;
    }
}