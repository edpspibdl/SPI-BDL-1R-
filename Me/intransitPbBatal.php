<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "SELECT
    h.obi_recid AS status,
    DATE(d.obi_tgltrans) AS tgl,
    d.obi_notrans AS notrans,
    h.obi_nopb AS pb,
    cus_namamember AS nama,
    d.obi_prdcd AS plu,
    d.obi_qtyorder AS qtyo,
    d.obi_qtyrealisasi AS qtyr,
    d.obi_qtyintransit AS int
FROM tbtr_obi_d d
LEFT JOIN tbtr_obi_h h 
    ON d.obi_tgltrans = h.obi_tgltrans 
    AND d.obi_notrans = h.obi_notrans
LEFT JOIN tbmaster_customer c 
    ON h.obi_kdmember = c.cus_kodemember
WHERE d.obi_qtyintransit <> 0
  AND d.obi_tgltrans BETWEEN '2025-01-01' AND CURRENT_DATE
  AND h.obi_recid LIKE 'B%'
ORDER BY d.obi_notrans DESC";

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
    <h1>INTRANSIT PB BATAL</h1>
    <!-- Pemberitahuan -->
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="alert alert-danger mt-2" role="alert">
    <i class="fas fa-skull-crossbones"> </i>
    <strong>Pastikan Ketika ME Intransit Harus 0</strong>
    <br>
    <i class="fas fa-info-circle"> </i>
    <strong>Total Records Intrasit: <?= $recordCount ?></strong>
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
                  <th style="text-align:left">STATUS</th>
                  <th style="text-align:left">TGL PB</th>
                  <th style="text-align:left">NO TRANS</th>
                  <th style="text-align:left">PB</th>
                  <th style="text-align:left">NAMA</th>
                  <th style="text-align:left">PLU</th>
                  <th style="text-align:left">QTY_O</th>
                  <th style="text-align:left">QTY_R</th>
                  <th style="text-align:left">INT</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1; // Inisialisasi nomor baris
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<tr>';
                  echo '<td align="left"><font size="2">' . $no++ . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['status'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['tgl'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['notrans'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['pb'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['nama'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['plu'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['qtyo'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['qtyr'] ?? '') . '</font></td>';
                  echo '<td align="left"><font size="2">' . htmlspecialchars($row['int'] ?? '') . '</font></td>';
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