<?php
// Expect $data and $trensale are defined here

?>
<style>
    /* Styles sama seperti sebelumnya */
    body { font-family: Arial, sans-serif; font-size: 12px; background-color: #f9f9f9; }
    .section-box { margin-top: 10px; padding: 8px; border: 1px solid #ccc; background: #fff; }
    table { width: 100%; border-collapse: collapse; background-color: white; }
    table, th, td { border: 1px solid #ccc; }
    th { background-color: #0074D9; color: white; }
    td, th { padding: 4px; text-align: center; }
    .header-table td { text-align: left; background-color: #f1f1f1; }
    .section-title { font-weight: bold; margin-top: 10px; margin-bottom: 5px; }
</style>

<div class="section-box">
    <table class="header-table">
        <tr>
            <td>PLU</td>
            <td><input type="text" value="<?= htmlspecialchars($data['prd_prdcd'] ?? '') ?>" readonly></td>
            <td>Flag Gdg</td>
            <td><input type="text" value="<?= htmlspecialchars($data['prd_flaggudang'] ?? '') ?>" readonly></td>
            <td>Kd Cabang</td>
            <td><input type="text" value="<?= htmlspecialchars($data['prd_kodecabang'] ?? '') ?>" readonly></td>
        </tr>
        <tr>
            <td>Product</td>
            <td colspan="3"><input type="text" value="<?= htmlspecialchars($data['prd_deskripsipanjang'] ?? '') ?>" readonly></td>
            <td>Kat. Toko</td>
            <td><input type="text" value="<?= htmlspecialchars($data['prd_kategoritoko'] ?? '') ?>" readonly></td>
        </tr>
        <tr>
            <td>Kat.Brg</td>
            <td colspan="3"><input type="text" value="<?= htmlspecialchars($data['div_dept_kat'] ?? '') ?>" readonly></td>
            <td>Upd.</td>
            <td><input type="text" value="<?= htmlspecialchars($data['prd_create_dt'] ?? '') ?>" readonly></td>
        </tr>
    </table>
</div>

<div style="margin-top: 20px;">
    <div class="section-title">TREND SALES</div>
    <table>
        <thead>
            <tr><th>Bulan</th><th>QTY</th><th>RUPIAH</th></tr>
        </thead>
        <tbody>
            <?php if (!empty($trensale)): ?>
                <?php foreach ($trensale as $row): ?>
                    <tr><td>JAN</td><td><?= number_format($row['sls_qty_01'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_01'] ?? 0, 2) ?></td></tr>
                    <tr><td>FEB</td><td><?= number_format($row['sls_qty_02'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_02'] ?? 0, 2) ?></td></tr>
                    <tr><td>MAR</td><td><?= number_format($row['sls_qty_03'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_03'] ?? 0, 2) ?></td></tr>
                    <tr><td>APR</td><td><?= number_format($row['sls_qty_04'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_04'] ?? 0, 2) ?></td></tr>
                    <tr><td>MEI</td><td><?= number_format($row['sls_qty_05'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_05'] ?? 0, 2) ?></td></tr>
                    <tr><td>JUN</td><td><?= number_format($row['sls_qty_06'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_06'] ?? 0, 2) ?></td></tr>
                    <tr><td>JUL</td><td><?= number_format($row['sls_qty_07'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_07'] ?? 0, 2) ?></td></tr>
                    <tr><td>AGS</td><td><?= number_format($row['sls_qty_08'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_08'] ?? 0, 2) ?></td></tr>
                    <tr><td>SEP</td><td><?= number_format($row['sls_qty_09'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_09'] ?? 0, 2) ?></td></tr>
                    <tr><td>OKT</td><td><?= number_format($row['sls_qty_10'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_10'] ?? 0, 2) ?></td></tr>
                    <tr><td>NOV</td><td><?= number_format($row['sls_qty_11'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_11'] ?? 0, 2) ?></td></tr>
                    <tr><td>DES</td><td><?= number_format($row['sls_qty_12'] ?? 0, 2) ?></td><td><?= number_format($row['sls_rph_12'] ?? 0, 2) ?></td></tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">Data tidak ditemukan</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
