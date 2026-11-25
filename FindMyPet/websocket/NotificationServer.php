<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class NotificationServer implements MessageComponentInterface
{
    protected SplObjectStorage $clients;
    protected array $userConnections = [];

    protected PDO $db;

    // Initialize WebSocket Database Connection
    public function __construct($dbHost, $dbName, $dbUser, $dbPass)
    {
        $this->clients = new SplObjectStorage();

        // Connect to Database
        $this->db = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Notification server initialized.\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $userId = isset($query['user_id']) ? (int)$query['user_id'] : 0;

        if ($userId > 0) {
            if (!isset($this->userConnections[$userId])) {
                $this->userConnections[$userId] = [];
            }

            $this->userConnections[$userId][] = $conn;
            echo "User {$userId} connected.\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {}

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        foreach ($this->userConnections as $userId => &$connections) {
            foreach ($connections as $index => $client) {
                if ($client === $conn) {
                    unset($connections[$index]);
                    echo "User {$userId} disconnected.\n";
                    break;
                }
            }
            if (empty($connections)) {
                unset($this->userConnections[$userId]);
            }
        }
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "Socket Error: {$e->getMessage()}\n";
        $conn->close();
    }

    public function checkAndPushNotifications()
    {
        $sql = "SELECT id, user_id, message, type, link FROM notification WHERE is_pushed = 0 ORDER BY id ASC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($notifications as $noti) {
            $targetUserId = (int)$noti['user_id'];
            $payload = json_encode([
                "id"      => (int)$noti["id"],
                "type"    => $noti["type"],
                "message" => $noti["message"],
                "link"    => $noti["link"],
                "time"    => date("H:i")
            ]);

            if ($targetUserId === 0) {
                foreach ($this->clients as $client) {
                    $client->send($payload);
                }
            } else {
                if (isset($this->userConnections[$targetUserId])) {
                    foreach ($this->userConnections[$targetUserId] as $client) {
                        $client->send($payload);
                    }
                }
            }
            $updateSql = "UPDATE notification SET is_pushed = 1 WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':id' => $noti["id"]
            ]);
        }
    }
}
