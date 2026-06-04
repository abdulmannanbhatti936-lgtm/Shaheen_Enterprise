<?php
require_once __DIR__ . '/../controller/orderController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderController->update($_POST['order_id'], $_POST['status']);
}

$res = $orderController->listAll();
$allOrders = $res['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-sidebar">
        <h2>Shaheen Admin</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="manage-product.php"><i class="fas fa-leaf"></i> Products</a></li>
            <li><a href="manage-orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h1>Manage Orders</h1>
        </header>

        <section class="order-list">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allOrders as $o): ?>
                        <tr>
                            <td>#
                                <?= $o['id'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($o['username']) ?>
                            </td>
                            <td>$
                                <?= $o['total'] ?>
                            </td>
                            <td><span class="status <?= strtolower($o['status']) ?>">
                                    <?= $o['status'] ?>
                                </span></td>
                            <td>
                                <?= date('M d, Y', strtotime($o['created_at'])) ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <form method="POST" style="display:inline-flex; gap:5px;">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="status">
                                            <option value="Pending" <?= $o['status'] == 'Pending' ? 'selected' : '' ?>>Pending
                                            </option>
                                            <option value="Shipped" <?= $o['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped
                                            </option>
                                            <option value="Delivered" <?= $o['status'] == 'Delivered' ? 'selected' : '' ?>>
                                                Delivered</option>
                                            <option value="Cancelled" <?= $o['status'] == 'Cancelled' ? 'selected' : '' ?>>
                                                Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-small">Update</button>
                                    </form>
                                    <button class="btn btn-outline btn-small"
                                        onclick="viewOrderDetails(<?= $o['id'] ?>)">Details</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Order Details Modal -->
        <div id="details-modal" class="modal">
            <div class="modal-content">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2>Order Details #<span id="modal-order-id"></span></h2>
                    <button class="btn-icon" onclick="document.getElementById('details-modal').style.display='none'"><i
                            class="fas fa-times"></i></button>
                </div>
                <div id="order-items-list">
                    <!-- Items loaded here -->
                </div>
                <div style="margin-top:20px; text-align:right;">
                    <button class="btn"
                        onclick="document.getElementById('details-modal').style.display='none'">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../Frontend/js/main.js"></script>
    <script>
        function viewOrderDetails(orderId) {
            document.getElementById('modal-order-id').innerText = orderId;
            const list = document.getElementById('order-items-list');
            list.innerHTML = 'Loading...';
            document.getElementById('details-modal').style.display = 'block';

            apiCall(`../api/getOrders.php?action=admin_details&id=${orderId}`)
                .then(data => {
                    if (data.status === 'success') {
                        const items = data.data;
                        list.innerHTML = `
                            <table style="width:100%; box-shadow:none; border:1px solid #eee;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map(item => `
                                        <tr>
                                            <td style="display:flex; align-items:center; gap:10px;">
                                                <img src="${getImgPath(item.image)}" width="40" style="border-radius:4px;">
                                                <span>${item.name}</span>
                                            </td>
                                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                                            <td>${item.quantity}</td>
                                            <td>$${(item.price * item.quantity).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        list.innerHTML = '<p style="color:red; padding:20px; text-align:center;">Failed to load order details.</p>';
                    }
                });
        }
    </script>
</body>

</html>