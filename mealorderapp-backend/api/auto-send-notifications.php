<?php
include '../db.php';

$notifications = [
    [
        'mobile' => null,
        'title' => 'New Dish Added!',
        'message' => 'Try our new delicious pasta today.',
        'type' => 'promo',
    ],
    [
        'mobile' => null,
        'title' => "Today's Party Time",
        'message' => 'Join the party tonight with special offers!',
        'type' => 'system',
    ],
];

foreach ($notifications as $notif) {
    if ($notif['mobile'] === null) {
        $stmt = $conn->prepare("INSERT INTO notifications (mobile, title, message, type, is_read, created_at) VALUES (NULL, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("sss", $notif['title'], $notif['message'], $notif['type']);
    } else {
        $stmt = $conn->prepare("INSERT INTO notifications (mobile, title, message, type, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("ssss", $notif['mobile'], $notif['title'], $notif['message'], $notif['type']);
    }
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo json_encode(['success' => true, 'message' => 'Automatic notifications sent']);
