<?php
require_once '../layout/_top.php';
?>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>Monitoring SLP</h1>


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

<script>
          document.addEventListener('DOMContentLoaded', function() {
                    const tableBody = document.querySelector('#table-1 tbody');
                    const hasData = tableBody && tableBody.querySelectorAll('tr:not(.no-data-row)').length > 0;

                    const options = {
                              responsive: true,
                              lengthMenu: [10, 25, 50, 100],
                              columnDefs: [{
                                        targets: [7],
                                        orderable: false
                              }],
                              buttons: [{
                                                  extend: 'copy',
                                                  text: 'Copy'
                                        },
                                        {
                                                  extend: 'excel',
                                                  text: 'Excel',
                                                  filename: 'SLP_BELUM_REAL' + new Date().toISOString().split('T')[0],
                                                  title: null
                                        }
                              ],
                              dom: 'Bfrtip',
                              language: {
                                        zeroRecords: ""
                              }
                    };

                    const table = $('#table-1').DataTable(
                              hasData ?
                              options : {
                                        searching: false,
                                        paging: false,
                                        info: false,
                                        ordering: false,
                                        buttons: [],
                                        language: {
                                                  zeroRecords: "Tidak ada data yang tersedia."
                                        }
                              }
                    );

                    if (!hasData) {
                              $('#table-1').removeClass('dataTable');
                              $('#table-1 .no-data-row td').attr('colspan', $('#table-1 thead th').length);
                    }

                    if (table.buttons?.container && options.buttons.length) {
                              table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
                    }
          });

          $(document).ready(function() {
                    const table = $('#table-1').DataTable();
                    if ($.fn.DataTable.isDataTable('#table-1')) table.columns.adjust().draw();
                    $("#load").fadeOut();
          });
</script>