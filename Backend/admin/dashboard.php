<?php
require_once __DIR__ . '/../controller/authController.php';
require_once __DIR__ . '/../controller/productController.php';
require_once __DIR__ . '/../controller/orderController.php';

// Simple check for admin (assuming admin has a specific flag or just user_id 1 for now)
if (!$authController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit;
}

// Standardized Controller Responses {status, message, data}
$resProducts = $productController->listAll();
$resOrders = $orderController->listAll();

$allProducts = $resProducts['data'] ?? [];
$allOrders = $resOrders['data'] ?? [];

$totalSales = 0;
$uniqueUsers = [];
$completedOrdersCount = 0;

foreach ($allOrders as $order) {
    if ($order['status'] != 'Cancelled') {
        $totalSales += (float)$order['total'];
        $completedOrdersCount++;
        if (isset($order['user_id'])) {
            $uniqueUsers[$order['user_id']] = true;
        }
    }
}

$avgOrderValue = $completedOrdersCount > 0 ? $totalSales / $completedOrdersCount : 0;
$totalCustomers = count($uniqueUsers);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shaheen Enterprise</title>
    <link rel="stylesheet" href="../../Frontend/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-body">
    <div class="admin-sidebar">
        <h2>Shaheen Admin</h2>
        <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="manage-product.php"><i class="fas fa-leaf"></i> Products</a></li>
            <li><a href="manage-orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <header class="admin-header">
            <h1>Dashboard Overview</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="display: flex; gap: 10px;">
                    <a href="../api/export.php?type=orders" class="btn btn-outline"
                        style="padding: 10px 20px; font-size: 0.8rem;"><i class="fas fa-file-csv"></i> Orders CSV</a>
                    <a href="../api/export.php?type=customers" class="btn btn-outline"
                        style="padding: 10px 20px; font-size: 0.8rem;"><i class="fas fa-file-csv"></i> Users CSV</a>
                </div>
                <div class="user-info"
                    style="display: flex; align-items: center; gap: 20px; margin-left: 20px; border-left: 1px solid var(--border-light); padding-left: 20px;">
                    <a href="../../Frontend/index.php"
                        style="font-size: 0.9rem; color: var(--herbal-green); text-decoration: underline;">Shop</a>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-info">
                    <h3>Total Products</h3>
                    <p><?= count($allProducts) ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?= count($allOrders) ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <p>$<?= number_format($totalSales, 2) ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h3>Unique Customers</h3>
                    <p><?= $totalCustomers ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <div class="stat-info">
                    <h3>Avg. Order Value</h3>
                    <p>$<?= number_format($avgOrderValue, 2) ?></p>
                </div>
            </div>
        </div>

        <section class="recent-activity"
            style="margin-top: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div class="stat-card" style="padding: 0;">
                <h2 style="padding: 20px;">Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($allOrders, 0, 5) as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td>$<?= $order['total'] ?></td>
                                <td><span class="status <?= strtolower($order['status']) ?>"><?= $order['status'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="stat-card" style="padding: 0;">
                <h2 style="padding: 20px;">VIP Insights</h2>
                <div style="padding: 0 20px 20px;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 20px;">Top Ritual Enthusiasts
                        (by order count)</p>
                    <ul style="list-style: none;">
                        <?php
                        $customerCounts = array_count_values(array_column($allOrders, 'username'));
                        arsort($customerCounts);
                        $vips = array_slice($customerCounts, 0, 5);
                        foreach ($vips as $user => $count): ?>
                            <li
                                style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                                <span><?= htmlspecialchars($user) ?></span>
                                <span class="badge badge-featured"><?= $count ?> Orders</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            apiCall('../api/getStats.php')
                .then(data => {
                    if (data.status === 'success') {
                        // Sales Trend Chart
                        new Chart(document.getElementById('salesChart'), {
                            type: 'line',
                            data: {
                                labels: data.salesTrend.map(s => s.date),
                                datasets: [{
                                    label: 'Revenue ($)',
                                    data: data.salesTrend.map(s => s.total),
                                    borderColor: '#4F633D',
                                    backgroundColor: 'rgba(79, 99, 61, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true } }
                            }
                        });

                        // Status Chart
                        new Chart(document.getElementById('statusChart'), {
                            type: 'doughnut',
                            data: {
                                labels: data.statuses.map(s => s.status),
                                datasets: [{
                                    data: data.statuses.map(s => s.count),
                                    backgroundColor: ['#4F633D', '#8BA194', '#FFF7E2', '#e74c3c']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    }
                });
        });
    </script>
</body>

</html>