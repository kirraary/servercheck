<?php
require_once __DIR__ . '/inc/functions.php';
requireLogin();
$userId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$website = getWebsiteById($id, $userId);
if (!$website) {
    flash('error', 'Website not found.');
    header('Location: dashboard.php');
    exit;
}

$pdo = dbConnection();
$stmt = $pdo->prepare('DELETE FROM websites WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);
flash('success', 'Website removed successfully.');
header('Location: monitor.php');
exit;
