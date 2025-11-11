<?php
/**
 * Crashout API – Production-Clean Version
 * Supports actions: add, stats, clear
 * Logs errors to /var/log/crashout_php_errors.log
 */

header('Content-Type: application/json');

// 🔹 Quiet error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/crashout_php_errors.log');

// 🔹 Load database configuration
$config = include('config.php');

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log('DB Connection failed: ' . $e->getMessage());
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? '';

// ============================================================
// 🔹 ADD EVENT
// ============================================================
if ($action === 'add') {
    $category = strtolower(trim($_POST['category'] ?? ''));
    $reason   = trim($_POST['reason'] ?? '');

    if ($category === '') {
        echo json_encode(['success' => false, 'error' => 'Missing category']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO crashout_events (category, detail, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$category, $reason]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log('DB Insert failed: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}

// ============================================================
// 🔹 GET STATS
// ============================================================
if ($action === 'stats') {
    try {
        $stmt = $pdo->query("
            SELECT category, COUNT(*) AS total
            FROM crashout_events
            GROUP BY category
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        echo json_encode($rows);
    } catch (PDOException $e) {
        error_log('DB Select failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch stats']);
    }
    exit;
}

// ============================================================
// 🔹 CLEAR ALL EVENTS
// ============================================================
if ($action === 'clear') {
    try {
        $pdo->exec("TRUNCATE TABLE crashout_events");
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log('DB Truncate failed: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Failed to clear crashouts']);
    }
    exit;
}

// ============================================================
// 🔹 DEFAULT RESPONSE
// ============================================================
echo json_encode(['error' => 'Invalid action']);
exit;
?>
