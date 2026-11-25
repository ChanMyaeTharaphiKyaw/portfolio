<?php
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage("Please login first to delete blog", 'error');
    redirect('login.php');
}

// Validate Blog ID
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {
    $blogId = $paramResult;

    // Retrieve Blog Record
    $blog = getById($pdo, 'blog', $blogId);

    // Check Blog Exists
    if ($blog['status'] == 200) {
        $blogData = $blog['data'];

        // Delete Blog Record 
        $blogDelete = deleteQuery($pdo, 'blog', $blogId);

        if ($blogDelete) {

            // Delete Blog Image
            if (!empty($blogData['image'])) {
                $imagePath = '../' . $blogData['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            setMessage('Blog Deleted Successfully.', 'success');
        } else {
            setMessage('Failed to delete blog.', 'error');
        }
    } else {
        // Blog ID Does Not Exist
        setMessage($blog['message'], 'error');
    }
} else {
    // Invalid Blog ID
    setMessage($paramResult, 'error');
}
redirect('blogs.php');