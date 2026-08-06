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
}
