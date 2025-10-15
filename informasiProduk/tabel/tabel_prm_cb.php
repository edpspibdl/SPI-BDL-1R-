<div class="d-flex flex-wrap mt-0" style="gap: 2rem;">
            <!-- Cashback Card -->
            <div class="card w-100">
                <div class="card-body p-2" id="cashbackContainer">
                    <h4 class="text-center mb-3 mt-1">Promo Cashback</h4>
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="primary">
                                <th rowspan="2" class="text-center">KD Promo</th>
                                <th rowspan="2" class="text-center">Nama Promosi</th>
                                <th colspan="3" class="text-center">Minimum Beli / Struk</th>
                                <th rowspan="2" class="text-center">Nilai CB</th>
                                <th rowspan="2" class="text-center">Sisa</th>
                                <th colspan="4" class="text-center">Maximum Beli / Struk</th>
                                <th colspan="2" class="text-center">Periode</th>
                                <th rowspan="2" class="text-center">Jenis Member</th>
                            </tr>
                            <tr>
                                <th>Qty</th>
                                <th>Sponsor Rp.</th>
                                <th>Total Rp.</th>
                                <th>Struk</th>
                                <th>Member / Hari</th>
                                <th>Frek / Event</th>
                                <th>Rph / Event</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($csbk)): ?>
                                <?php foreach ($csbk as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['cbd_kodepromosi'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['cbh_namapromosi'] ?? '') ?></td>
                                        <td class="text-end"><?= number_format($row['cbd_minstruk'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbh_minrphprodukpromo'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbh_mintotbelanja'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbd_cashback'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbk_sisa'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format($row['cbd_maxstruk'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars($row['cbd_maxmemberperhari'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['cbd_maxfrekperevent'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['cbd_maxrphperevent'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['cbh_tglawal'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['cbh_tglakhir'] ?? '') ?></td>
                                        <td>
                                            <?php
                                            $jenisMember = '';
                                            $jenisMember .= ($row['cba_reguler'] == '1') ? 'REG ' : '';
                                            $jenisMember .= ($row['cba_reguler_biruplus'] == '1') ? 'RB+ ' : '';
                                            $jenisMember .= ($row['cba_freepass'] == '1') ? 'FRE ' : '';
                                            $jenisMember .= ($row['cba_retailer'] == '1') ? 'RET ' : '';
                                            $jenisMember .= ($row['cba_silver'] == '1') ? 'SIL ' : '';
                                            $jenisMember .= ($row['cba_gold1'] == '1') ? 'GD1 ' : '';
                                            $jenisMember .= ($row['cba_gold2'] == '1') ? 'GD2 ' : '';
                                            $jenisMember .= ($row['cba_gold3'] == '1') ? 'GD3 ' : '';
                                            $jenisMember .= ($row['cba_platinum'] == '1') ? 'PLA ' : '';
                                            echo htmlspecialchars(trim($jenisMember));
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>