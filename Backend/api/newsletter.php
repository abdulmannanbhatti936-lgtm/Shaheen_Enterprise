<?php
/**
 * API Endpoint: newsletter.php
 * Handles newsletter subscriptions with unique email enforcement.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = ["status" => "error", "message" => "Invalid request method", "data" => null];
http_response_code(405);

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        $response = ["status" => "error", "message" => "Please enter a valid email address.", "data" => null];
    } else {
        try {
            // Ensure table exists
            $conn->exec("CREATE TABLE IF NOT EXISTS newsletters (
                id INT AUTO_INCREMENT PRIMARY KEY, 
                email VARCHAR(100) UNIQUE, 
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $stmt = $conn->prepare("INSERT INTO newsletters (email) VALUES (:email)");
            $stmt->execute([':email' => $email]);

            $response = ["status" => "success", "message" => "Welcome to the ritual! You've successfully subscribed.", "data" => null];
            http_response_code(200);
        } catch (PDOException $e) {
            // Error code 23000 is usually Integrity constraint violation (Duplicate key)
            if ($e->getCode() == '23000') {
                http_response_code(400);
                $response = ["status" => "error", "message" => "You are already part of our inner circle!", "data" => null];
            } else {
                http_response_code(500);
                $response = ["status" => "error", "message" => "Newsletter System Error. Please try again later.", "data" => null];
            }
        }
    }
}

echo json_encode($response);
?>
