<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

// Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn() ?: 0;
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 10")->fetchColumn();

// Recent Orders
$recent_orders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

$pageTitle = "Admin Dashboard";
include __DIR__ . '/../components/header.php';
?>

<div class="mb-8">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 sm-grid-cols-2 lg-grid-cols-4 gap-4 mb-8">
        <?php 
        $cardTitle = 'Total Users'; $cardValue = $total_users; include __DIR__ . '/../components/card.php';
        $cardTitle = 'Total Orders'; $cardValue = $total_orders; include __DIR__ . '/../components/card.php';
        $cardTitle = 'Total Revenue'; $cardValue = '$' . number_format($total_revenue, 2); include __DIR__ . '/../components/card.php';
        $cardTitle = 'Low Stock Items'; $cardValue = $low_stock_count; include __DIR__ . '/../components/card.php';
        ?>
    </div>

    <div class="grid grid-cols-1 lg-grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div>
            <h2 class="mb-4 text-xl">Recent Orders</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td>$<?= number_format($order['total'], 2) ?></td>
                            <td><span class="badge badge-info"><?= ucfirst($order['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-right">
                <a href="orders.php" class="text-sm">View All &rarr;</a>
            </div>
        </div>

        <!-- Very Simple Chart using HTML/CSS (Bar Chart) -->
        <div class="card">
            <h3 class="font-bold mb-4">Quick Stats</h3>
            <div class="flex flex-col gap-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Revenue Goal</span>
                        <span><?= round(($total_revenue / 10000) * 100) ?>%</span>
                    </div>
                    <div style="background: #f1f5f9; height: 10px; border-radius: 5px; overflow: hidden;">
                        <div style="width: <?= min(100, ($total_revenue / 10000) * 100) ?>%; background: var(--primary); height: 100%;"></div>
                    </div>
                </div>
                <!-- More random stats could go here -->
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
