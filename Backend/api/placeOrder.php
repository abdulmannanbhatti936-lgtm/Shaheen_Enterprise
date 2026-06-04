<?php
/**
 * API Endpoint: placeOrder.php
 * Handles atomic order placement, stock validation, and cart clearing.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/orderController.php';
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../controller/cartController.php';

// 1. Authentication Check
if (!$authController->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Please login to place an order", "data" => null]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user = $authController->getCurrentUser();
$cartItems = $cartController->get()['data']['items'] ?? [];
$total = $cartController->getTotal();

// 2. Pre-validation
if (empty($cartItems)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Your cart is empty", "data" => null]);
    exit;
}

if (!isset($data['address']) || empty(trim($data['address']))) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Shipping address is required", "data" => null]);
    exit;
}

try {
    // 3. Prepare Order Data
    $orderData = [
        'address' => trim($data['address']),
        'total' => $total,
        'items' => array_map(function ($item) {
            return [
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }, $cartItems)
    ];

    // 4. Execute Transaction via Controller
    $response = $orderController->place($user['id'], $orderData);

    if ($response['status'] === 'success') {
        // Clear cart upon successful order
        $cartController->clear();
        
        // Mock Email Sent (Log only for Phase 1)
        error_log("MOCK EMAIL: Order #" . $response['data']['order_id'] . " confirmation sent to " . $user['email']);
        
        http_response_code(200);
    } else {
        http_response_code(400);
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        "status" => "error",
        "message" => "Order System Error: " . $e->getMessage(),
        "data" => null
    ];
}

echo json_encode($response);
?>