<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Ensure this is the correct path

// Get the start and end date from the form input
$tanggalMulai = isset($_GET['tanggalMulai']) ? $_GET['tanggalMulai'] : '';
$tanggalSelesai = isset($_GET['tanggalSelesai']) ? $_GET['tanggalSelesai'] : '';

// Convert the dates to the format that SQL expects (YYYYMMDD)
$tanggalMulaiFormatted = date('Ymd', strtotime($tanggalMulai));
$tanggalSelesaiFormatted = date('Ymd', strtotime($tanggalSelesai));

// Prepare the SQL query with placeholders for the date range
$query = "SELECT
  sl_tag AS sl_tag,
  COUNT(DISTINCT sl_nomor_po) AS sl_nomor_po,
  COUNT(DISTINCT sl_nomor_bpb) AS sl_nomor_bpb,
  COUNT(sl_prdcd_po) AS sl_prdcd_po,
  COUNT(sl_prdcd_bpb) AS sl_prdcd_bpb,
  SUM(sl_qty_po) AS sl_qty_po,
  SUM(sl_qty_bpb) AS sl_qty_bpb,
  SUM(sl_rph_po) AS sl_rph_po,
  SUM(sl_rph_bpb) AS sl_rph_bpb
FROM (
  SELECT
    po.tpod_nopo AS sl_nomor_po,
    DATE_TRUNC('day', poh.tpoh_tglpo) AS sl_tanggal_po,
    mst.mstd_nodoc AS sl_nomor_bpb,
    mst.mstd_tgldoc AS sl_tanggal_bpb,
    prd.prd_kodedivisi AS sl_div,
    prd.prd_kodedepartement AS sl_dept,
    prd.prd_kodekategoribarang AS sl_katb,
    po.tpod_prdcd AS sl_prdcd_po,
    mst.mstd_prdcd AS sl_prdcd_bpb,
    prd.prd_deskripsipanjang AS sl_nama_barang,
    prd.prd_unit AS sl_unit,
    prd.prd_frac AS sl_frac,
    COALESCE(prd.prd_kodetag, ' ') AS sl_tag,
    PO.TPOD_QTYPO AS sl_qty_po,
    po.tpod_gross + po.tpod_ppn AS sl_rph_po,
    COALESCE(MST.MSTD_QTY, 0) AS sl_qty_bpb,
    COALESCE(mst.mstd_gross - mst.mstd_discrph + mst.mstd_ppnrph, 0) AS sl_rph_bpb,
    poh.tpoh_kodesupplier AS sl_kode_supplier,
    sup.sup_namasupplier AS sl_nama_supplier,
    spd.spd_qty_1 AS sl_spd_qty_1,
    spd.spd_qty_2 AS sl_spd_qty_2,
    spd.spd_qty_3 AS sl_spd_qty_3,
    stk.st_sales AS sl_sales_bulan_ini,
    stk.st_saldoakhir AS sl_stock_qty,
    stk.st_lastcost AS sl_lastcost,
    stk.st_avgcost AS sl_avgcost
  FROM tbtr_po_d po
  LEFT JOIN tbtr_mstran_d mst
    ON po.tpod_prdcd = mst.mstd_prdcd
    AND po.tpod_nopo = mst.mstd_nopo
  LEFT JOIN tbtr_po_h poh
    ON po.tpod_nopo = poh.tpoh_nopo
  LEFT JOIN tbmaster_prodmast prd
    ON po.tpod_prdcd = prd.prd_prdcd
  LEFT JOIN tbmaster_supplier sup
    ON poh.tpoh_kodesupplier = sup.sup_kodesupplier
  LEFT JOIN (
    SELECT
      sls_prdcd AS spd_prdcd,
      COALESCE(sls_qty_03, 0) AS spd_qty_1,
      COALESCE(sls_qty_04, 0) AS spd_qty_2,
      COALESCE(sls_qty_05, 0) AS spd_qty_3,
      TRUNC((COALESCE(sls_qty_03, 0) + COALESCE(sls_qty_04, 0) + COALESCE(sls_qty_05, 0)) / 90, 5) AS spd_qty,
      COALESCE(sls_rph_03, 0) AS spd_rph_1,
      COALESCE(sls_rph_04, 0) AS spd_rph_2,
      COALESCE(sls_rph_05, 0) AS spd_rph_3,
      TRUNC((COALESCE(sls_rph_03, 0) + COALESCE(sls_rph_04, 0) + COALESCE(sls_rph_05, 0)) / 90, 5) AS spd_rph
    FROM tbtr_salesbulanan
  ) spd
    ON po.tpod_prdcd = spd.spd_prdcd
  LEFT JOIN (
    SELECT
      *
    FROM tbmaster_stock
    WHERE
      st_lokasi = '01'
  ) stk
    ON po.tpod_prdcd = stk.st_prdcd
  WHERE
    mst.mstd_recordid IS NULL AND (po.tpod_recordid IS NULL OR po.tpod_recordid = '2')
) sledp
WHERE
  TO_CHAR(sl_tanggal_po, 'YYYYMMDD') BETWEEN :tanggalMulai AND :tanggalSelesai
GROUP BY
  sl_tag
ORDER BY
  sl_tag
";

// Execute the query and fetch the results using the PDO connection
try {
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':tanggalMulai', $tanggalMulaiFormatted);
    $stmt->bindValue(':tanggalSelesai', $tanggalSelesaiFormatted);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}

?>

<style>
    /* Styling untuk Tabel */
    #table-1 {
        width: 100%;
        /* DataTables will handle autoWidth better with proper column definitions */
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

    /* For data cells, ensure text doesn't overflow horizontally without breaking */
    #table-1 td {
        /* Default behavior: text wrapping if needed, adjust max-width as per content */
        white-space: normal;
        word-wrap: break-word;
        /* Ensure long words break */
    }

    /* Styling for the first column (No) */
    #table-1 .column-no {
        width: 50px;
        /* Adjust as needed */
        text-align: center;
    }

    /* Styling for percentage columns to ensure fixed width if desired */
    #table-1 .column-percent {
        width: 80px;
        /* Fixed width for percentage columns */
        text-align: right;
    }

    /* Styling for quantity/rupiah columns if you want them right-aligned and potentially fixed width */
    #table-1 .column-qty-rph {
        width: 100px;
        /* Adjust as needed */
        text-align: right;
    }


    /* Responsif untuk tabel */
    .table-responsive {
        overflow-x: auto;
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 style="margin-bottom: 0;">
            <span style="font-weight: bold; font-size: 1.5rem;">Service Level PO</span>
            <span style="font-weight: normal; font-size: 1rem; color: #6c757d;">Per Kode Tag</span>
            <div class="periode-laporan-text mb-3">
                <h6>Periode: <?= date('d-m-Y', strtotime($tanggalMulaiFormatted ?? 'now')) ?> s/d <?= date('d-m-Y', strtotime($tanggalSelesaiFormatted ?? 'now')) ?></h6>
            </div>
        </h3>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2" class="text-center">#</th>
                                    <th rowspan="2" class="text-center">Kode Tag</th>
                                    <th colspan="3" class="text-center">Dokumen</th>
                                    <th colspan="3" class="text-center">Item</th>
                                    <th colspan="3" class="text-center">Quantity</th>
                                    <th colspan="3" class="text-center">Rupiah</th>
                                </tr>

                                <tr class="info">
                                    <th>PO</th>
                                    <th>BPB</th>
                                    <th>%</th>

                                    <th>PO</th>
                                    <th>BPB</th>
                                    <th>%</th>

                                    <th>PO</th>
                                    <th>BPB</th>
                                    <th>%</th>

                                    <th>PO</th>
                                    <th>BPB</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noUrut = 0;
                                // Initialize total variables outside the loop
                                $dokumenPO = 0;
                                $dokumenBPB = 0;
                                $itemPO = 0;
                                $itemBPB = 0;
                                $qtyPO = 0;
                                $qtyBPB = 0;
                                $rphPO = 0;
                                $rphBPB = 0;

                                // Check if $result is an array and not empty
                                if (!empty($result) && is_array($result)) {
                                    foreach ($result as $row) {
                                        $noUrut++;

                                        // Safely calculate percentages, handle division by zero
                                        $dokumenPercent = ($row['sl_nomor_po'] > 0) ? ($row['sl_nomor_bpb'] / $row['sl_nomor_po'] * 100) : 0;
                                        $itemPercent = ($row['sl_prdcd_po'] > 0) ? ($row['sl_prdcd_bpb'] / $row['sl_prdcd_po'] * 100) : 0;
                                        $qtyPercent = ($row['sl_qty_po'] > 0) ? ($row['sl_qty_bpb'] / $row['sl_qty_po'] * 100) : 0;
                                        $rphPercent = ($row['sl_rph_po'] > 0) ? ($row['sl_rph_bpb'] / $row['sl_rph_po'] * 100) : 0;

                                        echo '<tr>';
                                        echo '<td class="text-center">' . $noUrut . '</td>';
                                        echo '<td class="text-left">' . htmlspecialchars($row['sl_tag'] ?? '') . '</td>';

                                        echo '<td class="text-right">' . number_format($row['sl_nomor_po'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right">' . number_format($row['sl_nomor_bpb'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right column-percent">' . number_format($dokumenPercent, 0, '.', ',') . '%</td>';

                                        echo '<td class="text-right">' . number_format($row['sl_prdcd_po'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right">' . number_format($row['sl_prdcd_bpb'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right column-percent">' . number_format($itemPercent, 0, '.', ',') . '%</td>';

                                        echo '<td class="text-right">' . number_format($row['sl_qty_po'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right">' . number_format($row['sl_qty_bpb'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right column-percent">' . number_format($qtyPercent, 0, '.', ',') . '%</td>';

                                        echo '<td class="text-right">' . number_format($row['sl_rph_po'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right">' . number_format($row['sl_rph_bpb'] ?? 0, 0, '.', ',') . '</td>';
                                        echo '<td class="text-right column-percent">' . number_format($rphPercent, 2, '.', ',') . '%</td>';

                                        echo '</tr>'; // Changed print to echo for consistency

                                        // Accumulate totals
                                        $dokumenPO += ($row['sl_nomor_po'] ?? 0);
                                        $dokumenBPB += ($row['sl_nomor_bpb'] ?? 0);
                                        $itemPO += ($row['sl_prdcd_po'] ?? 0);
                                        $itemBPB += ($row['sl_prdcd_bpb'] ?? 0);
                                        $qtyPO += ($row['sl_qty_po'] ?? 0);
                                        $qtyBPB += ($row['sl_qty_bpb'] ?? 0);
                                        $rphPO += ($row['sl_rph_po'] ?? 0);
                                        $rphBPB += ($row['sl_rph_bpb'] ?? 0);
                                    }
                                } else {
                                    echo '<tr class="no-data-row">';
                                    echo '<td colspan="14" class="text-center">Tidak ada data yang tersedia untuk periode ini.</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <?php if ($noUrut > 0): ?>
                                    <tr class="font-weight-bold table-info">
                                        <td class="text-center" colspan="2">TOTAL</td>
                                        <td class="text-right"><?= number_format($dokumenPO, 0, '.', ',') ?></td>
                                        <td class="text-right"><?= number_format($dokumenBPB, 0, '.', ',') ?></td>
                                        <td class="text-right column-percent"><?= number_format(($dokumenPO > 0) ? ($dokumenBPB / $dokumenPO * 100) : 0, 0, '.', ',') ?>%</td>

                                        <td class="text-right"><?= number_format($itemPO, 0, '.', ',') ?></td>
                                        <td class="text-right"><?= number_format($itemBPB, 0, '.', ',') ?></td>
                                        <td class="text-right column-percent"><?= number_format(($itemPO > 0) ? ($itemBPB / $itemPO * 100) : 0, 0, '.', ',') ?>%</td>

                                        <td class="text-right"><?= number_format($qtyPO, 0, '.', ',') ?></td>
                                        <td class="text-right"><?= number_format($qtyBPB, 0, '.', ',') ?></td>
                                        <td class="text-right column-percent"><?= number_format(($qtyPO > 0) ? ($qtyBPB / $qtyPO * 100) : 0, 0, '.', ',') ?>%</td>

                                        <td class="text-right"><?= number_format($rphPO, 0, '.', ',') ?></td>
                                        <td class="text-right"><?= number_format($rphBPB, 0, '.', ',') ?></td>
                                        <td class="text-right column-percent"><?= number_format(($rphPO > 0) ? ($rphBPB / $rphPO * 100) : 0, 2, '.', ',') ?>%</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
            autoWidth: true,
            columnDefs: [{
                targets: [4],
                orderable: false
            }],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'REPORT_SL_BY_MEMBER_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function() {
                this.api().columns.adjust().draw();
            }
        });

        // Tambahkan tombol ke bagian atas kiri
        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>