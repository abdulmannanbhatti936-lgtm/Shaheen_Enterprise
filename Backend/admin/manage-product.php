<?php
/**
 * Admin: Manage Products
 * Phase 2 Enhanced: Secure Image Uploads & Premium UI
 */
require_once __DIR__ . '/../controller/productController.php';

// Helper to handle image paths (handles legacy vs new uploads)
function formatImgPath($path) {
    if (empty($path)) return '../../Frontend/images/placeholder.jpg';
    if (strpos($path, 'uploads/') === 0 || strpos($path, 'images/') === 0) {
        return '../../Frontend/' . $path;
    }
    return '../../Frontend/images/' . $path;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $result = $productController->add($_POST, $_FILES['image'] ?? null);
    if ($result['status'] === 'success') {
        $msg = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$allRes = $productController->listAll();
$allProducts = ($allRes['status'] === 'success') ? $allRes['data'] : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Shaheen Admin</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-sidebar">
        <h2>Shaheen Admin</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="manage-product.php" class="active"><i class="fas fa-leaf"></i> Products</a></li>
            <li><a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
            <li><a href="manage-content.php"><i class="fas fa-edit"></i> Content</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h1>Manage Products</h1>
            <button class="btn" onclick="document.getElementById('add-modal').style.display='block'">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </header>

        <?php if (isset($msg)): ?>
            <div class="success-msg" style="margin: 20px 0; border-left: 4px solid var(--primary);"><?= $msg ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error-msg" style="margin: 20px 0; border-left: 4px solid #e74c3c;"><?= $error ?></div>
        <?php endif; ?>

        <section class="product-list" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProducts as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <img src="<?= formatImgPath($p['image']) ?>" 
                                     width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                            </td>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><span class="badge"><?= $p['category'] ?></span></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td>
                                <span style="color: <?= $p['stock'] <= 5 ? '#e67e22' : 'inherit' ?>">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <a href="edit-product.php?id=<?= $p['id'] ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="delete-product.php?id=<?= $p['id'] ?>" class="btn-icon text-danger" 
                                       onclick="return confirm('Archive this product?')" title="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Add Modal -->
        <div id="add-modal" class="modal">
            <div class="modal-content" style="max-width: 800px; padding: 40px; border-radius: 15px;">
                <h2 style="margin-bottom: 30px;">Add New Botanical Product</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div class="left-col">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" required placeholder="e.g. Saffron Infused Elixir">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" name="category" required placeholder="e.g. Face Care">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label>Price ($)</label>
                                    <input type="number" step="0.01" name="price" required>
                                </div>
                                <div class="form-group">
                                    <label>Stock</label>
                                    <input type="number" name="stock" value="50" required>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 15px;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" name="is_featured" style="width: auto;"> 
                                    Featured on Homepage
                                </label>
                            </div>
                        </div>
                        <div class="right-col">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" rows="4" required placeholder="Describe the essence and benefits..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Ingredients</label>
                                <textarea name="ingredients" rows="4" required placeholder="List the pure botanicals..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="image-source-selector"
                        style="margin-top: 30px; padding: 25px; background: #fefefe; border: 1px solid #eee; border-radius: 12px;">
                        <label><strong>📸 Product Image Configuration</strong></label>
                        <div style="display: flex; gap: 20px; margin: 15px 0;">
                            <label class="radio-tab"><input type="radio" name="img_type" value="upload" checked
                                    onclick="toggleImgType('upload')"> Secure Upload</label>
                            <label class="radio-tab"><input type="radio" name="img_type" value="url" onclick="toggleImgType('url')"> External URL</label>
                            <label class="radio-tab"><input type="radio" name="img_type" value="existing"
                                    onclick="toggleImgType('existing')"> Project Asset</label>
                        </div>

                        <div id="type-upload" class="type-input">
                            <p style="font-size: 0.8rem; color: #888; margin-bottom: 10px;">Max 2MB. Allowed: JPG, PNG, WEBP.</p>
                            <input type="file" name="image" accept="image/*">
                        </div>
                        <div id="type-url" class="type-input" style="display:none;">
                            <input type="url" name="image_url" placeholder="https://premium-assets.com/image.jpg"
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                        <div id="type-existing" class="type-input" style="display:none;">
                            <select name="existing_image_select"
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                                <option value="">-- Select from images/ directory --</option>
                                <?php
                                $images = $productController->getExistingImages();
                                foreach ($images as $img):
                                    ?>
                                    <option value="<?= $img ?>"><?= $img ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 40px; display: flex; gap: 15px; border-top: 1px solid #eee; padding-top: 25px;">
                        <button type="submit" name="add_product" class="btn" style="flex: 2;">Confirm & Publish</button>
                        <button type="button" class="btn btn-outline" style="flex: 1;"
                            onclick="document.getElementById('add-modal').style.display='none'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function toggleImgType(type) {
                document.querySelectorAll('.type-input').forEach(el => el.style.display = 'none');
                document.getElementById('type-' + type).style.display = 'block';
            }
        </script>
    </div>
</body>

</html>