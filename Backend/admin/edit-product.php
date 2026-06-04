<?php
/**
 * Admin: Edit Product
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

if (!isset($_GET['id'])) {
    header("Location: manage-product.php");
    exit;
}

$id = $_GET['id'];
$res = $productController->show($id);
if ($res['status'] !== 'success') {
    header("Location: manage-product.php");
    exit;
}
$product = $res['data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $result = $productController->update($id, $_POST, $_FILES['image'] ?? null);
    if ($result['status'] === 'success') {
        header("Location: manage-product.php?msg=updated");
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Shaheen Admin</title>
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
            <h1>Edit Product: <?= htmlspecialchars($product['name']) ?></h1>
            <a href="manage-product.php" class="btn btn-outline">Back to List</a>
        </header>

        <?php if (isset($error)): ?>
            <div class="error-msg" style="margin: 20px; border-left: 4px solid #e74c3c;"><?= $error ?></div>
        <?php endif; ?>

        <section class="edit-form-container" style="max-width: 900px; margin: 2rem auto;">
            <form method="POST" enctype="multipart/form-data" class="premium-form-card" 
                  style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                
                <input type="hidden" name="existing_image" value="<?= $product['image'] ?>">

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div class="left-col">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Price ($)</label>
                                <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?> style="width: auto;"> 
                                Featured on Homepage
                            </label>
                        </div>
                    </div>

                    <div class="right-col">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Ingredients</label>
                            <textarea name="ingredients" rows="5" required><?= htmlspecialchars($product['ingredients']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="image-update-section"
                    style="margin: 30px 0; padding: 25px; background: #fefefe; border: 1px solid #eee; border-radius: 12px;">
                    <div style="display: flex; gap: 30px; align-items: flex-start;">
                        <div style="flex: 0 0 200px;">
                            <label><strong>Current Preview</strong></label>
                            <img id="image-preview" src="<?= formatImgPath($product['image']) ?>" 
                                 style="display:block; width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-top: 10px;">
                        </div>
                        <div style="flex: 1;">
                            <label><strong>Update Image Source</strong></label>
                            <div style="display: flex; gap: 20px; margin: 15px 0;">
                                <label class="radio-tab"><input type="radio" name="img_type" value="upload" checked
                                        onclick="toggleImgType('upload')"> Secure Upload</label>
                                <label class="radio-tab"><input type="radio" name="img_type" value="url" onclick="toggleImgType('url')"> External URL</label>
                                <label class="radio-tab"><input type="radio" name="img_type" value="existing"
                                        onclick="toggleImgType('existing')"> Project Asset</label>
                            </div>

                            <div id="type-upload" class="type-input">
                                <input type="file" name="image" accept="image/*" onchange="previewFile(this)">
                            </div>
                            <div id="type-url" class="type-input" style="display:none;">
                                <input type="url" name="image_url" placeholder="https://external-site.com/new-image.jpg"
                                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;"
                                    oninput="previewUrl(this.value)">
                            </div>
                            <div id="type-existing" class="type-input" style="display:none;">
                                <select name="existing_image_select"
                                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;"
                                    onchange="previewExisting(this.value)">
                                    <option value="<?= $product['image'] ?>">-- KEEP CURRENT --</option>
                                    <?php
                                    $images = $productController->getExistingImages();
                                    foreach ($images as $img):
                                        ?>
                                        <option value="<?= $img ?>" <?= $img == $product['image'] ? 'selected' : '' ?>>
                                            <?= $img ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" name="update_product" class="btn" style="flex: 2;">Save Botanical Changes</button>
                    <a href="manage-product.php" class="btn btn-outline" style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </section>

        <script>
            function toggleImgType(type) {
                document.querySelectorAll('.type-input').forEach(el => el.style.display = 'none');
                document.getElementById('type-' + type).style.display = 'block';
            }

            function previewFile(input) {
                const preview = document.getElementById('image-preview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => preview.src = e.target.result;
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewUrl(url) {
                if (url) document.getElementById('image-preview').src = url;
            }

            function previewExisting(filename) {
                if (filename) {
                     // Check if it already has a path
                     if(filename.indexOf('/') === -1) {
                         document.getElementById('image-preview').src = '../../Frontend/images/' + filename;
                     } else {
                         document.getElementById('image-preview').src = '../../Frontend/' + filename;
                     }
                }
            }
        </script>
    </div>
</body>

</html>