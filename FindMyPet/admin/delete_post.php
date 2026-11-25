<?php
session_start();
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete post.', 'error');
    redirect('login.php');
}

// Validate Post ID Parameter
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $postId = $paramResult;

    // Retrieve Post Record
    $post = getPostById($pdo, $postId);

    // Check Post Exists
    if ($post['status'] == 200) {

        // Delete Post Record
        $postDeleteResult = deleteQuery($pdo, 'post', $postId);

        if ($postDeleteResult) {

            // Delete Post Image
            if (!empty($post['data']['image'])) {

                $imagePath = "../" . $post['data']['image'];

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            setMessage('Post deleted successfully.', 'success');
        } else {
            setMessage('Failed to delete post.', 'error');
        }
    } else {

        // Post ID Does Not Exist
        setMessage("Post not found.", 'error');
    }
} else {
    // Invalid Post ID
    setMessage("Invalid post ID provided.", 'error');
}

redirect("posts.php");
