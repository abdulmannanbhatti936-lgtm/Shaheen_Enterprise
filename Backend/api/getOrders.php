<?php
/**
 * API Endpoint: getOrders.php
 * Fetches order history for the authenticated user.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../controller/orderController.php';

if (!$authController->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Please login to view orders", "data" => null]);
    exit;
}

$user = $authController->getCurrentUser();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

try {
    if ($id && $action == 'details') {
        $res = $orderController->details($id);
        if ($res['status'] === 'success') {
            http_response_code(200);
            echo json_encode($res);
        } else {
            http_response_code(400);
            echo json_encode($res);
        }
        exit;
    }

    $res = $orderController->listByUser($user['id']);
    if ($res['status'] === 'success') {
        $orders = $res['data'];
        
        // Enrich orders with item count or brief summary if needed
        foreach ($orders as &$order) {
            $itemRes = $orderController->details($order['id']);
            $order['items'] = ($itemRes['status'] === 'success') ? $itemRes['data'] : [];
        }
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Orders retrieved successfully",
            "data" => $orders
        ]);
    } else {
        http_response_code(400);
        echo json_encode($res);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Order System Error: " . $e->getMessage(), "data" => null]);
}
?>