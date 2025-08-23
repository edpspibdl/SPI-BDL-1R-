<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header">
        <h1>Form Laporan Sales</h1>
    </div>
    <div class="container">
        <form id="laporanForm" method="POST" class="shadow p-4 rounded bg-light">
            <!-- Jenis Laporan -->
            <div class="form-group mb-4">
                <label for="jenisLaporan"><strong>Jenis Laporan:</strong></label>
                <select name="jenisLaporan" id="jenisLaporan" class="form-control" required>
                    <option value="">-- Pilih Jenis Laporan --</option>
                    <option value="produk">Produk</option>
                    <option value="member">Member</option>
                </select>
            </div>

            <!-- Rentang Tanggal -->
            <div class="row">
                <!-- Bulan 1 -->
                <div class="col-md-4">
                    <h5 class="text-center">BULAN 1<br><small class="text-muted">(Sebelum Promosi)</small></h5>
                    <div class="form-group">
                        <label for="tanggalAwal1">Tanggal Awal:</label>
                        <input type="date" name="tanggalAwal1" id="tanggalAwal1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggalAkhir1">Tanggal Akhir:</label>
                        <input type="date" name="tanggalAkhir1" id="tanggalAkhir1" class="form-control" required>
                    </div>
                </div>

                <!-- Bulan 2 -->
                <div class="col-md-4">
                    <h5 class="text-center">BULAN 2<br><small class="text-muted">(Selama Promosi)</small></h5>
                    <div class="form-group">
                        <label for="tanggalAwal2">Tanggal Awal:</label>
                        <input type="date" name="tanggalAwal2" id="tanggalAwal2" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggalAkhir2">Tanggal Akhir:</label>
                        <input type="date" name="tanggalAkhir2" id="tanggalAkhir2" class="form-control" required>
                    </div>
                </div>

                <!-- Bulan 3 -->
                <div class="col-md-4">
                    <h5 class="text-center">BULAN 3<br><small class="text-muted">(Setelah Promosi)</small></h5>
                    <div class="form-group">
                        <label for="tanggalAwal3">Tanggal Awal:</label>
                        <input type="date" name="tanggalAwal3" id="tanggalAwal3" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggalAkhir3">Tanggal Akhir:</label>
                        <input type="date" name="tanggalAkhir3" id="tanggalAkhir3" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Generate Report</button>
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('laporanForm').addEventListener('submit', function (e) {
        const jenis = document.getElementById('jenisLaporan').value;

        if (jenis === "produk") {
            this.action = "report_by_produk.php";
        } else if (jenis === "member") {
            this.action = "report_by_member.php";
        } else {
            alert("Jenis laporan belum diarahkan. Silakan lengkapi script untuk jenis laporan lainnya.");
            e.preventDefault(); // Hentikan submit sementara
        }
    });
</script>


<?php require_once '../layout/_bottom.php'; ?>
