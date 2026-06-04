<?php
require_once __DIR__ . '/../controller/productController.php';

if (isset($_GET['id'])) {
    $productController->delete($_GET['id']);
}
header("Location: manage-product.php");
exit;
?>