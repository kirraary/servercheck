<?php
require_once __DIR__ . '/config.php';

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function getUserByUsername($username) {
    $pdo = dbConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function getUserById($id) {
    $pdo = dbConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserWebsites($userId) {
    $pdo = dbConnection();
    $stmt = $pdo->prepare('SELECT * FROM websites WHERE user_id = ? ORDER BY last_checked DESC, created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getMonitorHistory($userId, array $filters = []) {
    $pdo = dbConnection();
    $sql = 'SELECT h.*, w.url, w.user_id FROM monitor_logs h JOIN websites w ON h.website_id = w.id WHERE w.user_id = ?';
    $params = [$userId];

    if (!empty($filters['query'])) {
        $sql .= ' AND w.url LIKE ?';
        $params[] = '%' . $filters['query'] . '%';
    }
    if (!empty($filters['status'])) {
        $sql .= ' AND h.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= ' AND h.checked_at >= ?';
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= ' AND h.checked_at <= ?';
        $params[] = $filters['date_to'] . ' 23:59:59';
    }
    $sql .= ' ORDER BY h.checked_at DESC LIMIT 200';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function updateUserSettings($userId, array $data) {
    $fields = [];
    $params = [];

    if (isset($data['username'])) {
        $fields[] = 'username = ?';
        $params[] = $data['username'];
    }
    if (isset($data['password'])) {
        $fields[] = 'password = ?';
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    if (isset($data['refresh_interval'])) {
        $fields[] = 'refresh_interval = ?';
        $params[] = (int)$data['refresh_interval'];
    }
    if (isset($data['monitor_timeout'])) {
        $fields[] = 'monitor_timeout = ?';
        $params[] = (int)$data['monitor_timeout'];
    }
    if (isset($data['theme'])) {
        $fields[] = 'theme = ?';
        $params[] = $data['theme'];
    }
    if (isset($data['notifications_enabled'])) {
        $fields[] = 'notifications_enabled = ?';
        $params[] = $data['notifications_enabled'] ? 1 : 0;
    }

    if (empty($fields)) {
        return false;
    }

    $params[] = $userId;
    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $pdo = dbConnection();
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function getUserSettings($userId) {
    return getUserById($userId);
}

function getDashboardStats($userId) {
    $pdo = dbConnection();
    $stats = [
        'total' => 0,
        'online' => 0,
        'offline' => 0,
        'last_checked' => null,
        'avg_response_time' => null,
    ];

    $stmt = $pdo->prepare('SELECT COUNT(*) AS total, SUM(status = "online") AS online, SUM(status = "offline") AS offline, MAX(last_checked) AS last_checked, AVG(response_time) AS avg_response_time FROM websites WHERE user_id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row) {
        $stats['total'] = (int)$row['total'];
        $stats['online'] = (int)$row['online'];
        $stats['offline'] = (int)$row['offline'];
        $stats['last_checked'] = $row['last_checked'];
        $stats['avg_response_time'] = $row['avg_response_time'] !== null ? (int)round($row['avg_response_time']) : null;
    }
    return $stats;
}

function ensureMonitorLogsTableExists() {
    $pdo = dbConnection();
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
        $pdo->exec($sql);
    }
}

function normalizeUrl($url) {
    $url = trim($url);
    if (empty($url)) {
        return '';
    }
    if (strpos($url, '://') === false) {
        $url = 'http://' . $url;
    }
    return rtrim($url, '/');
}

function fetchWebsiteStatus(string $url): array {
    $url = trim($url);
    if (strpos($url, '://') === false) {
        $url = 'http://' . $url;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ServerCheck-Uptime-Agent/1.0');

    $start = microtime(true);
    $body = curl_exec($ch);
    $end = microtime(true);
    $info = curl_getinfo($ch);
    $error = curl_errno($ch);

    $status = 'unknown';
    $responseTime = null;
    $ipAddress = null;
    $httpCode = null;

    if ($error !== 0) {
        $status = 'offline';
    }

    if (isset($info['http_code']) && $info['http_code'] > 0) {
        $httpCode = (int)$info['http_code'];
        $responseTime = isset($info['total_time']) ? (int)round($info['total_time'] * 1000) : (int)(($end - $start) * 1000);
        $ipAddress = !empty($info['primary_ip']) ? $info['primary_ip'] : null;

        if ($httpCode >= 200 && $httpCode < 400) {
            $status = 'online';
        } else {
            $status = 'offline';
        }
    }

    return [
        'status' => $status,
        'response_time' => $responseTime,
        'ip_address' => $ipAddress,
        'http_code' => $httpCode,
        'last_checked' => date('Y-m-d H:i:s'),
    ];
}

function isDuplicateWebsite($userId, $url) {
    $pdo = dbConnection();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM websites WHERE user_id = ? AND url = ?');
    $stmt->execute([$userId, $url]);
    return $stmt->fetchColumn() > 0;
}

function getWebsiteById($id, $userId) {
    $pdo = dbConnection();
    $stmt = $pdo->prepare('SELECT * FROM websites WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$id, $userId]);
    return $stmt->fetch();
}
