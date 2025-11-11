<?php
// FILE: components/comparison_table.php
// Membutuhkan variabel: $data, $search_executed, $error_messages, $remote_branch_names, $branch_target, $remote_connections, $all_branch_configs

if (!empty($error_messages)): ?>
    <div class="alert alert-danger p-3">
        <i class="fas fa-exclamation-triangle me-2"></i> 
        <strong>Terjadi Kesalahan pada Beberapa Cabang:</strong>
        <ul>
            <?php foreach ($error_messages as $code => $msg): ?>
                <li><b><?= htmlspecialchars($remote_branch_names[$code] ?? strtoupper($code)) ?>:</b> <?= htmlspecialchars($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php elseif ($search_executed && empty($data)): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i> 
        Tidak ada data ditemukan untuk PLU <b><?= htmlspecialchars($kodePLU) ?></b>.
    </div>

<?php elseif (!empty($data)): ?>
    <div class="alert alert-light border-start border-success border-3 mb-3">
        <strong>Keterangan:</strong><br>
        Harga cabang lokal (<?= htmlspecialchars($remote_branch_names[$branch_target]) ?>) digunakan sebagai rujukan.
        <br>
        <span class="text-danger"><i class="fas fa-arrow-up"></i></span> Harga Lokal <b class="text-danger">lebih tinggi</b> dari cabang lain<br>
        <span class="text-success"><i class="fas fa-arrow-down"></i></span> Harga Lokal <b class="text-success">lebih rendah</b> dari cabang lain<br>
        <span class="text-primary"><i class="fas fa-equals"></i></span> Harga Sama
    </div>

    <div class="table-responsive">
        <table id="GridView" class="display table table-bordered table-striped table-hover w-100">
            <thead class="text-center bg-light">
                <tr>
                    <th>No</th>
                    <th>PLU</th>
                    <th>Deskripsi</th>
                    
                    <th class="bg-success text-dark">
                        <?= htmlspecialchars($remote_branch_names[$branch_target]) ?>
                    </th>
                    
                    <?php 
                    // Tampilkan header untuk SETIAP CABANG REMOTE
                    foreach ($all_branch_configs as $code => $config): 
                        // Lewati cabang lokal
                        if ($code === $branch_target) {
                            continue;
                        }
                        // Hanya tampilkan jika koneksi berhasil
                        if (isset($remote_connections[$code])):
                    ?>
                        <th><?= htmlspecialchars($config['name']) ?></th>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </tr>
            </thead>
            <tbody style="font-size: 14pt;">
                <?php foreach ($data as $no => $row): ?>
                <tr>
                    <td><?= $no + 1 ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($row['prd_prdcd']) ?></td>
                    <td><?= htmlspecialchars($row['prd_deskripsipanjang']) ?></td>
                    
                    <td class="text-end fw-bold bg-secondary bg-opacity-25">
                        Rp <?= number_format($row['harga_final_lokal'] ?? 0, 0, ',', '.') ?>
                    </td>

                    <?php 
                        $hargaLokal = floatval($row['harga_final_lokal'] ?? 0);
                        // Loop melalui harga cabang remote
                        foreach ($all_branch_configs as $code => $config):
                            if ($code === $branch_target) { 
                                continue; // Lewati yang lokal
                            }

                            if (isset($remote_connections[$code])):
                                // Ambil data harga dari key yang menggunakan kode singkat
                                $hargaCabang = floatval($row["harga_{$code}"] ?? 0);
                                $selisih = $hargaLokal - $hargaCabang;
                                
                                // Atur ikon dan warna
                                if ($hargaCabang > 0 && abs($selisih) > 0) {
                                    $selisih_color = $selisih > 0 ? 'text-danger' : 'text-success'; 
                                    $icon = $selisih > 0 ? '<i class="fas fa-arrow-up"></i>' : '<i class="fas fa-arrow-down"></i>';
                                    
                                } else {
                                    // Harga sama atau harga remote 0
                                    $selisih_color = 'text-primary';
                                    $icon = '<i class="fas fa-equals"></i>';
                                }
                            ?>
                                <td class="text-end">
                                    Rp. <?= number_format($hargaCabang, 0, ',', '.') ?>
                                    <span class="<?= $selisih_color ?> ms-2" 
                                        title="Selisih: Rp <?= number_format($selisih, 0, ',', '.') ?>">
                                        (<?= $icon ?>)
                                    </span>
                                </td>
                            <?php 
                            endif; 
                        endforeach; 
                    ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center text-muted p-5">
        <i class="fas fa-box-open fa-3x mb-3"></i>
        <p>Masukkan Kode PLU dan tekan <b>Cari</b> untuk melihat harga promosi dan komparasinya.</p>
    </div>
<?php endif; ?>