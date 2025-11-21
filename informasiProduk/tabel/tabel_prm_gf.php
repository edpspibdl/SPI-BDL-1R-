<div class="d-flex flex-wrap mt-0" style="gap: 2rem;">
    <?php if (!empty($Gift)): ?>
        <!-- Hanya tampil jika ada data -->
        <div class="card w-100">
            <div class="card-body p-2" id="giftContainer">
                <h4 class="text-center mb-3 mt-1">Promo Gift</h4>

                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr class="primary">
                            <th rowspan="2" class="text-center">Nama Promosi GIFT</th>
                            <th colspan="2" class="text-center">Minimum Beli</th>
                            <th colspan="2" class="text-center">Minimum Total Belanja</th>
                            <th colspan="2" class="text-center">Maximum Total Belanja</th>
                            <th colspan="2" class="text-center">Maximum Per Event</th>
                            <th colspan="2" class="text-center">Hadiah</th>
                            <th colspan="2" class="text-center">Periode</th>
                            <th rowspan="2" class="text-center">Jenis Member</th>
                        </tr>
                        <tr class="primary">
                            <th>Qty</th>
                            <th>Rph</th>
                            <th>Struk</th>
                            <th>Sponsor</th>
                            <th>Jumlah Hari</th>
                            <th>Frek Hari</th>
                            <th>Jumlah Event</th>
                            <th>Frek Event</th>
                            <th>Qty</th>
                            <th>Nama</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($Gift as $row): ?>
                            <tr>
                                <td align="left"><?= $row['gif_kode_promosi'] . ' ' . $row['gif_nama_promosi'] ?></td>
                                <td align="right"><?= number_format($row['gif_min_beli_pcs'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_min_beli_rph'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_min_total_struk'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_min_total_sponsor'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_max_jml_hari'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_max_frek_hari'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_max_jml_event'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_max_frek_event'], 0, '.', ',') ?></td>
                                <td align="right"><?= number_format($row['gif_jumlah_hadiah'], 0, '.', ',') ?></td>
                                <td align="left">
                                    <?php
                                    if ($row['gif_jenis_hadiah'] == 'PR') {
                                        echo $row['gif_plu_hadiah'];
                                    } else {
                                        echo $row['gif_nama_hadian'];
                                    }
                                    ?>
                                </td>
                                <td align="center"><?= $row['gif_mulai'] ?></td>
                                <td align="center"><?= $row['gif_selesai'] ?></td>
                                <td align="left">
                                    <?php
                                    $jenisMember = '';
                                    if ($row['gif_reguler'] == '1') $jenisMember .= 'REG ';
                                    if ($row['gif_reguler_biruplus'] == '1') $jenisMember .= 'RB+ ';
                                    if ($row['gif_freepass'] == '1') $jenisMember .= 'FRE ';
                                    if ($row['gif_retailer'] == '1') $jenisMember .= 'RET ';
                                    if ($row['gif_silver'] == '1') $jenisMember .= 'SIL ';
                                    if ($row['gif_gold1'] == '1') $jenisMember .= 'GD1 ';
                                    if ($row['gif_gold2'] == '1') $jenisMember .= 'GD2 ';
                                    if ($row['gif_gold3'] == '1') $jenisMember .= 'GD3 ';
                                    if ($row['gif_platinum'] == '1') $jenisMember .= 'PLA ';
                                    echo $jenisMember;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Kalau tidak ada data, hanya tampil alert -->
        <div class="alert alert-danger text-center p-2 mb-0" role="alert" style="font-size: 14px; display:inline-block;">
            Tidak ada data promosi GIFT
        </div>
    <?php endif; ?>
</div>
