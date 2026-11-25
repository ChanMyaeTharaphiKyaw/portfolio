<?php
session_start();
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check User Authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    setMessage('Please login first to delete your post', 'error');
    redirect('login.php');
}

$currentUserId = $_SESSION['auth_user']['id'];

$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $postId = $paramResult;
    // Retrieve Post Details
    $post = getPostById($pdo, $postId);

    if ($post['status'] == 200) {

        $postOwnerId = $post['data']['user_id'];
            $petId = $post['data']['pet_id'];

        if ($postOwnerId != $currentUserId) {
            setMessage("You are not authorized to delete this post.", 'error');
            redirect("my_posts.php");
        }
        // Delete Uploaded Image
        if (!empty($post['data']['image'])) {

            $imagePath = "../" . $post['data']['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Delete Post Record 
        $postDeleted = deleteQuery($pdo, 'post', $postId);

        if ($postDeleted) {
            // Delete Pet Record
            $petDeleted = deleteQuery($pdo, 'pet', $petId);

            if ($petDeleted) {
                setMessage("Post deleted successfully.", 'success');
            } else {
                setMessage("Failed to delete post.", 'error');
            }
        }
    } else {
        setMessage("Post not found.", 'error');
    }
} else {
    setMessage("Invalid post ID provided.", 'error');
}

redirect("my_posts.php");
