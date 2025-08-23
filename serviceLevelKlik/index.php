<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
    <h1>Service Level <em>Offtake</em> atau <em>Selling In</em></h1>
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
                                    <!-- Tanggal Mulai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalMulai">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" required>
                                        </div>
                                    </div>

                                    <!-- Tanggal Selesai -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggalSelesai">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jenis Laporan -->
                                <div class="form-group">
                                    <label for="jenis">Jenis Laporan</label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="member">Per Member</option>
                                        <option value="produk">Per Produk</option>
                                        <option value="noPb">Per No PB</option>
                                    </select>
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
        const jenis = document.getElementById('jenis').value;

        if (!tanggalMulai || !tanggalSelesai || !jenis) {
            alert("Silakan lengkapi semua field terlebih dahulu.");
            return;
        }

        const params = new URLSearchParams({
            tanggalMulai,
            tanggalSelesai
        }).toString();

        if (jenis === 'member') {
            window.location.href = 'report_by_member.php?' + params;
        } else if (jenis === 'produk') {
            window.location.href = 'report_by_produk.php?' + params;
        } else if (jenis === 'noPb') {
            window.location.href = 'report_by_no_pb.php?' + params;
        }

    });
</script>

<?php
require_once '../layout/_bottom.php';
?>
