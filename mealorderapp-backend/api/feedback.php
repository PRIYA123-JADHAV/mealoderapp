<?php
header("Content-Type: application/json");
include '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->mobile) && !empty($data->message)) {
    $mobile = $conn->real_escape_string($data->mobile);
    $message = $conn->real_escape_string($data->message);

    $res = $conn->query("SELECT id FROM users WHERE mobile='$mobile'");
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $user_id = $user['id'];
        $conn->query("INSERT INTO feedback(user_id,message) VALUES('$user_id','$message')");
        echo json_encode(["success"=>true, "message"=>"Feedback submitted successfully"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"User not found"]);
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Invalid input"]);
}
?>
