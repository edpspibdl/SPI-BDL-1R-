<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Evaluasi Sales</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <h4 class="mb-0">Laporan Evaluasi Sales</h4>
    </div>

    <div class="card-body">
      <p>Selamat datang di halaman Laporan Evaluasi Sales. Di halaman ini, Anda dapat mengakses laporan kinerja penjualan yang disajikan setiap awal bulan.</p>
      <p>Berikut adalah daftar laporan evaluasi bulanan yang tersedia:</p>

      <div class="table-responsive">
        <table class="table table-bordered table-striped text-center" style="width: 50%; border: 2px solid #343a40;">
          <thead class="thead-dark">
            <tr>
              <th style="width: 5%;">No</th>
              <th>Nama Laporan</th>           
              <th style="width: 30%;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $reports = [
              ['name' => 'Sales 3 Bulan', 'actions' => [['label' => 'Lihat Halaman', 'url' => '../salesTigaBulan/index.php', 'class' => 'btn-danger']]],
              ['name' => 'Sales Di Luar Item Larangan', 'actions' => [['label' => 'Lihat Halaman', 'url' => '../salesPromo/index.php', 'class' => 'btn-success                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ']]],
            ];

            foreach ($reports as $index => $report) {
              echo '<tr>';
              echo '<td>' . ($index + 1) . '</td>';
              echo '<td class="text-left">' . $report['name'] . '</td>';
              echo '<td>';
              foreach ($report['actions'] as $action) {
                echo '<button class="btn btn-sm ' . $action['class'] . '" onclick="navigateTo(\'' . $action['url'] . '\')">';
                echo '<i class="fas fa-eye"></i> ' . $action['label'];
                echo '</button> ';
              }
              echo '</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer text-right">
      <small class="text-muted">Halaman diperbarui pada <?= date('d M Y H:i:s') ?></small>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- Tambahan style & script -->
<script src="../assets/js/page/modules-datatables.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function navigateTo(url) {
    window.location.href = url;
  }
</script>
