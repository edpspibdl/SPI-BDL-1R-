<?php
require_once '../layout/_top.php'; 
require_once '../helper/connection.php'; // Sudah memuat semua koneksi + konfigurasi

$kodePLU = '';
$data = [];
$error_messages = []; 
$search_executed = false;

// Ambil variabel dari session & connection.php
$db_target = $_SESSION['db_target'] ?? 'prod'; 
$branch_target = $_SESSION['branch_target'] ?? 'unknown';

// Ambil semua konfigurasi dan koneksi dari connection.php
$all_branch_configs = $ALL_BRANCH_CONFIGS;
$remote_connections = $remote_connections ?? [];
$remote_branch_names = $remote_branch_names ?? [];

// 🔍 LOGIKA PENCARIAN PRODUK
if (!empty($_GET['kodePLU'])) {
    $inputPLU = trim($_GET['kodePLU']);
    $search_executed = true;

    // Format input PLU ke 7 digit → ambil 6 digit untuk LIKE
    $kodePLU = str_pad($inputPLU, 7, '0', STR_PAD_LEFT);
    $base_like = substr($kodePLU, 0, 6) . '%';
    
    $data_lokal = [];
    $final_data = [];

    // 🔹 QUERY UTAMA (TIDAK DIUBAH)
    $query_lokal_kompleks = " 
        SELECT 
            subs1.PLU AS prd_prdcd, 
            subs1.DESK AS prd_deskripsipanjang, 
            COALESCE(subq1.hrg_netmm, subs1.HRGN) AS harga_final_lokal 
        FROM (
            SELECT PRD_PRDCD PLU, PRD_DESKRIPSIPANJANG DESK, PRD_HRGJUAL HRGN
            FROM TBMASTER_PRODMAST 
            WHERE PRD_PRDCD LIKE :base_plu 
        ) subs1
        LEFT JOIN (
            select PLUMM, hrg_netmm
            from (
                select pluN plumm, HRGN, HRGP,
                    (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END) HRGMM,
                    qty qtymm, cb cbmm,
                    (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END)),0)-COALESCE(cb,0)) hrg_netmm 
                from ( 
                    select pluN, HRGN, HRGP, hrg, qty, sum((jmlcbh*cbh)+(jmlcbd*cbd)) CB 
                    from ( 
                        select distinct pluN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty,
                            sum(case when pluN like '%0' 
                                then (case when COALESCE(MINRPHC,0)<>'0' 
                                    then (case when (hrg) > MAXRPHC then FLOOR(MAXRPHC/MINRPHC) else FLOOR((hrg)/MINRPHC) end) 
                                    else 0 end)
                                else (case when COALESCE(MINRPHC,0)<>'0' 
                                    then (case when (hrg*qty) > MAXRPHC then FLOOR(MAXRPHC/MINRPHC) else FLOOR((hrg*qty)/MINRPHC) end)
                                    else 0 end)
                            end ) jmlcbh, 
                            sum(case when COALESCE(MINJUALC,0)<>'0' 
                                then (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) 
                                    ELSE (case when qty > MAXJUALC then FLOOR(MAXJUALC/MINJUALC) else FLOOR(qty/MINJUALC) end) END)
                                else 0 end ) jmlcbd 
                        from ( 
                            SELECT PLUN, DESK, FRACN, UNIT,
                                (CASE WHEN UNIT LIKE '%RCG%' THEN (1 * MINJUALN) ELSE (FRACN*MINJUALN) END) QTY,
                                HRGN, HRGP,
                                (CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END) HRG,
                                MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, CBH, CBD
                            FROM (
                                SELECT PLUN, DESK, FRACN, UNIT, MINJUALN, HRGN, HRGP
                                FROM ( 
                                    SELECT PLUP, HRGP, FLAG FROM 
                                    (SELECT DISTINCT PRMD_PRDCD PLUP, PRMD_HRGJUAL HRGP,
                                        (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
                                              WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
                                              ELSE 'MERAH' END) FLAG
                                    FROM TBTR_PROMOMD 
                                    LEFT JOIN TBTR_PROMOMD_ALOKASI 
                                    ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
                                    WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=current_date 
                                      AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=current_date
                                      AND PRMD_PRDCD LIKE :base_plu
                                    ) subq8 WHERE FLAG='MERAH'
                                ) subq7
                                RIGHT JOIN ( 
                                    SELECT PRD_PRDCD PLUN, PRD_DESKRIPSIPANJANG DESK, PRD_FRAC FRACN, PRD_UNIT UNIT, PRD_MINJUAL MINJUALN, PRD_HRGJUAL HRGN
                                    FROM TBMASTER_PRODMAST
                                    WHERE PRD_PRDCD LIKE :base_plu
                                ) subq9 ON PLUN=PLUP
                            ) subq6
                            LEFT JOIN (
                                SELECT PLUC, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, CBH, CBD 
                                FROM (
                                    select cbd_kodepromosi kode, cbd_prdcd pluC,
                                        (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
                                              when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
                                              else cbh_mintotbelanja end) minrphC, 
                                        cbd_minstruk minjuALC,
                                        (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC,
                                        (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC,
                                        cbh_cashback cbh, cbd_cashback cbd
                                    from tbtr_cashback_hdr 
                                    left join tbtr_cashback_dtl on cbh_kodepromosi=cbd_kodepromosi 
                                    left join tbtr_cashback_alokasi on cbh_kodepromosi=cba_kodepromosi 
                                    where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', current_date) 
                                      and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', current_date)
                                      and CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
                                      and (COALESCE(cba_retailer,'0')='1' or COALESCE(cba_silver,'0')='1' 
                                           or COALESCE(cba_gold1,'0')='1' or COALESCE(cba_gold2,'0')='1' or COALESCE(cba_gold3,'0')='1')
                                      and cbh_namapromosi not like '%UNIQUE%' 
                                      and cbh_namapromosi not like '%PWP%' 
                                      and cbh_namapromosi not like '%UNICODE%' 
                                      and COALESCE(cbd_recordid,'2') <>'1' 
                                      and COALESCE(cbd_redeempoint,'0')='0'
                                ) subq10 WHERE PLUC LIKE :base_plu
                            ) subq11 ON SUBSTR(PLUN,1,6)||0=PLUC
                        ) subq5 
                        group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty 
                    ) subq4 
                    group by pluN, HRGN, HRGP, hrg, qty
                ) subq3
            ) subq2
            ORDER BY PLUMM
        ) subq1 ON subs1.PLU = subq1.PLUMM
        ORDER BY subs1.PLU
    ";

    // 🟩 Query cabang lokal
    try {
        $stmt_lokal = $conn->prepare($query_lokal_kompleks);
        $stmt_lokal->bindValue(':base_plu', $base_like, PDO::PARAM_STR); 
        $stmt_lokal->execute();
        $data_lokal = $stmt_lokal->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_messages[$branch_target] = "Error query cabang lokal ({$remote_branch_names[$branch_target]}): " . $e->getMessage();
    }

    // 🟨 Query cabang lain (remote)
    $query_remote_complex = str_replace("AS harga_final_lokal", "AS harga_final_remote", $query_lokal_kompleks);
    $remote_plu_data = [];

    foreach ($remote_connections as $code => $r_conn) {
        try {
            $stmt_remote = $r_conn->prepare($query_remote_complex);
            $stmt_remote->bindValue(':base_plu', $base_like, PDO::PARAM_STR);
            $stmt_remote->execute();
            $remote_results = $stmt_remote->fetchAll(PDO::FETCH_ASSOC);
            $remote_plu_data[$code] = array_column($remote_results, 'harga_final_remote', 'prd_prdcd');
        } catch (PDOException $e) {
            $error_messages[$code] = "Error query {$remote_branch_names[$code]} ($code): " . $e->getMessage();
        }
    }

    // Gabungkan hasil
    $final_data = [];
    foreach ($data_lokal as $d_lokal) {
        $plu = $d_lokal['prd_prdcd'];
        $row_data = $d_lokal;
        foreach ($remote_plu_data as $code => $remote_map) {
            $row_data["harga_{$code}"] = $remote_map[$plu] ?? null; 
        }
        $final_data[] = $row_data;
    }
    
    $data = $final_data;
}
?>


<style>
/* HILANGKAN SEMUA CSS KUSTOM SEPERTI .card-header-table DAN .card-body-table */
body { overflow-x: hidden; }
/* ... (CSS LAINNYA TIDAK BERUBAH) ... */
.produk-row:hover {
    cursor: pointer;
    background-color: #f0f8ff;
    transform: scale(1.01);
    transition: all 0.2s;
}
.table-responsive td { white-space: normal; }
.dataTables_wrapper { overflow-x: hidden; }
legend {
    width: auto;
    padding: 0 10px;
    font-size: 1.25rem;
}
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Cek PLU Promosi & Komparasi Harga</h1>
    </div>

    <div class="container-fluid">

        <div class="card shadow-lg border-0 mb-4 rounded-3">
            <div class="card-body p-4">
                <fieldset class="border rounded-3 p-3">
                    <legend class="fw-bold text-primary px-2">
                        Cari Produk
                    </legend>
                    <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                        <input type="text" name="kodePLU" id="kodePLU" 
                            class="form-control form-control-lg border-primary" 
                            placeholder="Masukkan Kode PLU (misal: 0013500)" 
                            value="<?= htmlspecialchars($kodePLU) ?>" required 
                            style="max-width: 300px;">
                        
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="fas fa-play-circle me-1"></i> Cari
                        </button>

                        <button type="button" class="btn btn-secondary btn-lg shadow-sm" 
                                data-toggle="modal" data-target="#produkModal">
                            <i class="fas fa-list-check me-1"></i> Pilih Produk
                        </button>
                    </form>
                </fieldset>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-4">
                <fieldset class="border rounded-3 p-3">
                    <legend class="fw-bold text-success px-2">
                        Hasil Komparasi PLU: <?= htmlspecialchars($kodePLU) ?>
                    </legend>

                    <?php if (!empty($error_messages)): ?>
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
                                          // Lewati cabang lokal, karena sudah di kolom pertama
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
                </fieldset>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 shadow-lg">
            
            <div class="modal-header p-0 border-0 pb-2">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-success px-3" id="produkModalLabel" style="font-size: 24pt;">
                        Pilih Produk Master
                    </h5>
                </div>
                <hr class="w-100 mx-3 mb-0">
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="produkTable" class="display table table-bordered table-striped table-hover w-100">
                        <thead class="text-center bg-light">
                            <tr>
                                <th>PLU</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $produkQuery = $conn->query("
                                SELECT PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_FLAGIGR 
                                  FROM TBMASTER_PRODMAST 
                                  WHERE PRD_RECORDID IS NULL 
                                  AND PRD_PRDCD LIKE '%0'
                                  ORDER BY PRD_PRDCD ASC
                               
                            ");
                            
                            while ($produk = $produkQuery->fetch(PDO::FETCH_ASSOC)) {
                                $plu = htmlspecialchars($produk['prd_prdcd']);
                                $desk = htmlspecialchars($produk['prd_deskripsipanjang']);
                                $unit = htmlspecialchars($produk['prd_flagigr']);
                                
                                echo "<tr class='produk-row' data-plu='{$plu}'>
                                            <td class='fw-medium'>{$plu}</td>
                                            <td>{$desk}</td>
                                            <td class='text-center'>{$unit}</td>
                                        </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer p-3">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
            
        </div>
    </div>
</div>

<?php require_once '../layout/_bottom.php'; ?>

<script>
$(document).ready(function() {
    // DataTables hasil pencarian
    if ($('#GridView').length) {
        $('#GridView').DataTable({
            language: {
                search: "Filter:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Sebelum" }
            },
            scrollX: true,
            paging: true,
            searching: true,
            info: true,
            ordering: false
        });
    }

    // DataTables modal produk
    $('#produkTable').DataTable({
        language: {
            search: "Cari Produk:",
            zeroRecords: "Produk tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ produk",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Sebelum" }
        },
        pageLength: 5,
        lengthChange: false,
        responsive: true
    });

    // Klik baris produk → isi input dan submit
    $('#produkTable tbody').on('click', '.produk-row', function() {
        var plu = $(this).data('plu');
        $('#kodePLU').val(plu);
        $('#produkModal').modal('hide');
        $('#kodePLU').closest('form').submit();
    });
});
</script>