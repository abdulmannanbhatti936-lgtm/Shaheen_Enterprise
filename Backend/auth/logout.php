<?php
require_once __DIR__ . '/../controller/authController.php';
$authController->logout();
header("Location: ../../Frontend/logout_success.php");
exit;
?>