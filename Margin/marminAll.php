<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "SELECT      
            PRD_KODEDIVISI AS DIV,        
            PRD_KODEDEPARTEMENT AS DEP,        
            PRD_KODEKATEGORIBARANG AS KAT,        
            CAST(PRD_PRDCD AS NUMERIC) AS PLU,       
            PRD_DESKRIPSIPANJANG AS DESK,        
            PRD_FRAC AS FRAC,        
            PRD_UNIT AS UNIT,        
            ROUND(PRD_AVGCOST, 2) AS AVGCOST,        
            ROUND(PRD_LASTCOST, 2) AS LCOST,     
            LPP,        
            ROUND(
                CASE         
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 = 'Y' THEN ((HRGJUAL - PRD_AVGCOST * 1.11) / HRGJUAL) * 100   
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 <> 'Y' THEN ((HRGJUAL - PRD_AVGCOST) / HRGJUAL) * 100    
                    WHEN PRD_FLAGBKP1 = 'N' THEN ((HRGJUAL - PRD_AVGCOST) / HRGJUAL) * 100    
                END, 2
            ) AS MARGINACOST,        
            ROUND(
                CASE         
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 = 'Y' THEN ((HRGJUAL - PRD_LASTCOST * 1.11) / HRGJUAL) * 100  
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 <> 'Y' THEN ((HRGJUAL - PRD_LASTCOST) / HRGJUAL) * 100  
                    WHEN PRD_FLAGBKP1 = 'N' THEN ((HRGJUAL - PRD_LASTCOST) / HRGJUAL) * 100        
                END, 2
            ) AS MARGINLCOST, 
            COALESCE(QTY_PO_OUT, 0) AS PO_OUT 
        FROM (
            SELECT        
                PRD_KODECABANG,   
                prd_recordid,			
                PRD_KATEGORITOKO,     
                PRD_KODEDIVISI,        
                PRD_KODEDEPARTEMENT,        
                PRD_KODEKATEGORIBARANG,        
                PRD_PRDCD,        
                PRD_DESKRIPSIPANJANG,        
                PRD_FRAC,        
                PRD_UNIT,        
                CASE        
                    WHEN PRMD_HRGJUAL IS NULL THEN PRD_HRGJUAL        
                    ELSE PRMD_HRGJUAL        
                END AS HRGJUAL,        
                ROUND(PRD_AVGCOST, 2) AS PRD_AVGCOST,        
                ROUND(PRD_LASTCOST, 2) AS PRD_LASTCOST,        
                ST_SALDOAKHIR AS LPP,        
                PRD_FLAGBKP1,        
                PRD_FLAGBKP2        
            FROM tbmaster_prodmast        
            LEFT JOIN (
                SELECT * 
                FROM TBTR_PROMOMD 
                WHERE DATE_TRUNC('day', PRMD_TGLAKHIR) >= DATE_TRUNC('day', CURRENT_DATE)
            ) prm ON PRD_PRDCD = PRMD_PRDCD        
            LEFT JOIN (
                SELECT * 
                FROM TBMASTER_STOCK 
                WHERE ST_LOKASI = '01'
            ) st ON PRD_PRDCD = ST_PRDCD
        ) SUB1       
        LEFT JOIN (
            SELECT TPOD_PRDCD, SUM(QTY_PO_OUT) AS QTY_PO_OUT 
            FROM (
                SELECT 
                    d.tpod_prdcd,
                    SUM(d.tpod_qtypo) AS qty_po_out
                FROM tbtr_po_d d
                JOIN tbtr_po_h h ON d.tpod_nopo = h.tpoh_nopo
                WHERE (d.tpod_qtypb = '0' OR d.tpod_qtypb IS NULL)
                  AND DATE_TRUNC('day', h.tpoh_tglpo + (h.tpoh_jwpb || ' days')::interval) >= DATE_TRUNC('day', CURRENT_DATE)
                  AND h.tpoh_recordid IS NULL
                GROUP BY d.tpod_prdcd
                ORDER BY d.tpod_prdcd
            ) po_out
            GROUP BY TPOD_PRDCD
        ) po_out ON PRD_PRDCD = TPOD_PRDCD 
        WHERE PRD_PRDCD LIKE '%0' 
        AND prd_recordid IS NULL
        ORDER BY 8 DESC";

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
    <h1>MARGIN ALL</h1>
    <!-- Tombol untuk membuka modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#marginMinusModal" onclick="window.location.href='./marginMinus.php'">
    REKAP MARGIN MINUS
</button>


  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th>DIV</th>
                  <th>DEP</th>
                  <th>KAT</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>FRAC</th>
                  <th>UNIT</th>
                  <th>AVGCOST</th>
                  <th>LCOST</th>
                  <th>LPP</th>
                  <th>MARGINACOST</th>
                  <th>MARGINLCOST</th>
                  <th>PO_OUT</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Loop melalui hasil query dan tampilkan data
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>";
                  echo "<td>" . $row['div'] . "</td>";
                  echo "<td>" . $row['dep'] . "</td>";
                  echo "<td>" . $row['kat'] . "</td>";
                  echo "<td>" . $row['plu'] . "</td>";
                  echo "<td class='desk-column'>" . $row['desk'] . "</td>";
                  echo "<td>" . $row['frac'] . "</td>";
                  echo "<td>" . $row['unit'] . "</td>";
                  echo "<td>" . $row['avgcost'] . "</td>";
                  echo "<td>" . $row['lcost'] . "</td>";
                  echo "<td>" . $row['lpp'] . "</td>";
                  echo "<td>" . $row['marginacost'] . "</td>";
                  echo "<td>" . $row['marginlcost'] . "</td>";
                  echo "<td>" . $row['po_out'] . "</td>";
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
          filename: 'Margin_All_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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
