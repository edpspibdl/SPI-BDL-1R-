<?php
// FILE: form_report.php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Cek Order Plu</h1>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <form role="form" id="formReport">
                        <fieldset>
                            <div class="form-group">
                                <h5>Input Plu</h5>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="plu" id="plu" placeholder="Masukkan PLU (kosongkan untuk semua produk)">
                                    <small class="form-text text-muted">Akan menampilkan semua data order jika PLU dikosongkan.</small>
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
    document.getElementById('formReport').addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah pengiriman form default

        const plu = document.getElementById('plu').value; // Ambil nilai PLU

        // Bangun parameter URL
        const params = new URLSearchParams({
            plu: plu
        }).toString();

        // Redirect ke report.php dengan parameter PLU
        window.location.href = 'report.php?' + params;
    });
</script>

<?php require_once '../layout/_bottom.php'; ?>