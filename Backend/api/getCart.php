<?php
/**
 * API Endpoint: getCart.php
 * Returns the current session-based shopping cart data
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/cartController.php';

try {
    $response = $cartController->get();
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