<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "SELECT prd_kodedivisi AS div, prmd_prdcd AS plu, prd_deskripsipanjang AS deskripsi, prd_kodetag AS tag, prd_frac AS frac, prd_unit AS unit, st_saldoakhir AS stok,
                prd_hrgjual AS hrg_normal, prmd_hrgjual AS hrg_promo, prd_hrgjual - prmd_hrgjual AS diskon
                FROM tbtr_promomd
                LEFT JOIN tbmaster_prodmast ON prmd_prdcd = prd_prdcd
                LEFT JOIN tbmaster_stock ON st_prdcd = prmd_prdcd
                WHERE st_lokasi = '01' AND date_trunc('day' , prmd_tglakhir) >=(current_date) AND prd_hrgjual - prmd_hrgjual < 0
                ORDER BY deskripsi, plu";

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
    <h1>DISKON MINUS</h1>
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
                  <th>DIV</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>TAG</th>
                  <th>FRAC</th>
                  <th>UNIT</th>
                  <th>STOK</th>
                  <th>HARGA NORMAL</th>
                  <th>HARGA PROMO</th>
                  <th>DISKON</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Loop melalui hasil query dan tampilkan data
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>";
                  echo "<td>" . $row['div'] . "</td>";
                  echo "<td>" . $row['plu'] . "</td>";
                  echo "<td class='desk-column'>" . $row['deskripsi'] . "</td>";
                  echo "<td>" . $row['tag'] . "</td>";
                  echo "<td>" . $row['frac'] . "</td>";
                  echo "<td>" . $row['unit'] . "</td>";
                  echo "<td>" . $row['stok'] . "</td>";
                  echo "<td>" . $row['hrg_normal'] . "</td>";
                  echo "<td>" . $row['hrg_promo'] . "</td>";
                  echo "<td>" . $row['diskon'] . "</td>";
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
          filename: 'Diskon_Minus_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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
