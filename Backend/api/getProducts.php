<?php
/**
 * API Endpoint: getProducts.php
 * Handles product listing, detail, search, and category retrieval
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/productController.php';

$action = $_GET['action'] ?? 'all';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$searchTerm = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$response = ["status" => "error", "message" => "Invalid request", "data" => null];
http_response_code(400);

try {
    if ($id) {
        $response = $productController->show($id);
    } elseif ($action == 'search') {
        $response = $productController->search($searchTerm, $category);
    } elseif ($action == 'categories') {
        $response = $productController->getCategories();
    } elseif ($action == 'featured') {
        $response = $productController->listFeatured();
    } else {
        $response = $productController->listAll();
    }

    if ($response['status'] === 'success') {
        http_response_code(200);
    } else {
        http_response_code(404);
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