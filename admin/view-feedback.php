<?php
include(__DIR__ . '/../config/db.php');

// ✅ Handle deletion safely
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: view-feedback.php");
    exit;
}

$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Feedback & Reviews</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ccc;
    }
    th {
      background-color: #007bff;
      color: #fff;
    }
    .delete-btn {
      color: red;
      text-decoration: none;
      font-weight: bold;
    }
    .delete-btn:hover {
      text-decoration: underline;
    }
    .back-btn {
      margin-top: 20px;
      display: inline-block;
      background: #007bff;
      color: #fff;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
    }
    .no-data {
      text-align: center;
      padding: 15px;
      font-size: 16px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📝 Customer Feedback & Reviews</h2>

    <?php if ($result->num_rows > 0) { ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Message</th>
        <th>Submitted At</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['mobile']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['message']) ?></td>
          <td><?= $row['submitted_at'] ?></td>
          <td><a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this feedback?')">Delete</a></td>
        </tr>
      <?php endwhile; ?>
    </table>
    <?php } else { ?>
      <div class="no-data">No feedback available yet.</div>
    <?php } ?>

    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
