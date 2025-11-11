<?php
/**
 * Crashout API
 * Handles adding new crashout events and returning category stats
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load DB config
$config = include('config.php');

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

// Determine which API action is being requested
$action = $_GET['action'] ?? '';

/**
 * 🔹 ADD EVENT
 * Called when user clicks a crashout button
 */
if ($action === 'add') {
    $category = trim($_POST['category'] ?? '');
    $reason   = trim($_POST['reason'] ?? ''); // frontend still sends 'reason'

    if ($category === '') {
        echo json_encode(['success' => false, 'error' => 'Missing category']);
        exit;
    }

    try {
        // Note: DB column name is 'detail', not 'reason'
        $stmt = $pdo->prepare("INSERT INTO crashout_events (category, detail, created_at) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$category, $reason]);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Insert failed']);
        }
    } catch (PDOException $e) {
        file_put_contents('/var/log/crashout_api.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}

/**
 * 🔹 STATS
 * Returns total count for each crashout category
 */
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

/**
 * 🔹 DEFAULT RESPONSE
 * Triggered if no or unknown action is passed
 */
echo json_encode(['error' => 'Invalid action']);
exit;
?>
