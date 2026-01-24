<?php
/**
 * connection.php
 * Support: Web (Session) & Task Scheduler (CLI)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ===============================
 * KONFIGURASI CABANG & DATABASE
 * ===============================
 */
$ALL_BRANCH_CONFIGS = [
    'spi1r' => [
        'name' => 'SPI METRO',
        'prod' => [
            'host' => '172.31.146.253',
            'dbname' => 'spibdl1r',
            'user' => 'edp',
            'pass' => '3dp1grVIEW',
        ],
        'sim' => [
            'host' => '172.31.146.167',
            'dbname' => 'simspibdl1r',
            'user' => 'simspibdl1r',
            'pass' => 'simspibdl1r',
        ],
    ],

    'spi2u' => [
        'name' => 'SPI PRINGSEWU',
        'prod' => [
            'host' => '172.31.147.194',
            'dbname' => 'spibdl2u',
            'user' => 'edp',
            'pass' => '3dp1grVIEW',
        ],
        'sim' => [
            'host' => '172.31.147.194',
            'dbname' => 'simspibdl2u',
            'user' => 'simspibdl2u',
            'pass' => 'simspibdl2u',
        ],
    ],

    'igrbdl' => [
        'name' => 'INDOGROSIR BANDAR LAMPUNG',
        'prod' => [
            'host' => '192.168.247.191',
            'dbname' => 'igrbdl',
            'user' => 'edp',
            'pass' => '3dp1grVIEW',
        ],
        'sim' => [
            'host' => '192.168.247.191',
            'dbname' => 'simigrbdl',
            'user' => 'edp_sim_igr',
            'pass' => 'Password_Sim_IGR',
        ],
    ],
];

/**
 * ==================================
 * DETEKSI MODE: WEB / CLI
 * ==================================
 */
$isCLI = (php_sapi_name() === 'cli');

/**
 * Jika dijalankan via Task Scheduler (CLI)
 */
if ($isCLI) {
    $db_target     = 'prod';   // prod / sim
    $branch_target = 'spi1r';  // default CLI
} else {
    $db_target     = $_SESSION['db_target'] ?? '';
    $branch_target = $_SESSION['branch_target'] ?? '';
}

/**
 * ==================================
 * PEMBATASAN AKSES SPI1R BERDASARKAN IP
 * (SILENT REDIRECT KE SPI2U)
 * ==================================
 */
if ($_SESSION['branch_target'] === 'spi1r') {
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';

    // Cek subnet 192.168.170.XX
    if (!preg_match('/^192\.168\.170\.\d{1,3}$/', $client_ip)) {
        // Silent redirect ke SPI2U
        $_SESSION['branch_target'] = 'spi2u';
    }
}

/**
 * ===============================
 * VALIDASI KONFIGURASI
 * ===============================
 */
if (
    !isset($ALL_BRANCH_CONFIGS[$branch_target]) ||
    !isset($ALL_BRANCH_CONFIGS[$branch_target][$db_target])
) {
    die(
        "⚠️ Koneksi gagal: Konfigurasi tidak ditemukan. " .
        "branch_target={$branch_target}, db_target={$db_target}"
    );
}

$config   = $ALL_BRANCH_CONFIGS[$branch_target][$db_target];
$host     = $config['host'];
$dbname   = $config['dbname'];
$username = $config['user'];
$password = $config['pass'];

/**
 * ===============================
 * KONEKSI DATABASE UTAMA
 * ===============================
 */
try {
    $conn = new PDO(
        "pgsql:host={$host};dbname={$dbname}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("❌ Koneksi database gagal: " . $e->getMessage());
}

/**
 * ===============================
 * KONEKSI REMOTE (OPSIONAL)
 * ===============================
 */
$remote_connections  = [];
$remote_branch_names = [];

foreach ($ALL_BRANCH_CONFIGS as $code => $branch) {
    $remote_branch_names[$code] = $branch['name'];

    if ($code === $branch_target) continue;
    if (!isset($branch[$db_target])) continue;

    try {
        $remote = $branch[$db_target];
        $dsn = "pgsql:host={$remote['host']};dbname={$remote['dbname']}";

        $remote_connections[$code] = new PDO(
            $dsn,
            $remote['user'],
            $remote['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    } catch (PDOException $e) {
        $remote_connections[$code] = null;
    }
}
