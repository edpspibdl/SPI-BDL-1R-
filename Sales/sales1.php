<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<meta http-equiv="refresh" content="60">
<title>Sales Today - SPI BDL 1R</title>
<style>
          /* Styling for the table */
          .table {
                    font-size: 13px;
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 1rem;
          }

          .table th,
          .table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
          }

          .table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid #dee2e6;
                    background-color: #f8f9fa;
                    /* Light background for headers */
          }

          .table-striped tbody tr:nth-of-type(odd) {
                    background-color: rgba(0, 0, 0, 0.05);
                    /* Zebra striping */
          }

          .table-bordered th,
          .table-bordered td {
                    border: 1px solid #dee2e6;
                    /* All borders */
          }

          .table-hover tbody tr:hover {
                    background-color: rgba(0, 0, 0, 0.075);
                    /* Hover effect */
          }

          /* Text alignment */
          .text-center {
                    text-align: center;
          }

          .text-right {
                    text-align: right;
          }

          /* Specific column width for NS Progress */
          .ns-progress-column {
                    width: 150px;
                    /* Adjust as needed */
          }

          /* Progress bar styling (assuming Bootstrap progress bar classes) */
          .progress {
                    height: 19px;
                    margin-bottom: 0;
                    overflow: hidden;
                    font-size: 0.75rem;
                    background-color: #e9ecef;
                    border-radius: 0.25rem;
          }

          .progress-bar {
                    height: 100%;
                    color: #fff;
                    text-align: center;
                    white-space: nowrap;
                    background-color: #28a745;
                    /* Success green */
                    transition: width 0.6s ease;
          }

          .progress-bar-striped {
                    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
                    background-size: 1rem 1rem;
          }

          .progress-bar-animated {
                    animation: progress-bar-stripes 1s linear infinite;
          }

          @keyframes progress-bar-stripes {
                    from {
                              background-position: 1rem 0;
                    }

                    to {
                              background-position: 0 0;
                    }
          }

          /* Custom style for status badges */
          .badge {
                    display: inline-block;
                    padding: 0.35em 0.65em;
                    font-size: 75%;
                    font-weight: 700;
                    line-height: 1;
                    text-align: center;
                    white-space: nowrap;
                    vertical-align: baseline;
                    border-radius: 0.25rem;
          }

          .badge-open {
                    background-color: #28a745;
                    /* Bootstrap success green */
                    color: #fff;
          }

          .badge-closing {
                    background-color: #dc3545;
                    /* Bootstrap danger red */
                    color: #fff;
          }

          /* Card styling (assuming Bootstrap card classes are available) */
          .card {
                    position: relative;
                    display: flex;
                    flex-direction: column;
                    min-width: 0;
                    word-wrap: break-word;
                    background-color: #fff;
                    background-clip: border-box;
                    border: 1px solid rgba(0, 0, 0, 0.125);
                    border-radius: 0.25rem;
          }

          .card-header {
                    padding: 0.75rem 1.25rem;
                    margin-bottom: 0;
                    background-color: rgba(0, 0, 0, 0.03);
                    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
          }

          .card-body {
                    flex: 1 1 auto;
                    padding: 1.25rem;
          }

          .card-footer {
                    padding: 0.75rem 1.25rem;
                    background-color: rgba(0, 0, 0, 0.03);
                    border-top: 1px solid rgba(0, 0, 0, 0.125);
          }

          /* Basic responsive table */
          .table-responsive {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
          }
</style>
<script>
          var date = new Date();
          var hari = date.getDay();
          var tanggal = date.getDate();
          var bulan = date.getMonth();
          var tahun = date.getFullYear();

          switch (hari) {
                    case 0:
                              hari = "Minggu";
                              break;
                    case 1:
                              hari = "Senin";
                              break;
                    case 2:
                              hari = "Selasa";
                              break;
                    case 3:
                              hari = "Rabu";
                              break;
                    case 4:
                              hari = "Kamis";
                              break;
                    case 5:
                              hari = "Jum'at";
                              break;
                    case 6:
                              hari = "Sabtu";
                              break;
          }
          switch (bulan) {
                    case 0:
                              bulan = "Januari";
                              break;
                    case 1:
                              bulan = "Februari";
                              break;
                    case 2:
                              bulan = "Maret";
                              break;
                    case 3:
                              bulan = "April";
                              break;
                    case 4:
                              bulan = "Mei";
                              break;
                    case 5:
                              bulan = "Juni";
                              break;
                    case 6:
                              bulan = "Juli";
                              break;
                    case 7:
                              bulan = "Agustus";
                              break;
                    case 8:
                              bulan = "September";
                              break;
                    case 9:
                              bulan = "Oktober";
                              break;
                    case 10:
                              bulan = "November";
                              break;
                    case 11:
                              bulan = "Desember";
                              break;
          }

          var tampilTanggal = hari + ", " + tanggal + " " + bulan + " " + tahun;
</script>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>Sales Today By Kasir</h1>
          </div>
          <div class="container-fluid">
                    <div class="card">
                              <div class="card-body">
                                        <h1 class="text-center bg-primary text-white py-3 mb-4" style="font-size:24px;">
                                                  PREVIEW TRANSAKSI HARI INI <script>
                                                            document.write(tampilTanggal);
                                                  </script>
                                                  <span style='color:#ffff00'> ( Live )</span>
                                        </h1>
                                        <div class="row">
                                                  <?php
                                                  try {
                                                            $sql = "SELECT 
                                        js_cashierid AS id_kasir,
                                        username AS nama_kasir,
                                        js_cashierstation AS kassa,
                                        COALESCE(js_totsalesamt, 0) AS total_transaksi,
                                        COALESCE(e_wallet, 0) AS e_wallet,
                                        COALESCE(js_totcashsalesamt, 0) AS tunai,
                                        COALESCE(js_totdebitamt, 0) AS kdebit,
                                        COALESCE((js_totcc1amt + js_totcc2amt), 0) AS kkredit,
                                        COALESCE(js_totcreditsalesamt, 0) AS kredit,
                                        COALESCE(js_freqcashdrawl, 0) AS jumlah_ns,
                                        COALESCE(js_cashdrawalamt, 0) AS ns,
                                        COALESCE(ppob, 0) AS ppob,
                                        CASE 
                                            WHEN js_cashdrawerend IS NULL AND js_cashierid NOT IN ('IDM', 'OMI', 'BKL', 'ONL') THEN 'OPEN'
                                            ELSE 'CLOSING'
                                        END AS status
                                    FROM tbtr_jualsummary 
                                    LEFT JOIN tbmaster_user ON js_cashierid = userid 
                                    LEFT JOIN (
                                        SELECT dpp_create_by, 
                                            SUBSTRING(dpp_stationkasir FROM 1 FOR 2) AS stat, 
                                            SUM(dpp_jumlahdeposit) AS ppob 
                                        FROM tbtr_deposit_mitraigr
                                        WHERE DATE(dpp_create_dt) = CURRENT_DATE
                                        GROUP BY dpp_create_by, SUBSTRING(dpp_stationkasir FROM 1 FOR 2)
                                    ) AS deposit_data 
                                    ON js_cashierid = dpp_create_by AND js_cashierstation = js_cashierstation
                                    LEFT JOIN (
                                        SELECT js_cashierid AS id, 
                                            SUM(js_isaku_amt + js_ewallet_totamount) AS e_wallet 
                                        FROM tbtr_jualsummary
                                        WHERE js_transactiondate::date = CURRENT_DATE
                                        GROUP BY js_cashierid
                                    ) AS wallet 
                                    ON wallet.id = js_cashierid
                                    WHERE DATE(js_transactiondate) = CURRENT_DATE
                                    ORDER BY 
                                        CASE 
                                            WHEN js_cashdrawerend IS NULL AND js_cashierid NOT IN ('IDM', 'OMI', 'BKL', 'ONL') THEN 'OPEN'
                                            ELSE 'CLOSING' 
                                        END DESC";
                                                            $stmt = $conn->prepare($sql);
                                                            $stmt->execute();
                                                  } catch (PDOException $e) {
                                                            die("Error fetching data: " . $e->getMessage());
                                                  }
                                                  ?>
                                                  <div class="col-md-12">
                                                            <div class="card">
                                                                      <div class="card-header">
                                                                                <h5 class="card-title">Data Transaksi</h5>
                                                                      </div>
                                                                      <div class="card-body">
                                                                                <div class="table-responsive">
                                                                                          <table class="table table-bordered table-striped table-hover compact" style="width:100%">
                                                                                                    <thead>
                                                                                                              <tr>
                                                                                                                        <th>Kassa</th>
                                                                                                                        <th>ID</th>
                                                                                                                        <th>Kasir</th>
                                                                                                                        <th class="text-center">TOTAL TRANSAKSI</th>
                                                                                                                        <th class="text-center">E-WALLET</th>
                                                                                                                        <th class="text-center">TUNAI</th>
                                                                                                                        <th class="text-center">K.DEBIT</th>
                                                                                                                        <th class="text-center">K.KREDIT</th>
                                                                                                                        <th class="text-center">KREDIT</th>
                                                                                                                        <th class="text-center ns-progress-column">NS PROGRESS</th>
                                                                                                                        <th class="text-center">JUMLAH NS</th>
                                                                                                                        <th class="text-center">NOMINAL NS</th>
                                                                                                                        <th class="text-center">PPOB</th>
                                                                                                                        <th class="text-center">STATUS</th>
                                                                                                              </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                              <?php
                                                                                                              $TOTAL_TRANSAKSI = 0;
                                                                                                              $E_WALLET = 0;
                                                                                                              $TUNAI = 0;
                                                                                                              $KDEBIT = 0;
                                                                                                              $KKREDIT = 0;
                                                                                                              $KREDIT = 0;
                                                                                                              $NS = 0;
                                                                                                              $PPOB = 0;

                                                                                                              if ($stmt->rowCount() > 0) {
                                                                                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                                                                  $TOTAL_TRANSAKSI += $row['total_transaksi'];
                                                                                                                                  $E_WALLET        += $row['e_wallet'];
                                                                                                                                  $TUNAI           += $row['tunai'];
                                                                                                                                  $KDEBIT          += $row['kdebit'];
                                                                                                                                  $KKREDIT         += $row['kkredit'];
                                                                                                                                  $KREDIT          += $row['kredit'];
                                                                                                                                  $NS              += $row['ns'];
                                                                                                                                  $PPOB            += $row['ppob'];

                                                                                                                                  $row_class = ($row['status'] == "CLOSING") ? "table-secondary" : "";
                                                                                                                                  echo "<tr class='" . $row_class . "'>";
                                                                                                              ?>
                                                                                                                                  <td><?= htmlspecialchars($row['kassa']) ?></td>
                                                                                                                                  <td><?= htmlspecialchars($row['id_kasir']) ?></td>
                                                                                                                                  <td><?= htmlspecialchars($row['nama_kasir']) ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['total_transaksi'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['e_wallet'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['tunai'], 0, ',', '.') ?></td>
                                                                                                                                  <?php
                                                                                                                                  // **PERBAIKAN DI SINI:**
                                                                                                                                  $target_ns_progress = 10000000; // Define the target value for NS Progress (10 juta)
                                                                                                                                  $current_tunai = floatval($row['tunai']);

                                                                                                                                  // Calculate percentage
                                                                                                                                  if ($target_ns_progress > 0) {
                                                                                                                                            $persen = ($current_tunai / $target_ns_progress) * 100;
                                                                                                                                  } else {
                                                                                                                                            $persen = 0; // Avoid division by zero if target is 0
                                                                                                                                  }

                                                                                                                                  // Ensure percentage does not exceed 100%
                                                                                                                                  $persen = min(100, max(0, $persen));
                                                                                                                                  ?>
                                                                                                                                  <td class="text-right"><?= number_format($row['kdebit'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['kkredit'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['kredit'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="ns-progress-column">
                                                                                                                                            <div class="progress">
                                                                                                                                                      <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?= round($persen) ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= round($persen) ?>%">
                                                                                                                                                                <?= round($persen) ?>%
                                                                                                                                                      </div>
                                                                                                                                            </div>
                                                                                                                                  </td>
                                                                                                                                  <td class="text-center"><?= number_format($row['jumlah_ns'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['ns'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-right"><?= number_format($row['ppob'], 0, ',', '.') ?></td>
                                                                                                                                  <td class="text-center">
                                                                                                                                            <?php
                                                                                                                                            $ket = htmlspecialchars($row['status']);
                                                                                                                                            if ($ket == 'OPEN') {
                                                                                                                                                      echo "<span class='badge badge-open'>" . $ket . "</span>";
                                                                                                                                            } else {
                                                                                                                                                      echo "<span class='badge badge-closing'>" . $ket . "</span>";
                                                                                                                                            }
                                                                                                                                            ?>
                                                                                                                                  </td>
                                                                                                                                  </tr>
                                                                                                              <?php
                                                                                                                        }
                                                                                                              } else {
                                                                                                                        echo "<tr><td colspan='14' class='text-center'>Tidak ada data transaksi hari ini.</td></tr>";
                                                                                                              }
                                                                                                              ?>
                                                                                                    </tbody>
                                                                                                    <tfoot>
                                                                                                              <tr>
                                                                                                                        <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($TOTAL_TRANSAKSI, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($E_WALLET, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($TUNAI, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($KDEBIT, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($KKREDIT, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($KREDIT, 0, ',', '.') ?></strong></td>
                                                                                                                        <td colspan="2" class="text-center"></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($NS, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-right"><strong><?= number_format($PPOB, 0, ',', '.') ?></strong></td>
                                                                                                                        <td class="text-center"></td>
                                                                                                              </tr>
                                                                                                    </tfoot>
                                                                                          </table>
                                                                                </div>
                                                                      </div>
                                                                      <div class="card-footer">
                                                                                <div class="col-md-12">
                                                                                          <marquee scrollamount="5" behavior="scroll" direction="left">
                                                                                                    <strong style="color:green;">Progress Transaksi SPIBDL1R :</strong>
                                                                                                    <span style="color:red; font-size:20px;">
                                                                                                              <?= number_format($TOTAL_TRANSAKSI, 0, ',', '.') ?>
                                                                                                    </span>
                                                                                          </marquee>
                                                                                </div>
                                                                      </div>
                                                            </div>
                                                  </div>
                                        </div>
                              </div>
                    </div>
          </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
          $(document).ready(function() {
                    // Any other non-DataTables specific JavaScript can go here.
          });
</script>