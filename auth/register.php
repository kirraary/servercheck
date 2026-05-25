<?php
require_once __DIR__ . '/../inc/functions.php';
if (isLoggedIn()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        flash('error', 'All fields are required.');
        header('Location: register.php');
        exit;
    }
    if ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
        header('Location: register.php');
        exit;
    }
    if (getUserByUsername($username)) {
        flash('error', 'Username is already taken.');
        header('Location: register.php');
        exit;
    }

    $pdo = dbConnection();
    $stmt = $pdo->prepare('INSERT INTO users (username, password, refresh_interval, monitor_timeout, theme, notifications_enabled) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $username,
        password_hash($password, PASSWORD_DEFAULT),
        5,
        15,
        'dark',
        1,
    ]);
    flash('success', 'Account created successfully. Please login.');
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServerCheck Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <a href="../index.php" class="back-home">← Back to home</a>
    <div class="auth-page">
        <div class="auth-shell">
            <div class="auth-card">
                <h1>Create Account</h1>
                <p>Register a new ServerCheck account.</p>
                <?php if ($error = flash('error')): ?>
                    <div class="alert error"><?php echo escape($error); ?></div>
                <?php endif; ?>
                <form action="register.php" method="post">
                    <label>Username</label>
                    <input type="text" name="username" autocomplete="username" required>
                    <label>Password</label>
                    <input type="password" name="password" autocomplete="new-password" required>
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" autocomplete="new-password" required>
                    <button type="submit" class="primary-btn">Create Account</button>
                </form>
                <p class="auth-meta">Already registered? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>