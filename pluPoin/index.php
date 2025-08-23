<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

try {
  $query = "SELECT
    cast(GFD_PRDCD as numeric) AS GFD_PRDCD,
    PRD_DESKRIPSIPANJANG,
    GFD_KODEPROMOSI,
    GFH_NAMAPROMOSI,
    PRD_FRAC AS FRAC,
    GFD_PCS,
    TRUNC(CASE 
        WHEN GFD_PCS <> 0 THEN PRD_FRAC::numeric / GFD_PCS
        ELSE NULL
    END) AS NILAI,
    GFH_JMLHADIAH,
    COALESCE(
        ROUND(CASE 
            WHEN GFD_PCS <> 0 THEN GFH_JMLHADIAH * (PRD_FRAC::numeric / GFD_PCS)
            ELSE NULL
        END), 
        ROUND(CASE 
            WHEN GFD_PCS <> 0 THEN PRD_FRAC::numeric / GFD_PCS
            ELSE NULL
        END)
    ) AS POINT_IN_CTN,
    GFH_FLAGSPI,
    GFH_MEKANISME,
    GFH_TGLAWAL,
    GFH_TGLAKHIR
FROM tbtr_gift_hdr
JOIN tbtr_gift_dtl ON GFD_KODEPROMOSI = GFH_KODEPROMOSI
JOIN tbmaster_prodmast ON PRD_PRDCD = GFD_PRDCD
WHERE GFH_JENISHADIAH = 'PR'
AND GFH_TGLAWAL <= CURRENT_DATE
AND GFH_TGLAKHIR >= CURRENT_DATE
AND GFH_FLAGSPI = 'Y'";

  $stmt = $conn->query($query);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>



<!-- ✅ Fade out style -->
<style>
  #loader-lottie.fade-out {
    opacity: 0;
    transition: opacity 0.5s ease;
    pointer-events: none;
  }
</style>

<!-- Styling untuk Tabel -->
<style>
  /* Styling untuk tabel */
  #table-1 {
    width: 100%;
    table-layout: auto;
    /* Menyesuaikan lebar kolom dengan isi konten */
    border-collapse: collapse;
    /* Menggabungkan border antar sel */
  }

  #table-1 th,
  #table-1 td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
    /* Membuat border untuk semua cell */
  }

  #table-1 th {
    background-color: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #333;
    /* Menambahkan pembatas tebal di bawah header */
  }

  #table-1 td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  /* Styling untuk kolom DESK */
  #table-1 .desk-column {
    word-wrap: break-word;
    /* Memastikan teks di kolom DESK membungkus */
    white-space: normal;
    /* Teks dapat membungkus pada kolom DESK */
    max-width: auto;
    /* Membatasi lebar maksimum kolom DESK */
  }

  /* Responsif untuk tabel */
  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">

  <!-- ✅ Loader Lottie -->
  <div id="loader-lottie" style="position: fixed; z-index: 9999; background: white; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
    <lottie-player
      id="main-lottie"
      src="https://lottie.host/83957713-bacd-4759-ac76-af798f0ca249/vipxVVdQsn.json"
      background="transparent"
      speed="1"
      style="width: 200px; height: 200px;"
      loop autoplay>
    </lottie-player>
  </div>
  <div class="section-header d-flex justify-content-between">
    <h1>ALL PLU POIN</h1>
    <a href="../LaporanLaporan/index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th>No</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>KD PROMO</th>
                  <th>NAMA PROMO</th>
                  <th>MINOR</th>
                  <th>JUMLAH HADIAH</th>
                  <th>POIN /CTN</th>
                  <th>MEKANISME</th>
                  <th>TGL AWAL</th>
                  <th>TGL AKHIR</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>";
                  echo "<td>" . $nomor++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfd_prdcd']) . "</td>";
                  echo "<td class='table-responsive'>" . htmlspecialchars($row['prd_deskripsipanjang']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfd_kodepromosi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfh_namapromosi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfd_pcs']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfh_jmlhadiah']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['point_in_ctn']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfh_mekanisme']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfh_tglawal']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['gfh_tglakhir']) . "</td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- ✅ Loader animation JS -->
<!-- ✅ Loader Animation Script -->
<script>
  const loader = document.getElementById('loader-lottie');
  const lottiePlayer = document.querySelector('lottie-player');
  const MIN_DISPLAY_TIME = 1000;
  const MAX_WAIT_TIME = 3000;
  const startTime = Date.now();

  function hideLoader() {
    const elapsed = Date.now() - startTime;
    const remaining = Math.max(0, MIN_DISPLAY_TIME - elapsed);

    setTimeout(() => {
      loader.classList.add('fade-out');
      setTimeout(() => loader.style.display = 'none', 500);
    }, remaining);
  }

  // ⛑ Backup: Hilangkan loader maksimal setelah MAX_WAIT_TIME
  setTimeout(hideLoader, MAX_WAIT_TIME);
</script>

<!-- ✅ DataTables Script -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: false,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{
        targets: [2], // kolom ke-3 tidak bisa diurutkan
        orderable: false
      }],
      buttons: [{
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'PLU_POIN_' + new Date().toISOString().split('T')[0],
          title: null
        }
      ],
      dom: 'Bfrtip',
      initComplete: function() {
        // ✅ Hilangkan loader setelah DataTables selesai inisialisasi
        hideLoader();
      }
    });

    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });
</script>