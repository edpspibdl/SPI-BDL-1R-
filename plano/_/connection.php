<?php
// PostgreSQL connection parameters
$host = '172.31.146.253';
$port = '5432';
$database = 'spibdl1r';
$username = 'edp';
$password = '3dp1grVIEW';

// Establish PostgreSQL PDO connection
try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$database;user=$username;password=$password");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
