<?php

class Signature
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM signatures WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM signatures WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($userId, array $data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO signatures
                (user_id, name, title, email, phone, linkedin, github, portfolio, custom_text,
                 image_shape, image_size, layout, accent_color, show_icons, font_family, links_columns, image_base64)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $userId, $data['name'], $data['title'], $data['email'], $data['phone'],
            $data['linkedin'], $data['github'], $data['portfolio'], $data['custom_text'],
            $data['image_shape'], $data['image_size'], $data['layout'], $data['accent_color'],
            $data['show_icons'] ? 1 : 0, $data['font_family'], $data['links_columns'],
            $data['image_base64'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update($id, $userId, array $data)
    {
        $sql = "UPDATE signatures SET
                    name = ?, title = ?, email = ?, phone = ?, linkedin = ?, github = ?, portfolio = ?,
                    custom_text = ?, image_shape = ?, image_size = ?, layout = ?, accent_color = ?,
                    show_icons = ?, font_family = ?, links_columns = ?";
        $params = [
            $data['name'], $data['title'], $data['email'], $data['phone'],
            $data['linkedin'], $data['github'], $data['portfolio'], $data['custom_text'],
            $data['image_shape'], $data['image_size'], $data['layout'], $data['accent_color'],
            $data['show_icons'] ? 1 : 0, $data['font_family'], $data['links_columns'],
        ];

        if (!empty($data['image_base64'])) {
            $sql .= ", image_base64 = ?";
            $params[] = $data['image_base64'];
        }

        $sql .= " WHERE id = ? AND user_id = ?";
        $params[] = $id;
        $params[] = $userId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM signatures WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}
