<?php
/**
 * API Endpoint: getStats.php
 * Aggregates site-wide statistics for the Admin Dashboard.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controller/authController.php';

// Restricted to logged-in users (In Phase 2, we will add strict role-based access)
if (!$authController->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized", "data" => null]);
    exit;
}

try {
    // 1. Get Sales Trend (Last 7 Days)
    $salesTrend = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $query = "SELECT SUM(total) as daily_total FROM orders WHERE DATE(created_at) = :date AND status != 'Cancelled'";
        $stmt = $conn->prepare($query);
        $stmt->execute([':date' => $date]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $salesTrend[] = [
            "date" => date('M d', strtotime($date)),
            "total" => (float)($res['daily_total'] ?? 0)
        ];
    }

    // 2. Get Product Category Distribution
    $queryCategories = "SELECT category, COUNT(*) as count FROM products GROUP BY category";
    $stmtCategories = $conn->query($queryCategories);
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get Order Status Distribution
    $queryStatuses = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $stmtStatuses = $conn->query($queryStatuses);
    $statuses = $stmtStatuses->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Stats retrieved",
        "data" => [
            "salesTrend" => $salesTrend,
            "categories" => $categories,
            "statuses" => $statuses
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Stats Error: " . $e->getMessage(), "data" => null]);
}
?>