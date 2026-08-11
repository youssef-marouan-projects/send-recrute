<?php

class CampaignRecipient
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function bulkCreate($campaignId, array $rows)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO campaign_recipients (campaign_id, email, post) VALUES (?, ?, ?)"
        );
        foreach ($rows as $row) {
            $stmt->execute([$campaignId, $row['email'], $row['post'] ?? null]);
        }
    }

    // Grabs up to $limit still-pending recipients for this campaign.
    // Used by the batch-send endpoint so one HTTP request only does a
    // little work at a time (keeps within PHP's execution-time limits
    // and lets the front-end show live progress between calls).
    public function getPendingBatch($campaignId, $limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM campaign_recipients WHERE campaign_id = ? AND status = 'pending' LIMIT ?"
        );
        $stmt->bindValue(1, $campaignId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function saveBody($id, $body)
    {
        $stmt = $this->db->prepare("UPDATE campaign_recipients SET body = ? WHERE id = ?");
        return $stmt->execute([$body, $id]);
    }

    public function markSent($id)
    {
        $stmt = $this->db->prepare("UPDATE campaign_recipients SET status = 'sent', sent_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markFailed($id, $error)
    {
        $stmt = $this->db->prepare("UPDATE campaign_recipients SET status = 'failed', error = ? WHERE id = ?");
        return $stmt->execute([$error, $id]);
    }

    public function countRemaining($campaignId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM campaign_recipients WHERE campaign_id = ? AND status = 'pending'");
        $stmt->execute([$campaignId]);
        return (int) $stmt->fetchColumn();
    }
}
