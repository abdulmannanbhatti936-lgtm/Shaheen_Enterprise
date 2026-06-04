<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../config/db.php';

if (!$authController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Blog CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    if (isset($_POST['add_post'])) {
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, slug, content) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $slug, $content);
        $stmt->execute();
    }
}

$posts = $conn->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Journal - Shaheen Admin</title>
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
            <li><a href="manage-content.php"><i class="fas fa-edit"></i> Content</a></li>
            <li><a href="manage-blog.php" class="active"><i class="fas fa-pen"></i> Journal</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h1>Wellness Journal Manager</h1>
            <button class="btn" onclick="document.getElementById('post-modal').style.display='block'">Write
                Story</button>
        </header>

        <section class="stat-card" style="margin-top: 30px;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?= date('M d, Y', strtotime($post['created_at'])) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($post['title']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($post['author']) ?>
                            </td>
                            <td><span class="badge badge-new">
                                    <?= $post['status'] ?>
                                </span></td>
                            <td>
                                <a href="#" class="btn-icon"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn-icon text-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Post Modal -->
        <div id="post-modal" class="modal">
            <div class="modal-content" style="max-width: 800px;">
                <h2>Create New Story</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Story Title</label>
                        <input type="text" name="title" required placeholder="e.g. The Ritual of Saffron">
                    </div>
                    <div class="form-group">
                        <label>Content (Full Story)</label>
                        <textarea name="content" rows="10" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_post" class="btn">Publish Story</button>
                        <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('post-modal').style.display='none'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>