<?php
include(__DIR__ . '/../config/db.php');
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Orders</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background: #f4f4f4; }
    a { color: red; text-decoration: none; }
    select { padding: 6px; }
    button { padding: 6px 10px; background: #28a745; color: #fff; border: none; cursor: pointer; }
    button:hover { background: #218838; }
    .back-btn { margin-bottom: 10px; display: inline-block; text-decoration: none; color: #007bff; }
  </style>
</head>
<body>

<a class="back-btn" href="dashboard.php">⬅ Back to Dashboard</a>

<h2>Orders List</h2>

<table>
  <tr>
    <th>Order ID</th>
    <th>User ID</th>
    <th>Total Amount (₹)</th>
    <th>Status</th>
    <th>Address</th>
    <th>Order Date</th>
    <th>Action</th>
  </tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['user_id'] ?></td>
  <td><?= $row['total_amount'] ?></td>
  <td>
    <select onchange="updateStatus(<?= $row['id'] ?>, this.value)">
      <option value="pending" <?= ($row['status']=='pending'?'selected':'') ?>>Pending</option>
      <option value="processing" <?= ($row['status']=='processing'?'selected':'') ?>>Processing</option>
      <option value="delivered" <?= ($row['status']=='delivered'?'selected':'') ?>>Delivered</option>
      <option value="canceled" <?= ($row['status']=='canceled'?'selected':'') ?>>Canceled</option>
    </select>
  </td>
  <td><?= $row['address'] ?></td>
  <td><?= $row['created_at'] ?></td>
  <td><a href="delete-order.php?id=<?= $row['id'] ?>">Delete</a></td>
</tr>
<?php } ?>
</table>

<script>
function updateStatus(orderId, status) {
  fetch("http://localhost/mealorderapp/admin/update-order-status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `order_id=${orderId}&status=${status}`
  })
  .then(res => res.text())
  .then(response => {
    alert("✅ Order status updated to: " + status);
    console.log(response);
  })
  .catch(() => {
    alert("❌ Failed to update order status.");
  });
}
</script>

</body>
</html>
