<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query untuk mengambil data dari tabel mahasiswa di PostgreSQL
try {
    $query = "SELECT *
FROM log_import_tax3
WHERE tgl >= CURRENT_DATE - INTERVAL '7 days'
ORDER BY tgl DESC;
";
    $stmt = $conn->prepare($query);
    $stmt->execute();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>JOB TAX SPI BDL 1R</h1>
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>NO</th>
                  <th>TANGGAL</th>
                  <th>USER ID</th>
                  <th>JAM MULAI</th>                    
                  <th>JAM SELSAI</th>
                  <th>MESSAGE</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $noUrut = 1;
                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td align="right">' . $noUrut++ . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['tgl']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['userid']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['jammulai']) . '</td>';
                    echo '<td align="left">' . htmlspecialchars($data['jamakhir']) . '</td>';
                    echo '<td align="left">' . htmlspecialchars($data['msg']) . '</td>';
                    echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>
<!-- Page Specific JS File -->
<?php
if (isset($_SESSION['info'])) :
  if ($_SESSION['info']['status'] == 'success') {
?>
    <script>
      iziToast.success({
        title: 'Sukses',
        message: `<?= htmlspecialchars($_SESSION['info']['message']) ?>`,
        position: 'topCenter',
        timeout: 5000
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      iziToast.error({
        title: 'Gagal',
        message: `<?= htmlspecialchars($_SESSION['info']['message']) ?>`,
        timeout: 5000,
        position: 'topCenter'
      });
    </script>
<?php
  }

  unset($_SESSION['info']);
  $_SESSION['info'] = null;
endif;
?>
<script src="../assets/js/page/modules-datatables.js"></script>
