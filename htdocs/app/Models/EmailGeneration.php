<?php

class EmailGeneration
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $cvUploadId, $jobPost, $language, $result)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO email_generations (user_id, cv_upload_id, job_post, language, result)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $cvUploadId, $jobPost, $language, $result]);
        return (int) $this->db->lastInsertId();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM email_generations WHERE user_id = ? ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Admin-only: every generated email from every user, newest first
    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT e.*, u.name AS user_name, u.email AS user_email
             FROM email_generations e
             JOIN users u ON u.id = e.user_id
             ORDER BY e.id DESC"
        );
        return $stmt->fetchAll();
    }
}
