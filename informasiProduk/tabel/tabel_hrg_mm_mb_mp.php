<!-- Harga Member -->
            <div class="card flex-fill" style="min-width: 400px;">
                <div class="card-body p-2" id="hrgMmContainer">
                    <h5 class="text-center mb-2 mt-2">Harga Member MM/MB/MP</h5>
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="info">
                                <th rowspan="2" class="text-center">PLU</th>
                                <th colspan="3" class="text-center" style="background-color:red; color:white;">Member Merah</th>
                                <th colspan="3" class="text-center" style="background-color:blue; color:white;">Member Biru</th>
                                <th colspan="3" class="text-center" style="background-color:black; color:white;">Member Platinum</th>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <th>CB</th>
                                <th>Harga Net</th>
                                <th>Harga</th>
                                <th>CB</th>
                                <th>Harga Net</th>
                                <th>Harga</th>
                                <th>CB</th>
                                <th>Harga Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($hrgMember)): ?>
                                <?php foreach ($hrgMember as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['plu'] ?? '') ?></td>
                                        <td class="text-end"><?= number_format($row['hrgmm'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbmm'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['hrg_netmm'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['hrgbiru'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbbiru'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['hrg_netbiru'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['hrgpla'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbpla'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['hrg_netpla'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>