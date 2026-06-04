<?php
/**
 * API Endpoint: addToCart.php
 * Adds a product to the session-based shopping cart
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/cartController.php';
require_once __DIR__ . '/../controller/productController.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : (isset($data['product_id']) ? (int)$data['product_id'] : null);
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if (!$id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Product ID is missing", "data" => null]);
    exit;
}

try {
    $productRes = $productController->show($id);
    
    if ($productRes['status'] === 'success' && $productRes['data']) {
        $productData = $productRes['data'];
        $product = [
            'id' => $productData['id'],
            'name' => $productData['name'],
            'price' => $productData['price'],
            'image' => $productData['image'],
            'quantity' => $quantity
        ];
        
        $response = $cartController->add($product);
        http_response_code(200);
    } else {
        http_response_code(404);
        $response = ["status" => "error", "message" => "Product not found", "data" => null];
    }
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