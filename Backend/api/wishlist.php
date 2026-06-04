<?php
/**
 * API Endpoint: wishlist.php
 * Handles user rituals (wishlist) - list, add, remove toggle
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../models/Wishlist.php';

// 1. Authentication Check
if (!$authController->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Please login to save your rituals", "data" => null]);
    exit;
}

$wishlistModel = new Wishlist($conn);
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$response = ["status" => "error", "message" => "Invalid request", "data" => null];
http_response_code(400);

try {
    if ($method === 'GET' && $action === 'list') {
        // Retrieve User Wishlist
        $items = $wishlistModel->list($user_id);
        $response = [
            "status" => "success",
            "message" => "Rituals retrieved successfully",
            "data" => ["items" => $items]
        ];
        http_response_code(200);
    } 
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;

        if (!$product_id) {
            $response = ["status" => "error", "message" => "Product ID is missing", "data" => null];
        } else {
            if ($action === 'add') {
                if ($wishlistModel->add($user_id, $product_id)) {
                    $response = ["status" => "success", "message" => "Saved to your Rituals!", "data" => null];
                    http_response_code(200);
                } else {
                    $response = ["status" => "error", "message" => "Unable to save ritual", "data" => null];
                }
            } elseif ($action === 'remove') {
                if ($wishlistModel->remove($user_id, $product_id)) {
                    $response = ["status" => "success", "message" => "Ritual removed", "data" => null];
                    http_response_code(200);
                } else {
                    $response = ["status" => "error", "message" => "Unable to remove ritual", "data" => null];
                }
            } else {
                $response = ["status" => "error", "message" => "Invalid action", "data" => null];
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        "status" => "error",
        "message" => "Wishlist System Error: " . $e->getMessage(),
        "data" => null
    ];
}

echo json_encode($response);
?>