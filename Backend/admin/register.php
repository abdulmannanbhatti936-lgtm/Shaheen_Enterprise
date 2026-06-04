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
$success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $error = "Invalid CSRF token";
    } else {
        // Registering with 'admin' role
        $result = $authController->register($_POST['username'], $_POST['email'], $_POST['password'], 'admin');
        if ($result['success']) {
            $success = "Admin account created successfully! Redirecting to admin login...";
            header("refresh:2;url=login.php");
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
    <title>Admin Registration - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
</head>

<body class="auth-body">
    <div class="auth-container" style="border-top: 5px solid var(--primary);">
        <h1 style="color: var(--primary);">Admin Registration</h1>
        <p>Create a new administrator account</p>

        <?php if ($error): ?>
            <div class="error-msg"
                style="background: #fee; color: #c33; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-size: 0.9rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-msg"
                style="background: #efe; color: #393; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-size: 0.9rem;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="form-group">
                <input type="text" name="username" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Admin Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Secure Password" required>
            </div>
            <button type="submit" class="btn btn-full">Create Admin Account</button>
        </form>

        <div style="margin-top: 30px; border-top: 1px solid var(--border-light); padding-top: 20px;">
            <p class="auth-switch">Already registered? <a href="login.php"
                    style="color: var(--primary); font-weight: bold;">Admin Login</a></p>
            <a href="../../Frontend/index.php" class="btn-outline"
                style="display: block; margin-top: 15px; padding: 12px 0;">Back to Site</a>
        </div>
    </div>
</body>

</html>