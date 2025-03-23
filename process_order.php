<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce"; // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Insert Order into Database
$name = $conn->real_escape_string($data['name']);
$phone = $conn->real_escape_string($data['phone']);
$address = $conn->real_escape_string($data['address']);
$payment_method = $conn->real_escape_string($data['payment_method']);
$cart = json_encode($data['cart']);

$sql = "INSERT INTO orders (name, phone, address, payment_method, cart) 
        VALUES ('$name', '$phone', '$address', '$payment_method', '$cart')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Order placed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Order failed"]);
}

$conn->close();
?>
