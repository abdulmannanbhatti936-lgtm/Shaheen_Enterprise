<?php
/**
 * Database Configuration & Global Error Handling
 * Phase 4: Production Ready
 */
require_once __DIR__ . '/../helpers/Logger.php';

// Setup Global Error Handlers
set_error_handler(['Logger', 'handleError']);
set_exception_handler(['Logger', 'handleException']);

// Load .env file
if (file_exists(__DIR__ . '/../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../.env');
} else {
    $env = [];
}

// Environment Detection
$app_env = $env['APP_ENV'] ?? 'development';
if ($app_env === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// DB Credentials
$host = $env['DB_HOST'] ?? "localhost";
$user = $env['DB_USER'] ?? "root";
$pass = $env['DB_PASS'] ?? "Manan.01";
$db_name = $env['DB_NAME'] ?? "herbal_db";

try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    Logger::log("Database Connection Failed: " . $e->getMessage(), 'CRITICAL');
    
    // Clean user-facing error
    if ($app_env === 'production') {
        die("<h1>Service Unavailable</h1><p>We're experiencing a technical issue. Please try again later.</p>");
    } else {
        die("<h3>Database Error:</h3><p>" . $e->getMessage() . "</p>");
    }
}
?>