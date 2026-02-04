<div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
    <div style="height: 200px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; position: relative;">
        <!-- Simple placeholder logic or actual image -->
        <!-- In a real app we might check if file exists -->
        <div style="font-size: 3rem; color: #cbd5e1;">📷</div>
        <?php if(!empty($product['image'])): ?>
            <!-- <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($product['image']) ?>" ... > -->
            <!-- Using placeholder for now to ensure visual is okay without actual images -->
        <?php endif; ?>
    </div>
    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
        <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem;"><?= htmlspecialchars($product['name']) ?></h3>
        <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 1rem;"><?= htmlspecialchars($item['category_name'] ?? 'Electronics') ?></div>
        <p style="color: var(--text); font-size: 0.875rem; margin-bottom: 1.5rem; flex: 1; opacity: 0.8;">
            <?= htmlspecialchars(substr($product['description'] ?? '', 0, 60)) . (strlen($product['description']) > 60 ? '...' : '') ?>
        </p>
        <div class="flex items-center justify-between mt-auto">
            <span style="font-weight: 700; color: var(--secondary); font-size: 1.25rem;">$<?= number_format($product['price'], 2) ?></span>
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <form action="<?= BASE_URL ?>/customer/cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-secondary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>
