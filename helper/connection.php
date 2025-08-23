<?php


// Set default database connection target if not set
if (!isset($_SESSION['db_target'])) {
    $_SESSION['db_target'] = 'prod'; // default connection to 'prod'
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
