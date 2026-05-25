<?php
require_once __DIR__ . '/inc/layout.php';
requireLogin();
$userId = currentUserId();
$user = getUserSettings($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save_settings') {
        $username = trim($_POST['username'] ?? $user['username']);
        $refreshInterval = intval($_POST['refresh_interval'] ?? $user['refresh_interval']);
        $monitorTimeout = intval($_POST['monitor_timeout'] ?? $user['monitor_timeout']);
        $theme = $_POST['theme'] === 'light' ? 'light' : 'dark';
        $notifications = isset($_POST['notifications_enabled']) ? 1 : 0;

        if ($username === '') {
            flash('error', 'Username cannot be empty.');
        } else {
            $existing = getUserByUsername($username);
            if ($existing && $existing['id'] !== $userId) {
                flash('error', 'That username is already taken.');
            } else {
                updateUserSettings($userId, [
                    'username' => $username,
                    'refresh_interval' => $refreshInterval,
                    'monitor_timeout' => $monitorTimeout,
                    'theme' => $theme,
                    'notifications_enabled' => $notifications,
                ]);
                flash('success', 'Settings saved successfully.');
            }
        }
    }
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Current password is incorrect.');
        } elseif ($newPassword === '' || $confirmPassword === '') {
            flash('error', 'Please enter and confirm your new password.');
        } elseif ($newPassword !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
        } else {
            updateUserSettings($userId, ['password' => $newPassword]);
            flash('success', 'Password updated successfully.');
        }
    }
    header('Location: settings.php');
    exit;
}

$user = getUserSettings($userId);
renderPageStart('settings', 'Settings', 'Configure your account and monitoring preferences.');
?>

<section class="panel-section">
    <?php if ($message = flash('success')): ?>
        <div class="alert success"><?php echo escape($message); ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert error"><?php echo escape($message); ?></div>
    <?php endif; ?>

    <div class="settings-grid">
        <div class="settings-card neon-card">
            <h3>Account</h3>
            <form method="post" class="settings-form">
                <input type="hidden" name="action" value="save_settings">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo escape($user['username']); ?>" required>
                <label>Auto refresh interval (seconds)</label>
                <input type="number" name="refresh_interval" min="3" value="<?php echo escape($user['refresh_interval'] ?? 5); ?>">
                <label>Monitoring timeout (seconds)</label>
                <input type="number" name="monitor_timeout" min="5" value="<?php echo escape($user['monitor_timeout'] ?? 15); ?>">
                <label>Theme</label>
                <select name="theme">
                    <option value="dark"<?php echo ($user['theme'] ?? 'dark') === 'dark' ? ' selected' : ''; ?>>Dark</option>
                    <option value="light"<?php echo ($user['theme'] ?? 'dark') === 'light' ? ' selected' : ''; ?>>Light</option>
                </select>
                <label class="checkbox-label">
                    <input type="checkbox" name="notifications_enabled" value="1"<?php echo ($user['notifications_enabled'] ?? 1) ? ' checked' : ''; ?>> Enable notifications (future-ready)
                </label>
                <button type="submit" class="primary-btn">Save Settings</button>
            </form>
        </div>

        <div class="settings-card neon-card">
            <h3>Security</h3>
            <form method="post" class="settings-form">
                <input type="hidden" name="action" value="change_password">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
                <label>New Password</label>
                <input type="password" name="new_password" required>
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
                <button type="submit" class="primary-btn">Update Password</button>
            </form>
        </div>
    </div>
</section>

<?php renderPageEnd(); ?>
