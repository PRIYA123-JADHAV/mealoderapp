<?php
include(__DIR__ . '/../config/db.php');

$result = $conn->query("SELECT n.*, u.name AS username FROM notifications n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Notifications</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
<h2 style="text-align:center;">All Notifications</h2>
<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Title</th>
        <th>Message</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?: 'All Users' ?></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['message'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>
