<?php
require_once __DIR__ . '/../inc/functions.php';
if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = getUserByUsername($username);
    if (!$user || !password_verify($password, $user['password'])) {
        flash('error', 'Invalid username or password.');
        header('Location: login.php');
        exit;
    }
    $_SESSION['user_id'] = $user['id'];
    header('Location: ../dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServerCheck Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <a href="../index.php" class="back-home">← Back to home</a>
    <div class="auth-page">
        <div class="auth-shell">
            <div class="auth-card">
                <h1>Welcome Back</h1>
                <p>Login to your ServerCheck dashboard.</p>
                <?php if ($error = flash('error')): ?>
                    <div class="alert error"><?php echo escape($error); ?></div>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <label>Username</label>
                    <input type="text" name="username" autocomplete="username" required>
                    <label>Password</label>
                    <input type="password" name="password" autocomplete="current-password" required>
                    <button type="submit" class="primary-btn">Sign In</button>
                </form>
                <p class="auth-meta">Don’t have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>

</html>