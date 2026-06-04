<?php
/**
 * API Endpoint: getUser.php
 * Fetches the currently authenticated user's profile data.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/authController.php';

if ($authController->isLoggedIn()) {
    $user = $authController->getCurrentUser();
    // Security: Ensure sensitive fields are never exposed
    unset($user['password']);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "message" => "User data retrieved", 
        "data" => $user
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Unauthorized: Please login to access profile", 
        "data" => null
    ]);
}
?>