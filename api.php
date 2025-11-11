<?php
/**
 * Crashout API
 * Handles adding new crashout events and returning category stats
 */
header('Content-Type: application/json');

// Load DB credentials from config.php
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
 * 🔹 VALID CATEGORIES
 * Only these can be added to the database.
 */
$valid = ['sports', 'gaming', 'delivery', 'minorities', 'technology', 'other'];

/**
 * 🔹 ADD EVENT
 * Called when user clicks a crashout button
 */
if ($action === 'add') {
    $category = strtolower(trim($_POST['category'] ?? ''));
    $reason = trim($_POST['reason'] ?? '');

    if ($category === '') {
        echo json_encode(['success' => false, 'error' => 'Missing category']);
        exit;
    }

    if (!in_array($category, $valid)) {
        echo json_encode(['success' => false, 'error' => 'Invalid category']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO crashout_events (category, reason, created_at) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$category, $reason]);
        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        // Log any database error for debugging
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

        // Ensure all valid categories exist in output (even if count is 0)
        $stats = [];
        foreach ($valid as $cat) {
            $stats[ucfirst($cat)] = isset($rows[$cat]) ? (int)$rows[$cat] : 0;
        }

        echo json_encode($stats);
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
