<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include(__DIR__ . '/../config/db.php');

// ✅ Use existing tables and column names
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$totalDelivered = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='completed'")->fetch_assoc()['total'] ?? 0;
$totalRevenue = $conn->query("SELECT SUM(total_amount) AS total FROM orders WHERE status IN ('pending','completed')")->fetch_assoc()['total'] ?? 0;

// If you don't have menus table yet, show 0
$totalDishes = 0;
if ($conn->query("SHOW TABLES LIKE 'menus'")->num_rows > 0) {
    $totalDishes = $conn->query("SELECT COUNT(*) AS total FROM menus")->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: -250px; background-color: #343a40; transition: 0.3s; z-index: 1000; display: flex; flex-direction: column; }
        .sidebar-menu { flex: 1; overflow-y: auto; padding-top: 60px; }
        .sidebar-menu a { padding: 12px 25px; text-decoration: none; font-size: 16px; color: #ddd; display: block; transition: 0.2s; }
        .sidebar-menu a:hover { background-color: #495057; }
        .logout { padding: 20px; text-align: center; }
        .overlay { height: 100%; width: 0; position: fixed; z-index: 999; top: 0; left: 0; background-color: rgba(0,0,0,0.4); transition: 0.3s; }
        .header { background-color: #007bff; color: white; padding: 20px; display: flex; align-items: center; justify-content: space-between; }
        .menu-icon { font-size: 28px; cursor: pointer; margin-right: 15px; }
        .main-content { margin-left: 0; padding: 20px; transition: margin-left 0.3s; }
        .stats { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-top: 30px; }
        .card { background: white; padding: 25px; border-radius: 10px; width: 220px; box-shadow: 0 0 10px rgba(0,0,0,0.08); text-align: center; }
        .card h2 { margin: 0; font-size: 28px; color: #007bff; }
        .card p { margin-top: 5px; color: #444; }
        @media (max-width: 768px) { .card { width: 90%; } }
    </style>
</head>
<body>

<div id="mySidebar" class="sidebar">
    <div class="sidebar-menu">
        <a href="view-customers.php">👥 View Customers</a>
        <a href="view-orders.php">📦 View Orders</a>
        <a href="payments.html">💰 Payments List</a> <!-- ✅ NEW MENU ITEM ADDED -->
        <a href="manage-menus.php">🍽️ Manage Menus</a>
        <a href="send-notification.php">📢 Send Notifications</a>
        <a href="view-feedback.php">📝 View Feedback</a>
        <a href="generate-reports.php">📊 Generate Reports</a>
        <a href="export-report.php" class="btn btn-success">📤 Export Orders CSV</a>
    </div>
    <div class="logout">
        <a href="logout.php" style="background: #dc3545; color: white; padding: 10px 20px; display: block; border-radius: 6px;">🚪 Logout</a>
    </div>
</div>


<div id="overlay" class="overlay" onclick="closeSidebar()"></div>

<div class="header">
    <div style="display: flex; align-items: center; justify-content: center; position: relative;">
        <span class="menu-icon" onclick="openSidebar()" style="position: absolute; left: 20px;">&#9776;</span>
        <h1 style="margin: 10px; text-align:center; margin-left:450px;">Welcome, <?= $_SESSION['admin']; ?> 👋</h1>
    </div>
    <div style="text-align: center; margin-right:20px;">Admin Dashboard – TummyMealBox</div>
</div>

<div class="main-content">
    <div class="stats">
        <div class="card">
            <h2><?= $totalCustomers ?></h2>
            <p>Total Customers</p>
        </div>
        <div class="card">
            <h2><?= $totalOrders ?></h2>
            <p>Total Orders</p>
        </div>
        <div class="card">
            <h2><?= $totalDelivered ?></h2>
            <p>Delivered Orders</p>
        </div>
        <div class="card">
            <h2>₹<?= number_format($totalRevenue ?? 0, 2) ?></h2>
            <p>Total Revenue</p>
        </div>
        <div class="card">
            <h2><?= $totalDishes ?></h2>
            <p>Total Dishes</p>
        </div>
    </div>
</div>

<script>
    function openSidebar() { document.getElementById("mySidebar").style.left = "0"; document.getElementById("overlay").style.width = "100%"; }
    function closeSidebar() { document.getElementById("mySidebar").style.left = "-250px"; document.getElementById("overlay").style.width = "0"; }
</script>

</body>
</html>
