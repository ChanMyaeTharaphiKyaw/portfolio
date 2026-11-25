<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    $check_sql = "SELECT * FROM event_registrations WHERE event_id = :event_id AND user_id = :user_id";
    $stmt = $pdo->prepare($check_sql);
    $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);

    if ($stmt->fetch()) {
        $_SESSION['event_error'] = "You are already registered for this event.";
    } else {
        $sql = "INSERT INTO event_registrations (event_id, user_id) VALUES (:event_id, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);

        $_SESSION['event_success'] = "You have successfully registered for the event.";
    }
}

header("Location: index.php#events");
exit();