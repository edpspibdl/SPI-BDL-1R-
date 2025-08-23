<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
          $query = "
                SELECT 
  PRD_KODEDIVISI    AS DIV,
  PRD_KODEDEPARTEMENT    AS DEPT,
  PRD_KODEKATEGORIBARANG AS KAT,
  PRD_PRDCD              AS PLU,
  PRD_DESKRIPSIPANJANG   AS DESKRIPSI,
  PRD_FRAC               AS FRAC,
  PRD_UNIT               AS Unit,
  PRD_KODETAG            AS TAG,
  COALESCE(ST_AVGCOST, 0) AS HARGA,
  COALESCE(ST_SALDOAKHIR, 0) AS LPP,
  COALESCE(QTY_PLANO, 0) AS QTYPLANO,
  COALESCE(RP_LPP, 0) AS RP_LPP,
  COALESCE(RP_PLANO, 0) AS RP_PLANO,
  COALESCE(QTY_PLANO, 0) - COALESCE(ST_SALDOAKHIR, 0) AS SELISIHQTY,
  COALESCE(RP_PLANO, 0) - COALESCE(RP_LPP, 0) AS SELISIHRP,
  COALESCE(PBO_QTYREALISASI, 0) AS QTY_BLM_DSPB,
  COALESCE(RP_PBO_QTYREALISASI, 0) AS RP_BLM_DSPB
FROM (
  SELECT 
    PRD_KODEDIVISI,
    PRD_KODEDEPARTEMENT,
    PRD_KODEKATEGORIBARANG,
    PRD_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_UNIT,
    PRD_FRAC,
    PRD_KODETAG,
    ST_AVGCOST,
    ST_SALDOAKHIR,
    QTY_PLANO,
    CASE
      WHEN PRD_UNIT = 'KG' THEN (ST_SALDOAKHIR * ST_AVGCOST) / 1000
      ELSE ST_SALDOAKHIR * ST_AVGCOST
    END AS RP_LPP,
    CASE
      WHEN PRD_UNIT = 'KG' THEN (QTY_PLANO * ST_AVGCOST) / 1000
      ELSE QTY_PLANO * ST_AVGCOST
    END AS RP_PLANO,
    PBO_QTYREALISASI,
    CASE
      WHEN PRD_UNIT = 'KG' THEN (PBO_QTYREALISASI * ST_AVGCOST) / 1000
      ELSE PBO_QTYREALISASI * ST_AVGCOST
    END AS RP_PBO_QTYREALISASI
  FROM (
    SELECT 
      PRD_KODEDIVISI,
      PRD_KODEDEPARTEMENT,
      PRD_KODEKATEGORIBARANG,
      PRD_PRDCD,
      PRD_DESKRIPSIPANJANG,
      PRD_UNIT,
      PRD_FRAC,
      PRD_KODETAG
    FROM tbmaster_prodmast
    WHERE prd_prdcd LIKE '%0'
  ) p
  LEFT JOIN (
    SELECT 
      ST_PRDCD,
      COALESCE(ST_SALDOAKHIR, 0) AS ST_SALDOAKHIR,
      COALESCE(ST_AVGCOST, 0) AS ST_AVGCOST
    FROM tbmaster_stock
    WHERE st_lokasi = '01'
  ) s ON p.PRD_PRDCD = s.ST_PRDCD
  LEFT JOIN (
    SELECT 
      LKS_PRDCD,
      SUM(COALESCE(LKS_QTY, 0)) AS QTY_PLANO
    FROM tbmaster_lokasi
    GROUP BY LKS_PRDCD
  ) l ON p.PRD_PRDCD = l.LKS_PRDCD
  LEFT JOIN (
    SELECT 
      substr(PBO_PLUIGR, 1, 6) || '0' as PBO_PLUIGR,
      SUM(PBO_QTYREALISASI) AS PBO_QTYREALISASI
    FROM tbmaster_pbomi
    WHERE PBO_NOKOLI IS NOT NULL
      AND PBO_RECORDID = '4'
      AND PBO_CREATE_DT >= CURRENT_DATE - interval '5 days'
      AND NOT EXISTS (
        SELECT 1
        FROM tbtr_realpb
        WHERE PBO_NOKOLI || '' || PBO_KODEOMI || '' || PBO_PLUIGR || '' || PBO_QTYREALISASI = RPB_NOKOLI || '' || RPB_KODEOMI || '' || RPB_PLU2 || '' || RPB_QTYREALISASI
      )
    GROUP BY PBO_PLUIGR
  ) pbo ON p.PRD_PRDCD = pbo.PBO_PLUIGR
  ORDER BY 1, 2, 3, 4
) subquery
WHERE PRD_KODEDIVISI = '1'
  AND PRD_KODEDEPARTEMENT = '14'";

          $stmt = $conn->query($query); // Eksekusi query dengan PDO

} catch (PDOException $e) {
          die("Error: " . $e->getMessage());
}
?>

<!-- Styling untuk Tabel -->
<style>
          /* Styling untuk tabel */
          #table-1 {
                    width: 100%;
                    table-layout: auto;
                    /* Menyesuaikan lebar kolom dengan isi konten */
                    border-collapse: collapse;
                    /* Menggabungkan border antar sel */
          }

          #table-1 th,
          #table-1 td {
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #ddd;
                    /* Membuat border untuk semua cell */
          }

          #table-1 th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                    border-bottom: 2px solid #333;
                    /* Menambahkan pembatas tebal di bawah header */
          }

          #table-1 td {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
          }

          /* Styling untuk kolom DESK */
          #table-1 .desk-column {
                    word-wrap: break-word;
                    /* Memastikan teks di kolom DESK membungkus */
                    white-space: normal;
                    /* Teks dapat membungkus pada kolom DESK */
                    max-width: 300px;
                    /* Membatasi lebar maksimum kolom DESK */
          }

          /* Responsif untuk tabel */
          .table-responsive {
                    overflow-x: auto;
          }
</style>

<section class="section">
          <div class="section-header d-flex justify-content-between">
                    <h1>LPP Rokok</h1>
          </div>
          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <div class="table-responsive">
                                                            <table class="table table-hover table-striped" id="table-1">
                                                                      <thead>
                                                                                <tr>
                                                                                          <th style='text-align:left'>No.</th>
                                                                                          <th style='text-align:left'>DIV</th>
                                                                                          <th style='text-align:left'>DEP</th>
                                                                                          <th style='text-align:left'>KAT</th>
                                                                                          <th style='text-align:left'>PLU</th>
                                                                                          <th style='text-align:left'>DESKRIPSI</th>
                                                                                          <th style='text-align:left'>FRAG</th>
                                                                                          <th style='text-align:left'>UNIT</th>
                                                                                          <th style='text-align:left'>TAG</th>
                                                                                          <th style='text-align:right'>HARGA</th>
                                                                                          <th style='text-align:right'>LPP</th>
                                                                                          <th style='text-align:right'>QTY PLANO</th>
                                                                                          <th style='text-align:right'>RP LPP</th>
                                                                                          <th style='text-align:right'>RP PLANO</th>
                                                                                          <th style='text-align:right'>SELISIH QTY</th>
                                                                                          <th style='text-align:right'>SELISIH RP</th>
                                                                                </tr>
                                                                      </thead>
                                                                      <tbody>
                                                                                <?php
                                                                                $nomor = 1; // Inisialisasi nomor urut
                                                                                if ($stmt->rowCount() > 0) {
                                                                                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                                ?>
                                                                                                    <tr class='s'>
                                                                                                              <td><?= $nomor ?></td>
                                                                                                              <td><?= htmlspecialchars($row['div']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['dept']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['kat']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['plu']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['frac']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['unit']) ?></td>
                                                                                                              <td><?= htmlspecialchars($row['tag']) ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['harga'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['lpp'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['qtyplano'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['rp_lpp'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['rp_plano'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['selisihqty'], 0, ',', '.') ?></td>
                                                                                                              <td style='text-align:right'><?= number_format($row['selisihrp'], 0, ',', '.') ?></td>
                                                                                                    </tr>
                                                                                <?php
                                                                                                    $nomor++;
                                                                                          }
                                                                                } else {
                                                                                          // Pesan jika tidak ada data
                                                                                          echo "<tr><td colspan='16' class='text-center'>Tidak ada data LPP Rokok untuk hari ini.</td></tr>";
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


<script>
          document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#table-1').DataTable({
                              responsive: true,
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
                                                  filename: 'LPP_VS_PLANO_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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