<?php
require_once '../_/connection.php';

$bulan_ini_nama = date('F Y');

// ========== QUERY UTAMA ==========
$query = "
SELECT 
    COALESCE(kecamatan, 'TIDAK TERDEFINISI') as kecamatan,
    TRUNC(SUM(dtl_netto)) AS total_sales_bulan_ini
FROM (
    SELECT 
        CASE 
            WHEN trjd_transactiontype = 'R' THEN (
                CASE 
                    WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN trjd_nominalamt
                    ELSE (trjd_nominalamt - (CASE WHEN trjd_flagtax1 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100)))) ELSE 0 END))
                END * -1
            )
            ELSE (
                CASE 
                    WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN trjd_nominalamt
                    ELSE (trjd_nominalamt - (CASE WHEN trjd_flagtax1 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100)))) ELSE 0 END))
                END
            )
        END AS dtl_netto,
        PSKECA.kecamatan
    FROM tbtr_jualdetail
    LEFT JOIN tbmaster_prodmast ON trjd_prdcd = prd_prdcd
    LEFT JOIN (
        SELECT cus_kodemember AS KDMM, POS_KECAMATAN AS kecamatan 
        FROM TBMASTER_CUSTOMER
        JOIN TBMASTER_KODEPOS ON cus_alamatmember7 = POS_KODE AND UPPER(cus_alamatmember8) = UPPER(pos_kelurahan)
        WHERE CUS_KODEIGR = '2G'
    ) PSKECA ON trjd_cus_kodemember = PSKECA.KDMM
    WHERE trjd_transactiondate >= DATE_TRUNC('month', CURRENT_DATE)
) AS sales_data
GROUP BY kecamatan
ORDER BY total_sales_bulan_ini DESC";

$result = pg_query($conn, $query);

$all_data = [];
while ($row = pg_fetch_assoc($result)) {
    $all_data[] = $row;
}

$total_rows = count($all_data);
$display_data = [];

// LOGIKA FIX: Ambil Top 2 dan Bottom 2 tanpa duplikat
if ($total_rows > 0) {
    if ($total_rows <= 4) {
        $display_data = $all_data;
    } else {
        $top_2 = array_slice($all_data, 0, 2, true);
        $bottom_2 = array_slice($all_data, -2, 2, true);
        // Menggunakan union (+) untuk menjaga index asli dan menghindari duplikat
        $display_data = $top_2 + $bottom_2; 
    }
}
?>

<style>
    .card-custom { border-radius: 12px; border: none; overflow: hidden; }
    .header-top { background: #1e3c72; color: white; padding: 15px; text-align: center; }
    .table-sales thead th { background: #f8f9fa; text-align: center; border-bottom: 2px solid #dee2e6; }
    .rank-badge { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: auto; font-weight: bold; }
    .bg-top { background: #ffc107; color: #000; }
    .bg-bottom { background: #e74c3c; color: #fff; }
    .separator { background: #f1f1f1 !important; height: 40px; vertical-align: middle !important; }
</style>


        <div class="table-responsive">
            <table class="table table-hover table-sales mb-0">
                <thead>
                    <tr>
                        <th>RANK</th>
                        <th>KECAMATAN</th>
                        <th class="text-end">NOMINAL SALES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 0;
                    $shown_count = count($display_data);
                    
                    foreach ($display_data as $key => $r): 
                        $counter++;
                        $real_rank = $key + 1; // Index asli dari query
                    ?>
                        <tr>
                            <td>
                                <div class="rank-badge <?= ($real_rank <= 2) ? 'bg-top' : 'bg-bottom' ?>">
                                    <?= $real_rank ?>
                                </div>
                            </td>
                            <td class="align-middle fw-bold"><?= strtoupper($r['kecamatan']) ?></td>
                            <td class="align-middle text-end text-success fw-bold">
                                Rp <?= number_format($r['total_sales_bulan_ini'], 0, ',', '.') ?>
                            </td>
                        </tr>

                        <?php 
                        // GARIS OFF SIDE FIX: Muncul hanya jika ada lonjakan index (ada data yang dilewati)
                        if ($total_rows > 4 && $counter == 2): ?>
                            <tr>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
