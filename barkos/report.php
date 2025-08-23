<?php
require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Pastikan path yang benar untuk koneksi database

// Mengambil input SPD Days dari form yang dikirim menggunakan POST
$input_value = isset($_POST['spd_days']) ? $_POST['spd_days'] : null;

$bln = date("m");
$bln_1 = date("m", strtotime("-3 month"));
$bln_2 = date("m", strtotime("-2 month"));
$bln_3 = date("m", strtotime("-1 month"));
$bln1 = sprintf("%02s", $bln_1);
$bln2 = sprintf("%02s", $bln_2);
$bln3 = sprintf("%02s", $bln_3);

$query = "
SELECT
    hgb.kodesup AS kode_supplier,
    sup.sup_namasupplier AS nama_supplier,
    rsl.rsl_prdcd AS plu,
    pm.prd_deskripsipanjang AS desk,
    pm.prd_kodetag as tag,
    rsl.avgsalesqty AS avg_sales,
    rsl.avgsalesrph AS avg_rph,
    rsl.sales_1,
    rsl.sales_2,
    rsl.sales_3,
    ROUND(COALESCE(CASE WHEN slv.po != 0 THEN (slv.bpb / slv.po) * 100 ELSE 0 END, 0), 0) || ' %' AS SL,
    COALESCE(st.st_saldoakhir, 0) AS saldo_akhir,
    COALESCE(ROUND(rsl.avgsalesqty / 30 * :input_value, 2), 0) AS SPD,  
    COALESCE(
        ROUND(
            CASE
                WHEN rsl.avgsalesqty / 30 * :input_value = 0 THEN 0
                ELSE COALESCE(st.st_saldoakhir, 0) / (rsl.avgsalesqty / 30 * :input_value)
            END, 2
        ), 0.00
    ) AS DSI,
     CASE WHEN POOUT IS NULL THEN 'TIDAK ADA PO' ELSE 'ADA PO' END KET_PO,
    mstd.BPB_TERAKHIR,
    CASE
        WHEN COALESCE(st.st_saldoakhir, 0) = 0 OR COALESCE(st.st_saldoakhir, 0) < COALESCE(ROUND(rsl.avgsalesqty / 30 * :input_value, 2), 0) THEN 'Barkos'
        ELSE 'Aman'
    END AS keterangan
FROM
    tbmaster_prodmast pm
    LEFT JOIN (
        SELECT
            rsl_prdcd,
            ROUND(SUM(RSL_qty_$bln1)) AS sales_1,
            ROUND(SUM(RSL_qty_$bln2)) AS sales_2,
            ROUND(SUM(RSL_qty_$bln3)) AS sales_3,
            ROUND(
                (COALESCE(SUM(RSL_qty_$bln1), 0) + COALESCE(SUM(RSL_qty_$bln2), 0) + COALESCE(SUM(RSL_qty_$bln3), 0)) / 3
            ) AS avgsalesqty,
            ROUND(
                (COALESCE(SUM(RSL_rph_$bln1), 0) + COALESCE(SUM(RSL_rph_$bln2), 0) + COALESCE(SUM(RSL_rph_$bln3), 0)) / 3
            ) AS avgsalesrph
        FROM
            tbtr_rekapsalesbulanan
        GROUP BY
            rsl_prdcd
    ) rsl ON pm.prd_prdcd = rsl.rsl_prdcd
    LEFT JOIN (
        SELECT
            hgb_prdcd,
            hgb_kodesupplier AS kodesup
        FROM
            tbmaster_hargabeli
        WHERE
            hgb_tipe = '2'
    ) hgb ON pm.prd_prdcd = hgb.hgb_prdcd
    LEFT JOIN (
        SELECT
            st_prdcd,
            st_saldoakhir
        FROM
            tbmaster_stock
        WHERE
            st_lokasi = '01'
    ) st ON pm.prd_prdcd = st.st_prdcd
    LEFT JOIN tbmaster_supplier sup ON hgb.kodesup = sup.sup_kodesupplier
    LEFT JOIN (
        SELECT 
            MSTD_PRDCD,
            MAX(MSTD_TGLDOC) AS BPB_TERAKHIR
        FROM TBTR_MSTRAN_D
        WHERE MSTD_TYPETRN = 'B'
        GROUP BY MSTD_PRDCD
    ) mstd ON pm.prd_prdcd = mstd.MSTD_PRDCD
     LEFT JOIN (
    SELECT
        sl_prdcd_po AS slv_prdcd,
        SUM(sl_qty_po) AS po,
        SUM(sl_qty_bpb) AS bpb
    FROM (
        SELECT
            po.tpod_prdcd AS sl_prdcd_po,
            po.tpod_qtypo AS sl_qty_po,
            COALESCE(mst.mstd_qty, 0) AS sl_qty_bpb
        FROM tbtr_po_d po
        LEFT JOIN tbtr_mstran_d mst ON po.tpod_prdcd = mst.mstd_prdcd
                                 AND po.tpod_nopo = mst.mstd_nopo
        WHERE po.tpod_prdcd IS NOT NULL
    ) subquery
    GROUP BY sl_prdcd_po
) slv ON pm.prd_prdcd = slv.slv_prdcd
 LEFT JOIN (
    SELECT tpod_prdcd,
           SUM(tpod_qtypo)  AS POOUT,
           COUNT(tpod_nopo) AS JLHPOOUT
    FROM tbtr_po_d
    WHERE tpod_nopo IN (
        SELECT tpoh_nopo
        FROM tbtr_po_h
        WHERE tpoh_recordid IS NULL
          AND (tpoh_tglpo + INTERVAL '1 day' * tpoh_jwpb) >= CURRENT_DATE
    )
    GROUP BY tpod_prdcd
) po ON pm.prd_prdcd = po.tpod_prdcd
WHERE
    pm.prd_recordid IS NULL
    AND pm.prd_prdcd LIKE '%0'
    AND rsl.rsl_prdcd IS NOT NULL
    AND (pm.prd_kodetag IS NULL OR pm.prd_kodetag NOT IN ('H',
                                                'A',
                                                'N',
                                                'O',
                                                'X',
                                                'T',
                                                'G'))
    AND pm.prd_prdcd NOT IN (
        '0002310', '0003030', '0003040', '0005090', '0009170', '0009190', '0009380', '0009480', '0011280', 
        '0030600', '0030610', '0030650', '0030680', '0030700', '0032020', '0032070', '0033500', '0033560', 
        '0033570', '0033580', '0047180', '0053250', '0054950', '0203710', '0346810', '0365020', '0365030', 
        '0365050', '425130', '0425150', '450300', '481890', '481900', '539960', '543000', '757750', '814340', 
        '00819360', '0873130', '0873140', '0873150', '874920', '874930', '1077740', '1084080', '1112220', 
        '1116230', '1116240', '1116250', '1116260', '1116290', '1116460', '1117650', '1117690', '1130140', 
        '1144270', '1144730', '1144920', '1153710', '1153720', '1154480', '1163720', '1163730', '1163740', 
        '1163750', '1166950', '1178040', '1178160', '1178610', '1178620', '1178680', '1178690', '1209170', 
        '1211940', '1220090', '1220100', '1236280', '1239240', '1239250', '1239260', '1246360', '1255020', 
        '1255030', '1257970', '1257980', '1259590', '1259910', '1269280', '1271480', '1271490', '1271520', 
        '1271530', '1281800', '1282000', '1301700', '1311920', '1311940', '1311990', '1312010', '1312050', 
        '1312060', '1312910', '1324120', '1332970', '1347320', '1347330', '1350380', '1356510', '1363040', 
        '1382230', '1393250', '1393460', '1402850', '1403680', '1411160', '1430360', '1430370', '1430380', 
        '1430390', '1442000', '1476440', '1479580', '1479730', '1481790', '1482840', '1498000', '1498220', 
        '1498270', '1510880', '1515080', '1515990', '1524470', '1524620', '1527470', '1557670', '1635490',
        '0505840', '0646900', '1386390', '1411370', '1418130', '1511330', '1556260', '1557650', '1703800',
        '0030180', '0030200', '1664840', '1652500', '1147320', '1241640', '1345920', '1377990', '1378030',
        '1500370', '1579800', '1515970', '1515950', '1674540', '1372000', '1674560', '0875020', '1152780',
        '1386150'
    )
";

$stmt = $conn->prepare($query);
$stmt->bindValue(':input_value', $input_value); // Bind nilai input_value dari form
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Inisialisasi count untuk keterangan "Aman" dan "Barkos"
$count_aman = 0;
$count_barkos = 0;

// Menghitung jumlah keterangan "Aman" dan "Barkos"
foreach ($result as $row) {
    if ($row['keterangan'] == 'Aman') {
        $count_aman++;
    } else {
        $count_barkos++;
    }
}
?>

<style>
    .report-container {
        margin-top: 30px;
    }

    .table th,
    .table td {
        vertical-align: middle;
        white-space: nowrap;
        /* Prevent text from wrapping */
    }

    .table thead th {
        background-color: #007bff;
        color: white;
    }

    .table td {
        text-align: left;
    }

    .table td:first-child,
    .table th:first-child {
        text-align: left;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table {
        width: 100%;
        table-layout: auto;
        /* Automatically adjust columns based on content */
    }
</style>

<body>

    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h3 class="text-center">BARKOS</h3>
            <a href="index.php" class="btn btn-primary">BACK</a>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-dismissible fade show mt-2" role="alert" style="background-color:rgb(20, 137, 184); border-color: #c3e6cb;">
                            <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.5rem;"></i>
                            <strong class="ml-2">Aman :</strong> <span class="badge badge-success"><?= $count_aman ?></span>
                            <br>
                            <i class="fas fa-exclamation-circle" style="color: #ffc107; font-size: 1.5rem;"></i>
                            <strong class="ml-2">Barkos :</strong> <span class="badge badge-warning"><?= $count_barkos ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>KD SUPP</th>
                                        <th>NAMA SUPP</th>
                                        <th>PLU</th>
                                        <th>DESK</th>
                                        <th>TAG</th>
                                        <th>Sales <?php echo date("M", strtotime("-3 month")); ?></th>
                                        <th>Sales <?php echo date("M", strtotime("-2 month")); ?></th>
                                        <th>Sales <?php echo date("M", strtotime("-1 month")); ?></th>
                                        <th>SL</th>
                                        <th>LPP</th>
                                        <th>Avg Sales</th>
                                        <th>Avg Rph</th>
                                        <th>SPD <?= $input_value ?> DAY</th>
                                        <th>DSI</th>
                                        <th>KET PO</th>
                                        <th>BPB TERAKHIR</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td><?= $row['keterangan'] ?></td>
                                            <td><?= $row['kode_supplier'] ?></td>
                                            <td><?= $row['nama_supplier'] ?></td>
                                            <td><?= $row['plu'] ?></td>
                                            <td><?= $row['desk'] ?></td>
                                            <td><?= $row['tag'] ?></td>
                                            <td><?= $row['sales_1'] ?></td>
                                            <td><?= $row['sales_2'] ?></td>
                                            <td><?= $row['sales_3'] ?></td>
                                            <td><?= $row['sl'] ?></td>
                                            <td><?= number_format($row['saldo_akhir'], 0) ?></td>
                                            <td><?= number_format($row['avg_sales'], 2) ?></td>
                                            <td><?= number_format($row['avg_rph'], 2) ?></td>
                                            <td><?= number_format($row['spd'], 2) ?></td>
                                            <td><?= number_format($row['dsi'], 2) ?></td>
                                            <td><?= $row['ket_po'] ?></td>
                                            <td><?= $row['bpb_terakhir'] ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once '../layout/_bottom.php'; ?>

    <!-- Add the required CSS and JS libraries -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#table-1').DataTable({
                responsive: false,
                lengthMenu: [10, 25, 50, 100],
                autoWidth: true,
                columnDefs: [{
                    targets: [4], // Kolom "DESK" tidak dapat diurutkan
                    orderable: false
                }],
                buttons: [{
                        extend: 'copy',
                        text: 'Copy'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        filename: 'BARKOS_SPI_BDL_' + new Date().toISOString().split('T')[0],
                        title: null
                    }
                ],
                dom: 'Bfrtip',
                initComplete: function() {
                    this.api().columns.adjust().draw();
                }
            });

            table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
        });
    </script>

</body>