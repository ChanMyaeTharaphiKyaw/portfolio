<?php
session_start();
require_once 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']); 
    $message = trim($_POST['message']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact-error'] = "Please enter a valid email address.";
        header("Location: contact.php");
        exit();
    }

    try {
        $sql = "INSERT INTO contact_messages (full_name, email, subject, message) 
                VALUES (:full_name, :email, :subject, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $full_name,
            ':email'     => $email,
            ':subject'   => $subject,
            ':message'   => $message
        ]);

        $_SESSION['contact-success'] = "Thank you! We have received your enquiry regarding: " . htmlspecialchars($subject);
        header("Location: contact.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: Could not save your message.";
        header("Location: contact.php");
        exit();
    }
}