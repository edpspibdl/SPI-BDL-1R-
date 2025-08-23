<?php
// FILE: form_report.php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>History Back Office</h1>
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

                                <div class="form-group">
                                    <label for="jenisTransaksi">Jenis Transaksi</label>
                                    <select class="form-control" name="jenisTransaksi" id="jenisTransaksi">
                                        <option value="All">All</option>
                                        <option value="B">BPB</option>
                                        <option value="L">BPB Lain-lain</option>
                                        <option value="K">Retur Supplier</option>
                                        <option value="I">Terima TAC</option>
                                        <option value="O">Kirim TAC</option>
                                        <option value="X">MPP</option>
                                        <option value="H">Hilang</option>
                                        <option value="P">Repacking</option>
                                        <option value="Z">Perubahan Status</option>
                                        <option value="F">Pemusnahan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis">Jenis Laporan</label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="produk">1. Laporan Per Produk</option>
                                        <option value="produk-detail">1B. Laporan Per Produk Detail</option>
                                        <option value="supplier">2. Laporan Per Supplier</option>
                                        <option value="divisi">3. Laporan Per Divisi</option>
                                        <option value="departement">4. Laporan Per Departement</option>
                                        <option value="ketegori">5. Laporan Per Kategori</option>
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
        const jenisTransaksi = document.getElementById('jenisTransaksi').value;

        if (!tanggalMulai || !tanggalSelesai || !jenis) {
            alert("Silakan lengkapi semua field terlebih dahulu.");
            return;
        }

        const params = new URLSearchParams({
            tanggalMulai,
            tanggalSelesai,
            jenisTransaksi
        }).toString();

        if (jenis === 'produk') {
            window.location.href = 'report_by_produk.php?' + params;
        } else if (jenis === 'produk-detail') {
            window.location.href = 'report_by_produk_detail.php?' + params;
        } else if (jenis === 'supplier') {
            window.location.href = 'report_by_supplier.php?' + params;
        } else if (jenis === 'divisi') {
            window.location.href = 'report_by_divisi.php?' + params;
        } else if (jenis === 'departement') {
            window.location.href = 'report_by_departement.php?' + params;
        } else if (jenis === 'ketegori') {
            window.location.href = 'report_by_kategori.php?' + params;
        } else {
            alert('Jenis laporan tidak dikenali: ' + jenis);
        }

    });
</script>

<?php require_once '../layout/_bottom.php'; ?>