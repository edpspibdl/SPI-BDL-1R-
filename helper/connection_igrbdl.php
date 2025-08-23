<?php

// Informasi koneksi PostgreSQL
$db_host = '192.168.247.191';  // Sesuaikan dengan host PostgreSQL Anda
$db_port = '5432';           // Port default PostgreSQL
$db_name = 'igrbdl';       // Nama database PostgreSQL
$db_user = 'edp';            // Username PostgreSQL
$db_pass = '3dp1grVIEW';     // Password PostgreSQL

try {
    // Buat koneksi menggunakan PDO
    $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");

    // Atur mode error untuk koneksi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
