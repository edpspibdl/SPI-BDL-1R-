<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "select prd_kodeigr,prd_recordid, prd_prdcd, prd_deskripsipendek, prd_deskripsipanjang, prd_kategoritoko, prd_kodecabang,st_saldoakhir,prd_flagigr
from tbmaster_prodmast
LEFT JOIN TBMASTER_STOCK ON PRD_PRDCD = ST_PRDCD
where prd_recordid is null
AND PRD_PRDCD LIKE ('%0')
and prd_flagigr = 'N'
AND st_lokasi  = '01'";

    $stmt = $conn->query($query); // Eksekusi query dengan PDO

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Styling untuk Tabel -->
<style>
    /* Styling untuk tabel */
    #table-1 {
      width: 100%;
      table-layout: auto; /* Menyesuaikan lebar kolom dengan isi konten */
      border-collapse: collapse; /* Menggabungkan border antar sel */
    }

    #table-1 th, #table-1 td {
      padding: 8px;
      text-align: left;
      border: 1px solid #ddd; /* Membuat border untuk semua cell */
    }

    #table-1 th {
      background-color: #f8f9fa;
      font-weight: bold;
      border-bottom: 2px solid #333; /* Menambahkan pembatas tebal di bawah header */
    }

    #table-1 td {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* Styling untuk kolom DESK */
    #table-1 .desk-column {
      word-wrap: break-word;  /* Memastikan teks di kolom DESK membungkus */
      white-space: normal;    /* Teks dapat membungkus pada kolom DESK */
      max-width: 300px;       /* Membatasi lebar maksimum kolom DESK */
    }

    /* Responsif untuk tabel */
    .table-responsive {
      overflow-x: auto;
    }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>FLAG JUAL </h1>
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
                  <th style="text-align:left">No</th>
                  <th style="text-align:left">RECORD ID</th>
                  <th style="text-align:left">PLU</th>
                  <th style="text-align:left">DESK</th>
                  <th style="text-align:left">KD CABANG</th>
                  <th style="text-align:left">KAT TOKO</th>
                  <th style="text-align:left">LPP</th>
                  <th style="text-align:left">FLAG JUAL</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor = 1; // Pastikan variabel $nomor diinisialisasi
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr class='s'>";
                  echo "<td>" . $nomor . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_recordid']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_prdcd']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_deskripsipanjang']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_kodecabang']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_kategoritoko']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['st_saldoakhir']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['prd_flagigr']) . "</td>";
                  echo "</tr>";
                  $nomor++;
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

<?php
require_once '../layout/_bottom.php';
?>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        {
          targets: [4], // Kolom "DESK" tidak dapat diurutkan
          orderable: false
        }
      ],
      buttons: [
        {
          extend: 'copy',
          text: 'Copy' // Ubah teks tombol jika diperlukan
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'LPP_VS_PLANO_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
          title: null
        }
        
      ],
      dom: 'Bfrtip' // Posisi tombol
    });

    // Tambahkan tombol ke wrapper tabel
    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  $(document).ready(function(){
    // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
    var table = $('#table-1').DataTable();
    table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
    $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
  });
</script>
