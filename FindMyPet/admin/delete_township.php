<?php

require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete township.', 'error');
    redirect('login.php');
}

// Validate Township ID Parameter
$paramResult = checkParamId('id');

if(is_numeric($paramResult)) {
    $townshipId = $paramResult;

    // Retrieve Township Record
    $township = getById($pdo, 'township', $townshipId);

    // Check Township Exists
    if($township['status'] == 200) {

        // Check Whether Township Is Used By Existing Posts
        $checkSql = "SELECT COUNT(*) FROM post WHERE township_id = :township_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'township_id' => $townshipId
        ]);

        $postCount = $checkStmt->fetchColumn();

        // Prevent Deletion If Township Has Related Posts
        if ($postCount > 0) {

            setMessage('Cannot delete this township because it contains existing posts.', 'error');
        } else {

            // Delete Township Record
            $townshipDeleteResult = deleteQuery($pdo, 'township', $townshipId);

            if ($townshipDeleteResult) {
                setMessage('Township deleted successfully.', 'success');
            } else {
                setMessage('Failed to delete township.', 'error');
            }
        }
    } else {

        // Township ID Does Not Exist
        setMessage($township['message'], 'error');
    }

} else {

    // Invalid Township ID
    setMessage($paramResult, 'error');
}

redirect('townships.php');