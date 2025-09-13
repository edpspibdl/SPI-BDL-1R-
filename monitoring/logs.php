<?php
header('Content-Type: application/json');

$logFile = __DIR__ . '/../log_pengunjung.txt';
if (!file_exists($logFile)) {
    echo json_encode(['error' => 'Log file not found']);
    exit;
}

$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$totalVisits = 0;
$uniqueVisitors = [];
$statusCounts = [];
$topPages = [];
$visitsOverTime = [];
$logs = [];

foreach ($lines as $line) {
    // Format log: [2025-09-11 15:34:10] IP: 192.168.1.5 | Page: /index.php | Agent: ...
    if (!preg_match('/\[(.*?)\] IP: (.*?) \| Page: (.*?) \| Agent:/', $line, $m)) {
        continue;
    }
    $timestamp = $m[1];
    $ip = $m[2];
    $page = $m[3];

    // 🚫 Skip kalau IP internal
    if (strpos($ip, '192.168.170.') === 0) {
        continue;
    }

    $method = "GET";  // default
    $status = 200;    // default (jika log kamu tidak menyimpan status)

    $totalVisits++;
    $uniqueVisitors[$ip] = true;
    $topPages[$page] = ($topPages[$page] ?? 0) + 1;
    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;

    $dateKey = substr($timestamp, 0, 10);
    $visitsOverTime[$dateKey] = ($visitsOverTime[$dateKey] ?? 0) + 1;

    $logs[] = [
        'ip' => $ip,
        'timestamp' => $timestamp,
        'method' => $method,
        'url' => $page,
        'status' => $status
    ];
}

ksort($visitsOverTime);

echo json_encode([
    'summary' => [
        'totalVisits' => $totalVisits,
        'uniqueVisitors' => count($uniqueVisitors),
        'statusCounts' => $statusCounts,
        'topPages' => $topPages,
        'visitsOverTime' => [
            'labels' => array_keys($visitsOverTime),
            'values' => array_values($visitsOverTime)
        ]
    ],
    'logs' => !empty($logs) ? array_reverse($logs) : [] // ✅ jaga jangan null
]);

