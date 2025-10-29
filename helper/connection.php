<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil target dari Sesi. Jika tidak ada, koneksi akan gagal di blok 'else' terakhir
$db_target = $_SESSION['db_target'] ?? ''; 
$branch_target = $_SESSION['branch_target'] ?? ''; 

// Inisialisasi variabel koneksi kosong
$host = null;
$dbname = null;
$username = null;
$password = null;

// --- Logika Konfigurasi Koneksi Berdasarkan Cabang dan Mode ---

if ($branch_target === 'spi1r') {
    if ($db_target === 'prod') {
        // Konfigurasi SPI1R (Produksi)
        $host = '172.31.146.253';
        $dbname = 'spibdl1r';
        $username = 'edp'; // Ganti dengan Username Prod SPI yang sebenarnya
        $password = '3dp1grVIEW'; // Ganti dengan Password Prod SPI yang sebenarnya
    } elseif ($db_target === 'sim') {
        // Konfigurasi SPI1R (Simulasi)
        $host = '172.31.146.167';
        $dbname = 'simspibdl1r';
        $username = 'simspibdl1r'; // Ganti dengan Username Sim SPI yang sebenarnya
        $password = 'simspibdl1r'; // Ganti dengan Password Sim SPI yang sebenarnya
    }
    
} elseif ($branch_target === 'igrbdl') {
    if ($db_target === 'prod') {
        // Konfigurasi IGRBDL (Produksi)
        $host = '192.168.247.191';
        $dbname = 'igrbdl';
        $username = 'edp'; // Ganti dengan Username Prod IGR yang sebenarnya
        $password = '3dp1grVIEW'; // Ganti dengan Password Prod IGR yang sebenarnya
    } elseif ($db_target === 'sim') {
        // Konfigurasi IGRBDL (Simulasi - Ganti dengan kredensial Sim IGR yang benar)
        $host = '192.168.247.191'; 
        $dbname = 'simigrbdl'; 
        $username = 'edp_sim_igr'; 
        $password = 'Password_Sim_IGR'; 
    }
}

// --- Koneksi PDO ---

// Cek apakah konfigurasi ditemukan (host tidak null)
if ($host && $dbname && $username && $password) {
    try {
        $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi gagal ke $dbname ($host) sebagai $username: " . $e->getMessage());
    }
} else {
    // Fallback eksplisit karena branch_target atau db_target tidak valid
    die("Koneksi gagal: Konfigurasi target cabang ($branch_target) atau database mode ($db_target) tidak ditemukan atau tidak valid.");
}
?>