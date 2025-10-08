<?php
require_once '../layout/_top.php';


// 🔹 Panggil API Python
$url = "http://127.0.0.1:5000/marmin";
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
    <h1>MARGIN MINUS</h1>
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
                foreach ($data as $row) {
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
