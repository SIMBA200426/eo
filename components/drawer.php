<div class="drawer-overlay" id="drawerOverlay" onclick="toggleDrawer()"></div>
<aside class="drawer" id="drawer">
    <div class="drawer-header">
        <h2 style="font-size: 1.25rem;">Menu</h2>
        <button class="btn btn-secondary" onclick="toggleDrawer()" style="border:none; padding: 0.25rem;">✕</button>
    </div>
    <nav class="drawer-content">
        <?php
        $role = $_SESSION['role'] ?? 'guest';
        ?>

        <!-- Common Links -->
        <a href="<?= BASE_URL ?>/public/index.php" class="nav-link">Home</a>
        <a href="<?= BASE_URL ?>/public/products.php" class="nav-link">All Products</a>

        <?php if ($role === 'guest'): ?>
            <hr style="margin: 0.5rem 0; border: 0; border-top: 1px solid var(--border);">
            <a href="<?= BASE_URL ?>/auth/login.php" class="nav-link">Login</a>
            <a href="<?= BASE_URL ?>/auth/register.php" class="nav-link">Register</a>
        <?php endif; ?>

        <?php if ($role === 'customer'): ?>
            <div style="margin-top: 1rem; color: var(--muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 1rem;">Customer</div>
            <a href="<?= BASE_URL ?>/customer/dashboard.php" class="nav-link">Dashboard</a>
            <a href="<?= BASE_URL ?>/customer/orders.php" class="nav-link">My Orders</a>
            <a href="<?= BASE_URL ?>/customer/cart.php" class="nav-link">My Cart</a>
            <a href="<?= BASE_URL ?>/customer/profile.php" class="nav-link">Profile</a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
            <div style="margin-top: 1rem; color: var(--muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0 1rem;">Admin</div>
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link">Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/products.php" class="nav-link">Products</a>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="nav-link">Orders</a>
            <a href="<?= BASE_URL ?>/admin/users.php" class="nav-link">Users</a>
        <?php endif; ?>

        <?php if ($role !== 'guest'): ?>
            <hr style="margin: 0.5rem 0; border: 0; border-top: 1px solid var(--border);">
            <a href="<?= BASE_URL ?>/auth/logout.php" class="nav-link" style="color: #ef4444;">Logout</a>
        <?php endif; ?>
    </nav>
    <div style="padding: 1rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--muted); text-align: center;">
        &copy; <?= date('Y') ?> ElectroShop
    </div>
</aside>
