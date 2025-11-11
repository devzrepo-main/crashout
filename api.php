<?php
/**
 * Crashout API — stable & debug-safe version
 * Supports: add / stats / clear
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load DB config
$config = include('config.php');

// ✅ Database connection
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// ✅ Read action
$action = $_GET['action'] ?? '';

// 🔍 Debug log (comment out later if not needed)
file_put_contents('/var/log/crashout_debug.log', date('[Y-m-d H:i:s] ') . "Action received: " . $action . PHP_EOL, FILE_APPEND);

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
        $stmt = $pdo->prepare("INSERT INTO crashout_events (category, detail, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$category, $reason]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        file_put_contents('/var/log/crashout_api.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}

// ============================================================
// 🔹 GET STATS
// ============================================================
if ($action === 'stats') {
    try {
        $stmt = $pdo->query("SELECT category, COUNT(*) AS total FROM crashout_events GROUP BY category");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        echo json_encode($rows);
    } catch (PDOException $e) {
        file_put_contents('/var/log/crashout_api.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(['error' => 'Failed to fetch stats']);
    }
    exit;
}

// ============================================================
// 🔹 CLEAR ALL CRASHOUTS
// ============================================================
if ($action === 'clear') {
    try {
        $pdo->exec("TRUNCATE TABLE crashout_events");
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        file_put_contents('/var/log/crashout_api.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Failed to clear crashouts']);
    }
    exit;
}

// ============================================================
// 🔹 INVALID ACTION FALLBACK
// ============================================================
file_put_contents('/var/log/crashout_debug.log', date('[Y-m-d H:i:s] ') . "Invalid action triggered.\n", FILE_APPEND);
echo json_encode(['error' => 'Invalid action']);
exit;
?>
