<?php
session_start();
header('Content-Type: application/json');

require_once '../database/pdo.php';

$userId = $_SESSION['auth_user']['id'] ?? 0;
if ($userId == 0) { 
    echo json_encode(['success' => false]); 
    exit; 
}

// Mark Notification as Read
if (isset($_GET['mark_read']) && $_GET['mark_read'] == 1 && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notification SET is_read = 1 WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $_GET['id'], 'user_id' => $userId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Retrieve Notifications for Dropdown
try {
    $sql = "SELECT * FROM notification WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notifications as &$n) {
        $n['time'] = date('F j, h:i A', strtotime($n['created_at']));
    }

    echo json_encode($notifications);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}