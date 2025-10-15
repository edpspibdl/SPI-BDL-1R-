<div class="d-flex flex-wrap mt-0" style="gap: 2rem;">
            <!-- Harga Jual -->
            <div class="card flex-fill" style="min-width: 400px;">
                <div class="card-body p-2" id="hargaJualContainer">
                    <h5 class="text-center mb-2 mt-2">Harga Jual</h5>
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="info">
                                <th rowspan="2" class="text-center">PLU</th>
                                <th rowspan="2" class="text-center">Satuan / Frac</th>
                                <th rowspan="2" class="text-center">Harga Jual</th>
                                <th rowspan="2" class="text-center">Tag</th>
                                <th rowspan="2" class="text-center">MinJual</th>
                                <th colspan="3" class="text-center" style="background-color:blue; color:white;">Harga Promo MD</th>
                                <th rowspan="2" class="text-center">Usulan Hrg</th>
                                <th rowspan="2" class="text-center">%</th>
                            </tr>
                            <tr>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Mulai</th>
                                <th class="text-center">Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($barcodes)): ?>
                                <?php $noUrut = 0; ?>
                                <?php foreach ($barcodes as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['prd_prdcd'] ?? '') ?></td>
                                        <td><?= htmlspecialchars(($row['prd_unit'] ?? '') . ' / ' . ($row['prd_frac'] ?? '')) ?></td>
                                        <td class="text-end"><?= number_format($row['prd_hrgjual'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['prd_kodetag'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['prd_minjual'] ?? '') ?></td>
                                        <td class="text-end"><?= number_format($row['md_hrgjual'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['prmd_tglawal'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['prmd_tglakhir'] ?? '') ?></td>

                                        <!-- Usulan Harga -->
                                        <td align="right">
                                            <input type="text" id="hrg<?= $noUrut ?>" name="hrg[]" onkeyup="sum<?= $noUrut ?>();" size="10" placeholder="">

                                            <!-- Hidden Fields -->
                                            <input type="hidden" id="avg<?= $noUrut ?>" name="avg[]" value="<?= htmlspecialchars($row['prd_avgcost'] ?? '') ?>">
                                            <input type="hidden" id="kali<?= $noUrut ?>" name="kali[]" value="<?= htmlspecialchars($row['kali'] ?? '') ?>">
                                        </td>

                                        <!-- Persentase -->
                                        <td align="right">
                                            <?php
                                            $hrgJual = $row['prd_hrgjual'] ?? 0;
                                            $avgCost = ($row['prd_avgcost'] ?? 0) * ($row['kali'] ?? 1);
                                            $percent = ($hrgJual != 0) ? (($hrgJual - $avgCost) / $hrgJual * 100) : 0;
                                            ?>
                                            <input type="text" id="prc<?= $noUrut ?>" name="prc[]" size="5"
                                                value="<?= number_format($percent, 2, '.', ',') ?>"
                                                placeholder="0" required disabled>
                                        </td>
                                    </tr>

                                    <!-- JS for dynamic calculation -->
                                    <script>
                                        function sum<?= $noUrut ?>() {
                                            let hrg = parseFloat(document.getElementById('hrg<?= $noUrut ?>').value) || 0;
                                            let avg = parseFloat(document.getElementById('avg<?= $noUrut ?>').value) || 0;
                                            let kali = parseFloat(document.getElementById('kali<?= $noUrut ?>').value) || 1;
                                            let prc = 0;

                                            if (hrg > 0) {
                                                prc = ((hrg - (avg * kali)) / hrg) * 100;
                                            }

                                            document.getElementById('prc<?= $noUrut ?>').value = prc.toFixed(2);
                                        }
                                    </script>

                                    <?php $noUrut++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>