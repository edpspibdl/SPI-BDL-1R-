<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Monitoring Penerimaan VS Slp</h1>

    </div>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form role="form" method="get" action="report.php">
                        <fieldset>
                            <div class="form-group">
                                <h5>Pilih Tanggal Periode</h5>
                                <div class="row">
                                    <!-- Tanggal Mulai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalAwal">Tanggal Awal</label>
                                            <input type="date" class="form-control" id="tanggalAwal" name="tanggalAwal" placeholder="Pilih Tanggal Awal">
                                        </div>
                                    </div>

                                    <!-- Tanggal Selesai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalAkhir">Tanggal Akhir</label>
                                            <input type="date" class="form-control" id="tanggalAkhir" name="tanggalAkhir" placeholder="Pilih Tanggal Akhir">
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


<script>
    $(document).ready(function() {
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