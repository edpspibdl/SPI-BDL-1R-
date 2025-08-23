<?php
require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Pastikan path yang benar untuk koneksi database

// Mengambil input SPD Days dari form yang dikirim menggunakan POST
$input_value = isset($_POST['get_container']) ? $_POST['get_container'] : null;


$query = "
SELECT DISTINCT ON (pobi_tgltransaksi, pobi_notransaksi, pobi_create_by, pobi_nocontainer, pobi_prdcd)
    pobi_tgltransaksi, pobi_notransaksi, pobi_create_by ,pobi_nocontainer, pobi_prdcd, 
    prd_deskripsipanjang, pobi_frac, pobi_qty, obi_qtyorder, obi_qtyrealisasi, pobi_create_dt
FROM tbtr_packing_obi
LEFT JOIN tbmaster_prodmast ON pobi_prdcd = prd_prdcd
LEFT JOIN TBTR_OBI_D ON pobi_notransaksi = obi_notrans 
   AND pobi_tgltransaksi = obi_tgltrans -- Gabungkan kondisi JOIN untuk menghindari duplikasi
   AND pobi_prdcd = OBI_PRDCD
   WHERE pobi_nocontainer = :input_value
ORDER BY pobi_tgltransaksi DESC;

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
    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap; /* Prevent text from wrapping */
    }
    .table thead th {
        background-color: #007bff;
        color: white;
    }
    .table td {
        text-align: left;
    }
    .table td:first-child, .table th:first-child {
        text-align: left;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .table {
        width: 100%;
        table-layout: auto; /* Automatically adjust columns based on content */
    }
</style>

<body>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">PACKING</h3>
        <a href="index.php" class="btn btn-primary">BACK</a>
    </div>
    <div class="alert alert-success mt-2">
        <strong>Note:</strong> Jika Packer adalah <b>CK2</b>, maka jam CCTV Dimajukan  <b>±12 menit</b> dari Jam yang ada di data.
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>TGL TRANS</th>
                                    <th>NO TRANS</th>
                                    <th>PACKER</th>
                                    <th>NO CONTAINER</th>
                                    <th>PLU</th>
                                    <th>DESK</th>
                                    <th>QTY ORDER</th>
                                    <th>QTY PACKING</th>
                                    <th>QTY REALISASI</th>
                                    <th>CREATE DT</th>  
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['pobi_tgltransaksi']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_notransaksi']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_create_by']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_nocontainer']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_prdcd']) ?></td>
                                    <td><?= htmlspecialchars($row['prd_deskripsipanjang']) ?>
                                    <td><?= htmlspecialchars($row['obi_qtyorder']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_qty']) ?></td>   
                                    <td><?= htmlspecialchars($row['obi_qtyrealisasi']) ?></td>
                                    <td><?= htmlspecialchars($row['pobi_create_dt']) ?></td>
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



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#table-1').DataTable({
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: true,
            columnDefs: [
                {
                    targets: [4], // Kolom "DESK" tidak dapat diurutkan
                    orderable: false
                }
            ],
            buttons: [
                {
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
            initComplete: function () {
                this.api().columns.adjust().draw();
            }
        });

        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>

</body>
