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
$query = "
    SELECT
        mstd_kodedivisi,
        mstd_kodedepartement,
        mstd_kodekategoribrg,
        mstd_prdcd,
        prd_deskripsipanjang,
        mstd_frac,
        mstd_kodesupplier,
        mstd_hrgsatuan,
        sup_namasupplier,
        mstd_tglpo,
        mstd_nopo,
        tpod_qtypo / mstd_frac AS po_ctn,
        tpod_qtypo AS po_pcs,
        tpod_gross AS po_excl,
        tpod_gross + tpod_ppn AS po_incl,
        mstd_tgldoc,
        mstd_nodoc,
        mstd_qty / mstd_frac AS bpb_ctn,
        mstd_qty AS bpb_pcs,
        mstd_gross AS bpb_excl,
        mstd_gross + mstd_ppnrph AS bpb_incl,
        CASE 
            WHEN mstd_recordid IS NULL THEN 'PO_REALISASI'
            ELSE 'PO_MATI' 
        END AS KET
    FROM
        tbtr_mstran_d
        JOIN tbmaster_prodmast ON prd_prdcd = mstd_prdcd
        LEFT JOIN tbmaster_supplier ON sup_kodesupplier = mstd_kodesupplier
        JOIN tbtr_po_d ON tpod_nopo = mstd_nopo
             AND tpod_prdcd = mstd_prdcd
    WHERE
        mstd_typetrn = 'B' -- B = BPB
        AND mstd_recordid IS NULL -- null = realisasi , 1 = batal
        AND to_char(mstd_tglpo,'YYYYMMDD') BETWEEN :tanggalMulai AND :tanggalSelesai
        AND (mstd_kodedepartement || mstd_kodekategoribrg) IN (
            SELECT
                (kat_kodedepartement || kat_kodekategori)
            FROM
                tbmaster_kategori
            WHERE
                kat_namakategori LIKE '%NOODLE%'
        ) -- kategoribarang input
        AND sup_namasupplier LIKE '%INDOMARCO ADI PRIMA%'
    ORDER BY mstd_tglpo ASC;
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
        <h3 class="text-center">NOODLE Report</h3>
        <a href="../LaporanBulanan/index.php" class="btn btn-primary">BACK</a>
    </div>

    <!-- Check if there are results and display the table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>Divisi</th>
                                    <th>Departemen</th>
                                    <th>Kategori Barang</th>
                                    <th>PLU</th>
                                    <th>Deskripsi Produk</th>
                                    <th>Frac</th>
                                    <th>Kode Supplier</th>
                                    <th>Harga Satuan</th>
                                    <th>Nama Supplier</th>
                                    <th>Tanggal PO</th>
                                    <th>No. PO</th>
                                    <th>PO (CTN)</th>
                                    <th>PO (PCS)</th>
                                    <th>PO (Excl)</th>
                                    <th>PO (Incl)</th>
                                    <th>Tanggal Dokumen</th>
                                    <th>No. Dokumen</th>
                                    <th>BPB (CTN)</th>
                                    <th>BPB (PCS)</th>
                                    <th>BPB (Excl)</th>
                                    <th>BPB (Incl)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?= $row['mstd_kodedivisi'] ?></td>
                                        <td><?= $row['mstd_kodedepartement'] ?></td>
                                        <td><?= $row['mstd_kodekategoribrg'] ?></td>
                                        <td><?= $row['mstd_prdcd'] ?></td>
                                        <td><?= $row['prd_deskripsipanjang'] ?></td>
                                        <td><?= $row['mstd_frac'] ?></td>
                                        <td><?= $row['mstd_kodesupplier'] ?></td>
                                        <td><?= number_format($row['mstd_hrgsatuan'], 2) ?></td>
                                        <td><?= $row['sup_namasupplier'] ?></td>
                                        <td><?= date('d-M-Y', strtotime($row['mstd_tglpo'])) ?></td>
                                        <td><?= $row['mstd_nopo'] ?></td>
                                        <td><?= number_format($row['po_ctn'], 2) ?></td>
                                        <td><?= number_format($row['po_pcs'], 2) ?></td>
                                        <td><?= number_format($row['po_excl'], 2) ?></td>
                                        <td><?= number_format($row['po_incl'], 2) ?></td>
                                        <td><?= date('d-M-Y', strtotime($row['mstd_tgldoc'])) ?></td>
                                        <td><?= $row['mstd_nodoc'] ?></td>
                                        <td><?= number_format($row['bpb_ctn'], 2) ?></td>
                                        <td><?= number_format($row['bpb_pcs'], 2) ?></td>
                                        <td><?= number_format($row['bpb_excl'], 2) ?></td>
                                        <td><?= number_format($row['bpb_incl'], 2) ?></td>
                                        <td><?= $row['ket'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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
