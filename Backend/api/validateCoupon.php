<?php
/**
 * API Endpoint: validateCoupon.php
 * Validates a promo code against the current cart total.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../controller/cartController.php';

$data = json_decode(file_get_contents("php://input"), true);
$code = isset($data['code']) ? trim($data['code']) : '';

if (empty($code)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Coupon code is required", "data" => null]);
    exit;
}

try {
    $total = $cartController->getTotal();
    $couponModel = new Coupon($conn);
    $response = $couponModel->validate($code, $total);

    if ($response['status'] === 'success') {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        "status" => "error", 
        "message" => "Coupon System Error: " . $e->getMessage(), 
        "data" => null
    ];
}

echo json_encode($response);
?>