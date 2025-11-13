<?php
include(__DIR__ . '/../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['dish_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO menu (dish_name, price, category, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $category, $image_url);
    $stmt->execute();

    header("Location: manage-menus.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add New Dish</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background: #f8f9fa;
    }
    form {
      background: white;
      padding: 25px;
      border-radius: 8px;
      max-width: 500px;
      margin: auto;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    input, select {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background: #28a745;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    a {
      display: inline-block;
      margin-top: 10px;
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center;">➕ Add New Dish</h2>
  <form method="POST">
    <label>Dish Name:</label>
    <input type="text" name="dish_name" required>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" required>

    <label>Category:</label>
    <select name="category" required>
      <option value="">-- Select Category --</option>
      <option value="Starters">Starters</option>
      <option value="Lunch">Lunch</option>
      <option value="Dinner">Dinner</option>
      <option value="Dessert">Dessert</option>
      <option value="Breakfast">Breakfast</option>
    </select>

    <label>Image URL:</label>
    <input type="text" name="image_url" placeholder="images/food1.png" required>

    <button type="submit">Add Dish</button>
    <a href="manage-menus.php">← Back to Menu List</a>
  </form>
</body>
</html>
