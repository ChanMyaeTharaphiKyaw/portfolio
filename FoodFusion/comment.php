<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to post a comment.";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id   = $_SESSION['user_id'];
    $post_id   = (int)($_POST['post_id'] ?? 0);
    $post_type = $_POST['post_type'] ?? '';
    $comment   = trim($_POST['comment'] ?? '');

    if ($post_id <= 0 || empty($comment)) {
        $_SESSION['comment_error'] = "Please write something before posting.";
        header("Location: community_details.php?id=" . $post_id . "&type=" . $post_type);
        exit;
    }

    if ($post_type === 'recipe') {
        $sql = "INSERT INTO recipe_comments (recipe_id, user_id, comment_text)
                VALUES (:post_id, :user_id, :comment)";
    } elseif ($post_type === 'community') {
        $sql = "INSERT INTO post_comments (post_id, user_id, comment_text)
                VALUES (:post_id, :user_id, :comment)";
    } else {
        die('Invalid post type.');
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':comment' => $comment
    ]);

    header("Location: community_details.php?id=" . $post_id . "&type=" . $post_type);
    exit;
}
?>