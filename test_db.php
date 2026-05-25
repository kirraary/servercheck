<?php
require __DIR__ . '/inc/config.php';

try {
    $pdo = dbConnection();
    $stmt = $pdo->query("SELECT DATABASE() AS db, NOW() AS now");
    $row = $stmt->fetch();
    echo "OK - connected to: " . ($row['db'] ?? DB_NAME) . " at " . ($row['now'] ?? '');
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage();
}
