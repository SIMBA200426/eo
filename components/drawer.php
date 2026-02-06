<?php
// Detect Mode
$isAdminPath = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
$isCustomerPath = (strpos($_SERVER['PHP_SELF'], '/customer/') !== false);
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isCustomer = (isset($_SESSION['role']) && $_SESSION['role'] === 'customer');

$showSidebar = (($isAdminPath && $isAdmin) || ($isCustomerPath && $isCustomer));
$sidebarType = '';
if ($showSidebar) {
   $sidebarType = $isAdminPath ? 'admin' : 'customer';
}
?>
<div class="drawer-overlay" id="drawerOverlay" onclick="toggleDrawer()"></div>

<aside class="drawer <?= $showSidebar ? 'app-sidebar' : '' ?>" id="drawer">
    <?php if(!$showSidebar): ?>
    <div class="drawer-header">
        <h2 style="font-size: 1.25rem;">Menu</h2>
        <button class="btn btn-secondary" onclick="toggleDrawer()" style="border:none; padding: 0.25rem;">✕</button>
    </div>
    <?php else: ?>
    <!-- Sidebar Header -->
    <div class="drawer-header" style="background: rgba(0,0,0,0.1); border-color: rgba(255,255,255,0.1); color: white;">
        <h2 style="font-size: 1.25rem;"><?= $sidebarType === 'admin' ? 'Admin Panel' : 'My Account' ?></h2>
        <button class="btn btn-secondary md:hidden" onclick="toggleDrawer()" style="border:none; padding: 0.25rem; background: transparent; color: white;">✕</button>
    </div>
    <?php endif; ?>

    <nav class="drawer-content">
        <?php
        $role = $_SESSION['role'] ?? 'guest';
        ?>

        <?php if(!$showSidebar): ?>
            <!-- Public Navigation -->
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
             <?php endif; ?>

            <?php if ($role !== 'guest'): ?>
                <hr style="margin: 0.5rem 0; border: 0; border-top: 1px solid var(--border);">
                <a href="<?= BASE_URL ?>/auth/logout.php" class="nav-link" style="color: #ef4444;">Logout</a>
            <?php endif; ?>
        
        <?php elseif ($sidebarType === 'admin'): ?>
            <!-- Admin Sidebar Navigation -->
            <div style="margin-bottom: 1rem;">
                <p style="padding: 0 1rem; margin-bottom: 0.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.5);">Main</p>
                <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link admin-link">
                   <span>📊</span> Dashboard
                </a>
            </div>

            <div style="margin-bottom: 1rem;">
                <p style="padding: 0 1rem; margin-bottom: 0.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.5);">Management</p>
                <a href="<?= BASE_URL ?>/admin/products.php" class="nav-link admin-link">
                    <span>📦</span> Products
                </a>
                <a href="<?= BASE_URL ?>/admin/categories.php" class="nav-link admin-link">
                    <span>📑</span> Categories
                </a>
                <a href="<?= BASE_URL ?>/admin/orders.php" class="nav-link admin-link">
                    <span>🛍️</span> Orders
                </a>
                <a href="<?= BASE_URL ?>/admin/users.php" class="nav-link admin-link">
                    <span>👥</span> Users
                </a>
            </div>

            <div style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); pt-4;">
                <a href="<?= BASE_URL ?>/public/index.php" class="nav-link admin-link" target="_blank">
                    <span>🏠</span> View Website
                </a>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="nav-link admin-link" style="color: #fca5a5;">
                    <span>🚪</span> Logout
                </a>
            </div>
            
        <?php elseif ($sidebarType === 'customer'): ?>
            <!-- Customer Sidebar Navigation -->
            <div style="margin-bottom: 1rem;">
                <p style="padding: 0 1rem; margin-bottom: 0.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.5);">Overview</p>
                <a href="<?= BASE_URL ?>/customer/dashboard.php" class="nav-link admin-link">
                   <span>📊</span> Dashboard
                </a>
            </div>

            <div style="margin-bottom: 1rem;">
                <p style="padding: 0 1rem; margin-bottom: 0.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.5);">Shopping</p>
                <a href="<?= BASE_URL ?>/customer/orders.php" class="nav-link admin-link">
                    <span>📦</span> My Orders
                </a>
                <a href="<?= BASE_URL ?>/customer/cart.php" class="nav-link admin-link">
                    <span>🛒</span> My Cart
                </a>
                <a href="<?= BASE_URL ?>/customer/profile.php" class="nav-link admin-link">
                    <span>👤</span> Profile
                </a>
            </div>

            <div style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); pt-4;">
                <a href="<?= BASE_URL ?>/public/index.php" class="nav-link admin-link">
                    <span>🛍️</span> Keep Shopping
                </a>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="nav-link admin-link" style="color: #fca5a5;">
                    <span>🚪</span> Logout
                </a>
            </div>
        <?php endif; ?>
    </nav>
    
    <?php if(!$showSidebar): ?>
    <div style="padding: 1rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--muted); text-align: center;">
        &copy; <?= date('Y') ?> ElectroShop
    </div>
    <?php else: ?>
    <div style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.75rem; color: rgba(255,255,255,0.4); text-align: center;">
        <?= $sidebarType === 'admin' ? 'Admin Access' : 'Customer Area' ?>
    </div>
    <?php endif; ?>
</aside>
