<?php
require_once '../database/pdo.php';
require_once '../database/functions.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $verifySql = "SELECT id, full_name, email, verification_token, token_expire, is_verified FROM users WHERE verification_token = :token LIMIT 1";
    $verifyStmt = $pdo->prepare($verifySql);
    $verifyStmt->execute(['token' => $token]);
    $user = $verifyStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // 1. Check if already verified
        if ($user['is_verified'] == '1') {
            setMessage("Your email address has already been verified. Please log in.", "info");
            redirect("login.php");
        }

        // Check if token is expired
        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime > $user['token_expire']) {
            setMessage("Your previous verification link expired. Please request a new verification email.", "error");
            redirect("login.php");
        }

        // Update Verification for Valid Token
        $sql = "UPDATE users SET is_verified = '1', token_expire = NULL WHERE verification_token = :verification_token";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['verification_token' => $token]);

        setMessage("Your email has been verified successfully. Please log in.", "success");
        redirect("login.php");

    } else {
        // Token not found
        setMessage("Invalid Token.", "error");
       redirect("login.php");
    }
} else {
    setMessage("Not Allowed.", "error");
    redirect("login.php");
}
