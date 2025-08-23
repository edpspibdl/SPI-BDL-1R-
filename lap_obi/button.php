<?php
$tanggalxxx = date_create($tgl);
$tanggalxxx = date_format($tanggalxxx, "d/m/Y");
?>
<div class="row mb-4">
    <!-- Form Input Tanggal -->
    <div class="col-md-3">
        <form action="index.php" method="GET" class="ml-md-5">
            <div class="input-group date" id="datepicker">
                <!-- Ikon Kalender -->
                <span class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </span>
                <!-- Input Tanggal -->
                <input 
                    type="text" 
                    class="form-control" 
                    name="tanggal" 
                    id="tanggal" 
                    placeholder="dd/mm/yyyy" 
                    value="<?= isset($_GET['tanggal']) ? $_GET['tanggal'] : date('d/m/Y'); ?>">
                <!-- Tombol Submit -->
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <!-- Parameter Tersembunyi -->
            <input type="hidden" value="<?= $rpt; ?>" name="rpt" id="rpt">
        </form>
    </div>

    <!-- Tombol Pilihan Laporan -->
    <div class="col-md-9">
        <div class="btn-group btn-group-toggle ml-md-5" role="group">
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=rekap" class="btn btn-outline-primary <?= $rpt == 'rekap' ? 'active' : ''; ?>">REKAP</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=sum" class="btn btn-outline-primary <?= $rpt == 'sum' ? 'active' : ''; ?>">SUMMARY</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=perpb" class="btn btn-outline-primary <?= $rpt == 'perpb' ? 'active' : ''; ?>">PBOBI</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=pb" class="btn btn-outline-primary <?= $rpt == 'pb' ? 'active' : ''; ?>">DETAIL PB</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=real" class="btn btn-outline-primary <?= $rpt == 'real' ? 'active' : ''; ?>">REALISASI</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=seb" class="btn btn-outline-primary <?= $rpt == 'seb' ? 'active' : ''; ?>">SEBAGIAN</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=nol" class="btn btn-outline-primary <?= $rpt == 'nol' ? 'active' : ''; ?>">NOL</a>
            <a href="index.php?tanggal=<?= $tgl ?>&rpt=btl" class="btn btn-outline-primary <?= $rpt == 'btl' ? 'active' : ''; ?>">BATAL</a>
        </div>
    </div>
</div>

<!-- Tambahkan library DataTables dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi datetimepicker
        $('#datepicker').datetimepicker({
            format: 'DD/MM/YYYY',
            weekStart: 1,
            autoclose: true,
            todayHighlight: true,
            minView: 2,
            forceParse: false
        });
    });
</script>
