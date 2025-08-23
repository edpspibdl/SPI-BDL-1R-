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
                                <h5>Pilih Tanggal Periode</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalMulai">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalSelesai">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input PLU -->
                                <div class="form-group">
                                    <label for="plu">PLU (Kode Produk)</label>
                                    <input type="text" class="form-control" name="plu" id="plu" placeholder="Masukkan PLU atau kosongkan untuk semua">
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
        e.preventDefault();

        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const tanggalSelesai = document.getElementById('tanggalSelesai').value;
        const plu = document.getElementById('plu').value; // PLU

        if (!tanggalMulai || !tanggalSelesai) {
            alert("Silakan lengkapi tanggal terlebih dahulu.");
            return;
        }

        const params = new URLSearchParams({
            tanggalMulai,
            tanggalSelesai,
            plu
        }).toString();

        // Default langsung ke report_by_produk.php
        window.location.href = 'report_by_produk.php?' + params;
    });
</script>

<?php require_once '../layout/_bottom.php'; ?>