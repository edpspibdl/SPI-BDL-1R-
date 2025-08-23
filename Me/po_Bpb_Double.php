<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// --- Query untuk Tabel 1: INTRANSIT PB BATAL ---
try {
          $query1 = "select * from (
    select mstd_nopo, count(mstd_nodoc) double_bpb from (
        select mstd_nodoc, mstd_nopo from tbtr_mstran_d where mstd_typetrn='B' and mstd_recordid is null and mstd_tgldoc >= TO_DATE('2025-07-01', 'YYYY-MM-DD') and mstd_nopo is not null group by mstd_nodoc, mstd_nopo 
    ) aa
group by mstd_nopo) bb where double_bpb>1";

          $stmt1 = $conn->query($query1); // Eksekusi query dengan PDO
          $recordCount1 = $stmt1->rowCount(); // Get number of records

} catch (PDOException $e) {
          die("Error for Table 1: " . $e->getMessage());
}

try {
          $query2 = "select * from (
    select mstd_nodoc, count(PO) double_po from (
        select mstd_nodoc, coalesce(mstd_nopo,'HAHA') PO from tbtr_mstran_d where mstd_typetrn='B' and mstd_recordid is null and mstd_tgldoc >= TO_DATE('2025-07-01', 'YYYY-MM-DD') group by mstd_nodoc, mstd_nopo
    ) aa group by mstd_nodoc
) bb where double_po>1";

          $stmt2 = $conn->query($query2); // Eksekusi query dengan PDO
          $recordCount2 = $stmt2->rowCount(); // Get number of records

} catch (PDOException $e) {
          die("Error for Table 2: " . $e->getMessage());
}
?>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>BPB Double dalam 1 PO</h1>
                    <a href="./index.php" class="btn btn-primary">BACK</a>
          </div>
          <div class="alert alert-danger mt-2" role="alert">
                    <i class="fas fa-skull-crossbones"> </i>
                    <strong>Pastikan Ketika ME Harus 0</strong>

          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <table class="table table-hover table-striped" id="table-1">
                                                                      <thead>
                                                                                <tr>
                                                                                          <th>
                                                                                                    <font size="2">NO</font>
                                                                                          </th>
                                                                                          <th style="text-align:left">NODOC</th>
                                                                                          <th style="text-align:left">DOUBLE BPB</th>

                                                                                </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                $no1 = 1; // Inisialisasi nomor baris untuk Tabel 1
                                                                                while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                                                                          echo '<tr>';
                                                                                          echo '<td align="left"><font size="2">' . $no1++ . '</font></td>';
                                                                                          echo '<td align="left"><font size="2">' . htmlspecialchars($row1['mstd_nopo'] ?? '') . '</font></td>';
                                                                                          echo '<td align="left"><font size="2">' . htmlspecialchars($row1['double_bpb'] ?? '') . '</font></td>';
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


          <div class="section-header d-flex justify-content-between mt-5">
                    <h1>PO Double dalam 1 BPB</h1>
          </div>
          <div class="alert alert-info mt-2" role="alert">
                    <i class="fas fa-info-circle"> </i>
                    <strong>PO Double dalam 1 BPB</strong>
          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <table class="table table-hover table-striped" id="table-2">
                                                                      <thead>
                                                                                <tr>
                                                                                          <th>
                                                                                                    <font size="2">NO</font>
                                                                                          </th>
                                                                                          <th style="text-align:left">NO DOC</th>
                                                                                          <th style="text-align:left">DOUBLE PO</th
                                                                                                    </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                $no2 = 1; // Inisialisasi nomor baris untuk Tabel 2
                                                                                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                                                                          echo '<tr>';
                                                                                          echo '<td align="left"><font size="2">' . $no2++ . '</font></td>';
                                                                                          echo '<td align="left"><font size="2">' . htmlspecialchars($row2['mstd_nodoc'] ?? '') . '</font></td>';
                                                                                          echo '<td align="left"><font size="2">' . htmlspecialchars($row2['double_po'] ?? '') . '</font></td>';
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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
          document.addEventListener('DOMContentLoaded', function() {
                    // Inisialisasi DataTables untuk Tabel 1
                    const table1 = $('#table-1').DataTable({
                              responsive: true,
                              lengthMenu: [10, 25, 50, 100],
                              columnDefs: [{
                                        targets: [5], // Kolom "NAMA" tidak dapat diurutkan (indeks 5 karena ada NO, STATUS, TGL, NOTRANS, PB)
                                        orderable: false
                              }],
                              buttons: [{
                                        extend: 'copy',
                                        text: 'Copy'
                              }, {
                                        extend: 'excel',
                                        text: 'Excel',
                                        filename: 'INTRANSIT_BATAL_' + new Date().toISOString().split('T')[0],
                                        title: null
                              }],
                              dom: 'Bfrtip'
                    });
                    table1.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');

                    // Inisialisasi DataTables untuk Tabel 2
                    const table2 = $('#table-2').DataTable({
                              responsive: true,
                              lengthMenu: [10, 25, 50, 100],
                              columnDefs: [{
                                        targets: [5], // Kolom "NAMA" tidak dapat diurutkan
                                        orderable: false
                              }],
                              buttons: [{
                                        extend: 'copy',
                                        text: 'Copy'
                              }, {
                                        extend: 'excel',
                                        text: 'Excel',
                                        filename: 'INTRANSIT_MASUK_' + new Date().toISOString().split('T')[0],
                                        title: null
                              }],
                              dom: 'Bfrtip'
                    });
                    table2.buttons().container().appendTo('#table-2_wrapper .col-md-6:eq(0)');

                    // Sesuaikan kolom dan sembunyikan spinner setelah kedua tabel diinisialisasi
                    table1.columns.adjust().draw();
                    table2.columns.adjust().draw();
                    $("#load").fadeOut();
          });
</script>