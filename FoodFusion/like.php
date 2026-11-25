<?php
session_start();
require_once 'pdo.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Login required'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = (int) ($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';


if ($type === 'recipe') {
    $table = 'recipe_likes';
    $id_column = 'recipe_id';
} elseif ($type === 'community') {
    $table = 'post_likes';
    $id_column = 'post_id';
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid type'
    ]);
    exit;
}

$check = $pdo->prepare("SELECT 1 FROM $table WHERE $id_column = ? AND user_id = ?");
$check->execute([$post_id, $user_id]);

if ($check->fetch()) {

    $stmt = $pdo->prepare("DELETE FROM $table WHERE $id_column = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $action = 'unliked';
} else {

    $stmt = $pdo->prepare("INSERT INTO $table ($id_column, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $user_id]);
    $action = 'liked';
}

    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $id_column = ?");
    $count_stmt->execute([$post_id]);
    $new_count = $count_stmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'action' => $action,
        'new_count' => (int)$new_count
    ]);
exit;
