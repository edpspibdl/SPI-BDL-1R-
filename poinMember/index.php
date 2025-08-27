<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "   SELECT *
FROM (
    SELECT KD_MEMBER,    
           NAMA,    
           HP_CUSTOMER,    
           HP_MYPOIN,    
           CASE 
               WHEN HP_CUSTOMER = HP_MYPOIN THEN 'Sama' 
               ELSE 'Tidak Sama' 
           END AS NO_HP_VS_MYPOIN,		   
           COALESCE(PEROLEHAN_POIN, 0) AS PEROLEHAN_POIN,    
           COALESCE(PENUKARAN_POIN, 0) AS PENUKARAN_POIN,    
           COALESCE(PEROLEHAN_POIN, 0) - COALESCE(PENUKARAN_POIN, 0) AS ESTIMASI,
           sub4.kunjungan_terakhir
    FROM (    
        SELECT 
            POR_KODEMEMBER AS KD_MEMBER,       
            CUS_NAMAMEMBER AS NAMA,      
            CUS_HPMEMBER AS HP_CUSTOMER,      
            POR_KODEMYPOIN AS HP_MYPOIN,      
            SUM(POR_PEROLEHANPOINT) AS PEROLEHAN_POIN,      
            PENUKARAN_POIN,
            CUS_KODEMEMBER
        FROM TBTR_PEROLEHANMYPOIN       
        LEFT JOIN TBMASTER_CUSTOMER 
               ON POR_KODEMEMBER = CUS_KODEMEMBER 
        LEFT JOIN (      
            SELECT 
                POT_KODEMEMBER AS RD_MEMBER,       
                POT_KODEMYPOIN AS RD_MYPOIN,      
                SUM(POT_PENUKARANPOINT) AS PENUKARAN_POIN      
            FROM TBTR_PENUKARANMYPOIN      
            WHERE POT_CREATE_DT >= CURRENT_DATE - INTERVAL '2 YEAR'  -- TANGGAL PENUKARAN
            GROUP BY POT_KODEMEMBER, POT_KODEMYPOIN      
        ) GF 
               ON POR_KODEMEMBER = RD_MEMBER 
              AND POR_KODEMYPOIN = RD_MYPOIN   
        WHERE POR_FLAGUPDATE = 'Y' 
          AND POR_CREATE_DT >= CURRENT_DATE - INTERVAL '2 YEAR' -- TANGGAL PEROLEHAN
        GROUP BY POR_KODEMEMBER, CUS_NAMAMEMBER, CUS_HPMEMBER, POR_KODEMYPOIN, PENUKARAN_POIN, CUS_KODEMEMBER
    ) GB 
    LEFT JOIN (
        SELECT trjd_cus_kodemember, 
               MAX(trjd_transactiondate) AS kunjungan_terakhir          
        FROM tbtr_jualdetail 
        GROUP BY trjd_cus_kodemember
    ) sub4 
           ON GB.KD_MEMBER = sub4.trjd_cus_kodemember    
) DF
WHERE ESTIMASI >= 0
ORDER BY ESTIMASI DESC";

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
    <h1>MONITORING POIN MEMBER</h1>
   
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
                  <th style="text-align:left">KD MEMBER</th>
                  <th style="text-align:left">NAMA</th>
                  <th style="text-align:left">NO HP CUS</th>
                  <th style="text-align:left">NO HP MYPOIN</th>
                  <th style="text-align:left">NO HP CUS VS MYPOIN</th>
                  <th style="text-align:left">PEROLEHAN</th>
                  <th style="text-align:left">PENUKARAN</th>
                  <th style="text-align:left">SISA POIN</th>
                  <th style="text-align:left">BELANJA TERAKHIR</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor = 1; // Pastikan variabel $nomor diinisialisasi
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr class='s'>";
                  echo "<td>" . $nomor . "</td>";
                  echo "<td>" . htmlspecialchars($row['kd_member']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['hp_customer']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['hp_mypoin']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['no_hp_vs_mypoin']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['perolehan_poin']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['penukaran_poin']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['estimasi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['kunjungan_terakhir']) . "</td>";
                  
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
