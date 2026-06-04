<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Backend/controller/authController.php';
$currentUser = $authController->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Primary SEO Meta Tags -->
    <title><?= $pageTitle ?? 'Shaheen Enterprise - Premium Wellness' ?></title>
    <meta name="description" content="<?= $pageDescription ?? 'Shaheen Enterprise - Premium Organic Wellness products, hand-crafted with 100% natural ingredients for your daily care.' ?>">
    <meta name="keywords" content="<?= $pageKeywords ?? 'organic, premium, wellness, body care, natural beauty, vegan products' ?>">

    <!-- Open Graph / Facebook -->
    <?php 
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $ogImg = $pageImage ?? 'images/og-image.jpg';
    if (strpos($ogImg, 'http') !== 0) {
        $ogImg = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . 
                (strpos($ogImg, '/') === 0 ? '' : '/') . $ogImg;
    }
    ?>
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $currentUrl ?>">
    <meta property="og:title" content="<?= $pageTitle ?? 'Shaheen Enterprise - Organic Wellness' ?>">
    <meta property="og:description" content="<?= $pageDescription ?? 'Discover the healing power of nature with Shaheen Enterprise.' ?>">
    <meta property="og:image" content="<?= $ogImg ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $currentUrl ?>">
    <meta property="twitter:title" content="<?= $pageTitle ?? 'Shaheen Enterprise - Organic Wellness' ?>">
    <meta property="twitter:description" content="<?= $pageDescription ?? 'Discover the healing power of nature with Shaheen Enterprise.' ?>">
    <meta property="twitter:image" content="<?= $ogImg ?>">

    <!-- Structured Data (JSON-LD) -->
    <?php if (isset($pageSchema)): ?>
    <script type="application/ld+json">
        <?= json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php endif; ?>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#4F633D">

</head>

<body <?php if (strpos($_SERVER['PHP_SELF'], 'Backend') !== false)
    echo 'class="admin-body"'; ?>>
    <div id="custom-cursor"></div>
    <div id="cursor-follower"></div>
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <a href="index.php" class="brand-identity">
                        <div class="botanical-emblem"></div>
                        <div class="brand-name">
                            <span class="primary">Shaheen</span>
                            <span class="secondary">Enterprise</span>
                        </div>
                    </a>
                </div>
                <div class="menu-toggle" id="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="nav-links">
                    <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                        <li><a href="../Backend/admin/dashboard.php" style="color: var(--accent); font-weight: bold;"><i
                                    class="fas fa-user-shield"></i> Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="journal.php">Journal</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="nav-icons">
                    <a href="javascript:void(0)" onclick="toggleCartDrawer()" style="position: relative;">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count">0</span>
                    </a>
                    <?php if ($currentUser): ?>
                        <a href="profile.php"><i class="fas fa-user"></i></a>
                    <?php else: ?>
                        <a href="../Backend/auth/login.php"><i class="fas fa-sign-in-alt"></i></a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Slide-out Mini-Cart Drawer -->
    <div class="cart-overlay" id="cart-drawer">
        <div class="cart-drawer-header">
            <div class="brand-name" style="font-size: 1.2rem;">
                <span class="primary">Your</span>
                <span class="secondary">Rituals</span>
            </div>
            <div class="drawer-close" onclick="toggleCartDrawer()">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div class="cart-drawer-items" id="drawer-cart-items">
            <!-- Loaded via JS -->
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                Your cart is currently empty.
            </div>
        </div>
        <div class="cart-drawer-footer">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <strong>Subtotal</strong>
                <strong id="drawer-total">$0.00</strong>
            </div>
            <a href="cart.php" class="btn btn-outline btn-full" style="margin-bottom: 10px;">Go to Cart</a>
            <a href="checkout.php" class="btn btn-full">Checkout Now</a>
        </div>
    </div>

    <!-- Global Product Quick View Modal -->
    <div class="modal-overlay" id="quick-view-modal">
        <div class="modal-content">
            <div class="modal-close" onclick="closeQuickView()"><i class="fas fa-times"></i></div>
            <div class="modal-flex" id="modal-body-content">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>