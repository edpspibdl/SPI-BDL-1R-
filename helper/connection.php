<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // pastikan session aktif
}

// kalau ada request untuk switch DB (via GET/POST)
if (isset($_GET['db'])) {
    $_SESSION['db_target'] = $_GET['db'];
} elseif (isset($_POST['db_target'])) {
    $_SESSION['db_target'] = $_POST['db_target'];
}

// Set default database connection target kalau belum ada
if (!isset($_SESSION['db_target'])) {
    $_SESSION['db_target'] = 'prod'; // default 'prod'
}

$db_target = $_SESSION['db_target'];

switch ($db_target) {
    case 'prod':
        $db_host = '172.31.146.253';
        $db_port = '5432';
        $db_name = 'spibdl1r';
        $db_user = 'edp';
        $db_pass = '3dp1grVIEW';
        break;

    case 'sim':
        $db_host = '172.31.146.167';
        $db_port = '5432';
        $db_name = 'simspibdl1r';
        $db_user = 'simspibdl1r';
        $db_pass = 'simspibdl1r';
        break;

    default:
        die("Database target not recognized.");
}

try {
    // Create a PDO connection
    $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
