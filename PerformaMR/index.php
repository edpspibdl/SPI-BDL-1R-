<?php
require_once '../helper/connection.php';

$title_caption = 'Monitoring Performa MR';
// Konsistensi variabel tanggal
$tgl = isset($_GET['tanggalMulai']) ? $_GET['tanggalMulai'] : date('Y-m-d');

// ==== Logika Fungsi Perhitungan Target ====
$q_member = "SELECT COUNT(*) AS member_terdaftar 
             FROM tbmaster_customer 
             WHERE cus_kodeigr='2G' 
               AND cus_namamember <> 'NEW' 
               AND cus_recordid IS NULL";

try {
    // Gunakan metode PDO ($conn->query) karena $conn adalah objek PDO
    $stmt_member = $conn->query($q_member);
    $member_data = $stmt_member->fetch(PDO::FETCH_ASSOC);
    $total_member_terdaftar = (int)($member_data['member_terdaftar'] ?? 0);
} catch (PDOException $e) {
    // Jika error saat query
    $total_member_terdaftar = 0;
    error_log($e->getMessage());
}

// Logika Target Bulanan
$bulan = (int)date('n');
$target_bulanan = [
    1 => 1188, 2 => 1247, 3 => 1309, 4 => 1375, 5 => 1444, 6 => 1516, 
    7 => 1592, 8 => 1671, 9 => 1755, 10 => 1026, 11 => 1077, 12 => 1131
];
$target_member = $target_bulanan[$bulan] ?? 0;
$kurang = $target_member - $total_member_terdaftar;
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../include/head.php'; ?>
<style>
    body { font-size: 1rem; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    /* Styling Card Custom */
    .card-custom { 
        border: 2px solid #000 !important; 
        border-radius: 0; 
        margin-bottom: 25px; 
        box-shadow: 5px 5px 0px #000; 
    }
    
    .header-panel { 
        background: #000 !important; 
        color: #fff !important; 
        padding: 12px 15px !important; 
        font-weight: bold !important; 
        text-transform: uppercase;
        font-size: 1.1rem;
        letter-spacing: 1px;
    }

    .target-box h2 { font-size: 3.5rem; font-weight: 800; margin: 5px 0; color: #007bff; }
    .target-box h6 { font-size: 1rem; color: #666; text-transform: uppercase; }
    
    /* Penyesuaian Tabel agar tidak terlalu raksasa namun tetap jelas */
    .table { font-size: 0.95rem !important; }
    .alert h3 { margin-bottom: 0; font-weight: bold; font-size: 1.5rem; }
</style>

<body>
<?php include '../include/nav-bar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 col-md-7">
            <div class="card card-custom">
                <div class="header-panel">TABLE REKAP MR</div>
                <div class="card-body">
                    <?php 
                    include 'query.php'; // Pastikan query.php menggunakan variabel $conn yang sama
                    include 'tabel.php'; 
                    ?>
                </div>
            </div>

            <div class="card card-custom">
                <div class="header-panel">GRAFIK DISTRIBUSI KECAMATAN</div>
                <div class="card-body">
                    <?php include 'saleskecamatan.php'; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-5">
            <div class="card card-custom">
                <div class="header-panel">PROGRESS TARGET MEMBER</div>
                <div class="card-body text-center py-4">
                    <div class="target-box">
                        <h6>Member Terdaftar</h6>
                        <h2><?php echo number_format($total_member_terdaftar); ?></h2>
                        <p class="lead">Target Bulan Ini: <strong><?php echo number_format($target_member); ?></strong></p>
                        <hr style="border-top: 2px solid #000;">
                        <?php if ($kurang > 0): ?>
                            <div class="alert alert-danger py-3">
                                <h3>KURANG <?php echo number_format($kurang); ?> LAGI!</h3>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success py-3">
                                <h3>TARGET TERCAPAI! 🚀</h3>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="header-panel">PROPORSI SALES</div>
                <div class="card-body">
                    <?php include 'chart.php'; ?>
                </div>
            </div>

            <div class="card card-custom">
                <div class="header-panel">TOP SALES BY KECAMATAN</div>
                <div class="card-body p-0"> <?php include 'topsales.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../include/script-js.php'; ?>
</body>
</html>