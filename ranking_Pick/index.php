<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>MONITORING USER PICKING</h1>
        <a href="../pluNoSales/index.php" class="btn btn-primary">BACK</a>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form role="form" method="get" action="report.php">
                        <fieldset>
                            <h5 class="text-center mb-3">Pilih Tanggal Periode</h5>
                            <div class="form-group">
                                <label for="tanggalAwal">Tanggal Awal</label>
                                <input type="date" class="form-control" id="tanggalAwal" name="tanggalAwal">
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" class="btn btn-secondary">Bersihkan</button>
                                <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add the required CSS and JS libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Initialize Bootstrap Datepicker
        $('#tanggalAwal').datepicker({
            format: 'dd-M-yyyy',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true
        });
    });
</script>

<?php
require_once '../layout/_bottom.php';
?>
