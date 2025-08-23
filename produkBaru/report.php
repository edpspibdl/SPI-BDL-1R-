<?php
require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Pastikan path yang benar untuk koneksi database

// Mengambil input SPD Days dari form yang dikirim menggunakan POST
// Mengamankan input dengan mengonversi ke integer dan memberikan nilai default
$input_value = isset($_POST['spd_days']) ? (int)$_POST['spd_days'] : 30;

// Query yang sudah diperbaiki
$query = "SELECT M.MSTD_PRDCD,
                  P.PRD_DESKRIPSIPANJANG,
                  P.PRD_UNIT,
                  P.PRD_FRAC,
                  P.PRD_KODETAG,
                  K.PKM_PKMT,
                  M.MSTD_TGLDOC,
                  M.MSTD_NODOC,
                  M.MSTD_KODESUPPLIER,
                  S.SUP_NAMASUPPLIER,
                  M.MSTD_QTY,
                  M.MSTD_QTYBONUS1,
                  TRUNC((M.MSTD_GROSS - M.MSTD_DISCRPH) / M.MSTD_QTY) AS HPP,
                  M.MSTD_GROSS - M.MSTD_DISCRPH AS TOTAL
           FROM TBTR_MSTRAN_D M,
                TBMASTER_PRODMAST P,
                TBMASTER_SUPPLIER S,
                TBMASTER_KKPKM K
           WHERE M.MSTD_PRDCD = P.PRD_PRDCD
             AND M.MSTD_KODESUPPLIER = S.SUP_KODESUPPLIER
             AND P.PRD_PRDCD = K.PKM_PRDCD
             AND M.MSTD_PRDCD || M.MSTD_NODOC IN (
                 SELECT MSTD_PRDCD || MSTD_NODOC_MIN
                 FROM (
                     SELECT MSTD_PRDCD,
                            MIN(MSTD_NODOC) AS MSTD_NODOC_MIN
                     FROM TBTR_MSTRAN_D
                     WHERE MSTD_RECORDID IS NULL
                       AND MSTD_TYPETRN = 'B'
                     GROUP BY MSTD_PRDCD
                     HAVING DATE_TRUNC('day', MIN(MSTD_TGLDOC)) >= DATE_TRUNC('day', CURRENT_DATE) - (:spd_days || ' day')::INTERVAL
                 ) subquery
             )
           ORDER BY M.MSTD_NODOC DESC
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':spd_days', $input_value, PDO::PARAM_INT); // Bind nilai input_value dari form
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


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Unit</th>
                                        <th>Fraction</th>
                                        <th>Tag Code</th>
                                        <th>PKM</th>
                                        <th>Document Date</th>
                                        <th>Document No.</th>
                                        <th>Supplier Code</th>
                                        <th>Supplier Name</th>
                                        <th>Quantity</th>
                                        <th>Bonus Quantity</th>
                                        <th>HPP</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['mstd_prdcd']); ?></td>
                                            <td><?php echo htmlspecialchars($row['prd_deskripsipanjang']); ?></td>
                                            <td><?php echo htmlspecialchars($row['prd_unit']); ?></td>
                                            <td><?php echo htmlspecialchars($row['prd_frac']); ?></td>
                                            <td><?php echo htmlspecialchars($row['prd_kodetag']); ?></td>
                                            <td><?php echo htmlspecialchars($row['pkm_pkmt']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mstd_tgldoc']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mstd_nodoc']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mstd_kodesupplier']); ?></td>
                                            <td><?php echo htmlspecialchars($row['sup_namasupplier']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mstd_qty']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mstd_qtybonus1']); ?></td>
                                            <td><?php echo htmlspecialchars($row['hpp']); ?></td>
                                            <td><?php echo htmlspecialchars($row['total']); ?></td>
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