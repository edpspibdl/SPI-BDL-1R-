<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Daftar JOB</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Informasi JOB</h4>
    </div>
    <div class="card-body">
      <p>Selamat datang di halaman daftar JOB. Pada halaman ini, Anda dapat melihat status terkini dari proses JOB yang dijalankan.</p>
      <p>Berikut adalah daftar JOB yang tersedia:</p>
      <table class="table table-sm table-striped table-bordered" style="width: 50%; margin-left: 0 auto; border: 2px solid black;">
        <thead style="border: 2px solid black;">
          <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="text-align: center;">Nama JOB</th>
            <th style="width: 50%; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $jobs = [
            ['name' => 'DTA5', 'actions' => [['label' => 'Lihat Halaman', 'url' => './dta5.php', 'class' => 'btn-primary', 'icon' => 'fas fa-eye']]], 
            ['name' => 'SET PAGI', 'actions' => [['label' => 'Lihat Halaman', 'url' => './setPagi.php', 'class' => 'btn-success', 'icon' => 'fas fa-eye']]], 
            ['name' => 'TAX', 'actions' => [['label' => 'Lihat Halaman', 'url' => './tax.php', 'class' => 'btn-danger', 'icon' => 'fas fa-eye']]]
          ];

          foreach ($jobs as $index => $job) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . ($index + 1) . '</td>';
            echo '<td>' . $job['name'] . '</td>';
            echo '<td>';
            foreach ($job['actions'] as $action) {
              echo '<button class="btn btn-sm ' . $action['class'] . '" onclick="navigateTo(\'' . $action['url'] . '\')">';
              echo '<i class="' . $action['icon'] . '"></i> ' . $action['label'];
              echo '</button> ';
            }
            echo '</td>';
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer text-right">
      <small class="text-muted">Halaman diperbarui pada <?= date('d M Y H:i:s') ?></small>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<script src="../assets/js/page/modules-datatables.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function navigateTo(url) {
    // Use window.location.replace to navigate without changing the address bar
    window.location.replace(url);
  }
</script>
