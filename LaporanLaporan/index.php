<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<style>
  .btn-fixed-width {
    width: 200px;
    /* Atur lebar sesuai kebutuhan */
    text-align: center;
    /* Pusatkan teks */
  }
</style>


<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Daftar Laporan Pagi</h1>
    <div class="d-flex ml-auto">
      <a href="../masterLokasi/" class="btn btn-primary mr-2">Master Lokasi</a>
      <a href="../cekFlag/cekFlag.php" class="btn btn-primary mr-2">Flag Jual</a>
      <a href="../poOut/poOut.php" class="btn btn-primary mr-2">PO OUT</a>
      <a href="../Margin/marminAll.php" class="btn btn-primary mr-2">Margin All + Minus</a>
      <a href="../diskonminus/diskonMinus.php" class="btn btn-primary mr-2">Diskon Minus</a>
      <a href="../lppvsPlano/lppvsPlano.php" class="btn btn-primary mr-2">LPP VS PLANO</a>
      <a href="../perHargaPagi/perHargaPagi.php" class="btn btn-primary mr-2">Per Harga Pagi</a>
      <a href="../hj/index.php" class="btn btn-primary">Upload HJ</a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Laporan Pagi & Bulanan</h4>
    </div>
    <div class="card-body">
      <p>Selamat datang di halaman daftar Laporan.</p>

      <div class="row mb-4">
        <div class="col-12 text-center">
          <button id="downloadAllReportsBtn" class="btn btn-info btn-lg btn-fixed-width">
            <i class="fas fa-file-download"></i> Download Semua Laporan Pagi
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <h6 class="mb-3">Laporan Pagi</h6>
          <table class="table table-sm table-striped table-bordered" style="border: 2px solid black;">
            <thead>
              <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="text-align: center;">Nama Laporan</th>
                <th style="text-align: center;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <?php
              // PENTING: Pindahkan definisi $reports di luar loop agar bisa diakses oleh JavaScript
              $reports = [
                ["All Item SPI BDL", "primary", ["../allitem/download_allitem.php"]],
                ["PO Out Tanggal Mati", "success", ["../poOut/download_poout.php"]],
                ["All Item IGR BDL", "info", ["../allitem/download_allitemigrbdl.php"]],
                ["Margin All", "info", ["../Margin/download_marginall.php", "../Margin/download_marmin.php"]], // Ada 2 URL di sini
                ["Diskon Minus", "danger", ["../diskonminus/download_diskonminus.php"]],
                ["Lpp Vs Plano", "success", ["../lppvsPlano/download_data.php"]],
                ["Perubahan Harga Pagi Hari", "info", ["../perHargaPagi/download_perhargapagi.php"]],
                ["Upload HJ", "warning", ["../Hj/download_hj.php"]],
              ];

              foreach ($reports as $i => [$name, $btnClass, $urls]) {
                echo "<tr><td>" . ($i + 1) . "</td><td>$name</td><td>";
                foreach ($urls as $url) {
                  // Tombol individu tetap ada dan berfungsi seperti sebelumnya
                  echo "<a href='#' class='btn btn-sm btn-$btnClass me-1 download-button' data-url='$url' title='Download $name'>
              <i class='fas fa-download'></i> Download
            </a> ";
                }
                echo "</td></tr>";
              }

              $viewMenus = [
                ["Barang Kosong", "primary", "../barkos/"],
                ["Sales Vs Ongkir IPP", "success", "../salesVSongkir/"],

              ];
              foreach ($viewMenus as $index => [$name, $btnClass, $url]) {
                $i = count($reports) + $index + 1;
                echo "<tr><td>$i</td><td>$name</td><td>";
                echo "<a href='$url' class='btn btn-sm btn-$btnClass me-1' title='$name'>
            <i class='fas fa-eye'></i> View
          </a>";
                echo "</td></tr>";
              }
              ?>
            </tbody>


          </table>
        </div>

        <div class="col-md-6">
          <h6 class="mb-3">Laporan Bulanan</h6>
          <table class="table table-sm table-striped table-bordered mb-4" style="border: 2px solid black;">
            <thead>
              <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="text-align: center;">Nama Laporan</th>
                <th style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $views = [
                ["Noodle", "../noodle/index.php", "primary"],
                ["Btb & Retur", "../btb&retur(ACC)/index.php", "success"],
                ["Pareto 3 Bulan", "../salesTigaBulan/index.php", "warning"],
                ["Rekap SPI", "../rekapSPI/index.php", "danger"]
              ];
              foreach ($views as $i => [$name, $link, $btnColor]) {
                echo "<tr>
                        <td style='text-align: center;'>" . ($i + 1) . "</td>
                        <td>$name</td>
                        <td><a href='$link' class='btn btn-sm btn-$btnColor'>
                              <i class='fas fa-eye'></i> View
                            </a></td>
                      </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card-footer text-right">
      <small class="text-muted">Halaman diperbarui pada <?= date('d M Y H:i:s') ?></small>
    </div>
  </div>
</section>


<iframe id="downloadFrame" style="display: none;"></iframe>

<?php require_once '../layout/_bottom.php'; ?>

<script src="../assets/js/page/modules-datatables.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allReportUrls = [];
    <?php
    foreach ($reports as $report) {
      foreach ($report[2] as $url) {
        echo "allReportUrls.push('" . $url . "');\n";
      }
    }
    ?>

    // Event listener tombol download individual
    document.querySelectorAll('.download-button').forEach(button => {
      button.addEventListener('click', e => {
        e.preventDefault();
        const fileUrl = button.getAttribute('data-url');
        Swal.fire({
          title: 'Processing...',
          text: 'File sedang diproses, harap tunggu...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
            document.getElementById('downloadFrame').src = fileUrl;
            setTimeout(() => {
              Swal.close();
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'File telah mulai diunduh.',
                timer: 2500,
                showConfirmButton: false
              });
            }, 2000);
          }
        });
      });
    });

    // Event listener tombol "Download Semua"
    const downloadAllBtn = document.getElementById('downloadAllReportsBtn');
    if (downloadAllBtn) {
      downloadAllBtn.addEventListener('click', () => {
        Swal.fire({
          title: 'Memproses Semua Laporan...',
          text: 'Semua file laporan sedang diproses dan akan diunduh satu per satu. Harap tunggu.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
            let currentUrlIndex = 0;

            const processNextDownload = () => {
              if (currentUrlIndex < allReportUrls.length) {
                const url = allReportUrls[currentUrlIndex];
                console.log("Downloading: " + url);
                document.getElementById('downloadFrame').src = url;

                currentUrlIndex++;
                setTimeout(processNextDownload, 1000); // 1 detik delay antar download
              } else {
                Swal.close();
                Swal.fire({
                  icon: 'success',
                  title: 'Selesai!',
                  text: 'Semua laporan telah selesai diproses dan mulai diunduh.',
                  timer: 3000,
                  showConfirmButton: false
                });
              }
            };
            processNextDownload();
          }
        });
      });
    }
  });
</script>