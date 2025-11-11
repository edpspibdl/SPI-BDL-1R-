<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan koneksi database sudah dibuat di sini
?>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>SALES DI LUAR ITEM LARANGAN</h1>
  </div>
  <div class="row">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-body">
          <form role="form" id="formReport">
            <fieldset>
              <div class="form-group">
                <h5>Pilih Periode dan Jenis Laporan</h5>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="tanggalMulai">Tanggal Mulai <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="tanggalSelesai">Tanggal Selesai <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai" required>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="jenis">Jenis Laporan <span class="text-danger">*</span></label>
                  <select class="form-control" id="jenis" name="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="member">Per Member</option>
                    <option value="produk">Per Produk</option>
                  </select>
                </div>

                <h5 class="mt-4">Filter Opsional</h5>
                <hr>

                <div class="form-group">
                  <label for="kodeMember">Kode Member</label>
                  <input type="text" class="form-control" id="kodeMember" name="kodeMember" placeholder="Masukkan kode member">
                </div>

                <div class="form-group">
                  <label for="kodePLU">Kode PLU</label>
                  <input type="text" class="form-control" id="kodePLU" name="kodePLU" placeholder="Masukkan kode PLU">
                </div>

                <div class="form-group">
                  <label for="kodeSupplier">Pilih Supplier</label>
                  <select class="form-control" id="kodeSupplier" name="kodeSupplier"></select>
                </div>
                
                <div class="form-group">
                  <label for="namaSupplier">Nama Supplier</label>
                  <input type="text" class="form-control" id="namaSupplier" name="namaSupplier" placeholder="Masukkan nama supplier">
                </div>

                <div class="form-group">
                  <label for="kodeDivisi">Kode Divisi</label>
                  <input type="text" class="form-control" id="kodeDivisi" name="kodeDivisi" placeholder="Masukkan kode divisi">
                </div>

                <div class="form-group text-center mt-4">
                  <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Bersihkan</button>
                  <button type="submit" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Tampilkan Laporan</button>
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
  // 1. Inisialisasi Select2 untuk Supplier
  $('#kodeSupplier').select2({
    placeholder: '-- Pilih Supplier --',
    allowClear: true,
    ajax: {
      url: 'get_supplier.php', // Pastikan path ini benar
      dataType: 'json',
      delay: 250,
      data: function(params) {
        return {
          q: params.term // parameter pencarian yang dikirim ke get_supplier.php
        };
      },
      processResults: function(data) {
        // Data yang diterima dari server harus diproses
        // menjadi format yang dimengerti Select2: {id: ..., text: ...}
        return {
          results: data.map(function(item) {
            return {
              id: item.sup_kodesupplier,
              text: item.sup_kodesupplier + ' - ' + item.sup_namasupplier
            };
          })
        };
      },
      cache: true
    },
    minimumInputLength: 1
  });

  // 2. Handler Submit Form
  $('#formReport').on('submit', function(e) {
    e.preventDefault();

    const tanggalMulai = $('#tanggalMulai').val();
    const tanggalSelesai = $('#tanggalSelesai').val();
    const jenis = $('#jenis').val();

    // Validasi Wajib
    if (!tanggalMulai || !tanggalSelesai || !jenis) {
      alert("⚠️ Harap lengkapi field **Tanggal Mulai**, **Tanggal Selesai**, dan **Jenis Laporan**.");
      return;
    }

    // Ambil semua data form, termasuk filter opsional
    const params = new URLSearchParams($(this).serialize()).toString();

    // Arahkan ke halaman laporan yang sesuai dengan semua parameter
    if (jenis === 'member') {
      window.location.href = 'report_by_member.php?' + params;
    } else if (jenis === 'produk') {
      window.location.href = 'report_by_produk.php?' + params;
    } else {
      alert("⚠️ Jenis Laporan tidak valid.");
    }
  });
});
</script>

<?php
require_once '../layout/_bottom.php';
?>