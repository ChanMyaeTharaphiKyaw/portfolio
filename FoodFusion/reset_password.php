<?php
session_start();
require_once 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $new_password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $errors = [];

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (
        !preg_match('/[A-Za-z]/', $new_password) ||
        !preg_match('/\d/', $new_password) ||
        !preg_match('/[\W_]/', $new_password)) {
        $errors[] = "Password must contain letters, numbers, and special characters.";
    }

    if (!empty($errors)) {
        $_SESSION['reset_errors'] = $errors;
        header("Location: login.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, failed_login_attempts = 0, lock_time = NULL WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);

        $_SESSION['success_message'] = "Password reset successfully. Please login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['reset_errors'] = ["Email not found."];
        header("Location: login.php");
        exit;
    }
}
