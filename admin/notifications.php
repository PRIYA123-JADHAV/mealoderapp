<?php
include(__DIR__ . '/../config/db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['mobile'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($mobile) || empty($message)) {
        $error = "Mobile number and message are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO notifications (mobile, title, message, type, related_id, is_read, created_at) VALUES (?, ?, ?, 'system', NULL, 0, NOW())");
        $title = "Admin Notification";  // You can change this or add a title input
        $stmt->bind_param("sss", $mobile, $title, $message);

        if ($stmt->execute()) {
            $success = "Notification sent successfully.";
        } else {
            $error = "Failed to send notification: " . $stmt->error;
        }
    }
}

// Get customers list with mobile numbers
$customers = $conn->query("SELECT id, name, mobile FROM customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notification</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="container">
        <h2>Send Notification to Customer</h2>

        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form method="POST">
            <label for="mobile">Select Customer Mobile Number:</label>
            <select name="mobile" required>
                <option value="">-- Select Mobile Number --</option>
                <?php while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['mobile']) ?>">
                        <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['mobile']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <br><br>

            <label for="message">Notification Message:</label><br>
            <textarea name="message" rows="5" cols="50" required></textarea>

            <br><br>
            <button type="submit">Send Notification</button>
        </form>
    </div>
</body>
</html>
