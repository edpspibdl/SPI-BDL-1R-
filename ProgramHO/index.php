<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<!-- ======================= -->
<!--   CSS TOMBOL GRADASI    -->
<!-- ======================= -->
<style>
  .btn-grad {
    padding: 6px 12px;
    border-radius: 6px;
    color: white !important;
    font-weight: bold;
    border: none;
    transition: 0.3s ease-in-out;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-align: center;
    width: 140px !important;  /* ✔ Semua tombol sama lebar */
  }
  .btn-grad:hover {
    transform: scale(1.07);
    opacity: 0.9;
  }

  .grad-blue { background: linear-gradient(45deg, #007bff, #00b4ff); }
  .grad-green { background: linear-gradient(45deg, #28a745, #6aff9c); }
  .grad-yellow { background: linear-gradient(45deg, #f6c23e, #ffe066); color: #000 !important; }
  .grad-orange { background: linear-gradient(45deg, #ff8c00, #ffb347); }
  .grad-red { background: linear-gradient(45deg, #dc3545, #ff6b6b); }
  .grad-purple { background: linear-gradient(45deg, #6f42c1, #b57aff); }
  .grad-info { background: linear-gradient(45deg, #17a2b8, #63d3ff); }
</style>


<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Daftar Web HeadOffice</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Menu Akses Web HeadOffice</h4>
    </div>

    <div class="card-body">
      <p>Berikut adalah daftar link penting HeadOffice</p>

      <div class="row">

        <!-- ========================= -->
        <!--     BAGIAN KIRI (ECOM)    -->
        <!-- ========================= -->
        <div class="col-md-6">
          <table class="table table-sm table-striped table-bordered" style="border: 2px solid black;">
            <thead style="border: 2px solid black;">
              <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="text-align: center;">Nama Web</th>
                <th style="width: 35%; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $webECOM = [
                ['name' => 'TSM 1', 'url' => 'http://172.20.30.3/tsm/', 'class' => 'grad-blue', 'icon' => 'fas fa-eye'],
                ['name' => 'ESS', 'url' => 'http://ess1.indomaret.lan/ESS/HomePortal/Login', 'class' => 'grad-green', 'icon' => 'fas fa-eye'],
                ['name' => 'My Point CS', 'url' => 'http://172.20.30.36/mypoincs/public/login', 'class' => 'grad-yellow', 'icon' => 'fas fa-eye'],
                ['name' => 'TTB SPI', 'url' => 'http://172.20.30.36/ttbspi/public/login', 'class' => 'grad-orange', 'icon' => 'fas fa-eye'],
                ['name' => 'Survei Harga', 'url' => 'https://survei-igr.com/index.php', 'class' => 'grad-purple', 'icon' => 'fas fa-eye'],
                ['name' => 'OTP Postgre', 'url' => 'http://172.20.30.36/select-postgre/public/select-postgre/login', 'class' => 'grad-red', 'icon' => 'fas fa-eye']
              ];

              $no = 1;
              foreach ($webECOM as $web) {
                echo "<tr>";
                echo "<td style='text-align:center;'>$no</td>";
                echo "<td>{$web['name']}</td>";
                echo "<td style='text-align:center;'>
                        <a class='btn btn-grad {$web['class']}' href='{$web['url']}' target='_blank'>
                          <i class='{$web['icon']}'></i> Kunjungi
                        </a>
                      </td>";
                echo "</tr>";
                $no++;
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- ========================= -->
        <!--    BAGIAN KANAN (LOKAL)   -->
        <!-- ========================= -->
        <div class="col-md-6">

          <table class="table table-sm table-striped table-bordered" style="border: 2px solid black;">
            <thead style="border: 2px solid black;">
              <tr>
                <th style="width: 10%; text-align: center;">No</th>
                <th style="text-align: center;">Nama Web</th>
                <th style="width: 35%; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $webLOKAL = [
                ['name' => 'Admin Panel', 'url' => 'http://172.31.135.157/login', 'class' => 'grad-info', 'icon' => 'fas fa-eye'],
                ['name' => 'Klik SPI', 'url' => 'https://spi.klikindogrosir.com/', 'class' => 'grad-blue', 'icon' => 'fas fa-eye'],
                ['name' => 'CMS Surveyor', 'url' => 'https://purchasing.ho.co.id', 'class' => 'grad-green', 'icon' => 'fas fa-eye']
              ];

              $no_local = 1;
              foreach ($webLOKAL as $web) {
                echo "<tr>";
                echo "<td style='text-align:center;'>$no_local</td>";
                echo "<td>{$web['name']}</td>";
                echo "<td style='text-align:center;'>
                        <a class='btn btn-grad {$web['class']}' href='{$web['url']}' target='_blank'>
                          <i class='{$web['icon']}'></i> Kunjungi
                        </a>
                      </td>";
                echo "</tr>";
                $no_local++;
              }
              ?>
            </tbody>
          </table>
        </div>

      </div><!-- END ROW -->
    </div>

    <div class="card-footer text-right">
      <small class="text-muted">Halaman diperbarui pada <?= date('d M Y H:i:s') ?></small>
    </div>

  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function navigateTo(url) {
    window.location.replace(url);
  }
</script>
