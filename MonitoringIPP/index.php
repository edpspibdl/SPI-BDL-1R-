<?php
require_once '../layout/_top.php';
?>
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>MONITORING IPP - PB DI ATAS JAM 16:45</h1>
        <div class="d-flex ml-auto">
            <a href="./tabel_nextday.php" class="btn btn-danger">Nextday</a>
        </div>
    </div>

    <div class="row">
        <!-- Rekap -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Rekap Delimen</h4>
                </div>
                <div class="card-body">
                    <?php
                    include 'query-rekap.php';
                    include 'tabel-rekap.php';
                    ?>
                </div>
            </div>
        </div>

        <!-- Detail -->
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>PB KIRIM IPP (BO)</h4>
                </div>
                <div class="card-body">
                    <?php
                    include 'query.php';
                    include 'tabel.php';
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<
    <!-- jQuery + Bootstrap + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#gridViewTable').DataTable({
                scrollX: true,
                autoWidth: false,
                responsive: false
            });
        });
    </script>

    <?php require_once '../layout/_bottom.php'; ?>