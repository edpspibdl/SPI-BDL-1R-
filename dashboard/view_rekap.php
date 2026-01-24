<?php
require_once '_obi.class.php';

$obi = new OBI;
$rkp = $obi->rekapTgl($_POST['id']);
?>

<div class="table-responsive">
    <h5 class="mb-3 text-center font-weight-bold">REKAP RUPIAH PB</h5>

    <table class="table table-bordered table-striped table-hover compact"
           style="width:100%; font-size:12px; white-space:nowrap">
        <thead class="thead-light">
            <tr class="text-center align-middle">
                <th rowspan="2">TANGGAL</th>
                <th colspan="4">PB BATAL</th>
                <th colspan="4">PB UPLOAD</th>
                <th colspan="4">PB REALISASI</th>
            </tr>
            <tr class="text-center">
                <th>PB</th>
                <th>ITEM</th>
                <th>QTY</th>
                <th>RUPIAH</th>

                <th>PB</th>
                <th>ITEM</th>
                <th>QTY</th>
                <th>RUPIAH</th>

                <th>PB</th>
                <th>ITEM</th>
                <th>QTY</th>
                <th>RUPIAH</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($rkp)) { ?>
                <?php foreach ($rkp as $row) { ?>
                    <tr>
                        <td class="text-center"><?= $row['obi_tgl'] ?></td>

                        <!-- PB BATAL -->
                        <td class="text-right"><?= number_format($row['pb_b']) ?></td>
                        <td class="text-right"><?= number_format($row['item_b']) ?></td>
                        <td class="text-right"><?= number_format($row['qty_b']) ?></td>
                        <td class="text-right"><?= number_format($row['rph_b']) ?></td>

                        <!-- PB UPLOAD -->
                        <td class="text-right"><?= number_format($row['pb_s']) ?></td>
                        <td class="text-right"><?= number_format($row['item_s']) ?></td>
                        <td class="text-right"><?= number_format($row['qty_s']) ?></td>
                        <td class="text-right"><?= number_format($row['rph_s']) ?></td>

                        <!-- PB REALISASI -->
                        <td class="text-right"><?= number_format($row['pb_r']) ?></td>
                        <td class="text-right"><?= number_format($row['item_r']) ?></td>
                        <td class="text-right"><?= number_format($row['qty_r']) ?></td>
                        <td class="text-right"><?= number_format($row['rph_r']) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="13" class="text-center text-muted">
                        Data tidak tersedia
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
