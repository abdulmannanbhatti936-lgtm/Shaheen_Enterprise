<?php
/**
 * API Endpoint: getRecommendations.php
 * Fetches product recommendations based on category and featured status.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/productController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$response = ["status" => "error", "message" => "Product ID required", "data" => null];
http_response_code(400);

if ($id) {
    try {
        // 1. Get current product to identify category
        $currentRes = $productController->show($id);
        
        if ($currentRes['status'] === 'success') {
            $currentProduct = $currentRes['data'];
            $category = $currentProduct['category'];

            // 2. Fetch all products to filter recommendations
            // Note: In a larger DB, this should be a specific SQL query
            $allRes = $productController->listAll();
            $allProducts = ($allRes['status'] === 'success') ? $allRes['data'] : [];

            $recommendations = array_filter($allProducts, function ($p) use ($category, $id) {
                return $p['category'] === $category && $p['id'] != $id;
            });

            // 3. Fallback to featured products if needed
            if (count($recommendations) < 4) {
                $featuredRes = $productController->listFeatured();
                $featured = ($featuredRes['status'] === 'success') ? $featuredRes['data'] : [];
                
                foreach ($featured as $f) {
                    if ($f['id'] != $id && !array_filter($recommendations, fn($r) => $r['id'] == $f['id'])) {
                        $recommendations[] = $f;
                    }
                    if (count($recommendations) >= 4) break;
                }
            }

            $response = [
                "status" => "success",
                "message" => "Recommendations retrieved",
                "data" => array_slice(array_values($recommendations), 0, 4)
            ];
            http_response_code(200);
        } else {
            $response = ["status" => "error", "message" => "Product not found", "data" => null];
            http_response_code(404);
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response = ["status" => "error", "message" => "Recommendation Engine Error: " . $e->getMessage(), "data" => null];
    }
}

echo json_encode($response);
?>