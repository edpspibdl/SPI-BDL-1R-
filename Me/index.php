<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Month End</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Monitoring Month End</h4>
    </div>
    <div class="card-body">
      <p>Berikut adalah Hal Beberapa Hal Yang Harus di Cek Lakukan Sebelum Melakukan Month End:</p>
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
            ['name' => 'Intransit PB OBI', 'actions' => [['label' => 'Lihat Halaman', 'url' => './intransitPbObi.php', 'class' => 'btn-primary', 'icon' => 'fas fa-eye']]],
            ['name' => 'Intransit PB Batal', 'actions' => [['label' => 'Lihat Halaman', 'url' => './intransitPbBatal.php', 'class' => 'btn-danger', 'icon' => 'fas fa-eye']]],
            ['name' => 'Koreksi LPP 01', 'actions' => [['label' => 'Lihat Halaman', 'url' => './koreksi.php', 'class' => 'btn-success', 'icon' => 'fas fa-eye']]],
            ['name' => 'PO & BPB Double', 'actions' => [['label' => 'Lihat Halaman', 'url' => './po_Bpb_Double.php', 'class' => 'btn-primary', 'icon' => 'fas fa-eye']]]
          ];

          foreach ($jobs as $index => $job) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . ($index + 1) . '</td>';
            echo '<td style="">' . $job['name'] . '</td>';
            echo '<td style="text-align: center;">';
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