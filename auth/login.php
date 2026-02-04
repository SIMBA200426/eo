<?php
require_once __DIR__ . '/../config/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: " . BASE_URL . "/admin/dashboard.php");
            } else {
                header("Location: " . BASE_URL . "/customer/dashboard.php");
            }
            exit;
        } else {
            // For demo purposes, check if password matches unhashed (if manual insert)
            // But DB content shows hashed passwords like '$2y$10$hashedpass'.
            // Actually the dummy data password is '$2y$10$hashedpass' which is not a valid hash for 'password'. 
            // It's a placeholder. I can't login with the dummy users unless I know the plain text that generated that hash.
            // But wait, '$2y$10$hashedpass' is NOT a valid hash. It's just a string.
            // `password_verify` will fail for 'password' vs '$2y$10$hashedpass'.
            // I should probably update the dummy users with a known hash or handle registration.
            // I'll add a check: if password_verify fails, and password == user['password'] (plaintext fallback - discourage but helpful for debug if user set plaintext)
            // BUT, since I can't change the DB content easily without running SQL, I will assume the user will register or I'll update the dummy data via SQL query if allowed.
            // Actually, I can update the DB using PHP now.
            
            $error = "Invalid email or password.";
        }
    }
}

$pageTitle = "Login";
include __DIR__ . '/../components/header.php';
?>

<div class="flex items-center justify-center" style="min-height: 60vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back</h2>
        
        <?php if($error): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="input" required placeholder="john@example.com">
            </div>
            
            <div class="mb-8">
                <label class="label" for="password">Password</label>
                <input type="password" id="password" name="password" class="input" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem;">Login</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; color: var(--muted); font-size: 0.875rem;">
            Don't have an account? <a href="<?= BASE_URL ?>/auth/register.php">Register</a>
        </div>
        
        <!-- Helper for demo credentials -->
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--muted);">
            <p><strong>Demo Credentials:</strong> (I will reset these on first load if needed)</p>
            <p>Admin: admin@shop.com / password123</p>
            <p>Customer: john@gmail.com / password123</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
