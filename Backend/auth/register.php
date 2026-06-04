<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../helpers/csrf.php';

if ($authController->isLoggedIn()) {
    header("Location: ../../Frontend/index.php");
    exit;
}

$error = "";
$success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please refresh and try again.";
    } else {
        $result = $authController->register($_POST['username'], $_POST['email'], $_POST['password']);
        if ($result['status'] === 'success') {
            // Send Welcome Email (Mocked in EmailService)
            require_once __DIR__ . '/../../Backend/services/EmailService.php';
            $emailService = new EmailService();
            $emailService->sendWelcome($_POST['email'], $_POST['username']);

            $success = $result['message'] . ". Redirecting to login...";
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
    <title>Register - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
</head>

<body class="auth-body">
    <div class="auth-container">
        <h1>Create Account</h1>
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-full">Register</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
        <p class="auth-switch"><a href="../../Frontend/index.php">Back to Home</a></p>
    </div>
</body>

</html>