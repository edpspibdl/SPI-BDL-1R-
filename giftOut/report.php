<?php require_once '../layout/_top.php'; ?>

<?php
// Ambil tanggal dari parameter GET, default ke tanggal hari ini
$tanggalMulai   = isset($_GET['tanggalMulai']) && $_GET['tanggalMulai'] != "" ? $_GET['tanggalMulai'] : date('Y-m-d');
$tanggalSelesai = isset($_GET['tanggalSelesai']) && $_GET['tanggalSelesai'] != "" ? $_GET['tanggalSelesai'] : date('Y-m-d');
$jenis_rpt      = isset($_GET['jenisLaporan']) && $_GET['jenisLaporan'] != "" ? $_GET['jenisLaporan'] : '1';

// Format tanggal untuk query database (YYYYMMDD)
$tgl_mulai   = date("Ymd", strtotime($tanggalMulai));
$tgl_selesai = date("Ymd", strtotime($tanggalSelesai));
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1 class="text-dark">Cek Data Perolehan Cashback Member</h1>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <div class="row">
                    <!-- Tabel Per Produk -->
                    <div class="col-lg-8 mb-4">
                        <div class="table-responsive">
                            <?php include 'per-produk.php'; ?>
                        </div>
                    </div>

                    <!-- Tabel Per PB -->
                    <div class="col-lg-12 mb-4">
                        <div class="table-responsive">
                            <?php include 'per-pb.php'; ?>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<style>
    .grid-view {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }

    .grid-view th,
    .grid-view td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .grid-view th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
    }

    .grid-view td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .grid-view .desk-column {
        word-wrap: break-word;
        white-space: normal;
        max-width: 300px;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        // Tabel Per Produk
        var table1 = $('#GridViewProduk').DataTable({
            language: {
                search: "Cari",
                lengthMenu: "_MENU_ Baris per halaman",
                zeroRecords: "Data tidak ada",
                info: "Halaman _PAGE_ dari _PAGES_ halaman",
                infoEmpty: "Data tidak ada",
                infoFiltered: "(Filter dari _MAX_ data)"
            },
            lengthMenu: [10],
            paging: true,
            responsive: true,
            buttons: ['copy', 'excel', 'colvis']
        });
        table1.buttons().container().appendTo('#GridViewProduk_wrapper .col-sm-6:eq(0)');

        // Tabel Per PB
        var table2 = $('#GridViewPB').DataTable({
            language: {
                search: "Cari",
                lengthMenu: "_MENU_ Baris per halaman",
                zeroRecords: "Data tidak ada",
                info: "Halaman _PAGE_ dari _PAGES_ halaman",
                infoEmpty: "Data tidak ada",
                infoFiltered: "(Filter dari _MAX_ data)"
            },
            lengthMenu: [10],
            paging: true,
            responsive: true,
            buttons: ['copy', 'excel', 'colvis']
        });
        table2.buttons().container().appendTo('#GridViewPB_wrapper .col-sm-6:eq(0)');
    });
</script>