<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

try {
          $query = "SELECT
        mtr.mtr_kodeigr,
        mtr.mtr_prdcd,
        prd.prd_deskripsipanjang,
        st.st_saldoakhir,
        mtr.mtr_qtyregulerbiru,
        mtr.mtr_qtyregulerbiruplus,
        mtr.mtr_qtyfreepass,
        mtr.mtr_qtyretailermerah,
        mtr.mtr_qtysilver,
        mtr.mtr_qtygold1,
        mtr.mtr_qtygold2,
        mtr.mtr_qtygold3,
        mtr.mtr_qtyplatinum
    FROM
        tbtabel_maxtransaksi mtr
    LEFT JOIN
        tbmaster_prodmast prd ON mtr.mtr_prdcd = prd.prd_prdcd
    LEFT JOIN
        tbmaster_stock st ON prd.prd_prdcd = st.st_prdcd
    WHERE
        mtr.mtr_qtyretailermerah NOT IN ('999','9999')
        AND prd.prd_deskripsipanjang IS NOT NULL
        AND st.st_lokasi = '01'";

          $stmt = $conn->query($query);
} catch (PDOException $e) {
          die("Error: " . $e->getMessage());
}
?>

<style>
          #table-1 {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: auto;
          }

          #table-1 th,
          #table-1 td {
                    padding: 8px 12px;
                    text-align: left;
                    border: 1px solid #ddd;
                    white-space: nowrap;
                    font-size: 12px;
          }

          #table-1 th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                    border-bottom: 2px solid #333;
          }

          .desk-column {
                    max-width: 400px;
          }
</style>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>Pembatasan Pembelian Item</h1>
                    <a href="../LaporanLaporan/index.php" class="btn btn-primary">BACK</a>
          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <table class="table table-hover table-striped" id="table-1">
                                                                      <thead>
                                                                                <tr>
                                                                                          <th>NO</th>
                                                                                          <th>KODE IGR</th>
                                                                                          <th>PRD CODE</th>
                                                                                          <th class="desk-column">DESKRIPSI PANJANG</th>
                                                                                          <th>SALDO AKHIR</th>
                                                                                          <th>QTY REGULER BIRU</th>
                                                                                          <th>QTY REGULER BIRU PLUS</th>
                                                                                          <th>QTY FREEPASS</th>
                                                                                          <th>QTY RETAILER MERAH</th>
                                                                                          <th>QTY SILVER</th>
                                                                                          <th>QTY GOLD 1</th>
                                                                                          <th>QTY GOLD 2</th>
                                                                                          <th>QTY GOLD 3</th>
                                                                                          <th>QTY PLATINUM</th>
                                                                                </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                $no = 1;
                                                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                          echo '<tr>'
                                                                                                    . '<td>' . $no++ . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_kodeigr']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_prdcd']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['prd_deskripsipanjang']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['st_saldoakhir']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtyregulerbiru']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtyregulerbiruplus']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtyfreepass']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtyretailermerah']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtysilver']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtygold1']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtygold2']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtygold3']) . '</td>'
                                                                                                    . '<td>' . htmlspecialchars($row['mtr_qtyplatinum']) . '</td>'
                                                                                                    . '</tr>';
                                                                                }
                                                                                ?>
                                                                      </tbody>
                                                            </table>
                                                  </div> <!-- end table-responsive -->
                                        </div>
                              </div>
                    </div>
          </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
          document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#table-1').DataTable({
                              responsive: false,
                              lengthMenu: [10, 25, 50, 100],
                              columnDefs: [{
                                        targets: [4], // Kolom "DESK" tidak dapat diurutkan
                                        orderable: false
                              }],
                              buttons: [{
                                                  extend: 'copy',
                                                  text: 'Copy' // Ubah teks tombol jika diperlukan
                                        },
                                        {
                                                  extend: 'excel',
                                                  text: 'Excel',
                                                  filename: 'PEMBATASAN_PEMBELIAN_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
                                                  title: null
                                        }

                              ],
                              dom: 'Bfrtip' // Posisi tombol
                    });

                    // Tambahkan tombol ke wrapper tabel
                    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
          });

          $(document).ready(function() {
                    // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
                    var table = $('#table-1').DataTable();
                    table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
                    $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
          });
</script>