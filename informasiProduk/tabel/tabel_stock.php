<!-- ================= STOK ================= -->
    <div class="col-sm-8">
        <div class="card-body p-0">
            <table class="table table-sm justify-content-md-center p-0" id="table-stock">
                <thead class="theadDataTables">
                    <tr>
                        <th class="text-center" colspan="11" scope="colgroup">S T O K</th>
                    </tr>
                    <tr class="justify-content-lg-center">
                        <th class="text-center small">LOKASI</th>
                        <th class="text-center small">AWAL</th>
                        <th class="text-center small">TERIMA</th>
                        <th class="text-center small">KELUAR</th>
                        <th class="text-center small">SALES</th>
                        <th class="text-center small">RETUR</th>
                        <th class="text-center small">ADJ</th>
                        <th class="text-center small">INSTRST</th>
                        <th class="text-center small">SO</th>
                        <th class="text-center small">AKHIR</th>
                        <th class="text-center small">AKHIR LPP</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($stok)) : ?>
                    <?php foreach ($stok as $row) : ?>
                        <tr class="baris">
                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                            <td class="text-right"><?= number_format($row['st_saldoawal']) ?></td>
                            <td class="text-right"><?= number_format($row['st_trfin']) ?></td>
                            <td class="text-right"><?= number_format($row['st_trfout']) ?></td>
                            <td class="text-right"><?= number_format($row['st_sales']) ?></td>
                            <td class="text-right"><?= number_format($row['st_retur']) ?></td>
                            <td class="text-right"><?= number_format($row['st_adj']) ?></td>
                            <td class="text-right"><?= number_format($row['st_intransit']) ?></td>
                            <td class="text-right"><?= number_format($row['so']) ?></td>
                            <td class="text-right"><?= number_format($row['st_saldoakhir']) ?></td>
                            <td class="text-right"><?= number_format($row['st_saldoakhir_lpp']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr class="baris">
                        <?php for ($i=1; $i<=11; $i++): ?>
                            <td class="p-0">
                                <input type="text" class="form-control text-right" disabled>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>