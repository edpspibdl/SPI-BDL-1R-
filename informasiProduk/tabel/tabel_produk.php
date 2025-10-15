<!-- Produk dan Promo MD berdampingan -->
        <div class="row">
            <!-- Kontainer Produk -->
            <div class="col-md-7 mt-0">
                <div id="produkContainer">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td><strong>PLU</strong></td>
                                <td><span id="kodePLU" class="form-control form-control-md bg-light text-left"><?= htmlspecialchars($data['prd_prdcd'] ?? '-') ?></span></td>
                                <td><strong>Flag Gdg</strong></td>
                                <td><span class="form-control form-control-md bg-light"><?= htmlspecialchars($data['prd_flaggudang'] ?? '-') ?></span></td>
                                <td><strong>Kd Cabang</strong></td>
                                <td><span class="form-control form-control-md bg-light"><?= htmlspecialchars($data['prd_kodecabang'] ?? '-') ?></span></td>
                                <td><strong>Kd Tag</strong></td>
                                <td onclick="loadModalInfoTag()" style="cursor: pointer;">
                                    <span class="form-control form-control-md bg-info d-flex justify-content-between align-items-center text-center m-0">
                                        <?= htmlspecialchars($data['prd_kodetag'] ?? '-') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Product</strong></td>
                                <td colspan="3"><span class="form-control form-control-md bg-light text-left"><?= htmlspecialchars($data['prd_deskripsipanjang'] ?? '-') ?></span></td>
                                <td><strong>Kat. Toko</strong></td>
                                <td colspan="3"><span class="form-control form-control-md bg-light "><?= htmlspecialchars($data['prd_kategoritoko'] ?? '-') ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Kat. Brg</strong></td>
                                <td colspan="3"><span class="form-control form-control-md bg-light text-left"><?= htmlspecialchars($data['div_dept_kat'] ?? '-') ?></span></td>
                                <td><strong>Upd.</strong></td>
                                <td colspan="3"><span class="form-control form-control-md bg-light"><?= htmlspecialchars($data['prd_create_dt'] ?? '-') ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tabel Baru di Sebelah Kanan -->
            <div class="col-md-5 mt-0">
                <div id="tabelBaruContainer">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr class="primary">
                                <th rowspan="2" class="text-center">PLU</th>
                                <th rowspan="2" class="text-center">Harga Khusus</th>
                                <th colspan="1" class="text-center">Mulai</th>
                                <th colspan="1" class="text-center">Selesai</th>
                                <th rowspan="2" class="text-center">Keterangan</th>

                            </tr>
                            <tr class="primary">
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($Hjk)): ?>
                                <?php foreach ($Hjk as $row): ?>
                                    <tr>
                                        <td align="left"><?= htmlspecialchars($row['hgk_prdcd']) ?></td>
                                        <td align="right"><?= number_format($row['hgk_hrgjual'], 0, '.', ',') ?></td>
                                        <td align="center"><?= htmlspecialchars($row['hgk_tglawal']) ?></td>
                                        <td align="center"><?= htmlspecialchars($row['hgk_tglakhir']) ?></td>
                                        <td align="left"><?= htmlspecialchars($row['hgk_hariaktif']) ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data harga khusus.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>