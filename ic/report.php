<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
require_once 'query_soic.php';

// Menangkap input periode (format: 2026-03)
$periodeInput = $_GET['periode'] ?? date('Y-m');

// Konversi ke tanggal awal dan akhir bulan
$tglAwal  = $periodeInput . "-01";
$tglAkhir = date("Y-m-t", strtotime($tglAwal)); // 't' untuk hari terakhir bulan

// Ambil data progress dan detail reset
$dataSoic    = getSoicData($conn, $tglAwal, $tglAkhir);
$detailReset = getDetailReset($conn, $tglAwal, $tglAkhir);

// Logic Dummy jika data kosong
$displayData = !empty($dataSoic) ? $dataSoic : [
    [
        'mso_tglso' => $tglAwal, 
        'mso_kodeigr' => $branch_target, 
        'status_so_detail' => 'TIDAK ADA AKTIVITAS',
        'mso_flagtahap' => 0,
        'mso_flagcetak' => 0,
        'mso_flagreset' => 'N',
        'is_empty' => true 
    ]
];
?>

<style>
    .so-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 5px solid #e2e8f0; }
    .status-completed { border-left-color: #47c363; }
    .status-active { border-left-color: #3abaf4; }
    .status-empty { border-left-color: #cbd5e1; opacity: 0.7; }
    
    .stepper-row { display: flex; flex-direction: column; gap: 20px; margin-top: 20px; }
    .step-container { display: flex; justify-content: space-between; align-items: center; position: relative; }
    .step-line { position: absolute; top: 15px; left: 5%; width: 90%; height: 2px; background: #e2e8f0; z-index: 1; }
    .step-line-fill { position: absolute; top: 0; left: 0; height: 100%; background: #47c363; transition: 0.5s; }
    .step-item { position: relative; z-index: 2; text-align: center; flex: 1; }
    .step-circle { width: 30px; height: 30px; border-radius: 50%; background: #fff; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-weight: 700; font-size: 11px; color: #94a3b8; }
    
    .passed .step-circle { background: #47c363; border-color: #47c363; color: #fff; }
    .active .step-circle { border-color: #3abaf4; color: #3abaf4; }
    .is-empty-state .step-circle { background: #f8fafc; color: #cbd5e1; }
    .step-label { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; }
    .group-label { font-size: 11px; font-weight: 700; color: #1e293b; margin-bottom: 10px; display: flex; align-items: center; }

    /* Style Tabel Detail */
    .card-detail { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 20px; }
    .table-detail thead th { background: #f4f6f9; color: #495057; text-transform: uppercase; font-size: 11px; }
    .table-detail tbody td { font-size: 12px; vertical-align: middle; }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3>Progress SO IC: <?= date('F Y', strtotime($tglAwal)) ?></h3>
        <div>
            <?php 
            // Cek apakah ada data SO yang valid (bukan dummy/empty)
            $showButton = false;
            if (!empty($dataSoic)) {
                foreach ($dataSoic as $check) {
                    // Tombol muncul jika mso_flagreset sudah 'Y' atau proses sudah dimulai
                    if ($check['mso_flagreset'] !== 'N') {
                        $showButton = true;
                        break;
                    }
                }
            }
            ?>

            <?php if ($showButton): ?>
                <button class="btn btn-info btn-sm shadow-sm mr-2" onclick="$('#area-detail').slideToggle()">
                    <i class="fas fa-table"></i> DETAIL BARANG RESET
                </button>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm shadow-sm mr-2" disabled title="Data reset belum tersedia">
                    <i class="fas fa-lock"></i> DETAIL BELUM TERSEDIA
                </button>
            <?php endif; ?>

            <a href="index.php" class="btn btn-primary btn-sm shadow-sm">GANTI PERIODE</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php foreach ($displayData as $row): 
                $isEmpty = isset($row['is_empty']);
                $isReset = ($row['mso_flagreset'] == 'Y');
                $valTahap = (int)$row['mso_flagtahap'];
                $valCetak = (int)$row['mso_flagcetak'];
                
                $cardClass = $isEmpty ? 'status-empty is-empty-state' : ($isReset ? 'status-completed' : 'status-active');
            ?>
            <div class="so-card <?= $cardClass ?>">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 <?= $isEmpty ? 'text-muted' : 'text-primary' ?>">
                            <?= $isEmpty ? 'Estimasi SO ' . date('F Y', strtotime($tglAwal)) : "Tanggal SO: " . date('d F Y', strtotime($row['mso_tglso'])) ?>
                        </h6>
                        <small class="text-muted">CABANG: <b><?= strtoupper($row['mso_kodeigr']) ?></b></small>
                    </div>
                    <span class="badge badge-<?= $isEmpty ? 'secondary' : ($isReset ? 'success' : 'info') ?>">
                        <?= $row['status_so_detail'] ?>
                    </span>
                </div>

                <div class="stepper-row">
                    <div>
                        <div class="group-label"><i class="fas fa-tasks mr-2"></i> TAHAPAN SOIC (1-5)</div>
                        <div class="step-container">
                            <div class="step-line">
                                <div class="step-line-fill" style="width: <?= (!$isEmpty && $valTahap > 0) ? ($isReset ? '100' : ($valTahap-1)*25) : '0' ?>%"></div>
                            </div>
                            <?php for ($i = 1; $i <= 5; $i++): 
                                $status = (!$isEmpty) ? (($isReset || $valTahap >= $i) ? 'passed' : (($valTahap + 1 == $i) ? 'active' : '')) : '';
                            ?>
                            <div class="step-item <?= $status ?>">
                                <div class="step-circle"><?= ($status == 'passed') ? '<i class="fas fa-check"></i>' : $i ?></div>
                                <div class="step-label">T-<?= $i ?></div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div>
                        <div class="group-label"><i class="fas fa-print mr-2"></i> PROSES CETAK (1-5)</div>
                        <div class="step-container">
                            <div class="step-line">
                                <div class="step-line-fill" style="width: <?= (!$isEmpty && $valCetak > 0) ? ($isReset ? '100' : ($valCetak-1)*25) : '0' ?>%"></div>
                            </div>
                            <?php for ($j = 1; $j <= 5; $j++): 
                                $statusC = (!$isEmpty) ? (($isReset || $valCetak >= $j) ? 'passed' : (($valCetak + 1 == $j && !$isReset) ? 'active' : '')) : '';
                            ?>
                            <div class="step-item <?= $statusC ?>">
                                <div class="step-circle"><?= ($statusC == 'passed') ? '<i class="fas fa-check"></i>' : $j ?></div>
                                <div class="step-label">C-<?= $j ?></div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row" id="area-detail" style="display: none;">
        <div class="col-12">
            <div class="card card-detail">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-list mr-2 text-primary"></i> Detail Item Reset SOIC (Lokasi 01)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-detail" id="table-detail-so">
                            <thead>
                                <tr>
                                    <th>DIV</th>
                                    <th>PLU</th>
                                    <th>DESKRIPSI BARANG</th>
                                    <th>FRC</th>
                                    <th>UNIT</th>
                                    <th class="text-right">QTY RESET</th>
                                    <th class="text-right">AVG COST</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detailReset as $item): ?>
                                <tr>
                                    <td><?= $item['prd_kodedivisi'] ?></td>
                                    <td><b><?= $item['rso_prdcd'] ?></b></td>
                                    <td><?= $item['prd_deskripsipanjang'] ?></td>
                                    <td><?= (int)$item['prd_frac'] ?></td>
                                    <td><?= $item['prd_unit'] ?></td>
                                    <td class="text-right text-danger font-weight-bold">
                                        <?= number_format($item['qty_reset'], 0) ?>
                                    </td>
                                    <td class="text-right">
                                        <?= number_format($item['rso_avgcostreset'], 0) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
$(document).ready(function() {
    $('#table-detail-so').DataTable({
        dom: 'Bfrtip',
        pageLength: 20,
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fas fa-file-excel"></i> Export Excel' },
            { extend: 'print', className: 'btn btn-light btn-sm', text: '<i class="fas fa-print"></i> Cetak' }
        ]
    });
});
</script>