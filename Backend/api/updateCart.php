<?php
/**
 * API Endpoint: updateCart.php
 * Updates or removes an item from the shopping cart
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/cartController.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : null;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : null;
$action = $data['action'] ?? 'update';

if (!$id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Item ID is missing", "data" => null]);
    exit;
}

try {
    if ($action === 'remove') {
        $response = $cartController->remove($id);
    } else {
        if ($quantity === null) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Quantity is required for update", "data" => null]);
            exit;
        }
        $response = $cartController->update($id, $quantity);
    }
    http_response_code(200);
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        "status" => "error",
        "message" => "Server Error: " . $e->getMessage(),
        "data" => null
    ];
}

echo json_encode($response);
?>