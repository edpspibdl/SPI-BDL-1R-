<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Koneksi PDO

try {
          $query = "SELECT
        DIV,
        DEPT,
        KATB,
        PLU,
        DESK,
        FRAC,
        TAG,
        to_char(EXP_TERDEKAT,'YYYY-MM-DD') EXP_TERDEKAT,
        ALAMAT,
        QTY_LKS,
        ROUND((QTY_LKS / FRAC),0) QTY_LKS_CTN,
        LPP,
        ROUND((LPP / FRAC),0) LPP_CTN
    FROM
        (SELECT
            PLU PLU_EXP,
            MIN(EXP)EXP_TERDEKAT
        FROM
            (SELECT
                lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut ALAMAT,
                LKS_PRDCD PLU_LKS,
                LKS_QTY QTY_LKS,
                lks_expdate EXP
            FROM TBMASTER_LOKASI
            WHERE LKS_PRDCD IS NOT NULL) as lks
        LEFT JOIN
            (SELECT
                PRD_KODEDIVISI DIV,
                PRD_KODEDEPARTEMENT DEPT,
                PRD_KODEKATEGORIBARANG KATB,
                PRD_PRDCD PLU,
                PRD_DESKRIPSIPANJANG DESK,
                PRD_FRAC FRAC,
                PRD_KODETAG TAG
            FROM TBMASTER_PRODMAST WHERE PRD_PRDCD LIKE '%0' AND PRD_FLAGIGR='Y') as prdcd ON PLU_LKS = PLU
        GROUP BY PLU) as sq
    LEFT JOIN
        (SELECT
            *
        FROM
            (SELECT
                lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut ALAMAT,
                LKS_PRDCD PLU_LKS,
                LKS_QTY QTY_LKS,
                lks_expdate EXP
            FROM TBMASTER_LOKASI
            WHERE LKS_PRDCD IS NOT NULL) as sq1
        LEFT JOIN
            (SELECT
                PRD_KODEDIVISI DIV,
                PRD_KODEDEPARTEMENT DEPT,
                PRD_KODEKATEGORIBARANG KATB,
                PRD_PRDCD PLU,
                PRD_DESKRIPSIPANJANG DESK,
                PRD_FRAC FRAC,
                PRD_KODETAG TAG
            FROM TBMASTER_PRODMAST WHERE PRD_PRDCD LIKE '%0' AND PRD_FLAGIGR='Y') as sq2 ON PLU_LKS = PLU) as sq3 ON PLU = PLU_EXP AND EXP = EXP_TERDEKAT
    LEFT JOIN
        (SELECT
            ST_PRDCD ,
            st_saldoakhir LPP
        FROM TBMASTER_STOCK WHERE ST_LOKASI='01') as st ON PLU = ST_PRDCD
    WHERE DEPT <>'14' AND ALAMAT LIKE '%S%' AND QTY_LKS <>'0'
    ORDER BY 8,1,4 ASC";

          $stmt = $conn->query($query);
} catch (PDOException $e) {
          die("Error: " . $e->getMessage());
}
?>

<style>
          #table-1 {
                    width: 100%;
                    table-layout: auto;
                    /* Penting agar lebar kolom mengikuti konten */
                    border-collapse: collapse;
          }

          #table-1 th,
          #table-1 td {
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #ddd;
          }

          #table-1 th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                    border-bottom: 2px solid #333;
          }

          #table-1 td.desk-column {
                    white-space: nowrap !important;
                    word-break: break-word;
                    min-width: 200px;
                    /* Bisa ubah jadi nilai minimum */
          }


          .table-responsive {
                    overflow-x: auto;
          }
</style>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>Monitoring ED Storage</h1>
          </div>
          <div class="row">
                    <div class="col-md-12">
                              <div class="alert alert-info" id="rekapJumlahPLU" style="margin-bottom: 20px;">
                                        Jumlah PLU dengan ED ≤ 3 bulan: <strong id="plu3">0</strong> |
                                        ≤ 6 bulan: <strong id="plu6">0</strong> |
                                        ≤ 12 bulan: <strong id="plu12">0</strong>
                              </div>
                    </div>
          </div>

          <div class="btn-group mb-10">
                    <button class="btn btn-danger filter-exp" data-months="3">ED ≤ 3 Bulan</button>
                    <button class="btn btn-warning filter-exp" data-months="6">ED ≤ 6 Bulan</button>
                    <button class="btn btn-success filter-exp" data-months="12">ED ≤ 12 Bulan</button>
                    <button class="btn btn-info" id="resetFilter">Tampilkan Semua</button>
          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <table class="table table-hover table-striped" id="table-1">
                                                                      <thead>
                                                                                <tr>
                                                                                          <th>#</th>
                                                                                          <th>DIV</th>
                                                                                          <th>DEPT</th>
                                                                                          <th>KATB</th>
                                                                                          <th>PLU</th>
                                                                                          <th>DESK</th>
                                                                                          <th>FRAG</th>
                                                                                          <th>TAG</th>
                                                                                          <th>EXP TERDEKAT</th>
                                                                                          <th>ALAMAT</th>
                                                                                          <th>QTY PCS STORAGE</th>
                                                                                          <th>QTY CTN STORAGE</th>
                                                                                          <th>LPP</th>
                                                                                          <th>LPP CTN</th>
                                                                                </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                $nomor = 1;
                                                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                          echo "<tr>";
                                                                                          echo "<td>" . $nomor++ . "</td>"; // Auto-incrementing row number
                                                                                          echo "<td>" . htmlspecialchars($row['div']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['dept']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['katb']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['plu']) . "</td>";
                                                                                          echo "<td class='desk-column'>" . htmlspecialchars($row['desk']) . "</td>"; // Apply desk-column class
                                                                                          echo "<td>" . htmlspecialchars($row['frac']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['tag']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['exp_terdekat']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['qty_lks']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['qty_lks_ctn']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['lpp']) . "</td>";
                                                                                          echo "<td>" . htmlspecialchars($row['lpp_ctn']) . "</td>";
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
<input type="hidden" id="filterBulan" value="0">

<?php require_once '../layout/_bottom.php'; ?>

<script type="text/javascript">
          function countPLUByExp(table) {
                    var count3 = 0,
                              count6 = 0,
                              count12 = 0;
                    var now = new Date();
                    now.setHours(0, 0, 0, 0); // Normalize 'now' to the start of the day

                    table.rows().every(function() {
                              var data = this.data();
                              var edString = data[8]; // Correct index for 'EXP TERDEKAT' (0-indexed, after the '#' column)
                              if (!edString) return;

                              var edDate = new Date(edString);
                              edDate.setHours(0, 0, 0, 0); // Normalize 'edDate' to the start of the day

                              // Calculate difference in months
                              var diffMonths = (edDate.getFullYear() - now.getFullYear()) * 12 + (edDate.getMonth() - now.getMonth());

                              if (diffMonths <= 12 && diffMonths >= 0) count12++;
                              if (diffMonths <= 6 && diffMonths >= 0) count6++;
                              if (diffMonths <= 3 && diffMonths >= 0) count3++;
                    });

                    $("#plu3").text(count3);
                    $("#plu6").text(count6);
                    $("#plu12").text(count12);
          }

          $(document).ready(function() {
                    var table = $("#table-1").DataTable({ // Changed #GridView to #table-1
                              language: {
                                        search: "Cari",
                                        lengthMenu: "_MENU_ Baris per halaman",
                                        zeroRecords: "Data tidak ada",
                                        info: "Halaman _PAGE_ dari _PAGES_ halaman",
                                        infoEmpty: "Data tidak ada",
                                        infoFiltered: "(Filter dari _MAX_ data)"
                              },
                              lengthChange: true,
                              lengthMenu: [10, 25, 50, 75, 100],
                              paging: true,
                              responsive: true,
                              // 'buttons' is usually a plugin, ensure it's loaded if uncommented
                              // buttons: ["copy", "excel", "colvis"],
                              initComplete: function() {
                                        countPLUByExp(this.api());
                              }
                    });

                    // If you are using DataTables Buttons extension, uncomment this line:
                    // table.buttons().container().appendTo("#table-1_wrapper .col-md-6:eq(0)");

                    $.fn.dataTable.ext.search.push(
                              function(settings, data, dataIndex) {
                                        var filterMonth = parseInt($("#filterBulan").val(), 10);
                                        if (isNaN(filterMonth) || filterMonth === 0) {
                                                  return true;
                                        }

                                        var edString = data[8]; // Correct index for 'EXP TERDEKAT'
                                        if (!edString) return false;

                                        var edDate = new Date(edString);
                                        edDate.setHours(0, 0, 0, 0); // Normalize date

                                        var now = new Date();
                                        now.setHours(0, 0, 0, 0); // Normalize date

                                        var diffMonths = (edDate.getFullYear() - now.getFullYear()) * 12 + (edDate.getMonth() - now.getMonth());

                                        return diffMonths >= 0 && diffMonths <= filterMonth;
                              }
                    );

                    $(".filter-exp").on("click", function() {
                              var months = $(this).data("months");
                              $("#filterBulan").val(months);
                              table.draw();
                              // Recount after filtering
                              countPLUByExp(table);
                    });

                    $("#resetFilter").on("click", function() {
                              $("#filterBulan").val(0);
                              table.draw();
                              // Recount after resetting filter
                              countPLUByExp(table);
                    });

                    $("#load").fadeOut();
          });
</script>