<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Koneksi PDO

try {
          $query = "SELECT DISTINCT
  CASE
    WHEN OBI_RECID = '5' THEN 'SIAP STRUK'
  END AS STATUS,
  SUBSTR(OBI_NOPB, 1, 7) AS NO_PB,
  TO_CHAR(OBI_CREATEDT, 'DD/MM/YY HH24:MI:SS') AS TGL_PB,
  TO_CHAR(OBI_TGLORDER, 'DD/MM/YY HH24:MI:SS') AS TGL_ORDER,
  OBI_KDMEMBER AS KODE_MEMBER,
  CUS_NAMAMEMBER AS NAMA_MEMBER,
  OBI_REALORDER + OBI_REALPPN - OBI_REALDISKON AS REALORDER,
  TO_CHAR(DSP_CREATE_DT, 'DD/MM/YY HH24:MI:SS') AS TGL_DSP,
  OBI_TIPEBAYAR AS TIPE_BAYAR,
  AWI_PINCODE AS PIN,
  STATUS_PIN,
  TO_CHAR(STI_CREATE_DT, 'DD/MM/YY HH24:MI:SS') AS TGL_INPUT_PIN,
  AWI_NOAWB,
  STI_NOSERAHTERIMA,
  STI_DRIVERNAME
FROM (
  SELECT
    OBI_RECID,
    OBI_NOPB,
    OBI_CREATEDT,
    OBI_KDMEMBER,
    CUS_NAMAMEMBER,
    OBI_REALORDER,
    OBI_REALPPN,
    OBI_REALDISKON,
    DSP_CREATE_DT,
    OBI_TGLORDER,
    OBI_TIPEBAYAR,
    AWI_PINCODE,
    CASE
      WHEN STI_NOSERAHTERIMA IS NOT NULL THEN 'SUDAH INPUT PIN A'
      WHEN RESPONSE LIKE '%Gagal, Kode AWB belum terbentuk%' THEN 'Gagal, Kode AWB Belum Terbentuk'
      WHEN RESPONSE LIKE '%Connection Failed%' THEN 'Connection Failed'
      WHEN RESPONSE LIKE '%Server bermasalah%' THEN 'Server Bermasalah'
      WHEN RESPONSE LIKE '%Input CODValue (DeliveryInfo)%' THEN 'Input CODValue (INFO IPP)'
      WHEN RESPONSE LIKE '%Data penerima harus diisi%' THEN 'Data Penerima Harus Diisi (INFO IPP)'
      ELSE 'BELUM INPUT PIN A'
    END AS STATUS_PIN,
    STI_CREATE_DT,
    AWI_NOAWB,
    STI_NOSERAHTERIMA,
    STI_DRIVERNAME,
    ROW_NUMBER() OVER (PARTITION BY RESPONSE ORDER BY CREATE_DT DESC) AS rnk
  FROM
    TBTR_OBI_H
    LEFT JOIN TBMASTER_CUSTOMER ON OBI_KDMEMBER = CUS_KODEMEMBER
    LEFT JOIN TBTR_AWB_IPP ON OBI_NOPB = AWI_NOPB
    LEFT JOIN TBTR_SERAHTERIMA_IPP ON STI_NOORDER = AWI_NOORDER
    LEFT JOIN LOG_CREATEAWB ON OBI_NOPB = NOPB
    LEFT JOIN TBTR_DSP_SPI ON OBI_NOPB = DSP_NOPB
 WHERE
    obi_kdekspedisi <> 'Ambil di Stock Point Indogrosir'
  and OBI_RECID = '5'
  AND OBI_TGLORDER >= CURRENT_DATE - INTERVAL '1 day' + INTERVAL '16 hours 45 minutes'
  AND OBI_TGLORDER < CURRENT_DATE + INTERVAL '16 hours 45 minutes'
) AS Z
WHERE rnk = 1
ORDER BY STATUS_PIN, AWI_NOAWB, TGL_ORDER
";

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
                    <h1>MONITORING IPP - PB DI DIBAWAH JAM 16:45</h1>
          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <!-- TOTAL DATA DITAMPILKAN DI SINI -->


                                                            <table class="table table-hover table-striped" id="table-1">
                                                                      <thead>
                                                                                <tr class="info">
                                                                                          <th class="text-center"><strong>STATUS</strong></th>
                                                                                          <th class="text-center"><strong>DELIMAN</strong></th>
                                                                                          <th class="text-center"><strong>NO PB</strong></th>
                                                                                          <th class="text-center"><strong>TGL PB</strong></th>
                                                                                          <th class="text-center"><strong>KODE MEMBER</strong></th>
                                                                                          <th class="text-center"><strong>NAMA MEMBER</strong></th>
                                                                                          <th class="text-center"><strong>REAL ORDER</strong></th>
                                                                                          <th class="text-center"><strong>TGL DSP</strong></th>
                                                                                          <th class="text-center"><strong>TIPE BAYAR</strong></th>
                                                                                          <th class="text-center"><strong>PIN</strong></th>
                                                                                          <th class="text-center"><strong>STATUS PIN</strong></th>
                                                                                          <th class="text-center"><strong>TGL INPUT PIN</strong></th>
                                                                                          <th class="text-center"><strong>NO AWB</strong></th>
                                                                                          <th class="text-center"><strong>NO SERTIM</strong></th>
                                                                                </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                          echo '<tr>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['status']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['sti_drivername']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['no_pb']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_pb']) . '</td>';
                                                                                          echo '<td class="text-center">' . htmlspecialchars($row['kode_member']) . '</td>';
                                                                                          echo '<td class="text-left" style="white-space: nowrap;">' . htmlspecialchars($row['nama_member']) . '</td>';
                                                                                          echo '<td class="text-end">' . number_format($row['realorder'], 0, '.', ',') . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_dsp']) . '</td>';
                                                                                          echo '<td class="text-center">' . htmlspecialchars($row['tipe_bayar']) . '</td>';
                                                                                          echo '<td class="text-center">' . htmlspecialchars($row['pin']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['status_pin']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['tgl_input_pin']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['awi_noawb']) . '</td>';
                                                                                          echo '<td class="text-center" style="white-space: nowrap;">' . htmlspecialchars($row['sti_noserahterima']) . '</td>';
                                                                                          echo '</tr>';
                                                                                }
                                                                                ?>
                                                                      </tbody>

                                                            </table>
                                                            <div class="mb-3">
                                                                      <strong id="total-data" class="text-muted">Menampilkan ...</strong>
                                                            </div>

                                                  </div> <!-- .table-responsive -->
                                        </div> <!-- .card-body -->
                              </div> <!-- .card -->
                    </div> <!-- .col-12 -->
          </div> <!-- .row -->
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- DataTables and Custom Script -->
<script type="text/javascript">
          $(document).ready(function() {
                    var table = $("#table-1").DataTable({
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
                              responsive: false,
                              info: false, // sembunyikan info bawaan DataTables di bawah tabel
                              initComplete: function() {
                                        updateTotalData(this.api());
                              }
                    });

                    table.on('draw', function() {
                              updateTotalData(table);
                    });

                    function updateTotalData(api) {
                              const pageInfo = api.page.info();
                              const text = "Menampilkan " + (pageInfo.start + 1) + " sampai " + pageInfo.end + " dari " + pageInfo.recordsTotal + " data";
                              $("#total-data").html(text);
                    }
          });
</script>