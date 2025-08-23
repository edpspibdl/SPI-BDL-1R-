<?php
require_once '../helper/connection.php';

try {
    $query = "SELECT 
		slp.slp_id,
        slp.slp_prdcd,
        prd.prd_deskripsipanjang AS slp_deskripsi,
        slp.slp_unit,
        slp.slp_frac,
        COALESCE(prd.prd_kodetag, ' ') AS slp_kodetag,
        slp.slp_qtycrt,
        (slp.slp_qtypcs % slp.slp_frac) AS slp_qtypcs,  -- Use MOD function equivalent
        slp.slp_id,
        slp.slp_flag,
        slp.slp_expdate,
        slp.slp_koderak || '.' || slp.slp_kodesubrak || '.' || slp.slp_tiperak || '.' || slp.slp_shelvingrak || '.' || slp.slp_nourut AS SLP_TUJUAN,
        CURRENT_DATE - slp_create_dt AS slp_hari_tertunda,  -- Difference between dates
        FLOOR(24 * (EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - slp_create_dt)) / 3600)) AS slp_jam_tertunda  -- Calculate hours
    FROM 
        tbtr_slp slp
    LEFT JOIN 
        tbmaster_prodmast prd ON slp.slp_prdcd = prd.prd_prdcd
    WHERE 
        slp.slp_flag IS NULL";

    $stmt = $conn->query($query);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
