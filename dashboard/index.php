<?php

ob_start(); // Mulai output buffering

// Daftar IP yang diizinkan
$allowed_ips = ['192.168.170.', '127.0.0.1'];
$user_ip = $_SERVER['REMOTE_ADDR']; // Ambil IP pengguna

// Cek apakah IP pengguna diizinkan
$allowed = false;
foreach ($allowed_ips as $ip_prefix) {
  if (strpos($user_ip, $ip_prefix) === 0) {
    $allowed = true;
    break;
  }
}

// Jika IP tidak diizinkan, arahkan ke halaman error yang benar
if (!$allowed) {
  header("Location: ../pageError/notaccess.php"); // Pastikan path ini sesuai struktur proyek
  exit;
}

ob_end_flush(); // Kirim output hanya jika tidak ada redirect

// Load layout setelah pengecekan IP
require_once '../layout/_top.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h3 class="text-center">Dashboard Pemesanan Barang</h3>
  </div>

  <link rel="stylesheet" href="./css_dashboard.css">

  <div class="container">
    <div class="card shadow-lg">
      <!-- Card Header -->
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5>Dashboard PB Overview</h5>
        <div>
          <a href="../lap_obi/" class="btn btn-light btn-sm">
            <i class="bi bi-list-task"></i> Detail Laporan
          </a>
          <button class="btn btn-light btn-sm btn-circle me-1" id="mnus" title="Kurangi">
            <i class="fas fa-minus"></i>
          </button>
          <button class="btn btn-light btn-sm btn-circle" id="plus" title="Tambah">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>

      <!-- Card Body -->
      <div class="card-body">
        <!-- Header Section -->
        <section id="header" class="dashboard-section mb-4">
          <h4 class="text-primary"><i class="bi bi-window"></i> Header</h4>
          <p>Informasi penting dari header akan ditampilkan di sini.</p>
        </section>

        <!-- Notification Section -->
        <section id="notif" class="dashboard-section mb-4">
          <h4 class="text-success"><i class="bi bi-bell"></i> Notifikasi</h4>
          <p>Notifikasi terbaru akan ditampilkan di sini.</p>
        </section>

        <!-- Recap Data Section -->
        <section id="rekap" class="dashboard-section mb-4">
          <h4 class="text-warning"><i class="bi bi-graph-up"></i> Rekap Data</h4>
          <p>Rekap data akan ditampilkan di sini.</p>
        </section>

        <!-- Timer Section -->
        <div class="text-center mb-4">
          <h5 class="text-muted">Refresh in <span id="detikan" class="text-danger">30</span>s</h5>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

  <!-- Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      let id = 5;

      const load_component = (id, url, component) => {
        $.ajax({
          type: 'POST',
          url: url,
          data: {
            id: id
          },
          cache: false,
          beforeSend: function() {
            $(component).html('<div style="text-align:center; color: #888;">Loading...</div>');
            $("#plus, #mnus").attr("disabled", true);
          },
          success: function(res) {
            if (!res.trim()) {
              $(component).html('<div style="color:red; text-align:center;">⚠️ Data kosong atau gagal dimuat.</div>');
              return;
            }

            $(component).html(res);
            if (component === '#rekap') $(component).fadeIn('fast');
            $("#plus, #mnus").attr("disabled", id <= 0);
          },
          error: function(xhr, status, error) {
            console.error("AJAX Error:", xhr.responseText);
            $(component).html('<div style="color:red; text-align:center;">❌ Gagal memuat komponen: ' + error + '</div>');

            Swal.fire({
              icon: 'error',
              title: 'Gagal Ambil Data',
              html: 'Tidak bisa mengambil data dari <code>' + url + '</code><br><small>' + error + '</small>',
            });
          }
        });
      };

      const load_all = () => {
        load_component(id, 'view_header.php', '#header');
        load_component(id, 'view_notifikasi.php', '#notif');
        load_component(id, 'view_rekap.php', '#rekap');
      };

      $("#plus").click(function() {
        id++;
        load_all();
      });

      $("#mnus").click(function() {
        if (id > 1) {
          id--;
          load_all();
        }
      });

      // Initial load
      load_all();

      // Refresh every 90 seconds
      setInterval(() => {
        load_all();
      }, 90000);

      // Countdown indicator
      let counter = 90;
      setInterval(() => {
        counter--;
        if (counter >= 0) $("#detikan").text(counter);
        if (counter === 0) counter = 90;
      }, 1000);
    });
  </script>

</section>

<?php require_once '../layout/_bottom.php'; ?>