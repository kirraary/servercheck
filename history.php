<?php
require_once __DIR__ . '/inc/layout.php';
requireLogin();
$userId = currentUserId();
$filters = [
    'query' => trim($_GET['query'] ?? ''),
    'status' => trim($_GET['status'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
];
$history = getMonitorHistory($userId, $filters);
renderPageStart('history', 'History', 'View your monitoring logs and uptime history.');
?>
<section class="panel-section">
    <div class="panel-heading">
        <div>
            <h3>History</h3>
            <p>Browse past monitoring checks and filter by website, status, or date.</p>
        </div>
    </div>

    <div class="history-filters neon-card">
        <form method="get" class="filter-form">
            <div class="filter-input">
                <label>Search URL</label>
                <input type="text" name="query" value="<?php echo escape($filters['query']); ?>" placeholder="Search by URL">
            </div>
            <div class="filter-input">
                <label>Status</label>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="online"<?php echo $filters['status'] === 'online' ? ' selected' : ''; ?>>Online</option>
                    <option value="offline"<?php echo $filters['status'] === 'offline' ? ' selected' : ''; ?>>Offline</option>
                    <option value="unknown"<?php echo $filters['status'] === 'unknown' ? ' selected' : ''; ?>>Unknown</option>
                </select>
            </div>
            <div class="filter-input">
                <label>From</label>
                <input type="date" name="date_from" value="<?php echo escape($filters['date_from']); ?>">
            </div>
            <div class="filter-input">
                <label>To</label>
                <input type="date" name="date_to" value="<?php echo escape($filters['date_to']); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="primary-btn">Filter</button>
                <a href="history.php" class="ghost-btn">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-card neon-card">
        <div class="table-header">
            <h4>Monitoring logs</h4>
            <span>Latest checks are shown first.</span>
        </div>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Website</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>HTTP</th>
                        <th>IP Address</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr><td colspan="7" class="empty-row">No history records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($history as $row): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i:s', strtotime($row['checked_at'])); ?></td>
                                <td><?php echo escape($row['url']); ?></td>
                                <td><span class="badge <?php echo $row['status'] === 'online' ? 'online' : ($row['status'] === 'offline' ? 'offline' : 'unknown'); ?>"><?php echo strtoupper($row['status']); ?></span></td>
                                <td><?php echo $row['response_time'] !== null ? escape($row['response_time']) . ' ms' : '-'; ?></td>
                                <td><?php echo $row['http_code'] !== null ? escape($row['http_code']) : '-'; ?></td>
                                <td><?php echo escape($row['ip_address'] ?: '-'); ?></td>
                                <td><?php echo escape($row['error_message'] ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php renderPageEnd(); ?>
