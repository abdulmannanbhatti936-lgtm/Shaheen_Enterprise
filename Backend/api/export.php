<?php
/**
 * API Endpoint: export.php
 * Generates CSV exports for Orders and Customers. Restricted to Admin.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controller/authController.php';

// Strict Admin-Only Access
if (!$authController->isLoggedIn() || $authController->getCurrentUser()['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo "Unauthorized: Admin access required.";
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'orders';
$filename = $type . '_export_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

try {
    if ($type === 'orders') {
        fputcsv($output, array('Order ID', 'Customer', 'Total ($)', 'Status', 'Date'));
        $query = "SELECT o.id, u.username, o.total, o.status, o.created_at 
                  FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
    } else if ($type === 'customers') {
        fputcsv($output, array('User ID', 'Username', 'Email', 'Role', 'Joined Date'));
        $query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
    }
} catch (Exception $e) {
    // In case of error during CSV generation, logging is better than partial CSV
    error_log("Export Error: " . $e->getMessage());
}

fclose($output);
exit;
?>