<?php
require_once __DIR__ . '/../config/db_connect.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Build Query
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
$params = [];

if ($category_id) {
    $sql .= " WHERE p.category_id = ?";
    $params[] = $category_id;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get Categories for filter sidebar/list
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

$pageTitle = "Products";
include __DIR__ . '/../components/header.php';
?>

<div class="flex flex-col md:flex-row gap-4" style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Sidebar / Filters -->
    <aside style="width: 100%; max-width: 250px; flex-shrink: 0;" class="mb-8">
        <h3 class="mb-4 text-lg font-bold">Categories</h3>
        <div class="card" style="padding: 0; overflow: hidden;">
            <a href="<?= BASE_URL ?>/public/products.php" class="nav-link <?= $category_id === null ? 'active' : '' ?>" style="display: block; width: 100%;">All Products</a>
            <?php foreach($cats as $cat): ?>
                <a href="?category=<?= $cat['id'] ?>" class="nav-link <?= $category_id === $cat['id'] ? 'active' : '' ?>" style="display: block; width: 100%;">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- Product Grid -->
    <div style="flex: 1;">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">
                <?php 
                if ($category_id) {
                    // find name
                    $found = array_filter($cats, fn($c) => $c['id'] == $category_id);
                    echo !empty($found) ? htmlspecialchars(reset($found)['name']) : 'Products';
                } else {
                    echo 'All Products';
                }
                ?>
            </h1>
            <span class="text-sm text-muted"><?= count($products) ?> items</span>
        </div>

        <?php if (empty($products)): ?>
            <div class="card p-8 text-center text-muted">
                No products found in this category.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm-grid-cols-2 lg-grid-cols-3 gap-4">
                <?php foreach($products as $product): ?>
                    <?php include __DIR__ . '/../components/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
