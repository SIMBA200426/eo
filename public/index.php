<?php
require_once __DIR__ . '/../config/db_connect.php';

// Fetch Featured Products (limit 4)
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
$products = $stmt->fetchAll();

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
$categories = $stmt->fetchAll();

$pageTitle = "Home";
include __DIR__ . '/../components/header.php';
?>

<!-- Hero Section -->
<section style="background: linear-gradient(to right, var(--primary), #1d4ed8); color: white; padding: 4rem 1rem; border-radius: var(--radius); margin-bottom: 3rem; text-align: center;">
    <h1 style="color: white; margin-bottom: 1rem; font-size: 2.5rem;">Welcome to ElectroShop</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
        Discover the latest gadgets and electronics at unbeatable prices.
    </p>
    <a href="<?= BASE_URL ?>/public/products.php" class="btn" style="background: white; color: var(--primary); padding: 0.75rem 2rem; font-weight: 600;">Shop Now</a>
</section>

<!-- Categories -->
<section class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Shop by Category</h2>
        <a href="<?= BASE_URL ?>/public/products.php" class="text-sm font-medium">View All &rarr;</a>
    </div>
    
    <div class="grid grid-cols-2 md-grid-cols-4 gap-4">
        <?php foreach($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/public/products.php?category=<?= $cat['id'] ?>" class="card flex items-center justify-center" style="min-height: 100px; text-align: center; color: var(--text);">
                <span style="font-weight: 600;"><?= htmlspecialchars($cat['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products -->
<section>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">New Arrivals</h2>
        <a href="<?= BASE_URL ?>/public/products.php" class="text-sm font-medium">View All &rarr;</a>
    </div>
    
    <div class="grid grid-cols-1 sm-grid-cols-2 lg-grid-cols-4 gap-4">
        <?php foreach($products as $product): ?>
            <?php include __DIR__ . '/../components/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/../components/footer.php'; ?>
