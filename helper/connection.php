<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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


$db_target = $_SESSION['db_target'] ?? '';       // contoh: 'prod' atau 'sim'
$branch_target = $_SESSION['branch_target'] ?? ''; // contoh: 'spi1r', 'spi2u', 'igrbdl'


if (
    isset($ALL_BRANCH_CONFIGS[$branch_target]) &&
    isset($ALL_BRANCH_CONFIGS[$branch_target][$db_target])
) {
    $config = $ALL_BRANCH_CONFIGS[$branch_target][$db_target];
    $host = $config['host'];
    $dbname = $config['dbname'];
    $username = $config['user'];
    $password = $config['pass'];

    try {
        $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("❌ Koneksi gagal ke database $dbname ($host) sebagai $username:<br>" . $e->getMessage());
    }
} else {
    die("⚠️ Koneksi gagal: Konfigurasi untuk cabang '$branch_target' atau mode '$db_target' tidak ditemukan.");
}


$remote_connections = [];
$remote_branch_names = [];

foreach ($ALL_BRANCH_CONFIGS as $code => $branch) {
    $remote_branch_names[$code] = $branch['name'];
    if ($code === $branch_target) continue; // skip cabang aktif

    if (isset($branch[$db_target])) {
        $remote = $branch[$db_target];
        try {
            $dsn_remote = "pgsql:host={$remote['host']};dbname={$remote['dbname']}";
            $conn_remote = new PDO($dsn_remote, $remote['user'], $remote['pass']);
            $conn_remote->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $remote_connections[$code] = $conn_remote;
        } catch (PDOException $e) {
            // Tidak fatal — hanya simpan pesan error
            $remote_connections[$code] = null;
            $error_messages[$code] = "Gagal koneksi ke {$branch['name']} ($code): " . $e->getMessage();
        }
    }
}

?>
