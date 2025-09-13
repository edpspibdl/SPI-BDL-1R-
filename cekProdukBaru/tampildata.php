<!DOCTYPE html>
<html>
<head>
    <title>FORMULIR CEK PRODUK BARU</title>
    <!-- Styling untuk Tabel -->
    <style>
      #table-1 {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
      }
      #table-1 th,
      #table-1 td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
      }
      #table-1 th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
      }
      #table-1 td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      #table-1 .desk-column {
        word-wrap: break-word;
        white-space: normal;
        max-width: 300px;
      }
      .table-responsive {
        overflow-x: auto;
      }
    </style>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" 
          href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
</head>
<body>
<?php
require_once '../helper/connection.php';
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['plu'])) {
    $plu = trim($_POST['plu']);
} elseif (isset($_GET['plu'])) {
    $plu = trim($_GET['plu']);
} else {
    echo "<h2>Jangan lupa isi PLU-nya... :)</h2>";
    exit;
}

$pluex = explode(",", $plu);
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>FORMULIR CEK PRODUK BARU</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                    <th>TGL Usulan</th>
                    <th>PLU</th>
                    <th>DESKRIPSI</th>
                    <th>PENERIMAAN</th>
                    <th>RECORDID</th>
                    <th>MASTER IAS</th>
                    <th>MASTER LOKASI</th>
                    <th>MASTER SPI</th>
                    <th>FLAG JUAL</th>
                    <th>FRAC</th>
                    <th>KODE CABANG</th>
                    <th>KATEGORI TOKO</th>
                    <th>PIHAK TERKAIT</th>
                    <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
<?php
foreach ($pluex as $plu0) {
    $plu0 = "'" . sprintf("%07s", $plu0) . "'";

    $sql = "
        SELECT DISTINCT
    p.prd_create_dt,
    p.prd_prdcd AS plu,
    p.prd_deskripsipendek AS deskripsi,
        CASE
        WHEN td.mstd_prdcd IS NULL THEN 'BELUM PENERIMAAN'
        ELSE 'SUDAH PENERIMAAN'
    END AS keterangan_penerimaan,
    p.prd_recordid,
    CASE WHEN p.prd_prdcd IS NOT NULL THEN 'Y' ELSE '' END AS master_ias,
    CASE WHEN l.lks_prdcd IS NOT NULL THEN 'Y' ELSE '' END AS master_lokasi,
    CASE WHEN p.prd_prdcd IS NOT NULL THEN 'Y' ELSE '' END AS master_spi,
    CASE WHEN p.prd_flagigr = 'Y' THEN 'Y' ELSE '' END AS flag_jual,
    p.prd_frac,
    p.prd_kodecabang,
    p.prd_kategoritoko,
    CASE
        WHEN l.lks_prdcd IS NULL THEN 'MOD'
        WHEN (p.prd_flagigr IS NULL OR p.prd_flagigr <> 'Y') THEN 'MD CABANG'
        WHEN (p.prd_kodecabang IS NULL AND p.prd_kategoritoko IS NULL) THEN 'MD CABANG'
        ELSE '-'
    END AS pihak_terkait,
    CASE
        WHEN p.prd_prdcd IS NOT NULL
         AND p.prd_deskripsipendek IS NOT NULL
         AND p.prd_recordid IS NULL
         AND l.lks_prdcd IS NOT NULL
         AND p.prd_flagigr = 'Y'
         AND p.prd_frac IS NOT NULL
         AND (p.prd_kodecabang IS NOT NULL OR p.prd_kategoritoko IS NOT NULL)
        THEN 'DONE'
        ELSE 'ON PROGRESS'
    END AS status,
    CASE
        WHEN td.mstd_prdcd IS NULL THEN 'BELUM PENERIMAAN'
        ELSE 'SUDAH PENERIMAAN'
    END AS keterangan_penerimaan
FROM tbmaster_prodmast p
LEFT JOIN tbmaster_lokasi l 
       ON l.lks_prdcd = p.prd_prdcd
LEFT JOIN tbtr_mstran_d td 
       ON td.mstd_prdcd = p.prd_prdcd
      AND td.mstd_typetrn = 'B'
WHERE p.prd_prdcd IN ($plu0)
ORDER BY p.prd_prdcd;
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>".$row['prd_create_dt']."</td>";
            echo "<td>".$row['plu']."</td>";
            echo "<td>".$row['deskripsi']."</td>";
            echo "<td>".$row['keterangan_penerimaan']."</td>";
            echo "<td>".$row['prd_recordid']."</td>";
            echo "<td>".$row['master_ias']."</td>";
            echo "<td>".$row['master_lokasi']."</td>";
            echo "<td>".$row['master_spi']."</td>";
            echo "<td>".$row['flag_jual']."</td>";
            echo "<td>".$row['prd_frac']."</td>";
            echo "<td>".$row['prd_kodecabang']."</td>";
            echo "<td>".$row['prd_kategoritoko']."</td>";
            echo "<td>".$row['pihak_terkait']."</td>";
            echo "<td>".$row['status']."</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='12'>Query gagal: " . implode(":", $stmt->errorInfo()) . "</td></tr>";
    }
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

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons + JSZip (untuk Excel) -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {
    $('#table-1').DataTable({
        pageLength: 10,
        dom: 'Bfrtip',  // tombol export muncul di atas tabel
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Formulir_Cek_Produk_Baru',
                text: '📥 Export Excel'
            }
        ]
    });
});
</script>
</body>
</html>
