<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$pageTitle = "My Profile";
include __DIR__ . '/../components/header.php';
?>

<h1 class="mb-4">My Profile</h1>

<div class="card max-w-lg">
    <div class="mb-4">
        <label class="label">Name</label>
        <div class="input bg-gray-50"><?= htmlspecialchars($user['name']) ?></div>
    </div>
    <div class="mb-4">
        <label class="label">Email</label>
        <div class="input bg-gray-50"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <div class="mb-4">
        <label class="label">Member Since</label>
        <div class="input bg-gray-50"><?= date('F j, Y', strtotime($user['created_at'])) ?></div>
    </div>
    
    <!-- Edit functionality not requested explicitly but implied by profile page -->
    <!-- For now just read only -->
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
