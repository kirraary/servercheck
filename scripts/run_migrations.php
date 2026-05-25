<?php
require_once __DIR__ . '/../inc/config.php';

$pdo = dbConnection();

// Update websites table columns
$columns = $pdo->query("SHOW COLUMNS FROM websites")->fetchAll(PDO::FETCH_COLUMN);
$needAlter = false;
$alterStmts = [];

if (!in_array('http_code', $columns)) {
    $needAlter = true;
    $alterStmts[] = 'ADD COLUMN http_code INT DEFAULT NULL';
}
if (!in_array('last_checked', $columns)) {
    $needAlter = true;
    $alterStmts[] = 'ADD COLUMN last_checked DATETIME DEFAULT NULL';
}
if ($needAlter) {
    $sql = 'ALTER TABLE websites ' . implode(', ', $alterStmts);
    echo "Running: $sql\n";
    $pdo->exec($sql);
    echo "Websites table migration complete.\n";
}

// Update users table settings columns
$userColumns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
$needAlterUser = false;
$alterUserStmts = [];
if (!in_array('refresh_interval', $userColumns)) {
    $needAlterUser = true;
    $alterUserStmts[] = 'ADD COLUMN refresh_interval INT NOT NULL DEFAULT 5';
}
if (!in_array('monitor_timeout', $userColumns)) {
    $needAlterUser = true;
    $alterUserStmts[] = 'ADD COLUMN monitor_timeout INT NOT NULL DEFAULT 15';
}
if (!in_array('theme', $userColumns)) {
    $needAlterUser = true;
    $alterUserStmts[] = "ADD COLUMN theme VARCHAR(20) NOT NULL DEFAULT 'dark'";
}
if (!in_array('notifications_enabled', $userColumns)) {
    $needAlterUser = true;
    $alterUserStmts[] = 'ADD COLUMN notifications_enabled TINYINT(1) NOT NULL DEFAULT 1';
}
if ($needAlterUser) {
    $sql = 'ALTER TABLE users ' . implode(', ', $alterUserStmts);
    echo "Running: $sql\n";
    $pdo->exec($sql);
    echo "Users table migration complete.\n";
}

// Create monitor_logs if missing
$tables = $pdo->query("SHOW TABLES LIKE 'monitor_logs'")->fetchAll(PDO::FETCH_COLUMN);
if (empty($tables)) {
    $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS monitor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    status VARCHAR(20) NOT NULL,
    response_time INT DEFAULT NULL,
    ip_address VARCHAR(100) DEFAULT NULL,
    http_code INT DEFAULT NULL,
    error_message VARCHAR(255) DEFAULT NULL,
    checked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
    echo "Creating monitor_logs table...\n";
    $pdo->exec($sql);
    echo "History table created.\n";
}

if (!$needAlter && !$needAlterUser && !empty($tables)) {
    echo "No migrations needed.\n";
}
