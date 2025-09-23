<?php
require_once '../layout/_top.php';


// 🔹 Panggil API Python
$url = "http://127.0.0.1:5000/lpp-vs-plano";
$response = @file_get_contents($url);

if ($response === FALSE) {
    sweetAlertError('Gagal mengambil data dari API Python!'); // cukup panggil fungsi
    exit;
}

$data = json_decode($response, true); // Ubah JSON jadi array

// Jika berhasil, bisa pakai SweetAlert sukses (opsional)
// sweetAlertSuccess('Data berhasil diambil dari API Python!');
?>




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
    max-width: 300px;
    /* Membatasi lebar maksimum kolom DESK */
  }

  /* Responsif untuk tabel */
  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>LPP VS PLANO </h1>
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
                  <th style="text-align:left">DIV</th>
                  <th style="text-align:left">DEPT</th>
                  <th style="text-align:left">KATB</th>
                  <th style="text-align:left">DISPLAY</th>
                  <th style="text-align:left">PLU</th>
                  <th style="text-align:left">DESKRIPSI</th>
                  <th style="text-align:left">UNIT</th>
                  <th style="text-align:left">FRAG</th>
                  <th style="text-align:left">TAG</th>
                  <th style="text-align:left">FLAG</th>
                  <th style="text-align:left">ACOST_PCS</th>
                  <th style="text-align:left">LPP_QTY</th>
                  <th style="text-align:left">LPP_RPH</th>
                  <th style="text-align:left">PLANO_QTY</th>
                  <th style="text-align:left">total_plano</th>
                  <th style="text-align:left">RP_PLANO</th>
                  <th style="text-align:left">SEL_QTY</th>
                  <th style="text-align:left">sel_rph</th>
                  <th style="text-align:left">Kategori</th>
                  <th style="text-align:left">Keterangan</th>
                  <th style="text-align:left">PICKING_TMI</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor = 1;
                foreach ($data as $row) {
                 echo "<tr class='s'>";
                  echo "<td>" . $nomor . "</td>";
                  echo "<td>" . htmlspecialchars($row['div']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['dept']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['katb']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['display_omi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['plu']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['frac']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['tag']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['flag']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['acost_pcs']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['lpp_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['lpp_rph']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['plano_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['total_plano']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['rp_plano']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['sel_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['sel_rph']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['picking_tmi']) . "</td>";
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
      responsive: false,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{
        targets: [],
        orderable: false
      }],
      buttons: [{
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

  $(document).ready(function() {
    // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
    var table = $('#table-1').DataTable();
    table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
    $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
  });
</script>