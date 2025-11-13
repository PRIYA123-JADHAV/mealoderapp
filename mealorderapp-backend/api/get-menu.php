<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php'; 

header('Content-Type: application/json');

// Fetch all menu items from DB
$sql = "SELECT * FROM menu ORDER BY id DESC";
$result = $conn->query($sql);

$menu = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fix image path
        $filename = basename(str_replace('\\', '/', $row['image_url']));
        $row['image_url'] = "images/" . $filename;

        $menu[] = $row;
    }
}

// Return JSON response
echo json_encode([
    "success" => true,
    "menu" => $menu
]);

$conn->close();
?>
