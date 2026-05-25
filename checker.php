<?php
require_once __DIR__ . '/inc/functions.php';

$pdo = dbConnection();
$stmt = $pdo->prepare('SELECT id, url FROM websites');
$stmt->execute();
$sites = $stmt->fetchAll();

if (!$sites) {
    echo "No websites to check.\n";
    exit;
}

// Update with http_code and last_checked
ensureMonitorLogsTableExists();
$update = $pdo->prepare('UPDATE websites SET status = ?, response_time = ?, ip_address = ?, http_code = ?, last_checked = NOW() WHERE id = ?');
$historyInsert = $pdo->prepare('INSERT INTO monitor_logs (website_id, status, response_time, ip_address, http_code, error_message, checked_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
foreach ($sites as $site) {
    $result = fetchWebsiteStatus($site['url']);
    $update->execute([
        $result['status'],
        $result['response_time'],
        $result['ip_address'],
        $result['http_code'],
        $site['id'],
    ]);
    $historyInsert->execute([
        $site['id'],
        $result['status'],
        $result['response_time'],
        $result['ip_address'],
        $result['http_code'],
        $result['status'] === 'offline' ? 'Offline or unreachable' : null,
    ]);
    echo sprintf("Checked %s -> %s in %sms\n", $site['url'], $result['status'], $result['response_time'] ?? 0);
}
