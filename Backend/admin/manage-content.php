<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../config/db.php';

if (!$authController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    $message = "Settings updated successfully!";
}

// Fetch Settings
$settings = [];
$res = $conn->query("SELECT * FROM site_settings");
while ($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Content - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-sidebar">
        <h2>Shaheen Admin</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="manage-product.php"><i class="fas fa-leaf"></i> Products</a></li>
            <li><a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
            <li><a href="manage-content.php" class="active"><i class="fas fa-edit"></i> Content</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h1>Site Content Manager</h1>
        </header>

        <?php if (isset($message)): ?>
            <div style="background: var(--primary); color: white; padding: 15px; border-radius: 5px; margin-bottom: 30px;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="stat-card" style="max-width: 800px; padding: 40px;">
            <h3>Hero Section Edit</h3>
            <form method="POST">
                <div class="form-group" style="margin-top: 20px;">
                    <label>Hero Title</label>
                    <input type="text" name="hero_title" value="<?= htmlspecialchars($settings['hero_title']) ?>"
                        style="width: 100%; border: 1px solid #ddd; padding: 10px;">
                </div>
                <div class="form-group">
                    <label>Hero Subtitle</label>
                    <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($settings['hero_subtitle']) ?>"
                        style="width: 100%; border: 1px solid #ddd; padding: 10px;">
                </div>
                <div class="form-group">
                    <label>Hero Description</label>
                    <textarea name="hero_description" rows="4"
                        style="width: 100%; border: 1px solid #ddd; padding: 10px;"><?= htmlspecialchars($settings['hero_description']) ?></textarea>
                </div>
                <button type="submit" class="btn" style="padding: 15px 40px;">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>