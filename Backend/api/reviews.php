<?php
/**
 * API Endpoint: reviews.php
 * Handles product reviews - listing, submission with duplicate check and rating recalculation.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controller/authController.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ["status" => "error", "message" => "Invalid request", "data" => null];
http_response_code(400);

try {
    if ($method === 'GET') {
        $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
        if (!$product_id) {
            $response = ["status" => "error", "message" => "Product ID required", "data" => null];
        } else {
            $stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id ORDER BY r.created_at DESC");
            $stmt->execute([':product_id' => $product_id]);
            $reviews = $stmt->fetchAll();

            $response = [
                "status" => "success",
                "message" => "Reviews retrieved successfully",
                "data" => ["reviews" => $reviews]
            ];
            http_response_code(200);
        }
    }

    if ($method === 'POST') {
        if (!$authController->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Please login to leave a review", "data" => null]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];
        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
        $rating = isset($data['rating']) ? (int)$data['rating'] : 0;
        $comment = isset($data['comment']) ? trim(htmlspecialchars(strip_tags($data['comment']))) : '';

        // 1. Validation
        if (!$product_id || $rating < 1 || $rating > 5 || empty($comment)) {
            $response = ["status" => "error", "message" => "Invalid review data. Please provide rating and comment.", "data" => null];
        } else {
            // 2. Duplicate Check
            $stmt = $conn->prepare("SELECT id FROM reviews WHERE product_id = :p AND user_id = :u");
            $stmt->execute([':p' => $product_id, ':u' => $user_id]);
            if ($stmt->fetch()) {
                $response = ["status" => "error", "message" => "You have already reviewed this product.", "data" => null];
            } else {
                $conn->beginTransaction();
                try {
                    // 3. Insert Review
                    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (:p, :u, :r, :c)");
                    $stmt->execute([':p' => $product_id, ':u' => $user_id, ':r' => $rating, ':c' => $comment]);

                    // 4. Recalculate Average Rating
                    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = :p");
                    $stmt->execute([':p' => $product_id]);
                    $avgResult = $stmt->fetch();
                    $newAvg = round($avgResult['avg_rating'], 1);

                    // 5. Update Product Table (Added average_rating column if not exists in SQL audit)
                    $stmt = $conn->prepare("UPDATE products SET average_rating = :avg WHERE id = :id");
                    $stmt->execute([':avg' => $newAvg, ':id' => $product_id]);

                    $conn->commit();
                    $response = ["status" => "success", "message" => "Review submitted successfully", "data" => ["average_rating" => $newAvg]];
                    http_response_code(200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        "status" => "error",
        "message" => "Review System Error: " . $e->getMessage(),
        "data" => null
    ];
}

echo json_encode($response);
?>
