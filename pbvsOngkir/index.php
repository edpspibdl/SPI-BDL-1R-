<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid
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
    <h1>PB VS ONGKIR</h1>
  </div>
  <div class="container-fluid">
    <form role="form" method="get" action="report.php"> 
      <fieldset>
        <div class="panel panel-primary">
          <div class="panel-heading">Lap PB VS ONGKIR</div>
          <div class="panel-body fixed-panel">
            <div class="form-group">
              <div class="row">
                <div class="col-md-3">
                  <div class="panel panel-default">
                    <div class="panel-heading">Pilih Tanggal</div>
                    <div class="panel-body">
                      <div class="form-group">
                        <!-- Tanggal Mulai -->
                        <div class="input-group date form_date" data-date="" data-date-format="dd-M-yyyy" data-link-field="tanggalMulai" data-link-format="yyyymmdd">
                          <input class="form-control" type="text" value="" readonly>
                          <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                          <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <input type="hidden" id="tanggalMulai" name="tanggalMulai" value="" />
                        
                        <!-- Tanggal Selesai -->
                        <div class="input-group date form_date" data-date="" data-date-format="dd-M-yyyy" data-link-field="tanggalSelesai" data-link-format="yyyymmdd">
                          <input class="form-control" type="text" value="" readonly>
                          <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                          <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <input type="hidden" id="tanggalSelesai" name="tanggalSelesai" value="" />
                      </div>

                      <button type="reset" class="btn btn-round btn-default">Bersihkan</button>
                      <button type="submit" class="btn btn-round btn-primary">Tampilkan Laporan</button> 
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </fieldset>
    </form> 
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
    // Initialize DataTable
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
          text: 'Copy' // Teks tombol untuk Copy
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

    // Menambahkan tombol ke wrapper tabel
    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  // Fungsi untuk menampilkan alert sukses login
  function showAlertAndRedirect() {
    Swal.fire({
      title: 'Sukses Login!',
      text: 'Anda berhasil login, sedang mengalihkan...',
      icon: 'success',
      timer: 2000, // Timer untuk menampilkan alert selama 2 detik
      showConfirmButton: false
    }).then(() => {
      // Redirect setelah alert muncul
      window.location.href = 'dashboard.php'; // Ganti dengan halaman yang sesuai
    });
  }

  // Panggil fungsi jika login sukses (contoh)
  // showAlertAndRedirect();
</script>
