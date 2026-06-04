<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
$email = $_GET['email'] ?? null;

if (!$id || !$email) {
    echo json_encode(["success" => false, "message" => "Order ID and Email are required"]);
    exit;
}

$stmt = $conn->prepare("SELECT o.status FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND u.email = ?");
$stmt->bind_param("is", $id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No matching order found"]);
    exit;
}

$order = $result->fetch_assoc();
echo json_encode(["success" => true, "status" => $order['status']]);
?>