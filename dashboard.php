<?php
require_once __DIR__ . '/inc/layout.php';
requireLogin();
$userId = currentUserId();
$stats = getDashboardStats($userId);
renderPageStart('dashboard', 'Dashboard', 'Overview statistics for your monitored servers.');
?>

<section class="overview-grid">
    <article class="stat-card neon-card">
        <span>Total monitored sites</span>
        <h3><?php echo $stats['total']; ?></h3>
        <p>Sites currently tracked.</p>
    </article>
    <article class="stat-card neon-card">
        <span>Online</span>
        <h3><?php echo $stats['online']; ?></h3>
        <p>Active and reachable servers.</p>
    </article>
    <article class="stat-card neon-card">
        <span>Offline</span>
        <h3><?php echo $stats['offline']; ?></h3>
        <p>Servers requiring attention.</p>
    </article>
    <article class="stat-card neon-card">
        <span>Avg response</span>
        <h3><?php echo $stats['avg_response_time'] !== null ? escape($stats['avg_response_time']) . ' ms' : '-'; ?></h3>
        <p>Average request latency.</p>
    </article>
</section>

<section class="panel-section">
    <div class="mini-grid">
        <div class="overview-card neon-card">
            <h4>Realtime activity</h4>
            <p>Live monitoring updates refresh every few seconds.</p>
        </div>
        <div class="overview-card neon-card">
            <h4>Healthy servers</h4>
            <p>Keep an eye on uptime and availability.</p>
        </div>
        <div class="overview-card neon-card">
            <h4>Performance</h4>
            <p>Track response time and HTTP reliability.</p>
        </div>
    </div>
</section>

<?php renderPageEnd(); ?>
