<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';  // Pastikan koneksi menggunakan PDO

// Mengambil data untuk grafik
$customerData = $conn->query("SELECT COUNT(*) FROM tbmaster_customer WHERE CUS_KODEIGR = '1R'")->fetchColumn();
$mahasiswaData = 100;  // Contoh data statis
$mataKuliahData = 50;  // Contoh data statis
$nilaiMasukData = 200; // Contoh data statis
?>

<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  <div class="column">
    <div class="row">
      <!-- Card 1: Total CUSTOMER -->
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="far fa-user"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total CUSTOMER</h4>
            </div>
            <div class="card-body">
              <!-- Grafik Line -->
              <canvas id="customerChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Total Mahasiswa -->
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="far fa-user"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Mahasiswa</h4>
            </div>
            <div class="card-body">
              <!-- Grafik Line -->
              <canvas id="mahasiswaChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Card 3: Total Mata Kuliah -->
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="far fa-file"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Mata Kuliah</h4>
            </div>
            <div class="card-body">
              <!-- Grafik Line -->
              <canvas id="mataKuliahChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4: Total Nilai Masuk -->
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="far fa-newspaper"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Nilai Masuk</h4>
            </div>
            <div class="card-body">
              <!-- Grafik Line -->
              <canvas id="nilaiMasukChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart 1: Total CUSTOMER
  const ctxCustomer = document.getElementById('customerChart').getContext('2d');
  const customerChart = new Chart(ctxCustomer, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May'],
      datasets: [{
        label: 'Total CUSTOMER',
        data: [30, 50, 70, 90, <?= $customerData ?>],
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true
      }]
    }
  });

  // Chart 2: Total Mahasiswa
  const ctxMahasiswa = document.getElementById('mahasiswaChart').getContext('2d');
  const mahasiswaChart = new Chart(ctxMahasiswa, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May'],
      datasets: [{
        label: 'Total Mahasiswa',
        data: [10, 20, 30, 40, <?= $mahasiswaData ?>],
        borderColor: 'rgba(255, 99, 132, 1)',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        fill: true
      }]
    }
  });

  // Chart 3: Total Mata Kuliah
  const ctxMataKuliah = document.getElementById('mataKuliahChart').getContext('2d');
  const mataKuliahChart = new Chart(ctxMataKuliah, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May'],
      datasets: [{
        label: 'Total Mata Kuliah',
        data: [5, 10, 15, 20, <?= $mataKuliahData ?>],
        borderColor: 'rgba(255, 159, 64, 1)',
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        fill: true
      }]
    }
  });

  // Chart 4: Total Nilai Masuk
  const ctxNilaiMasuk = document.getElementById('nilaiMasukChart').getContext('2d');
  const nilaiMasukChart = new Chart(ctxNilaiMasuk, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May'],
      datasets: [{
        label: 'Total Nilai Masuk',
        data: [150, 180, 220, 250, <?= $nilaiMasukData ?>],
        borderColor: 'rgba(153, 102, 255, 1)',
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        fill: true
      }]
    }
  });
</script>

<?php
require_once '../layout/_bottom.php';
?>
