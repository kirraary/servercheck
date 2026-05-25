<?php
require_once __DIR__ . '/inc/functions.php';
requireLogin();
$userId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$url = normalizeUrl($_POST['url'] ?? '');
if (empty($url)) {
    flash('error', 'Please enter a valid website address.');
    header('Location: dashboard.php');
    exit;
}

if (isDuplicateWebsite($userId, $url)) {
    flash('error', 'This website is already being monitored.');
    header('Location: dashboard.php');
    exit;
}

$statusData = fetchWebsiteStatus($url);

$pdo = dbConnection();
$stmt = $pdo->prepare('INSERT INTO websites (user_id, url, status, response_time, ip_address, http_code, last_checked) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $userId,
    $url,
    $statusData['status'],
    $statusData['response_time'],
    $statusData['ip_address'],
    $statusData['http_code'],
    $statusData['last_checked'],
]);
$websiteId = $pdo->lastInsertId();

if ($websiteId) {
    ensureMonitorLogsTableExists();
    $history = $pdo->prepare('INSERT INTO monitor_logs (website_id, status, response_time, ip_address, http_code, error_message, checked_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $history->execute([
        $websiteId,
        $statusData['status'],
        $statusData['response_time'],
        $statusData['ip_address'],
        $statusData['http_code'],
        null,
        $statusData['last_checked'],
    ]);
}

flash('success', 'Website added and checked immediately. Status: ' . strtoupper($statusData['status']) . '.');
header('Location: monitor.php');
exit;
