<?php
$pageTitle = "Signed Out - Shaheen Enterprise";
include 'includes/header.php';
?>

<main class="auth-body">
    <div class="auth-container" style="text-align: center; padding: 60px 40px;">
        <div style="margin-bottom: 30px;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--herbal-green);"></i>
        </div>
        <h1 style="margin-bottom: 10px;">Safely Logged Out</h1>
        <p style="margin-bottom: 40px;">Thank you for visiting Herbal Bliss. Your session has been closed securely.</p>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="../Backend/auth/login.php" class="btn btn-full">Login Again</a>
            <a href="index.php" class="btn-outline" style="width: 100%; text-align: center; display: block;">Return to
                Home</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>