<div class="d-flex flex-wrap" style="gap: 0rem;">
    <?php if (!empty($trensale)): ?>
        <!-- Tampilkan card hanya jika ada data -->
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
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Kalau tidak ada data -->
        <div class="alert alert-danger text-center p-2 mb-0" role="alert" style="font-size: 14px; display:inline-block;">
            Tidak ada data promosi Sales
        </div>
    <?php endif; ?>
</div>
