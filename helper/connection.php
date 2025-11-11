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
    // ======================= CABANG SPI METRO =======================
    if ($db_target === 'prod') {
        // Konfigurasi SPI1R (Produksi)
        $host = '172.31.146.253';
        $dbname = 'spibdl1r';
        $username = 'edp';
        $password = '3dp1grVIEW';
    } elseif ($db_target === 'sim') {
        // Konfigurasi SPI1R (Simulasi)
        $host = '172.31.146.167';
        $dbname = 'simspibdl1r';
        $username = 'simspibdl1r';
        $password = 'simspibdl1r';
    }

} elseif ($branch_target === 'spi2u') {
    // ======================= CABANG SPI PRINGSEWU =======================
    if ($db_target === 'prod') {
        // Konfigurasi SPI2U (Produksi)
        $host = '172.31.147.194';
        $dbname = 'spibdl2u';
        $username = 'edp';
        $password = '3dp1grVIEW';
    } elseif ($db_target === 'sim') {
        // Konfigurasi SPI2U (Simulasi)
        $host = '172.31.147.194';
        $dbname = 'simspibdl2u';
        $username = 'simspibdl2u';
        $password = 'simspibdl2u';
    }

} elseif ($branch_target === 'igrbdl') {
    // ======================= CABANG INDOGROSIR BANDAR LAMPUNG =======================
    if ($db_target === 'prod') {
        // Konfigurasi IGRBDL (Produksi)
        $host = '192.168.247.191';
        $dbname = 'igrbdl';
        $username = 'edp';
        $password = '3dp1grVIEW';
    } elseif ($db_target === 'sim') {
        // Konfigurasi IGRBDL (Simulasi)
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