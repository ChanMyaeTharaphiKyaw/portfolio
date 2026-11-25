<?php
session_start();
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete comment.', 'error');
    redirect('login.php');
}

// Validate Comment ID Parameter
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $commentId = (int)$paramResult;

    // Retrieve Comment Record
    $comment = getById($pdo, 'comment', $commentId);

    // Check Comment Exists
    if ($comment['status'] == 200) {

        // Delete Comment Record
        $commentDeleteResult = deleteQuery($pdo, 'comment', $commentId);

        if ($commentDeleteResult) {
            setMessage('Comment deleted successfully.', 'success');
        } else {
            setMessage('Failed to delete comment.', 'error');
        }
    } else {

        // Comment ID Does Not Exist
        setMessage($comment['message'], 'error');
    }
} else {

    // Invalid Comment ID
    setMessage($paramResult, 'error');
}

redirect("comments.php");