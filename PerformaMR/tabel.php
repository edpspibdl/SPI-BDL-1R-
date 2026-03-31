<?php
require_once '../_/connection.php';

// Ambil tanggal dari input (Default H-0)
$tgl = isset($_GET['tgl']) ? pg_escape_string($conn, $_GET['tgl']) : date('Y-m-d');

// mapping target get member per bulan
$bulan_ini = (int)date('m', strtotime($tgl));
$targets = [
    10 => 1026, 11 => 1077, 12 => 1131,
    1  => 1188, 2  => 1247, 3  => 1309,
    4  => 1375, 5  => 1444, 6  => 1516,
    7  => 1592, 8  => 1671, 9  => 1755,
];
$target_get_member = $targets[$bulan_ini] ?? 1117;

// ========== QUERY DATA UTAMA ==========
$query = "
SELECT 
    all_mr.mr AS mr,
    $target_get_member AS target_get_member,
    COALESCE(gm.get_member, 0) AS jumlah_get_member,
    COALESCE(po.total_po, 0) AS total_po,
    COALESCE(po.total_nominal, 0) AS total_nominal,
    COALESCE(po.ambt, 0) AS ambt,
    COALESCE(po.cod, 0) AS cod,
    COALESCE(blj.member_belanja, 0) AS mm_belanja,
    COALESCE(blj.belanja_1x, 0) AS mm_belanja_1x,
    COALESCE(blj.belanja_repeat, 0) AS mm_belanja_repeat
FROM (
    SELECT DISTINCT cus_nosalesman AS mr
    FROM tbmaster_customer
    WHERE cus_kodeigr = '2G'
) AS all_mr
LEFT JOIN (
    SELECT cus_nosalesman AS mr, COUNT(*) AS get_member
    FROM tbmaster_customer
    WHERE DATE(cus_tglregistrasi) = '$tgl' AND cus_kodeigr = '2G'
    GROUP BY cus_nosalesman
) gm ON all_mr.mr = gm.mr
LEFT JOIN (
  SELECT 
    c.cus_nosalesman AS mr,
    COUNT(DISTINCT obi.obi_nopb) AS total_po,
    SUM(obi.obi_ttlorder + obi.obi_ttlppn - obi.obi_ttldiskon) AS total_nominal,
    COUNT(DISTINCT CASE WHEN obi.obi_kdekspedisi = 'Ambil di Stock Point Indogrosir' THEN obi.obi_nopb END) AS ambt,
    COUNT(DISTINCT CASE WHEN obi.obi_kdekspedisi <> 'Ambil di Stock Point Indogrosir' THEN obi.obi_nopb END) AS cod
  FROM tbtr_obi_h obi
  JOIN tbmaster_customer c ON c.cus_kodemember = obi.obi_kdmember
  WHERE obi.obi_tglpb = '$tgl'
    AND (obi.obi_recid IS NULL OR obi.obi_recid NOT LIKE '%B%')
    AND c.cus_kodeigr = '2G'
  GROUP BY mr
) po ON all_mr.mr = po.mr
LEFT JOIN (
    SELECT 
        c.cus_nosalesman AS mr,
        COUNT(*) AS member_belanja,
        SUM(CASE WHEN cnt = 1 THEN 1 ELSE 0 END) AS belanja_1x,
        SUM(CASE WHEN cnt > 1 THEN 1 ELSE 0 END) AS belanja_repeat
    FROM (
        SELECT jh.jh_cus_kodemember, COUNT(DISTINCT DATE(jh.jh_transactiondate)) AS cnt
        FROM tbtr_jualheader jh
        WHERE to_char(jh.jh_transactiondate,'YYYY-MM') = to_char('$tgl'::date,'YYYY-MM')
          AND jh.jh_transactiondate::date <= '$tgl'
        GROUP BY jh.jh_cus_kodemember
    ) t
    JOIN tbmaster_customer c ON c.cus_kodemember = t.jh_cus_kodemember
    WHERE c.cus_recordid IS NULL AND c.cus_kodeigr = '2G' AND c.cus_namamember <> 'NEW' AND c.cus_flagmemberkhusus = 'Y'
    GROUP BY mr
) blj ON all_mr.mr = blj.mr
WHERE COALESCE(po.total_po,0) <> 0 OR COALESCE(po.total_nominal,0) <> 0 OR COALESCE(po.ambt,0) <> 0 OR COALESCE(po.cod,0) <> 0
ORDER BY all_mr.mr";

$result = pg_query($conn, $query);

// Logic Penampung Data (Sesuai style yang diinginkan)
$rows = [];
$gTotalGet = $gTotalPO = $gTotalNominal = $gTotalAmbt = $gTotalCod = 0;
$gTotalMM = $gTotalMM1x = $gTotalMMRepeat = 0;

while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
    $gTotalGet      += $row['jumlah_get_member'];
    $gTotalPO       += $row['total_po'];
    $gTotalNominal  += $row['total_nominal'];
    $gTotalAmbt     += $row['ambt'];
    $gTotalCod      += $row['cod'];
    $gTotalMM       += $row['mm_belanja'];
    $gTotalMM1x     += $row['mm_belanja_1x'];
    $gTotalMMRepeat += $row['mm_belanja_repeat'];
}
?>

<style>
    .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .header-panel { 
        background: linear-gradient(45deg, #0056b3, #007bff); 
        color: white; padding: 20px; border-radius: 15px 15px 0 0; 
        font-weight: bold; font-size: 1.5rem; text-align: center;
    }
    .table-rekap thead th { 
        background-color: #343a40 !important; color: white; 
        text-align: center; font-size: 1.1rem; padding: 15px;
    }
    .table-rekap tbody td { font-size: 1.1rem; padding: 12px; vertical-align: middle; }
    .summary-footer { 
        background: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;
        font-size: 1.1rem;
    }
    .text-nominal { color: #28a745; font-weight: bold; }
    .mr-name { text-align: left !important; font-weight: 700; color: #0056b3; }
</style>

<div class="container-fluid py-4">

    <div class="card card-custom shadow">
        <div class="card-body p-0">

                <div class="card-body p-3">
    <form method="GET" class="d-flex justify-content-center align-items-center gap-2" style="display: flex !important; flex-direction: row !important; justify-content: center !important;">
        
        <label class="fw-bold mb-0 text-nowrap" style="white-space: nowrap !important;">Periode:</label>
        
        <input type="date" name="tgl" class="form-control" 
               value="<?= htmlspecialchars($tgl) ?>" required 
               style="width: auto !important; min-width: 150px !important; flex: 0 1 auto !important;">
        
        <button type="submit" class="btn btn-primary px-3 fw-bold text-nowrap" 
                style="white-space: nowrap !important; width: auto !important;">
            CARI
        </button>
        
    </form>
</div>

            <div class="table-responsive">
                <table class="table table-hover table-rekap mb-0">
                    <thead>
                        <tr>
                            <th>MR</th>
                            <th>GET MEMBER</th>
                            <th>TOTAL ORDER</th>
                            <th>AMBT</th>
                            <th>COD</th>
                            <th>TOTAL NOMINAL</th>
                            <th>TOTAL MM BLJ</th>
                            <th>MM 1X</th>
                            <th>MM REPEAT</th>
                            <th>TARGET GET</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($rows)): ?>
                            <tr><td colspan="10" class="text-center py-5 text-muted">Data tidak ditemukan untuk tanggal ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($rows as $r): ?>
                                <tr class="text-center">
                                    <td class="mr-name"><?= htmlspecialchars($r['mr']) ?></td>
                                    <td><?= number_format($r['jumlah_get_member']) ?></td>
                                    <td><?= number_format($r['total_po']) ?></td>
                                    <td><?= number_format($r['ambt']) ?></td>
                                    <td><?= number_format($r['cod']) ?></td>
                                    <td class="text-nominal text-end">Rp <?= number_format($r['total_nominal'], 0, ',', '.') ?></td>
                                    <td class="fw-bold"><?= number_format($r['mm_belanja']) ?></td>
                                    <td><?= number_format($r['mm_belanja_1x']) ?></td>
                                    <td><?= number_format($r['mm_belanja_repeat']) ?></td>
                                    <td class="text-muted"><?= number_format($r['target_get_member']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="summary-footer text-center">
                        <tr>
                            <td class="text-start">GRAND TOTAL</td>
                            <td><?= number_format($gTotalGet) ?></td>
                            <td><?= number_format($gTotalPO) ?></td>
                            <td><?= number_format($gTotalAmbt) ?></td>
                            <td><?= number_format($gTotalCod) ?></td>
                            <td class="text-end text-nominal">Rp <?= number_format($gTotalNominal, 0, ',', '.') ?></td>
                            <td><?= number_format($gTotalMM) ?></td>
                            <td><?= number_format($gTotalMM1x) ?></td>
                            <td><?= number_format($gTotalMMRepeat) ?></td>
                            <td class="text-muted"><?= number_format($target_get_member) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>