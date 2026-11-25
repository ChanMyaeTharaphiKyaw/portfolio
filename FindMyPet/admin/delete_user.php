<?php
require_once "../database/pdo.php";
require_once "../database/functions.php";

// Check Admin Authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    setMessage('Please login first to delete user.', 'error');
    redirect('login.php');
}

// Validate User ID Parameter
$paramResult = checkParamId('id');

if (is_numeric($paramResult)) {

    $userId = $paramResult;

    // Retrieve User Record
    $user = getById($pdo, 'users', $userId);

    // Check User Exists
    if ($user['status'] == 200) {

        // Delete User Record
        $userDeleteResult = deleteQuery($pdo, 'users', $userId);

        if ($userDeleteResult) {

            setMessage('User deleted successfully.', 'success');
        } else {

            setMessage('Failed to delete user.', 'error');
        }
    } else {
        // User ID Does Not Exist
        setMessage($user['message'], 'error');
    }
} else {
    // Invalid User ID
    setMessage($paramResult, 'error');
}
redirect('users.php');