<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$pageTitle = "My Orders";
include __DIR__ . '/../components/header.php';

if ($order_id) {
    // Show Order Details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "<div class='card p-4'>Order not found.</div>";
    } else {
        // Fetch items
        $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll();
        ?>
        <div class="mb-4">
            <a href="<?= BASE_URL ?>/customer/orders.php" class="text-sm text-primary">&larr; Back to Orders</a>
        </div>
        <div class="card mb-8">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <div>
                    <h1 class="text-xl font-bold">Order #<?= $order['id'] ?></h1>
                    <div class="text-xs text-muted"><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></div>
                </div>
                <!-- Status Badge -->
                <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>

            <div class="mb-4">
                <h3 class="font-bold mb-2">Items</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-bold">Total</td>
                                <td class="font-bold">$<?= number_format($order['total'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

} else {
    // List Orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    ?>
    <h1 class="mb-4">My Orders</h1>
    <?php if(empty($orders)): ?>
        <div class="card p-4">No orders found.</div>
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
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        <td>$<?= number_format($order['total'], 2) ?></td>
                        <td><span class="badge badge-info"><?= ucfirst($order['status']) ?></span></td>
                        <td><a href="?id=<?= $order['id'] ?>" class="btn btn-sm btn-secondary">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php
}

include __DIR__ . '/../components/footer.php';
?>
