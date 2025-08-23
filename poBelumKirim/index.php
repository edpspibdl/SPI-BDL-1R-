<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "SELECT 
    CASE WHEN H.TPOH_RECORDID IS NULL THEN 'AKTIF' END AS STATUS,
    H.TPOH_KODESUPPLIER AS KODE_SUPPLIER,
    S.SUP_NAMASUPPLIER AS NAMA_SUPPLIER,
    H.TPOH_NOPO AS NO_PO, 
    D.TPOD_PRDCD AS PLU,
    P.PRD_DESKRIPSIPANJANG AS KETERANGAN,
    H.TPOH_TGLPO AS TANGGAL_PO,
    H.TPOH_JWPB AS J_WKT,
    H.TGL_EXP,
    TPOD_QTYPO
FROM (
    SELECT 
        TPOH_RECORDID,
        TPOH_KODESUPPLIER,
        TPOH_NOPO,
        TPOH_TGLPO,
        TPOH_JWPB,
        (TPOH_TGLPO + INTERVAL '1 day' * TPOH_JWPB) AS TGL_EXP
    FROM TBTR_PO_H
    WHERE TPOH_RECORDID IS NULL
    AND (TPOH_TGLPO + INTERVAL '1 day' * TPOH_JWPB) >= CURRENT_DATE - INTERVAL '1 day'
) AS H
JOIN TBTR_PO_D AS D ON H.TPOH_NOPO = D.TPOD_NOPO
JOIN TBMASTER_PRODMAST AS P ON D.TPOD_PRDCD = P.PRD_PRDCD
JOIN TBMASTER_SUPPLIER AS S ON H.TPOH_KODESUPPLIER = S.SUP_KODESUPPLIER";

  $stmt = $conn->query($query); // Eksekusi query dengan PDO
  // Count the number of records
  $recordCount = $stmt->rowCount(); // Get number of records

} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
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
    <h1>PO BELUM KIRIM</h1>
    <!-- Pemberitahuan -->
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th>
                    <font size="2">NO</font>
                  </th>
                  <th>
                    <font size="2">STATUS</font>
                  </th>
                  <th>
                    <font size="2">KDSUPPLIER</font>
                  </th>
                  <th>
                    <font size="2">NAMA SUPPLIER</font>
                  </th>
                  <th>
                    <font size="2">NO.PO</font>
                  </th>
                  <th>
                    <font size="2">PLU</font>
                  </th>
                  <th>
                    <font size="2">KETERANGAN</font>
                  </th>
                  <th>
                    <font size="2">TGL_PO</font>
                  </th>
                  <th>
                    <font size="2">W_EXP</font>
                  </th>
                  <th>
                    <font size="2">TGL_EXP</font>
                  </th>
                  <th>
                    <font size="2">QTY PO</font>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1; // Inisialisasi nomor baris
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<tr>';
                  echo '<td align="left"><font size="2">' . $no++ . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['status']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['kode_supplier']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['nama_supplier']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['no_po']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['plu']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['keterangan']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['tanggal_po']) . '</font></td>';
                  echo '<td align="center"><font size="2">' . number_format($row['j_wkt'], 0, '.', ',') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['tgl_exp']) . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['tpod_qtypo']) . '</font></td>';
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
</section>



<?php
require_once '../layout/_bottom.php';
?>

<!-- Add the required CSS and JS libraries -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{
        targets: [4], // Kolom "DESK" tidak dapat diurutkan
        orderable: false
      }],
      buttons: [{
          extend: 'copy',
          text: 'Copy' // Ubah teks tombol jika diperlukan
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'INTRANSIT' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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