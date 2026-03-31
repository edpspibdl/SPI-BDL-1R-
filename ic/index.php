<?php require_once '../layout/_top.php'; ?>

<section class="section">
    <div class="section-header">
        <h1>Monitoring SO IC</h1>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Pilih Periode</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="report.php">
                        <div class="form-group">
                            <label>Bulan & Tahun</label>
                            <input type="month" name="periode" class="form-control" 
                                   value="<?= date('Y-m') ?>" required>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-block">Tampilkan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>