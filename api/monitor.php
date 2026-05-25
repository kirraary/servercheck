<?php
require_once __DIR__ . '/../inc/functions.php';
requireLogin();
header('Content-Type: application/json');
$userId = currentUserId();
$websites = getUserWebsites($userId);
$stats = getDashboardStats($userId);

foreach ($websites as &$site) {
    $site['response_time'] = $site['response_time'] !== null ? (int)$site['response_time'] : null;
    $site['http_code'] = isset($site['http_code']) ? ($site['http_code'] !== null ? (int)$site['http_code'] : null) : null;
}

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'websites' => $websites,
]);
exit;
