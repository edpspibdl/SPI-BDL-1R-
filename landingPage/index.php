<?php
ob_start(); // Mulai output buffering

// Daftar IP yang diizinkan
$allowed_ips = ['192.168.170.', '127.0.0.1'];
$user_ip = $_SERVER['REMOTE_ADDR']; // Ambil IP pengguna

// Cek apakah IP pengguna diizinkan
$allowed = false;
foreach ($allowed_ips as $ip_prefix) {
  if (strpos($user_ip, $ip_prefix) === 0) {
    $allowed = true;
    break;
  }
}

// Jika IP tidak diizinkan, arahkan ke halaman error yang benar
if (!$allowed) {
  header("Location: ../pageError/notaccess.php");
  exit;
}

ob_end_flush();

// index.php atau dashboard.php
require_once '../layout/_top.php'; 
require_once '../helper/connection.php'; // koneksi PDO ada di sini

// Logika PHP minimal
$db_status = $_SESSION['db_target'] ?? 'prod';
$server_status = ($db_status === 'prod') ? 'PRODUCTION' : 'SIMULASI';

// =====================
// Hitung jumlah Margin Minus (pakai PDO)
// =====================
$marmin_count = 0;
try {
  $stmt = $conn->query("SELECT COUNT(*) as cnt FROM marmin"); // sesuaikan nama tabel/view
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $marmin_count = $row['cnt'] ?? 0;
} catch (PDOException $e) {
  $marmin_count = 0; // fallback kalau query error
}
?>

<link rel="stylesheet" href="./assets/css/style.css">

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Dashboard (Real Time)</h1>
    <div class="d-flex align-items-center">
      <span class="badge badge-info mr-2">Status Server: <?php echo $server_status; ?></span>
      <span class="badge badge-primary" id="clock"></span>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Sales Information</h2>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Sales Nett Bulan Ini</h4>
            </div>
            <div class="card-body">
              <span id="earnedThisMonthValue" class="loading-data">Memuat...</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"> <i class="fas fa-money-bill-wave"></i> </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Sales Gross Bulan Ini</h4>
            </div>
            <div class="card-body">
              <span id="grossThisMonthValue" class="loading-data">Memuat...</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Keuntungan/Margin Bulan Ini</h4>
            </div>
            <div class="card-body">
              <span id="profitMarginMonthValue" class="loading-data">Memuat...</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"> <i class="fas fa-users-cog"></i> </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Member Belanja Bulan Ini</h4>
            </div>
            <div class="card-body">
              <span id="memberKhususBulanIni" class="loading-data">Memuat...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <!-- Kolom Grafik -->
      <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="card">
          <div class="card-header">
            <h4>Sales & Margin Bulanan (12 Bulan Terakhir)</h4>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="monthlySalesChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Alert / Notifikasi
<div class="col-lg-4 col-md-4 col-sm-12">
  <div class="card shadow">
    <div class="card-header text-white">
      <h4><i class="fas fa-exclamation-circle"></i> Alert / Notifikasi</h4>
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">

        Margin Minus
        <a href="../Margin/marginMinus.php" 
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span class="text-danger font-weight-bold">
            <i class="fas fa-bell fa-shake"></i> Margin Minus
          </span>
          <span class="badge badge-danger rounded-pill px-3 py-2">
            <?php echo $marmin_count; ?>
          </span>
        </a>

        <!-- Margin Minus -->
        <!-- <a href="../barkos/report.php" 
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span class="text-danger font-weight-bold">
            <i class="fas fa-bell fa-shake"></i> Barkos
          </span>
          <span class="badge badge-danger rounded-pill px-3 py-2">
            <?php echo $count_barkos; ?>
          </span>
        </a> -->

        <!-- Penjualan Turun -->
        <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
          <span class="text-info font-weight-bold">
            <i class="fas fa-chart-line"></i> Penjualan Turun
          </span>
          <span class="badge badge-info rounded-pill px-3 py-2">5</span>
        </li>
      </ul>
    </div>
  </div>
</div> --> 
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./function.js"></script>

<?php require_once '../layout/_bottom.php'; ?>
