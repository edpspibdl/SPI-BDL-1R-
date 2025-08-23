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
$query = "SELECT SLK_TGLPB,
SLK_NOTRANS,
SLK_NOPB,
SKL_TIPEMEMBER, 
SLK_CUSNO, 
SLK_NAMAMEMBER, 
SUM(SLK_QTYORDER)SLK_QTYORDER, 
SUM(SLK_RPHORDER)SLK_RPHORDER, 
SUM(SLK_QTYREAL)SLK_QTYREAL, 
SUM(SLK_RPHREAL)SLK_RPHREAL 
FROM 
(SELECT D.OBI_NOTRANS SLK_NOTRANS,
OBI_NOPB SLK_NOPB, 
PRD_KODEDIVISI SLK_K_D, 
div_namadivisi SLK_NAMA_DIV, 
DEP.dep_namadepartement SLK_K_DEPT, 
PRD_KODEDEPARTEMENT SLK_NAMA_DEPT, 
PRD_KODEKATEGORIBARANG SLK_KATB, 
AMM_KODEMEMBER SLK_CUSNO, 
AMM_NAMAPENERIMA SLK_NAMAMEMBER, 
SUBSTRING(OBI_PRDCD FROM 1 for 6) || '0' AS slk_prdcd_ctn,
   prd_deskripsipanjang slk_namabarang,
   d.obi_qtyorder slk_qtyorder, date(d.obi_tgltrans) AS slk_tglpb,
   ( obi_hargasatuan + obi_ppn - obi_diskon ) * obi_qtyorder AS slk_rphorder,
   CASE
       WHEN h.obi_recid = '6' THEN
           ( obi_hargasatuan + obi_ppn - obi_diskon ) * obi_qtyrealisasi
       ELSE
           0
   END AS slk_rphreal,
   CASE
       WHEN h.obi_recid = '6' THEN
           obi_qtyrealisasi
       ELSE
           0
   END AS slk_qtyreal,
   CASE
       WHEN cus_jenismember = 'T'                    THEN
           'TMI'
       WHEN coalesce(cus_flagmemberkhusus, 'T') = 'Y' THEN
           'MEMBER MERAH'
       ELSE
           'MEMBER BIRU'
   END AS skl_tipemember
FROM
    tbtr_obi_d             d
    LEFT JOIN tbtr_obi_h             h ON d.obi_notrans = h.obi_notrans
                              AND date(d.obi_tgltrans) = date(h.obi_tgltrans)
    LEFT JOIN tbtr_alamat_mm         amm ON h.obi_kdmember = amm.amm_kodemember
                                    AND h.obi_tgltrans = amm_tglpb
    LEFT JOIN tbmaster_prodmast      prd ON substring(obi_prdcd, 1, 6)
                                       || '0' = prd_prdcd
    LEFT JOIN tbmaster_divisi        div ON prd.prd_kodedivisi = div.div_kodedivisi
    LEFT JOIN tbmaster_customer      cus ON amm.amm_kodemember = cus.cus_kodemember
    LEFT JOIN tbmaster_departement   dep ON prd.prd_kodedepartement = dep.dep_kodedepartement
) pb WHERE TO_CHAR(slk_tglpb, 'yyyymmdd') BETWEEN :tanggalMulai AND :tanggalSelesai
GROUP BY
    slk_nopb,
    slk_cusno,
    slk_namamember,
    slk_notrans,
    skl_tipemember,SLK_TGLPB ORDER by slk_tglpb,
      slk_notrans asc
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
            <span style="font-weight: bold; font-size: 1.5rem;">Service Level</span>
            <span style="font-weight: normal; font-size: 1rem; color: #6c757d;">per PB</span>
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
                                    <th rowspan="2" align="center">#</th>
                                    <th colspan="6">Pb</th>
                                    <th colspan="3">Qty</th>
                                    <th colspan="3">Rupiah</th>


                                </tr>

                                <tr class="info">
                                    <th>Tgl Pb</th>
                                    <th>No Trans</th>
                                    <th>No Pb</th>
                                    <th>Kode Member</th>
                                    <th>Nama Member</th>
                                    <th>Tipe Member</th>
                                    <th>Qty Order</th>
                                    <th>Qty Realisasi</th>
                                    <th>%</th>
                                    <th>Rph Order</th>

                                    <th>Rph Realisasi</th>
                                    <th>%</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noUrut = 0;
                                foreach ($result as $row):
                                    $noUrut++;
                                    print '<tr>';
                                    echo '<td align="right">'  . $noUrut . '</td>';
                                    echo '<td align="center">' . $row['slk_tglpb'] . '</td>';
                                    echo '<td align="center">' . $row['slk_notrans'] . '</td>';
                                    echo '<td align="center">' . $row['slk_nopb'] . '</td>';
                                    echo '<td align="center">' . $row['slk_cusno'] . '</td>';
                                    echo '<td align="left">' . $row['slk_namamember'] . '</td>';
                                    echo '<td align="left">' . $row['skl_tipemember'] . '</td>';

                                    echo '<td align="right">'  . number_format($row['slk_qtyorder'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">'  . number_format($row['slk_qtyreal'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">'  . number_format($row['slk_qtyreal'] / $row['slk_qtyorder'] * 100, 0, '.', ',') . '</td>';

                                    echo '<td align="right">'  . number_format($row['slk_rphorder'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">'  . number_format($row['slk_rphreal'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">'  . number_format($row['slk_rphreal'] / $row['slk_rphorder'] * 100, 2, '.', ',') . '</td>';

                                    print '</tr>';


                                endforeach;

                                ?>
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
            autoWidth: false,
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