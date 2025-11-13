<?php
include(__DIR__ . '/../config/db.php');

// Fetch all dishes from menu table
$result = $conn->query("SELECT * FROM menu ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Menus & Dishes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f6f8fa;
      padding: 20px;
    }
    h2 {
      color: #333;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      background: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    img {
      height: 60px;
    }
    .actions button {
      padding: 5px 10px;
      margin-right: 5px;
      border: none;
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }
    .edit-btn { background: #28a745; }
    .delete-btn { background: #dc3545; }
    .add-btn {
      display: inline-block;
      margin-bottom: 10px;
      background: #007bff;
      color: white;
      padding: 8px 15px;
      border-radius: 4px;
      text-decoration: none;
    }
    .back-btn {
      margin-top: 20px;
      display: inline-block;
      background: #007bff;
      color: white;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <h2>📋 Manage Menus & Dishes</h2>
  <a href="add-dish.php" class="add-btn">➕ Add New Dish</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Dish Name</th>
        <th>Price (₹)</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td>
            <?php if (!empty($row['image_url'])) { ?>
              <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="dish image">
            <?php } else { ?>
              No Image
            <?php } ?>
          </td>
          <td><?= htmlspecialchars($row['dish_name']) ?></td>
          <td>₹<?= number_format($row['price'], 2) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td class="actions">
            <a href="edit-dish.php?id=<?= $row['id'] ?>"><button class="edit-btn">Edit</button></a>
            <form method="POST" action="delete-dish.php" style="display:inline;" onsubmit="return confirm('Delete this dish?');">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button class="delete-btn" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <br><br>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</body>
</html>
