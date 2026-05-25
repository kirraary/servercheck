<?php
require_once __DIR__ . '/inc/layout.php';
requireLogin();
$userId = currentUserId();
$websites = getUserWebsites($userId);
$stats = getDashboardStats($userId);
renderPageStart('monitor', 'Monitor', 'Add and manage monitored websites.');
?>
<section class="overview-grid">
    <article class="stat-card neon-card">
        <span>Total Sites</span>
        <h3 id="statTotal"><?php echo $stats['total']; ?></h3>
        <p>Active monitored websites</p>
    </article>
    <article class="stat-card neon-card">
        <span>Online</span>
        <h3 id="statOnline"><?php echo $stats['online']; ?></h3>
        <p>Currently reachable</p>
    </article>
    <article class="stat-card neon-card">
        <span>Offline</span>
        <h3 id="statOffline"><?php echo $stats['offline']; ?></h3>
        <p>Needs attention</p>
    </article>
    <article class="stat-card neon-card">
        <span>Avg Response</span>
        <h3 id="statAvgResponse"><?php echo $stats['avg_response_time'] !== null ? escape($stats['avg_response_time']) . ' ms' : '-'; ?></h3>
        <p>Average latency</p>
    </article>
</section>

<section class="panel-section">
    <div class="panel-heading">
        <div>
            <h3>Monitor</h3>
            <p>Track uptime, HTTP codes, IP addresses, and response time.</p>
        </div>
        <form class="add-form" action="add_website.php" method="post">
            <input type="text" name="url" placeholder="Enter domain or URL" autocomplete="off" required>
            <button type="submit" class="primary-btn">Add Site</button>
        </form>
    </div>

    <?php if ($message = flash('success')): ?>
        <div class="alert success"><?php echo escape($message); ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert error"><?php echo escape($message); ?></div>
    <?php endif; ?>

    <div class="filter-row neon-card">
        <div class="filter-input">
            <label>Search</label>
            <input type="text" id="searchInput" placeholder="Search by URL, IP, or code">
        </div>
        <div class="filter-input">
            <label>Status</label>
            <select id="statusFilter">
                <option value="">All statuses</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="unknown">Unknown</option>
            </select>
        </div>
    </div>

    <div class="table-card neon-card">
        <div class="table-header">
            <h4>Monitored websites</h4>
            <span>Auto refresh every few seconds.</span>
        </div>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>HTTP</th>
                        <th>IP Address</th>
                        <th>Last Checked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="websiteRows">
                    <?php foreach ($websites as $site): ?>
                        <tr data-id="<?php echo $site['id']; ?>">
                            <td><?php echo escape($site['url']); ?></td>
                            <td><span class="badge <?php echo $site['status'] === 'online' ? 'online' : ($site['status'] === 'offline' ? 'offline' : 'unknown'); ?>"><?php echo strtoupper($site['status'] ?: 'unknown'); ?></span></td>
                            <td><?php echo $site['response_time'] !== null ? escape($site['response_time']) . ' ms' : '-'; ?></td>
                            <td><?php echo $site['http_code'] !== null ? escape($site['http_code']) : '-'; ?></td>
                            <td><?php echo escape($site['ip_address'] ?: '-'); ?></td>
                            <td><?php echo $site['last_checked'] ? date('M d, Y H:i:s', strtotime($site['last_checked'])) : '-'; ?></td>
                            <td>
                                <form class="delete-form" action="delete_website.php" method="post" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="id" value="<?php echo $site['id']; ?>">
                                    <button type="submit" class="danger-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php renderPageEnd(); ?>
