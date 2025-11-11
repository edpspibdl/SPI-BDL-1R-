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
?>

<link rel="stylesheet" href="./assets/css/style.css">

<!-- Tambahkan CSS untuk grafik responsif -->
<style>
  .chart-container {
    position: relative;
    width: 100%;
    height: 60vh;              /* 60% tinggi viewport, cukup proporsional */
    max-height: 500px;         /* Batasi tinggi maksimum */
    padding: 10px;
  }

  .chart-container canvas {
    width: 100% !important;    /* Ikuti lebar container */
    height: 100% !important;   /* Ikuti tinggi container */
    display: block;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Dashboard (Real Time)</h1>
  <div class="d-flex align-items-center">
  <span class="badge badge-secondary mr-2">
    Cabang: 
    <?php
      // Tampilkan nama cabang berdasarkan branch_target
      $branch = $_SESSION['branch_target'] ?? '';
      switch ($branch) {
        case 'spi1r':
          echo 'SPI METRO';
          break;
        case 'igr2u':
          echo 'SPI PRINGSEWU';
          break;
        case 'igrbdl':
          echo 'INDOGROSIR BANDAR LAMPUNG';
          break;
        default:
          echo 'Tidak Diketahui';
      }
    ?>
  </span>
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
          <div class="card-icon bg-info">
            <i class="fas fa-money-bill-wave"></i>
          </div>
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
          <div class="card-icon bg-info">
            <i class="fas fa-users-cog"></i>
          </div>
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

    <!-- GRAFIK -->
    <div class="row mt-4">
      <div class="col-lg-12 col-md-12 col-sm-12">
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
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="./function.js"></script>
<?php require_once '../layout/_bottom.php'; ?>
