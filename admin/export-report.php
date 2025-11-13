<?php
include(__DIR__ . '/../config/db.php');

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_report.csv"');

$output = fopen("php://output", "w");

// ✅ Updated column headers
fputcsv($output, ['Order ID', 'User ID', 'Mobile', 'Total Amount', 'Status', 'Order Date']);

$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['user_id'],
        $row['mobile'],
        $row['total_amount'],
        $row['status'],
        date('d-m-Y H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;
?>
