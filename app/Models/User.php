<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all users (with plan info, for the admin panel)
    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT u.*, p.slug AS plan_slug, p.name AS plan_name
             FROM users u
             LEFT JOIN plans p ON p.id = u.plan_id
             ORDER BY u.id DESC"
        );
        return $stmt->fetchAll();
    }

    // Get user by ID (with plan info)
    public function find($id)
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, p.slug AS plan_slug, p.name AS plan_name,
                    p.max_cv_uploads, p.max_emails
             FROM users u
             LEFT JOIN plans p ON p.id = u.plan_id
             WHERE u.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Create new user. Role defaults to 'user' and cannot be set
    // from public registration — only an admin changes roles later.
    // planId defaults to 1 (Free plan).
    public function create($name, $email, $password, $role = 'user', $planId = 1)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, plan_id)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([$name, $email, $hashed, $role, $planId]);
        return (int) $this->db->lastInsertId();
    }

    // Update user's basic profile info
    public function update($id, $name, $email)
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $email, $id]);
    }

    // Admin-only: change a user's role.
    // Admins bypass all plan limits by role (see canUploadCv/canGenerateEmail
    // below) — promoting someone to admin does NOT touch their plan_id.
    public function updateRole($id, $role)
    {
        if (!in_array($role, ['admin', 'user'], true)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    // Admin-only (or a future billing flow): change a user's plan
    public function updatePlan($id, $planId)
    {
        $stmt = $this->db->prepare("UPDATE users SET plan_id = ? WHERE id = ?");
        return $stmt->execute([$planId, $id]);
    }

    // Delete user
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Find by email (for login / registration checks)
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, p.slug AS plan_slug, p.name AS plan_name,
                    p.max_cv_uploads, p.max_emails
             FROM users u
             LEFT JOIN plans p ON p.id = u.plan_id
             WHERE u.email = ?"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Verify a plaintext password against the stored hash
    public function verifyPassword($user, $password)
    {
        return $user && password_verify($password, $user['password']);
    }

    // ---- Usage / plan-limit helpers ----------------------------------

    // true if this user is still under their plan's CV upload limit.
    // Admins always bypass limits, regardless of their plan.
    public function canUploadCv($id)
    {
        $user = $this->find($id);
        if (!$user) return false;
        if ($user['role'] === 'admin') return true;
        if ($user['max_cv_uploads'] === null) return true; // unlimited (pro)
        return (int) $user['cv_uploads_count'] < (int) $user['max_cv_uploads'];
    }

    // true if this user is still under their plan's email generation limit.
    // Admins always bypass limits, regardless of their plan.
    public function canGenerateEmail($id)
    {
        $user = $this->find($id);
        if (!$user) return false;
        if ($user['role'] === 'admin') return true;
        if ($user['max_emails'] === null) return true; // unlimited (pro)
        return (int) $user['emails_generated_count'] < (int) $user['max_emails'];
    }

    public function incrementCvUploads($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET cv_uploads_count = cv_uploads_count + 1 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    public function incrementEmailsGenerated($id)
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET emails_generated_count = emails_generated_count + 1 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}