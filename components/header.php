<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/db_connect.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - Electronics Shop' : 'Electronics Shop' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="container flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button class="hamburger" onclick="toggleDrawer()" aria-label="Toggle menu">
                ☰
            </button>
            <a href="<?= BASE_URL ?>/public/index.php" class="text-xl font-bold text-primary" style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">
                ElectroShop
            </a>
            
            <nav class="desktop-nav">
                <a href="<?= BASE_URL ?>/public/index.php">Home</a>
                <a href="<?= BASE_URL ?>/public/products.php">Products</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                        <a href="<?= BASE_URL ?>/admin/products.php">Products</a>
                        <a href="<?= BASE_URL ?>/admin/orders.php">Orders</a>
                        <a href="<?= BASE_URL ?>/admin/users.php">Users</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/customer/dashboard.php">Dashboard</a>
                        <a href="<?= BASE_URL ?>/customer/orders.php">My Orders</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/auth/logout.php" style="color: #ef4444;">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth/login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
        
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-sm hidden sm-block">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
            
            <a href="<?= BASE_URL ?>/customer/cart.php" class="relative">
                🛒 
                <?php 
                // Simple cart count check if cart table exists and user logged in, or generic
                // For now just icon
                ?>
            </a>
        </div>
    </div>
</header>

<?php include __DIR__ . '/drawer.php'; ?>

<main class="container mt-4 mb-8" style="min-height: 80vh;">
