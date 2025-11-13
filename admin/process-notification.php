<?php
include(__DIR__ . '/../config/db.php');

$title = trim($_POST['title']);
$message = trim($_POST['message']);
$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

if ($title && $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type, is_read) VALUES (?, ?, ?, 'system', 0)");
    $stmt->bind_param("iss", $user_id, $title, $message);
    $stmt->execute();

    header("Location: view-notifications.php?success=1");
    exit();
} else {
    header("Location: send-notification.php?error=1");
    exit();
}
