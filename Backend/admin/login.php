<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../helpers/csrf.php';

if ($authController->isLoggedIn()) {
    $user = $authController->getCurrentUser();
    if ($user['role'] === 'admin') {
        header("Location: dashboard.php");
        exit;
    }
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid CSRF token.";
    } else {
        $result = $authController->login($_POST['email'], $_POST['password']);
        if ($result['status'] === 'success') {
            $user = $authController->getCurrentUser();
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
                exit;
            } else {
                $authController->logout();
                $error = "Access denied. Only administrators can login here.";
            }
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
    <title>Admin Login - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
</head>

<body class="auth-body">
    <div class="auth-container" style="border-top: 5px solid var(--primary);">
        <h1 style="color: var(--primary);">Admin Login</h1>
        <p>Exclusive access for Shaheen Enterprise administrators</p>

        <?php if ($error): ?>
            <div class="error-msg"
                style="background: #fee; color: #c33; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-size: 0.9rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="form-group">
                <input type="email" name="email" placeholder="Admin Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-full">Access Dashboard</button>
        </form>

        <div style="margin-top: 30px; border-top: 1px solid var(--border-light); padding-top: 20px;">
            <p class="auth-switch">Need admin access? <a href="register.php"
                    style="color: var(--primary); font-weight: bold;">Register Admin</a></p>
            <a href="../../Frontend/index.php" class="btn-outline"
                style="display: block; margin-top: 15px; padding: 12px 0;">Back to Site</a>
        </div>
    </div>
</body>

</html>