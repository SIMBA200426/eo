<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
}

$orders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

$pageTitle = "Manage Orders";
include __DIR__ . '/../components/header.php';
?>

<h1 class="mb-4">Orders</h1>

<div class="card p-0 overflow-hidden">
    <div class="table-container" style="border: none; box-shadow: none;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                    <td>$<?= number_format($o['total'], 2) ?></td>
                    <td>
                        <form method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" onchange="this.form.submit()" class="input" style="padding: 0.25rem; font-size: 0.875rem; margin: 0; width: auto;">
                                <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $o['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="shipped" <?= $o['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <!-- View details link could go here, for now basic -->
                        <span class="text-xs text-muted">View Details</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
