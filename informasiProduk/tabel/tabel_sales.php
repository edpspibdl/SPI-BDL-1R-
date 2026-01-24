<div class="row">

    <!-- ================= TREND SALES ================= -->
    <div class="col-sm-4">
        <div class="card-body p-0">
            <table class="table table-sm justify-content-md-center col-sm-12 p-0" id="table-trendsales">
                <thead class="theadDataTables">
                    <tr>
                        <th class="text-center" colspan="3" scope="colgroup">TREND SALES</th>
                    </tr>
                    <tr>
                        <th width="20%"></th>
                        <th width="40%" class="text-center small">QTY</th>
                        <th width="40%" class="text-center small">RUPIAH</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $bulan = ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES'];

                if (!empty($trensale)) :
                    foreach ($trensale as $row) :
                        for ($i = 1; $i <= 12; $i++) :
                            $idx = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $qty = number_format($row['sls_qty_'.$idx] ?? 0);
                            $rph = number_format($row['sls_rph_'.$idx] ?? 0);
                ?>
                    <tr class="baris">
                        <td class="p-0 text-center"><?= $bulan[$i-1] ?></td>
                        <td class="p-0 text-right"><?= $qty ?></td>
                        <td class="p-0 text-right"><?= $rph ?></td>
                    </tr>
                <?php
                        endfor;
                    endforeach;
                else :
                    for ($i = 1; $i <= 12; $i++) :
                        $idx = str_pad($i, 2, '0', STR_PAD_LEFT);
                ?>
                    <tr class="baris">
                        <td class="p-0 text-center"><?= $bulan[$i-1] ?></td>
                        <td class="p-0">
                            <input type="text" class="form-control text-right" disabled id="sls_qty_<?= $idx ?>">
                        </td>
                        <td class="p-0">
                            <input type="text" class="form-control text-right" disabled id="sls_rph_<?= $idx ?>">
                        </td>
                    </tr>
                <?php
                    endfor;
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>