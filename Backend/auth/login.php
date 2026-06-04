<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../helpers/csrf.php';

if ($authController->isLoggedIn()) {
    header("Location: ../../Frontend/index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please refresh and try again.";
    } else {
        $result = $authController->login($_POST['email'], $_POST['password']);
        if ($result['status'] === 'success') {
            header("Location: ../../Frontend/index.php");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
</head>

<body class="auth-body">
    <div class="auth-container">
        <div class="brand-name" style="align-items: center; margin-bottom: 30px;">
            <span class="primary" style="font-size: 2.2rem;">Shaheen</span>
            <span class="secondary">Enterprise</span>
        </div>
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-full">Login</button>
        </form>
        <p style="text-align: center; margin: 20px 0; color: var(--text-muted); font-size: 0.8rem;">OR</p>
        <div class="social-login" style="display: flex; flex-direction: column; gap: 10px;">
            <button type="button" class="btn-outline btn-full"
                style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fab fa-google"></i> Continue with Google
            </button>
            <button type="button" class="btn-outline btn-full"
                style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fab fa-facebook-f"></i> Continue with Facebook
            </button>
        </div>
        <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px;">
            <p class="auth-switch">New here? <a href="register.php" style="font-weight: bold;">Create an account</a></p>
            <a href="../../Frontend/shop.php" class="btn-outline" style="padding: 12px 0; display: block;">Return to
                Shop</a>
        </div>
    </div>
</body>

</html>