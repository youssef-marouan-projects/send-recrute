<?php

class Campaign
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $cvUploadId, $signatureId, $subject, $message, $total)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO campaigns (user_id, cv_upload_id, signature_id, subject, message, status, total)
             VALUES (?, ?, ?, ?, ?, 'pending', ?)"
        );
        $stmt->execute([$userId, $cvUploadId, $signatureId, $subject, $message, $total]);
        return (int) $this->db->lastInsertId();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findForUser($id, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public function setStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE campaigns SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function incrementCounters($id, $sentDelta = 0, $failedDelta = 0)
    {
        $stmt = $this->db->prepare(
            "UPDATE campaigns SET sent_count = sent_count + ?, failed_count = failed_count + ? WHERE id = ?"
        );
        return $stmt->execute([$sentDelta, $failedDelta, $id]);
    }
}
