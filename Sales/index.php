<?php
// Auto Refresh
$title_caption = 'SALES TODAY';
$url = $_SERVER['REQUEST_URI'];
header("Refresh: 240; URL=$url");
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>SALES TODAY - SPI BDL 1R</h1>
    <div id="load" class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <!-- Card: Sales Today by Member -->
      <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <?php include "sales_today_by_member.php"; ?>
          </div>
        </div>
      </div>

	  <!-- Card: Sales Today by Kasir -->
      <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <?php include "sales_today_by_kasir.php"; ?>
          </div>
        </div>
      </div>

      <!-- Card: Produk Pareto -->
      <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <?php include "produk-pareto.php"; ?>
          </div>
        </div>
      </div>

      
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<script type="text/javascript">
  $(document).ready(function() {
    // Initialize DataTable
    var GridView = $('#GridView').DataTable({
      "language": {
        "search": "Cari",
        "lengthMenu": "_MENU_ Baris per halaman",
        "zeroRecords": "Data tidak ada",
        "info": "Halaman _PAGE_ dari _PAGES_ halaman",
        "infoEmpty": "Data tidak ada",
        "infoFiltered": "(Filter dari _MAX_ data)"
      },
      lengthChange: true,
      lengthMenu: [5, 10, 25, 50, 75, 100],
      paging: true,
      responsive: true,
      buttons: ['copy', 'excel', 'colvis'],
    });

    // Add buttons to DataTable
    GridView.buttons().container().appendTo('#GridView_wrapper .col-md-6:eq(0)');

    // Show DataTable
    $('#GridView').show();
    GridView.columns.adjust().draw();

    // Fade out loading indicator
    $("#load").fadeOut();
  });
</script>
