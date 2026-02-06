<?php
require_once __DIR__ . '/config/db_connect.php';

// Check products table structure
$stmt = $pdo->query("DESCRIBE products");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Products Table Structure:\n";
echo str_repeat("=", 50) . "\n";
foreach($columns as $col) {
    echo sprintf("%-20s %-20s\n", $col['Field'], $col['Type']);
}
