<?php
require_once '_obi.class.php';

$obi = new OBI;
$rkp = $obi->rekapTgl($_POST['id']);
?>
<div class="table-responsive">
    <!-- Add a title above the table -->
    <h5 class="mb-3">REKAP RUPIAH PB</h5> <!-- Title for the table -->
    
    <table class="table table-striped table-hover table-bordered table-nonfluid compact" style="width:100%; table-layout:auto; font-size:12px">
        <thead>
            <tr>
                <th rowspan="2" class="text-center"> TANGGAL </th>
                <th colspan="4" class="text-center"> PB BATAL </th>
                <th colspan="4" class="text-center"> PB UPLOAD </th>
                <th colspan="4" class="text-center"> PB REALISASI </th>
            </tr>
            <tr>
                <th class="text-center"> PB </th>
                <th class="text-center"> ITEM </th>
                <th class="text-center"> QTY </th>
                <th class="text-center"> RUPIAH </th>
                <th class="text-center"> PB </th>
                <th class="text-center"> ITEM </th>
                <th class="text-center"> QTY </th>
                <th class="text-center"> RUPIAH </th>
                <th class="text-center"> PB </th>
                <th class="text-center"> ITEM </th>
                <th class="text-center"> QTY </th>
                <th class="text-center"> RUPIAH </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rkp as $row) { ?>
                <tr>
                    <td class="text-center"><?= $row['obi_tgl'] ?></td>
                    <td class="text-center"><?= number_format($row['pb_b'], 0, '.', ',') ?></td>
                    <td class="text-center"><?= number_format($row['item_b'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['qty_b'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['rph_b'], 0, '.', ',') ?></td>

                    <td class="text-center"><?= number_format($row['pb_s'], 0, '.', ',') ?></td>
                    <td class="text-center"><?= number_format($row['item_s'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['qty_s'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['rph_s'], 0, '.', ',') ?></td>

                    <td class="text-center"><?= number_format($row['pb_r'], 0, '.', ',') ?></td>
                    <td class="text-center"><?= number_format($row['item_r'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['qty_r'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['rph_r'], 0, '.', ',') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Import jQuery (important to be version 3.6 or latest) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Import Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Import DataTables JS and Buttons for export -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/buttons-html5.min.js"></script>
