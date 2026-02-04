<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    // If trying to add to cart, redirect to login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; 
    // better yet, just redirect
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        // Check if exists
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $product_id]);
        }
        header("Location: " . BASE_URL . "/customer/cart.php");
        exit;
    } 
    elseif ($action === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
    }
    elseif ($action === 'checkout') {
        // Fetch cart items
        $stmt = $pdo->prepare("SELECT c.*, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll();

        if (empty($items)) {
            $message = "Your cart is empty.";
        } else {
            try {
                $pdo->beginTransaction();

                // Create Order
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, 0, 'pending')");
                $stmt->execute([$user_id]);
                $order_id = $pdo->lastInsertId();

                $total = 0;

                foreach ($items as $item) {
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;

                    // Insert Order Item
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

                    // Update Stock (optional simple check)
                    // $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    // $stmt->execute([$item['quantity'], $item['product_id']]);
                }

                // Update Order Total
                $stmt = $pdo->prepare("UPDATE orders SET total = ? WHERE id = ?");
                $stmt->execute([$total, $order_id]);

                // Clear Cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);

                $pdo->commit();
                
                header("Location: " . BASE_URL . "/customer/orders.php?success=1");
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Checkout failed: " . $e->getMessage();
            }
        }
    }
}

// Display Cart
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total_price = 0;
foreach($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$pageTitle = "My Cart";
include __DIR__ . '/../components/header.php';
?>

<div class="mb-8">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if ($message): ?>
        <div class="p-4 mb-4 text-white bg-red-500 rounded"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div style="flex: 2;">
            <?php if (empty($cart_items)): ?>
                <div class="card p-8 text-center text-muted">
                    Your cart is empty. <a href="<?= BASE_URL ?>/public/products.php">Shop now</a>
                </div>
            <?php else: ?>
                <div class="card p-0 overflow-hidden">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="flex items-center p-4 border-b border-gray-200">
                            <div class="w-16 h-16 bg-gray-100 rounded mr-4 flex items-center justify-center text-xl text-gray-400">
                                📷
                                <?php // if($item['image']) ... ?>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold"><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="text-sm text-muted">$<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?></div>
                            </div>
                            <div class="font-bold text-lg mr-4">
                                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Remove">&times;</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <?php if (!empty($cart_items)): ?>
        <div style="flex: 1;">
            <div class="card">
                <h3 class="font-bold mb-4 text-lg">Order Summary</h3>
                
                <div class="flex justify-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>$<?= number_format($total_price, 2) ?></span>
                </div>
                <div class="flex justify-between mb-4 pb-4 border-b border-gray-200">
                    <span class="text-muted">Tax (0%)</span>
                    <span>$0.00</span>
                </div>
                
                <div class="flex justify-between mb-6 text-xl font-bold">
                    <span>Total</span>
                    <span>$<?= number_format($total_price, 2) ?></span>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="checkout">
                    <button type="submit" class="btn btn-primary w-full py-3 text-center block">
                        Proceed to Checkout
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
