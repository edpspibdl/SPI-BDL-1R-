<?php
ob_start();
$allowed_ips = ['192.168.170.', '127.0.0.1']; 
$user_ip = $_SERVER['REMOTE_ADDR']; 
$allowed = false;
foreach ($allowed_ips as $ip_prefix) {
    if (strpos($user_ip, $ip_prefix) === 0) {
        $allowed = true;
        break;
    }
}
if (!$allowed) {
    header("Location: ../pageError/notaccess.php");
    exit;
}
ob_end_flush();
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>History Back Office</h1>
        <a href="../pluNoSales/index.php" class="btn btn-primary">BACK</a>
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
                                    <div class="col-md-6">
                                        <label for="tanggalMulai">Tanggal Awal</label>
                                        <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggalSelesai">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai">
                                    </div>
                                </div>
                                <div class="form-group mt-3">
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
                                    <label for="jenisLaporan">Jenis Laporan</label>
                                    <select class="form-control" name="jenisLaporan" id="jenisLaporan">
                                        <option value="1">1. Laporan per Produk</option>
                                        <option value="1B">1B. Laporan per Produk Detail</option>
                                        <option value="2">2. Laporan per Supplier</option>
                                        <option value="3">3. Laporan per Divisi</option>
                                        <option value="4">4. Laporan per Departemen</option>
                                        <option value="5">5. Laporan per Kategori</option>
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
<?php require_once '../layout/_bottom.php'; ?>