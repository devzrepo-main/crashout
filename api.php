<?php
header('Content-Type: application/json');

function db() {
  static $pdo = null;
  if ($pdo) return $pdo;

  $cfgPath = __DIR__ . '/config.php';
  if (!file_exists($cfgPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Missing config.php. Copy config.sample.php to config.php and set DB creds.']);
    exit;
  }
  $cfg = require $cfgPath;

  $dsn = "mysql:host={$cfg['host']};dbname={$cfg['db']};charset={$cfg['charset']}";
  $opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];
  $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $opts);
  return $pdo;
}

function json_body() {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function ok($data) {
  echo json_encode($data);
  exit;
}

function bad($msg, $code=400) {
  http_response_code($code);
  echo json_encode(['error' => $msg]);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? null;

$valid = ['sports','gaming','delivery','minorities','other'];

try {
  $pdo = db();

  if ($method === 'GET' && $action === 'stats') {
    // Counts per category
    $counts = array_fill_keys($valid, 0);
    $stmt = $pdo->query("SELECT category, COUNT(*) AS c FROM crashout_events GROUP BY category");
    foreach ($stmt as $row) {
      $cat = $row['category'];
      if (isset($counts[$cat])) $counts[$cat] = (int)$row['c'];
    }

    // Recent 20 events
    $recentStmt = $pdo->query("SELECT category, detail, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') AS when_fmt
                               FROM crashout_events ORDER BY id DESC LIMIT 20");
    $recent = [];
    foreach ($recentStmt as $r) {
      $recent[] = [
        'category' => $r['category'],
        'detail'   => $r['detail'],
        'when'     => $r['when_fmt'],
      ];
    }

    ok(['counts' => $counts, 'recent' => $recent]);
  }

  if ($method === 'POST') {
    $data = json_body();
    $cat = strtolower(trim($data['category'] ?? ''));
    $detail = trim($data['detail'] ?? '');

    if (!in_array($cat, $valid, true)) {
      bad('Invalid category.');
    }
    if ($detail !== '' && mb_strlen($detail) > 255) {
      bad('Detail too long (max 255 chars).');
    }

    $stmt = $pdo->prepare("INSERT INTO crashout_events (category, detail) VALUES (:cat, :detail)");
    $stmt->execute([':cat' => $cat, ':detail' => $detail !== '' ? $detail : null]);

    ok(['status' => 'ok']);
  }

  // Fallback
  bad('Not found', 404);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error', 'detail' => $e->getMessage()]);
}
