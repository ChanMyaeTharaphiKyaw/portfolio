<?php

class NotificationHelper
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Send Notification to Specific User
    public function sendToUser($userId, $postId, $message, $type, $link = null) {
        $sql = "INSERT INTO notification (user_id, post_id, message, type, link, is_read, is_pushed) VALUES (:user_id, :post_id, :message, :type, :link, 0, 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':post_id' => $postId,
            ':message' => $message,
            ':type' => $type,
            ':link' => $link
        ]);
    }

    // Send Notification to All Users
    public function sendToAll($message, $type = "announcement", $link = null) {
        $sql = "SELECT id FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        // Create Notification for Each User
        foreach ($users as $userId) {
            $this->sendToUser($userId, null, $message, $type, $link
            );
        }
    }
}
