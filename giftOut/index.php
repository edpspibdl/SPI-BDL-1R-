<?php require_once '../layout/_top.php'; ?>

<section class="section">
	<div class="section-header">
		<h1 class="text-center w-100">Pilih Tanggal Periode</h1>
	</div>

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-8 col-md-10">
				<div class="card shadow">
					<div class="card-body">
						<form action="report.php" method="GET">
							<div class="row mb-3">
								<!-- Tanggal Mulai -->
								<div class="col-md-6 mb-3">
									<label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
									<input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" required>
								</div>

								<!-- Tanggal Selesai -->
								<div class="col-md-6 mb-3">
									<label for="tanggalSelesai" class="form-label">Tanggal Selesai</label>
									<input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai" required>
								</div>
							</div>




							<!-- Jenis Laporan -->
							<div class="form-group">
								<label for="jenisLaporan">Jenis Laporan</label>
								<select class="form-control" id="jenisLaporan" name="jenisLaporan" required>
									<option value="">-- Pilih Jenis --</option>
									<option value="1">PerPB - PerProduk</option>
								</select>
							</div>


							<!-- Tombol -->
							<div class="text-end">
								<button type="reset" class="btn btn-secondary me-2">Bersihkan</button>
								<button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once '../layout/_bottom.php'; ?>