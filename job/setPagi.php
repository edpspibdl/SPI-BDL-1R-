<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query pertama untuk mengambil data dari tabel tbreport_setting_pagi_hari
try {
    $query1 = "SELECT * FROM tbreport_setting_pagi_hari ORDER BY tanggal DESC LIMIT 7";
    $stmt1 = $conn->prepare($query1);
    $stmt1->execute();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Query kedua untuk mengambil data dari tabel job_log_all
try {
    $query2 = "SELECT * FROM job_log_all WHERE job_name LIKE '%SETTING PAGI HARI%' ORDER BY job_start DESC LIMIT 7";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>JOB SETTING PAGI HARI SPI BDL 1R</h1>
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <!-- Information Card -->
      <div class="card mb-4">
      <div class="card-body">
  <h5 class="card-title">Informasi Penting</h5>
  <p class="card-text">Job ini wajib dijalankan setiap hari. Tabel terkait untuk pengaturan ini adalah:</p>
  <ul>
    <li><strong>tbreport_setting_pagi_hari</strong></li>
    <li><strong>job_log_all</strong></li>
  </ul>
</div>

      </div>

      <!-- Data Tables Side by Side -->
      <div class="d-flex justify-content-between">
        <!-- Data Table 1 (tbreport_setting_pagi_hari) -->
        <div class="card mb-4" style="width: 30%;">
          <div class="card-body">
            <h5 class="card-title">Tabel 1 tbreport_setting_pagi_hari</h5>
            <div class="table-responsive">
              <table class="table table-hover table-striped" id="table-1" style="table-layout: auto;">
                <thead>
                  <tr class="text-center">
                    <th>NO</th>
                    <th>TANGGAL</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $noUrut = 1;
                  while ($data = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>';
                      echo '<td align="right">' . $noUrut++ . '</td>';
                      echo '<td align="center">' . htmlspecialchars($data['tanggal']) . '</td>';
                      echo '<td align="center">' . htmlspecialchars($data['status']) . '</td>';
                      echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Data Table 2 (job_log_all) -->
        <div class="card mb-4" style="width: 68%;">
          <div class="card-body">
            <h5 class="card-title">Tabel 2 job_log_all</h5>
            <div class="table-responsive">
              <table class="table table-hover table-striped" id="table-2" style="table-layout: auto;">
                <thead>
                  <tr class="text-center">
                    <th>NO</th>
                    <th>JOB START</th>
                    <th>JOB NAME</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $noUrut = 1;
                  while ($data = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>';
                      echo '<td align="right">' . $noUrut++ . '</td>';
                      echo '<td align="center">' . htmlspecialchars($data['job_start']) . '</td>';
                      echo '<td align="center">' . htmlspecialchars($data['job_name']) . '</td>';
                      echo '<td align="center">' . htmlspecialchars($data['job_message']) . '</td>';
                      echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>
