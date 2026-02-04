<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Stats
// Total Orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_orders = $stmt->fetchColumn();

// Pending Orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending_orders = $stmt->fetchColumn();

// Completed Orders
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed_orders = $stmt->fetchColumn();

// Total Spent (sum of total in orders table)
$stmt = $pdo->prepare("SELECT SUM(total) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_spent = $stmt->fetchColumn() ?: 0;

// Recent Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();

$pageTitle = "My Dashboard";
include __DIR__ . '/../components/header.php';
?>

<div class="mb-8">
    <h1 class="mb-4">Dashboard</h1>
    
    <div class="grid grid-cols-1 sm-grid-cols-2 lg-grid-cols-4 gap-4 mb-8">
        <?php 
        $cardTitle = 'Total Orders'; $cardValue = $total_orders; include __DIR__ . '/../components/card.php';
        $cardTitle = 'Pending Orders'; $cardValue = $pending_orders; include __DIR__ . '/../components/card.php';
        $cardTitle = 'Completed Orders'; $cardValue = $completed_orders; include __DIR__ . '/../components/card.php';
        $cardTitle = 'Total Spent'; $cardValue = '$' . number_format($total_spent, 2); include __DIR__ . '/../components/card.php';
        ?>
    </div>

    <h2 class="mb-4 text-xl">Recent Orders</h2>
    <?php if(empty($recent_orders)): ?>
        <div class="card p-4 text-muted">You have no orders yet.</div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        <td>$<?= number_format($order['total'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info') ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/customer/orders.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
