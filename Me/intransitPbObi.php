<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "SELECT 
                  OBI_TIPEBAYAR, 
                  PK_OBIH, 
                  DIV, 
                  DEP, 
                  KATB, 
                  PLU, 
                  DESK, 
                  FRAC, 
                  UNIT,
                  CASE 
                    WHEN KET = 'KONV' THEN QTY * SAT_GRAM
                    ELSE QTY
                  END AS QTY,
                  QTY * HARGA AS TOT_RPH
                FROM (
                  SELECT 
                    OBI_TIPEBAYAR,
                    PK_OBIH, 
                    DIV, 
                    DEP, 
                    KATB, 
                    PLU, 
                    DESK, 
                    FRAC, 
                    UNIT, 
                    COALESCE(QTY, 0) AS QTY, 
                    HARGA,
                    COALESCE(KET, 'NON KONV') AS KET, 
                    SAT_GRAM
                  FROM (
                    SELECT 
                      obi_tipebayar,
                      pk_obih,
                      prd_kodedivisi AS DIV,
                      prd_kodedepartement AS DEP,
                      prd_kodekategoribarang AS KATB,
                      obi_prdcd AS PLU,
                      prd_deskripsipanjang AS DESK,
                      prd_frac AS FRAC,
                      prd_unit AS UNIT,
                      SUM(qty_obi) AS QTY,
                      obi_hargasatuan AS HARGA
                    FROM (
                      SELECT 
                        obi_tipebayar,
                        pk_obih,
                        obi_prdcd,
                        qty_obi,
                        obi_hargasatuan
                      FROM (
                        SELECT 
                          obi_notrans || '-' || obi_tgltrans AS pk_obih,
                          obi_tipebayar
                        FROM tbtr_obi_h
                        WHERE obi_recid IN ('4', '5')
                      ) AS left_table
                      JOIN (
                        SELECT 
                          obi_tgltrans,
                          obi_notrans || '-' || obi_tgltrans AS pk_obid,
                          substr(obi_prdcd, 1, 6) || '0' AS obi_prdcd,
                          SUM(obi_qtyrealisasi) AS qty_obi,
                          obi_hargasatuan
                        FROM tbtr_obi_d
                        GROUP BY obi_prdcd, obi_notrans, obi_tgltrans, obi_hargasatuan
                      ) AS right_table ON pk_obid = pk_obih
                    ) AS joined_table
                    JOIN tbmaster_prodmast ON prd_prdcd = obi_prdcd
                    GROUP BY 
                      obi_tipebayar, pk_obih, obi_prdcd, obi_hargasatuan, 
                      prd_deskripsipanjang, prd_frac, prd_unit, 
                      prd_kodedivisi, prd_kodedepartement, prd_kodekategoribarang
                  ) AS main_table
                  LEFT JOIN (
                    SELECT 
                      PLUIGR, 
                      SAT_GRAM, 
                      'KONV' AS KET 
                    FROM KONVERSI_ITEM_KLIKIGR
                  ) AS conversion_table ON PLUIGR = PLU
                  WHERE QTY <> 0
                  ORDER BY pk_obih
                ) AS final_table";

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
    <h1>INTRANSIT PB OBI</h1>
    <!-- Pemberitahuan -->
    
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="alert alert-danger mt-2" role="alert">
    <i class="fas fa-skull-crossbones"> </i> 
    <strong>Pastikan Ketika ME Intransit Harus 0 Dan Jangan Lupa Hitstok !!!</strong>
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
                  <th><font size="2">NO</font></th>
                  <th style="text-align:left">OBI_TYPEBAYAR</th>
                  <th style="text-align:left">PK_OBIH</th>
                  <th style="text-align:left">DIV</th>
                  <th style="text-align:left">DEP</th>
                  <th style="text-align:left">KATB</th>
                  <th style="text-align:left">PLU</th>
                  <th style="text-align:left">DESK</th>
                  <th style="text-align:left">FRAC</th>
                  <th style="text-align:left">UNIT</th>
                  <th style="text-align:left">QTY</th>
                  <th style="text-align:left">TOT_RPH</th>
                </tr>
              </thead>
              <tbody>
  <?php
  $no = 1; // Inisialisasi nomor baris
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td align="left"><font size="2">' . $no++ . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['obi_tipebayar'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['pk_obih'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['div'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['dep'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['katb'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['plu'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['desk'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['frac'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['unit'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['qty'] ?? '') . '</font></td>';
    echo '<td align="left"><font size="2">' . htmlspecialchars($row['tot_rph'] ?? '') . '</font></td>';
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
          filename: 'INTRANSIT' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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
