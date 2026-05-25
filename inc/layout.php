<?php
require_once __DIR__ . '/functions.php';

function getCurrentUser()
{
    static $user;
    if ($user === null) {
        $userId = currentUserId();
        $user = $userId ? getUserById($userId) : null;
    }
    return $user;
}

function renderPageStart(string $activePage, string $pageTitle, string $pageSubtitle = '')
{
    $user = getCurrentUser();
    $username = escape($user['username'] ?? 'Admin');
    $subtext = $pageSubtitle ?: 'Monitor your infrastructure in real time.';
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ServerCheck - <?php echo escape($pageTitle); ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>

    <body data-page="<?php echo escape($activePage); ?>">
        <div class="app-shell">
            <aside class="sidebar">
                <div class="brand-panel">
                    <div class="brand-icon"><img src="assets/images/servercheck-logo.png" alt="ServerCheck logo"></div>
                    <div>
                        <h1>ServerCheck</h1>
                        <p>Realtime uptime monitor</p>
                    </div>
                </div>
                <nav class="nav-panel">
                    <?php echo renderNavLink('dashboard', 'Dashboard', 'dashboard.php', $activePage); ?>
                    <?php echo renderNavLink('monitor', 'Monitor', 'monitor.php', $activePage); ?>
                    <?php echo renderNavLink('history', 'History', 'history.php', $activePage); ?>
                    <?php echo renderNavLink('settings', 'Settings', 'settings.php', $activePage); ?>
                </nav>
                <div class="status-card neon-card">
                    <span class="status-dot online"></span>
                    <div>
                        <strong>System Normal</strong>
                        <p>Everything is running smoothly.</p>
                    </div>
                </div>
                <div class="profile-card">
                    <div class="avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                    <div>
                        <p><?php echo escape($username); ?></p>
                        <span>Administrator</span>
                    </div>
                </div>
            </aside>
            <main class="main-panel">
                <header class="topbar">
                    <div>
                        <h2><?php echo escape($pageTitle); ?></h2>
                        <p><?php echo escape($subtext); ?></p>
                    </div>
                    <div class="top-actions">
                        <button class="ghost-btn" id="refreshButton">Refresh</button>
                        <a href="auth/logout.php" class="primary-btn">Logout</a>
                    </div>
                </header>
                <?php
}

function renderNavLink(string $key, string $label, string $href, string $activePage): string
{
    $activeClass = $key === $activePage ? 'nav-link active' : 'nav-link';
    return sprintf('<a class="%s" href="%s">%s</a>', $activeClass, $href, $label);
}

function renderPageEnd()
{
    ?>
            </main>
        </div>
        <script src="assets/js/app.js"></script>
    </body>

    </html>
    <?php
}
