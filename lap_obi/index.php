<?php require_once '../layout/_top.php'; ?>

<section class="section">
    <div class="section-header">
        <h1 class="text-primary">Dashboard</h1>
        <?php
        // Set timezone
        date_default_timezone_set('Asia/Jakarta');

        // Validasi dan set tanggal default
        $tgl = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : date("d-m-Y");

        // Validasi dan set laporan default
        $rpt = isset($_GET['rpt']) && !empty($_GET['rpt']) ? htmlspecialchars($_GET['rpt']) : "rekap";
        ?>
    </div>
</section>

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
    <div class="card bg-primary text-white" style="color: white;">
    <h4 class="mb-3 mt-3 ml-5">Laporan OBI <?= $tgl ?></h4>
</div>
        <div class="card-body">
            <?php include 'button.php'; ?>
            <div class="container-fluid p-0 mt-3">
                <?php
                // Pilihan laporan berdasarkan parameter 'rpt'
                switch ($rpt) {
                    case 'rekap':
                        include 'lap/rekap.php';
                        include 'pbday.php';
                        break;
                    case 'sum':
                        include 'lap/summary.php';
                        break;
                    case 'perpb':
                        include 'lap/perPB.php';
                        break;
                    case 'pb':
                        include 'lap/pbobi.php';
                        break;
                    case 'btl':
                        include 'lap/batal.php';
                        break;
                    case 'real':
                        include 'lap/real.php';
                        break;
                    case 'nol':
                        include 'lap/nol.php';
                        break;
                    case 'seb':
                        include 'lap/sebagian.php';
                        break;
                    default:
                        echo "<div class='alert alert-danger' role='alert'>Laporan tidak ditemukan.</div>";
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan library DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi DataTable
        var GridView = $('#GridView').DataTable({
            "language": {
                "search": "Cari",
                "lengthMenu": "_MENU_ Baris per halaman",
                "zeroRecords": "Data tidak ada",
                "info": "Halaman _PAGE_ dari _PAGES_ halaman",
                "infoEmpty": "Data tidak ada",
                "infoFiltered": "(Filter dari _MAX_ data)"
            },
            lengthChange: true,
            lengthMenu: [5, 10, 25, 50, 75, 100],
            paging: true,
            responsive: true,
            buttons: ['copy', 'excel', 'colvis']
        });

        // Tambahkan tombol DataTables ke halaman
        GridView.buttons().container()
            .appendTo('#GridView_wrapper .col-sm-6:eq(0)');

        // Tampilkan tabel
        $('#GridView').show();
        GridView.columns.adjust().draw();
    });
</script>

<?php require_once '../layout/_bottom.php'; ?>
