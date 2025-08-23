<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "SELECT DISTINCT
    tpod_prdcd AS PLU,
    tpm.prd_deskripsipanjang AS desk,
    tpod_qtypo AS QTY,
    tpod_nopo AS NO_PO,
    tpoh_kodesupplier AS KODE_SUPP,
    tms.sup_namasupplier AS NAMA_SUPP,
    DATE_TRUNC('DAY', tpoh_tglpo + (tpoh_jwpb || ' days')::interval) AS TGL_PO_OUT_MATI
FROM tbtr_po_d
LEFT JOIN (
    SELECT tpoh_nopo, tpoh_tglpo, tpoh_jwpb, tpoh_kodesupplier
    FROM tbtr_po_h
    WHERE tpoh_recordid IS NULL OR tpoh_recordid = 'X'
) AS po_header ON tpoh_nopo = tpod_nopo
LEFT JOIN tbmaster_supplier tms ON tms.sup_kodesupplier = tpoh_kodesupplier
LEFT JOIN tbmaster_prodmast tpm ON tpm.prd_prdcd = tpod_prdcd
WHERE DATE_TRUNC('DAY', tpoh_tglpo + (tpoh_jwpb || ' days')::interval) >= CURRENT_DATE
ORDER BY tpod_prdcd ASC;
";

  $stmt = $conn->query($query); // Eksekusi query dengan PDO

} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>

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
    <h1>PO OUT TANGGAL MATI </h1>
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
                  <th>
                    <font size="2">NO</font>
                  </th>
                  <th>
                    <font size="2">PLU</font>
                  </th>
                  <th>
                    <font size="2">DESK</font>
                  </th>
                  <th>
                    <font size="2">QTY</font>
                  </th>
                  <th>
                    <font size="2">SUPP</font>
                  </th>
                  <th>
                    <font size="2">NO PO</font>
                  </th>
                  <th>
                    <font size="2">TGL PO MATI</font>
                  </th>

                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1; // Inisialisasi $no dari 1
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<tr>'
                    . '<td align="left"><font size="2">' . $no . '</font></td>' // Gunakan $no di sini
                    . '<td align="left"><font size="2">' . ($row['plu']) . '</td>'
                    . '<td align="left"><font size="2">' . ($row['desk']) . '</td>'
                    . '<td align="left"><font size="2">' . $row['qty'] . '</td>'
                    . '<td align="left"><font size="2">' . $row['nama_supp'] . '</td>'
                    . '<td align="left"><font size="2">' . ($row['no_po']) . '</td>'
                    . '<td align="left"><font size="2">' . ($row['tgl_po_out_mati']) . '</td>'
                    . '</tr>';
                  $no++; // Increment $no setelah digunakan
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
      lengthMenu: [25, 50, 100],
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
          filename: 'PO_OUT_TGL_MATI_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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