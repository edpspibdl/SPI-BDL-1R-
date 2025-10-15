<div class="d-flex flex-wrap" style="gap: 0rem;">
                <!-- Trend Sales -->
                <div class="card w-100 p-2">
                    <div class="card-body p-2" id="cashbackContainer">
                        <h4 class="text-center mb-1 mt-1">Sales</h4>
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr class="primary text-center">
                                    <th>Bulan</th>
                                    <th>QTY</th>
                                    <th>RUPIAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($trensale)): ?>
                                    <?php foreach ($trensale as $row): ?>
                                        <?php
                                        $bulan = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'];
                                        for ($i = 1; $i <= 12; $i++):
                                            $qty = number_format($row['sls_qty_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0);
                                            $rph = number_format($row['sls_rph_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0);
                                        ?>
                                            <tr>
                                                <td><?= $bulan[$i - 1] ?></td>
                                                <td align="right"><?= $qty ?></td>
                                                <td align="right"><?= $rph ?></td>
                                            </tr>
                                        <?php endfor; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>