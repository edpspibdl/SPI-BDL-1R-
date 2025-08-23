<?php
require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Pastikan path yang benar untuk koneksi database

// Mengambil input SPD Days dari form yang dikirim menggunakan POST
$input_value = isset($_POST['spb_days']) ? $_POST['spb_days'] : null;

$bln = date("m");
$bln_1 = date("m", strtotime("-3 month"));
$bln_2 = date("m", strtotime("-2 month"));
$bln_3 = date("m", strtotime("-1 month"));
$bln1 = sprintf("%02s", $bln_1);
$bln2 = sprintf("%02s", $bln_2);
$bln3 = sprintf("%02s", $bln_3);

$query = "SELECT spa.SPB_CREATE_DT AS TGL,
                 TO_CHAR(spa.SPB_MODIFY_DT, 'HH24:MI:SS') AS JAM,
                 spa.SPB_LOKASIASAL AS LOKASI_ASAL,
                 spa.SPB_LOKASITUJUAN AS LOKASI_TUJUAN,
                 spa.SPB_PRDCD AS PLU,
                 prd.PRD_DESKRIPSIPANJANG AS DESK,
                 spa.spb_jenis AS JENIS,
                 spa.SPB_QTY,
                 TRUNC(spa.spb_minus / prd.prd_frac) || ' ' || prd.PRD_UNIT AS spb_minta_ctn,
                 (spa.spb_minus::numeric % prd.prd_frac) AS spb_minta_pcs,
                 CASE WHEN COALESCE(spa.spb_recordid, '0') = '2' THEN 'V' ELSE '-' END AS BATAL,
                 CASE WHEN COALESCE(spa.spb_recordid, '0') = '0' THEN 'V' ELSE '-' END AS BELUM_TURUN,
                 CASE WHEN COALESCE(spa.spb_recordid, '0') = '3' THEN 'V' ELSE '-' END AS DITURUNKAN,
                 CASE WHEN COALESCE(spa.spb_recordid, '0') = '1' THEN 'V' ELSE '-' END AS REALISASI,
                 CASE WHEN SUBSTRING(spa.spb_lokasitujuan, 1, 1) IN ('D', 'G') THEN 'SPB GUDANG'
                      WHEN spa.SPB_LOKASIASAL LIKE '%C%' THEN 'SPB SK TOKO'
                      ELSE 'SPB TOKO' END AS spb_lokasi,
                 spa.SPB_CREATE_BY,
                 COALESCE(spa.SPB_MODIFY_BY, '-') AS SPB_MODIFY_BY
          FROM tbtr_antrianspb spa
          LEFT JOIN tbmaster_prodmast prd ON spa.spb_prdcd = prd.prd_prdcd
          WHERE DATE_TRUNC('day', spa.SPB_CREATE_DT) = :input_value
    
";

$stmt = $conn->prepare($query);
$stmt->bindValue(':input_value', $input_value); // Bind nilai input_value dari form
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <h3 class="text-center">SPB Per Tanggal</h3>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>ASAL</th>
                                        <th>TUJUAN</th>
                                        <th>ISIRAK</th>
                                        <th>PLU</th>
                                        <th>DESK</th>
                                        <th>JENIS</th>
                                        <th>MINTACTN</th>
                                        <th>MINTAPCS</th>
                                        <th>BLMTURUN</th>
                                        <th>TURUN</th>
                                        <th>REALISASI</th>
                                        <th>SPB_LOKASI</th>
                                        <th>DIBUAT</th>
                                        <th>DIUBAH</th>
                                        <th>JAM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $noUrut = 0; ?>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td class="text-right"><?= ++$noUrut ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['lokasi_asal']) ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['lokasi_tujuan']) ?></td>
                                            <td class="text-right"><?= htmlspecialchars($row['spb_qty']) ?></td>
                                            <td class="text-right"><?= htmlspecialchars($row['plu']) ?></td>
                                            <td class="desk-column text-left"><?= htmlspecialchars($row['desk']) ?></td>
                                            <td class="text-right"><?= htmlspecialchars($row['jenis']) ?></td>
                                            <td class="text-right"><?= htmlspecialchars($row['spb_minta_ctn']) ?></td>
                                            <td class="text-right"><?= htmlspecialchars($row['spb_minta_pcs']) ?></td>
                                            <td class="text-center"><?= $row['belum_turun'] == 'V' ? 'BELUM' : '' ?></td>
                                            <td class="text-center"><?= $row['diturunkan'] == 'V' ? 'DITURUNKAN' : '' ?></td>
                                            <td class="text-center"><?= $row['realisasi'] == 'V' ? 'REALISASI' : '' ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['spb_lokasi']) ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['spb_create_by']) ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['spb_modify_by']) ?></td>
                                            <td class="text-left"><?= htmlspecialchars($row['jam']) ?></td>
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