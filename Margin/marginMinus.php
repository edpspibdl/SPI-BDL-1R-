<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "SELECT PRD_KODEDIVISI DIV,
  PRD_PRDCD PLU,
  PRD_DESKRIPSIPANJANG DESKRIPSI,
  PRD_FRAC FRAC,
  PRD_UNIT UNIT,
  PRD_KODETAG TAG,
  ST_SALDOAKHIR LPP,
  PRD_HRGJUAL HRG,
  PRMD_HRGJUAL HRG_P,
  LCOST LCOST_PCS,
  ACOST ACOST_PCS,
  ACOST_INCLUDE A_COST_INC,
  MARGIN_A MARGIN,
  MARGIN_L MARGIN_LCOST,
  MARGIN_A_MD,
  MARGIN_L_MD
FROM
  (SELECT PRD_KODEDIVISI,
    PRD_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_FRAC,
    PRD_UNIT,
    PRD_KODETAG,
    ST_SALDOAKHIR,
    PRD_HRGJUAL,
    PRMD_HRGJUAL,
    LCOST,
    ACOST,
    ACOST_INCLUDE,
    MARGIN_A,
    MARGIN_L,
    
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRMD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
          ELSE (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
        END AS MARGIN_A_MD,
   
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRMD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
          ELSE (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
        END AS MARGIN_L_MD FROM(SELECT PRD_KODEDIVISI,
  PRD_PRDCD,
  PRD_DESKRIPSIPANJANG,
  PRD_FRAC,
  PRD_UNIT,
  PRD_KODETAG,
  ST_SALDOAKHIR,
  PRD_HRGJUAL,
  ST_LASTCOST,prd_flagbkp2,prd_flagbkp1,ST_AVGCOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN (ST_LASTCOST*PRD_FRAC)/1000
    ELSE ST_LASTCOST *PRD_FRAC
  END AS LCOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN (ST_AVGCOST*PRD_FRAC)/1000
    ELSE ST_AVGCOST *PRD_FRAC
  END AS ACOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN ((ST_AVGCOST*PRD_FRAC)/1000)*1.11
    ELSE (ST_AVGCOST *PRD_FRAC)*1.11
  END AS ACOST_INCLUDE,
   
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
          ELSE (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
        END AS MARGIN_A,
  
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
          ELSE (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
        END AS MARGIN_L
  
FROM
(SELECT SUBSTR(PRD_PRDCD,1,6)
  ||0 PLU,
  PRD_PRDCD,
  PRD_KODEDIVISI,
  PRD_KODEDEPARTEMENT,
  PRD_KODEKATEGORIBARANG,
  PRD_KODETAG,
  PRD_DESKRIPSIPANJANG,
  PRD_UNIT,
  PRD_FRAC,
  PRD_HRGJUAL,
  prd_flagbkp1,
  prd_flagbkp2
FROM tbmaster_prodmast
)prd LEFT JOIN
(SELECT ST_PRDCD,
  ST_SALDOAKHIR,
  ST_LASTCOST,
  ST_AVGCOST
FROM tbmaster_Stock
WHERE st_lokasi='01'
)stk ON prd.PLU=stk.st_prdcd 
  WHERE COALESCE (PRD_KODETAG,'0') NOT IN ('N','X','Z') AND ST_SALDOAKHIR <>0 ORDER BY PRD_PRDCD ASC)HRG_N LEFT JOIN 
 (SELECT PRMD_PRDCD AS PLUMD,
  PRMD_HRGJUAL
FROM TBTR_PROMOMD
WHERE CURRENT_DATE BETWEEN DATE(PRMD_TGLAWAL) AND DATE(PRMD_TGLAKHIR)
)PRMD ON HRG_N.PRD_PRDCD=PRMD.PLUMD)MARGINM WHERE (MARGIN_A<0 OR MARGIN_A_MD<0)";

    // eksekusi query utama
    $stmt = $conn->query($query);

    // hitung total
    $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") AS sub";
    $countStmt = $conn->query($countQuery);
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalMarmin = $countRow['total'];

    // mode count untuk dashboard
    if (isset($_GET['count'])) {
        echo $totalMarmin;
        exit;
    }

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
    <h1>REKAP MARGIN MINUS (<?php echo $totalMarmin; ?> Produk)</h1>
    <a href="../LaporanLaporan/index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
            <thead>
                        <tr class="success">
                            <th colspan="8" style="background-color:cyan"><div align="center">PRODUK</div></th>
                            <th colspan="4" style="background-color:cyan"><div align="center">KONDISI SAAT INI</div></th>
                            <th colspan="2" style="background-color:cyan"><div align="center">M HARGA NORMAL</div></th>
                            <th colspan="2" style="background-color:cyan"><div align="center">M HARGA MD</div></th>
                        </tr>
                        <tr class="active">
                            <th class="text-center" style="background-color:greenyellow">DIV</th>
                            <th class="text-center" style="background-color:greenyellow">PLU</th>
                            <th class="text-center" style="background-color:greenyellow">DESKRIPSI</th>
                            <th class="text-center" style="background-color:greenyellow">FRAC</th>
                            <th class="text-center" style="background-color:greenyellow">UNIT</th>
                            <th class="text-center" style="background-color:greenyellow">TAG IGR</th>
                            <th class="text-center" style="background-color:greenyellow">STOCK</th>
                            <th class="text-center" style="background-color:greenyellow">L COST</th>
                            <th class="text-center" style="background-color:yellow">A COST EXC</th>
                            <th class="text-center" style="background-color:yellow">A COST INC</th>
                            <th class="text-center" style="background-color:yellow">HARGA NORMAL</th>
                            <th class="text-center" style="background-color:yellow">HARGA MD</th>
                            <th class="text-center" style="background-color:blueyellow">MGN-A</th>
                            <th class="text-center" style="background-color:blueyellow">MGN-L</th>
                            <th class="text-center" style="background-color:orange">MGN-A-MD</th>
                            <th class="text-center" style="background-color:orange">MGN-L-MD</th>
                        </tr>
                    </thead>
              <tbody>
              <?php
                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td align="center">' . $row['div'] . '</td>';
                        echo '<td align="center">' . $row['plu'] . '</td>';
                        echo '<td align="left">' . $row['deskripsi'] . '</td>';
                        echo '<td align="center">' . $row['frac'] . '</td>';
                        echo '<td align="center">' . $row['unit'] . '</td>';
                        echo '<td align="center">' . $row['tag'] . '</td>';
                        echo '<td align="right">' . number_format($row['lpp'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['lcost_pcs'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['acost_pcs'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['a_cost_inc'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['hrg'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['hrg_p'], 0, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['margin'], 2, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['margin_lcost'], 2, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['margin_a_md'], 2, '.', ',') . '</td>';
                        echo '<td align="right">' . number_format($row['margin_l_md'], 2, '.', ',') . '</td>';
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
          filename: 'Margin_Minus_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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
