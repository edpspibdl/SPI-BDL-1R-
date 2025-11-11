<!-- Produk dan Promo MD berdampingan -->
<div class="row promo-section">
  <!-- Kontainer Produk -->
  <div class="col-md-7 mt-0">
    <div id="produkContainer" class="card shadow-sm border-0">
      <div class="card-header py-2 px-3 border-bottom">
        <h6 class="m-0 text-primary fw-semibold" style="font-size: 0.9rem;">Deskripsi Produk</h6>
      </div>
      <div class="card-body p-2">
        <table class="table table-bordered table-sm mb-0 align-middle">
          <tbody style="font-size: 0.85rem;">
            <tr>
              <td><strong>PLU</strong></td>
              <td><span id="kodePLU" class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_prdcd'] ?? '-') ?></span></td>
              <td><strong>Flag Gdg</strong></td>
              <td><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_flaggudang'] ?? '-') ?></span></td>
              <td><strong>Kd Cabang</strong></td>
              <td><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_kodecabang'] ?? '-') ?></span></td>
              <td><strong>Kd Tag</strong></td>
              <td onclick="loadModalInfoTag()" style="cursor: pointer;">
                <span class="form-control form-control-sm bg-info text-center text-white fw-semibold">
                  <?= htmlspecialchars($data['prd_kodetag'] ?? '-') ?>
                </span>
              </td>
            </tr>
            <tr>
              <td><strong>Product</strong></td>
              <td colspan="3"><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_deskripsipanjang'] ?? '-') ?></span></td>
              <td><strong>Kat. Toko</strong></td>
              <td colspan="3"><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_kategoritoko'] ?? '-') ?></span></td>
            </tr>
            <tr>
              <td><strong>Kat. Brg</strong></td>
              <td colspan="3"><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['div_dept_kat'] ?? '-') ?></span></td>
              <td><strong>Upd.</strong></td>
              <td colspan="3"><span class="form-control form-control-sm bg-light"><?= htmlspecialchars($data['prd_create_dt'] ?? '-') ?></span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Kontainer Harga Khusus -->
  <div class="col-md-5 mt-0">
    <div id="tabelBaruContainer" class="card shadow-sm border-0">
      <div class="card-header py-2 px-3 border-bottom">
        <h6 class="m-0 text-primary fw-semibold" style="font-size: 0.9rem;">Harga Khusus</h6>
      </div>
      <div class="card-body p-2">
        <table class="table table-bordered table-sm mb-0 align-middle">
          <thead class="text-center" style="font-size: 0.8rem;">
            <tr>
              <th style="width: 15%;">PLU</th>
              <th style="width: 20%;">Harga Khusus</th>
              <th style="width: 20%;">Mulai</th>
              <th style="width: 20%;">Selesai</th>
              <th style="width: 25%;">Keterangan</th>
            </tr>
          </thead>
          <tbody style="font-size: 0.8rem;">
            <?php if (!empty($Hjk)): ?>
              <?php foreach ($Hjk as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['hgk_prdcd']) ?></td>
                  <td class="text-end"><?= number_format($row['hgk_hrgjual'], 0, '.', ',') ?></td>
                  <td class="text-center"><?= htmlspecialchars($row['hgk_tglawal']) ?></td>
                  <td class="text-center"><?= htmlspecialchars($row['hgk_tglakhir']) ?></td>
                  <td><?= htmlspecialchars($row['hgk_hariaktif']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada data harga khusus.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
