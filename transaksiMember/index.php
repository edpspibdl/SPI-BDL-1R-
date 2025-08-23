<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Variabel utama
$kodemember = "";
$tglawal = "";
$tglakhir = "";
$totaltran = 0;

// Cek apakah form telah disubmit
if (isset($_POST['kodemember'])) {
    $kodemember = strtoupper($_POST['kodemember']);
    $tglawal = $_POST['tglawal'];
    $tglakhir = $_POST['tglakhir'];
}
?>

<!-- Add custom CSS to avoid modal overlap -->
<style>
    /* Adjust the z-index values to ensure modal visibility */
.modal-backdrop {
    z-index: 1040 !important; /* Ensure the backdrop stays behind the modal */
    opacity: 0.5 !important;  /* Optionally adjust opacity for a lighter background */
}

.modal {
    z-index: 1050 !important; /* Modal content should have a higher z-index than backdrop */
}

.modal-content {
    z-index: 1060 !important; /* Ensure the modal content is on top */
}

/* Ensure modal backdrop does not appear when modal is closed */
.modal.fade .modal-backdrop {
    opacity: 0 !important;
}

</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>History Transaksi Member</h1>
    </div>
    
    <div class="container-fluid">
        <!-- Form Section -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white text-center">
                <strong>Input Parameter</strong>
            </div>
            <div class="card-body">
                <form method="post" action="index.php" id="transaksiForm">
                    <div class="row">
                        <!-- KODE MEMBER Column -->
                        <div class="col-md-3 form-group">
                            <label>KODE MEMBER:</label>
                            <input type="text" name="kodemember" id="kodemember" class="form-control" required>
                        </div>

                        <!-- PERIODE TRANSAKSI Column -->
                        <div class="col-md-3 form-group">
                            <label>PERIODE TRANSAKSI:</label>
                            <div class="input-group">
                                <input id="tglawal" type="date" name="tglawal" class="form-control" placeholder="Tanggal Awal">
                            </div>
                        </div>

                        <!-- Tanggal Akhir Column -->
                        <div class="col-md-3 form-group">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <input id="tglakhir" type="date" name="tglakhir" class="form-control" placeholder="Tanggal Akhir">
                            </div>
                        </div>

                        <!-- Buttons Column -->
                        <div class="col-md-3 text-right">
                            <button type="reset" class="btn btn-secondary btn-sm">Bersihkan</button>
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Space Between Form and Result -->
        <div class="mb-4"></div>

        <!-- Result Section -->
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <strong>
                    <?php if ($kodemember): ?>
                        Data History Transaksi: <?= htmlspecialchars($kodemember) ?> |
                        Periode <?= date('d-M-y', strtotime($tglawal)) ?> s/d <?= date('d-M-y', strtotime($tglakhir)) ?>
                    <?php else: ?>
                        Tidak ada data untuk ditampilkan.
                    <?php endif; ?>
                </strong>
            </div>

            <div id="result" class="panel-body">
                <?php
                if ($kodemember) {
                    if ($kodemember == "") {
                        echo "KODE MEMBER belum diinput.";
                    } else {
                        $sqlmember = "
                        SELECT
                            jh_transactiondate,
                            jh_transactiontype,
                            jh_cashierid,
                            jh_cashierstation,
                            jh_transactionno,
                            jh_transactionamt
                        FROM
                            tbtr_jualheader
                        WHERE
                            jh_cus_kodemember = :kodemember
                            AND jh_transactiondate BETWEEN :tglawal AND :tglakhir
                        ORDER BY
                            jh_transactiondate";
                        
                        $stmt = $conn->prepare($sqlmember);
                        $stmt->execute([ 
                            ':kodemember' => $kodemember,
                            ':tglawal' => $tglawal,
                            ':tglakhir' => $tglakhir
                        ]);
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($rows) > 0) {
                            echo "<div class='table-responsive'>";
                            echo "<table id='GridView' class='table table-hover'>";
                            echo "<thead><tr>
                                  <th>NO</th>
                                  <th style='text-align:center'>TGL TRANSAKSI</th>
                                  <th style='text-align:center'>TYPE</th>
                                  <th style='text-align:center'>NOMOR STRUK</th>
                                  <th style='text-align:right'>NOMINAL RUPIAH</th>
                                  <th style='text-align:center'>VIEW</th>
                                  </tr></thead><tbody>";
                            
                            $no = 0;
                            $totaltran = 0;
                            foreach ($rows as $row) {
                                $no++;
                                $tgltran = $row['jh_transactiondate'];
                                $trntype = $row['jh_transactiontype'];
                                $cashierid = $row['jh_cashierid'];
                                $station = $row['jh_cashierstation'];
                                $notran = $row['jh_transactionno'];
                                $nostruk = "$cashierid.$station.$notran";
                                $nominalamt = $row['jh_transactionamt'];
                                $totaltran += $nominalamt;
                                echo "<tr>
                                      <td>$no</td>
                                      <td style='text-align:center'>" . date("d-M-y", strtotime($tgltran)) . "</td>
                                      <td style='text-align:center'>$trntype</td>
                                      <td style='text-align:center'>$nostruk</td>
                                      <td style='text-align:right'>" . number_format($nominalamt, 0, '.', ',') . "</td>
                                      <td style='text-align:center'>
                                          <button class='btn btn-xs btn-info' onclick='viewDetails(\"$kodemember\", \"$tgltran\", \"$cashierid\", \"$station\", \"$notran\")'>Detail</button>
                                      </td>
                                      </tr>";
                            }
                            echo "<tr class='warning'>
                                  <td colspan='4' style='text-align:right'>TOTAL NILAI TRANSAKSI</td>
                                  <td style='text-align:right'>" . number_format($totaltran, 0, '.', ',') . "</td>
                                  <td></td>
                                  </tr></tbody></table></div>";
                        } else {
                            echo "<div class='alert alert-warning'>Data tidak ditemukan.</div>";
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</section>
<!-- Modal for Transaction Details -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="transactionDetailModalLabel">Transaction Detail</h4>
                <!-- Button Close -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetails"></div>
            <div class="modal-footer">
                <!-- Close Button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add the required CSS and JS libraries -->

<script type="text/javascript">
      $(document).ready(function() {
      // Initialize DataTables after the table content is rendered
      var GridView = $('#GridView').DataTable({
          destroy: true,
          responsive: true,
          paging: true,
          lengthChange: true,
          lengthMenu: [5, 10, 25, 50, 75, 100],
          buttons: ['copy', 'excel', 'colvis'],
          language: {
              search: "Cari",
              lengthMenu: "_MENU_ Baris per halaman",
              zeroRecords: "Data tidak ada",
              info: "Halaman _PAGE_ dari _PAGES_ halaman",
              infoEmpty: "Data tidak ada",
              infoFiltered: "(Filter dari _MAX_ data)"
          }
      });

      // Place DataTables buttons correctly
      GridView.buttons().container().appendTo('#GridView_wrapper .col-sm-6:eq(0)');

      // Display the table
      $('#GridView').show();
      GridView.columns.adjust().draw();
      $("#load").fadeOut();
  });

  // Function to open the modal and load transaction details
  function viewDetails(kodemember, tgltran, cashierid, station, tranzno) {
      // Close the modal first if it's already open
      $('#transactionDetailModal').modal('hide');

      // Delay modal opening to ensure it is fully closed
      setTimeout(function() {
          // Clear previous content to avoid issues when reopening the modal
          $('#transactionDetails').html('');

          // Send AJAX request to load transaction details
          $.ajax({
              url: 'transaksidetail.php',
              type: 'GET',
              data: {
                  mbr: kodemember,
                  tgltran: tgltran,
                  cashierid: cashierid,
                  station: station,
                  notran: tranzno
              },
              success: function(response) {
                  // Insert the response data into the modal content
                  $('#transactionDetails').html(response);

                  // Open the modal
                  $('#transactionDetailModal').modal('show');
              },
              error: function() {
                  alert('Gagal memuat detail transaksi.');
              }
          });
      }, 100); // Delay of 100ms before opening modal to ensure smooth transition
  }

  // Ensure modal content is cleared when it's closed
  $('#transactionDetailModal').on('hidden.bs.modal', function() {
      $('#transactionDetails').html('');  // Clear the content when modal is hidden
  });


</script>

<?php
require_once '../layout/_bottom.php';
?>
