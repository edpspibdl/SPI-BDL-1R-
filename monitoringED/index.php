<?php
require_once '../layout/_top.php';
?>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>Monitoring ED Storage</h1>

                    <a href="./exp_rak_storage.php" class="btn btn-danger">Cek EXP Rak Storage</a>

          </div>
          <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                    <strong>Catatan Penting!</strong><br>
                    1. Data Tanggal ED Barang diambil dari data yang diinput Cheker saat penerimaan barang.<br>
                    2. Kalau ada ED yang janggal, langsung SO dan edit tanggal ED-nya sesuaikan dengan fisik.
          </div>

          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <?php include 'tabel.php'; ?>
                                        </div>
                              </div>
                    </div>
          </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>

<!-- DataTables & Export Script -->
<script>
          document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#table-1').DataTable({
                              responsive: true,
                              lengthMenu: [10, 25, 50, 100],
                              columnDefs: [{
                                        targets: [4], // kolom DESK tidak diurutkan
                                        orderable: false
                              }],
                              buttons: [{
                                                  extend: 'copy',
                                                  text: 'Copy'
                                        },
                                        {
                                                  extend: 'excel',
                                                  text: 'Excel',
                                                  filename: 'ED_BARANG_' + new Date().toISOString().split('T')[0],
                                                  title: null
                                        }
                              ],
                              dom: 'Bfrtip'
                    });

                    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
          });

          $(document).ready(function() {
                    var table = $('#table-1').DataTable();
                    table.columns.adjust().draw();
                    $("#load").fadeOut();
          });
</script>