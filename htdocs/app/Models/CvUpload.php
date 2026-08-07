<?php

class CvUpload
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $originalName, $storedName, $path, $extension, $sizeBytes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO cv_uploads (user_id, original_name, stored_name, path, extension, size_bytes)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $originalName, $storedName, $path, $extension, $sizeBytes]);
        return (int) $this->db->lastInsertId();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cv_uploads WHERE user_id = ? ORDER BY id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM cv_uploads WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Admin-only: every CV uploaded by every user, newest first
    public function getAll()
    {
        $stmt = $this->db->query(
            "SELECT c.*, u.name AS user_name, u.email AS user_email
             FROM cv_uploads c
             JOIN users u ON u.id = c.user_id
             ORDER BY c.id DESC"
        );
        return $stmt->fetchAll();
    }
}
