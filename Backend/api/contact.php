<?php
/**
 * API Endpoint: contact.php
 * Handles contact form submissions.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Message.php';

$data = json_decode(file_get_contents("php://input"), true);
$response = ["status" => "error", "message" => "Invalid request data", "data" => null];
http_response_code(400);

if (isset($data['name'], $data['email'], $data['message'])) {
    try {
        $msg = new Message($conn);
        $msg->name = $data['name'];
        $msg->email = $data['email'];
        $msg->message = $data['message'];

        if ($msg->create()) {
            $response = ["status" => "success", "message" => "Message sent successfully. We'll be in touch soon.", "data" => null];
            http_response_code(200);
        } else {
            $response = ["status" => "error", "message" => "Could not save message. Please try again later.", "data" => null];
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response = ["status" => "error", "message" => "Contact System Error: " . $e->getMessage(), "data" => null];
    }
}

echo json_encode($response);
?>