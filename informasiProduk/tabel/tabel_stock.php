<!-- STOK + PKM -->
            <div style="flex: 3; min-width: 700px;">
                <!-- Card STOK -->
                <div class="card mb-4">
                    <h4 class="text-center mb-2 mt-2">Stock</h4>
                    <div class="card-body p-2">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-success">
                                <tr>
                                    <th>LOKASI</th>
                                    <th>AWAL</th>
                                    <th>TERIMA</th>
                                    <th>KELUAR</th>
                                    <th>SALES</th>
                                    <th>RETUR</th>
                                    <th>ADJ</th>
                                    <th>INTRANSIT</th>
                                    <th>SO</th>
                                    <th>AKHIR</th>
                                    <th>AKHIR LPP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stok)): ?>
                                    <?php foreach ($stok as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                            <td><?= number_format($row['st_saldoawal']) ?></td>
                                            <td><?= number_format($row['st_trfin']) ?></td>
                                            <td><?= number_format($row['st_trfout']) ?></td>
                                            <td><?= number_format($row['st_sales']) ?></td>
                                            <td><?= number_format($row['st_retur']) ?></td>
                                            <td><?= number_format($row['st_adj']) ?></td>
                                            <td><?= number_format($row['st_intransit']) ?></td>
                                            <td><?= number_format($row['so']) ?></td>
                                            <td><?= number_format($row['st_saldoakhir']) ?></td>
                                            <td><?= number_format($row['st_saldoakhir_lpp']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9">Data tidak ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>