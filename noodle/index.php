<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>NOODLE</h1>
        <a href="../LaporanBulanan/index.php" class="btn btn-primary">BACK</a>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form role="form" method="get" action="report.php">
                        <fieldset>
                            <div class="form-group">
                                <h5>Pilih Tanggal Periode Noodle</h5>
                                <div class="row">
                                    <!-- Tanggal Mulai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalMulai">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" placeholder="Pilih Tanggal Mulai" >
                                        </div>
                                    </div>

                                    <!-- Tanggal Selesai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalSelesai">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai" placeholder="Pilih Tanggal Selesai" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="reset" class="btn btn-secondary">Bersihkan</button>
                                    <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                                </div>
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
        $('#tanggalMulai').datepicker({
            format: 'dd-M-yyyy',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true
        });

        $('#tanggalSelesai').datepicker({
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
