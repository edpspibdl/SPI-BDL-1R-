<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Ensure this is the correct path

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query untuk mengambil data BTB dan Retur
$query = "
    SELECT 
        CASE WHEN mstd_typetrn = 'B' THEN 'BTB' ELSE 'RETUR' END AS status,
        mstd_nodoc AS nodoc,
        to_char(mstd_tgldoc, 'YYYY-MM-DD') AS tgldoc,
        sup_kodesupplier AS kode_supplier,
        sup_namasupplier AS nama_supplier,
        mstd_prdcd AS kode_produk,
        prd_deskripsipanjang AS deskripsi_produk,
        mstd_qty AS qty,
        mstd_gross AS gross,
        mstd_discrph AS diskon,
        mstd_ppnrph AS ppn
    FROM tbmaster_supplier
    JOIN tbtr_mstran_d ON mstd_kodesupplier = sup_kodesupplier
    JOIN tbmaster_prodmast ON mstd_prdcd = prd_prdcd
    WHERE mstd_typetrn IN ('B', 'K')
      AND to_char(mstd_tgldoc, 'YYYY') = :tahun
      AND to_char(mstd_tgldoc, 'MM') = :bulan
    ORDER BY mstd_tgldoc, mstd_nodoc
";

$stmt = $conn->prepare($query);
$stmt->bindValue(':bulan', $bulan);
$stmt->bindValue(':tahun', $tahun);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .report-container {
        margin-top: 30px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .table thead th {
        background-color: #007bff;
        color: white;
    }
    .table td {
        text-align: right;
    }
    .table td:first-child, .table th:first-child {
        text-align: left;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    
    /* Set table layout to auto for flexible column width */
    .table {
        table-layout: auto; /* This allows columns to adjust based on content */
    }
</style><style>
    .report-container {
        margin-top: 30px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .table thead th {
        background-color: #007bff;
        color: white;
    }
    .table td {
        text-align: right;
        white-space: nowrap; /* Mencegah teks dibungkus dalam sel */
    }
    .table td:first-child, .table th:first-child {
        text-align: left;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* Apply table-layout: auto for automatic column width adjustment */
    .table {
        width: 100%;
        table-layout: auto; /* Memungkinkan kolom menyesuaikan lebar berdasarkan konten */
    }
</style>


<body>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Laporan BTB dan Retur</h3>
        <a href="../LaporanBulanan/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>No Doc</th>
                                    <th>TGL DOC</th>
                                    <th>KD SUPP</th>
                                    <th>NAMA SUPP</th>
                                    <th>PLU</th>
                                    <th>DESK</th>
                                    <th>Qty</th>
                                    <th>Gross</th>
                                    <th>Diskon</th>
                                    <th>PPN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?= $row['status'] ?></td>
                                        <td><?= $row['nodoc'] ?></td>
                                        <td><?= $row['tgldoc'] ?></td>
                                        <td><?= $row['kode_supplier'] ?></td>
                                        <td><?= $row['nama_supplier'] ?></td>
                                        <td><?= $row['kode_produk'] ?></td>
                                        <td><?= $row['deskripsi_produk'] ?></td>
                                        <td><?= number_format($row['qty'], 2) ?></td>
                                        <td><?= number_format($row['gross'], 2) ?></td>
                                        <td><?= number_format($row['diskon'], 2) ?></td>
                                        <td><?= number_format($row['ppn'], 2) ?></td>
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
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: true, // Enable autoWidth to allow automatic width adjustment based on content
            columnDefs: [
                {
                    targets: [4], // Kolom "DESK" tidak dapat diurutkan
                    orderable: false
                }
            ],
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy' // Ubah teks tombol jika diperlukan
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'PO_OUT_TGL_MATI_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
                    title: null
                }
            ],
            dom: 'Bfrtip', // Posisi tombol
            initComplete: function () {
                // Adjust columns after table initialization to fit content
                this.api().columns.adjust().draw();
            }
        });

        // Tambahkan tombol ke wrapper tabel
        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>


</body>
