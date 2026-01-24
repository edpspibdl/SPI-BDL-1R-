  <!-- ===== PKM (DI BAWAH STOK) ===== -->
        <div class="card-body p-0">
            <table class="table table-sm p-0" id="table-pkm">
                <thead class="theadDataTables">
                    <tr>
                        <th style="border:1px solid;border-bottom:0"></th>
                        <th style="border:1px solid;border-bottom:0"></th>
                        <th style="border:1px solid;border-bottom:0"></th>
                        <th colspan="2" class="text-center" style="border:1px solid;border-bottom:0">PKMT</th>
                        <th colspan="2" class="text-center" style="border:1px solid;border-bottom:0">MINOR</th>
                        <th colspan="2" class="text-center" style="border:1px solid;border-bottom:0">MIN DISPLAY</th>
                    </tr>
                    <tr>
                        <th class="text-center" style="border:1px solid;border-top:0">DSI</th>
                        <th class="text-center" style="border:1px solid;border-top:0">TO</th>
                        <th class="text-center" style="border:1px solid;border-top:0">TOP</th>
                        <th class="text-center" style="border:1px solid;border-top:0">QTY</th>
                        <th class="text-center" style="border:1px solid;border-top:0">TO</th>
                        <th class="text-center" style="border:1px solid;border-top:0">QTY</th>
                        <th class="text-center" style="border:1px solid;border-top:0">TO</th>
                        <th class="text-center" style="border:1px solid;border-top:0">QTY</th>
                        <th class="text-center" style="border:1px solid;border-top:0">TO</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($pkm)) : ?>
                    <?php foreach ($pkm as $row) : ?>
                        <tr>
                            <td class="text-right"><?= number_format($row['dsi'],0,'.',',') ?></td>
                            <td class="text-right">0</td>
                            <td class="text-right">0</td>
                            <td class="text-right"><?= number_format($row['pkm_pkmt'],0,'.',',') ?></td>
                            <td class="text-right">0</td>
                            <td class="text-right"><?= number_format($row['pkm_minorder'],0,'.',',') ?></td>
                            <td class="text-right">0</td>
                            <td class="text-right"><?= number_format($row['pkm_mindisplay'],0,'.',',') ?></td>
                            <td class="text-right">0</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                        <tr>
                            <?php for ($i=1;$i<=9;$i++): ?>
                                <td class="p-0">
                                    <input type="text" class="form-control text-right" disabled>
                                </td>
                            <?php endfor; ?>
                        </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>