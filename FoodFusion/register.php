<?php
session_start();
require_once 'pdo.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $email = trim($_POST['email']);

    $errors = [];

    if (strlen($first_name) < 2) {
        $errors[] = "First name is too short.";
    }

    if (!preg_match("/^[a-zA-Z-' ]+$/", $first_name)) {
        $errors[] = "First name can only contain letters, spaces, apostrophes or hyphens.";
    }

    if (strlen($last_name) < 2) {
        $errors[] = "Last name is too short.";
    }

    if (!preg_match("/^[a-zA-Z-' ]+$/", $last_name)) {
        $errors[] = "Last name can only contain letters, spaces, apostrophes or hyphens.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "This email is already registered.";
    }

    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must contain letters, numbers and special character";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $profile = null;
    $uploadOk = 1;

    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = basename($_FILES["profile"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&  $imageFileType != "gif") {
            $errors[] = "Profile picture must be JPG, PNG, or GIF.";
            $uploadOk = 0;
        }

        if ($_FILES["profile"]["size"] > 500000) {
            $errors[] = "Profile picture must be less than 0.5MB.";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
            $profile = $target_file;
        }
    }

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_data'] = $_POST;
        $_SESSION['register_source'] = $_POST['form_source'] ?? 'header_register';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $sql = "INSERT INTO users (first_name, last_name, email, password, profile, registered_at)
            VALUES (:first_name, :last_name, :email, :password, :profile, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':password' => $hashed_password,
        ':profile' => $profile
    ]);

    $_SESSION['success_message'] = "Account created! You can now log in.";
    header("Location: login.php");
    exit;
}
