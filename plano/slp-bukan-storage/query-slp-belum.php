<?php
// slp belum di-realisasi

$query = "
    SELECT slp.slp_prdcd,
           prd.prd_deskripsipanjang AS slp_deskripsi,
           slp.slp_unit,
           slp.slp_frac,
           COALESCE(prd.prd_kodetag, ' ') AS slp_kodetag,
           slp.slp_qtycrt,
           MOD(slp.slp_qtypcs, slp.slp_frac) AS slp_qtypcs,
           slp.slp_id,
           slp.slp_flag,
           slp.slp_expdate,
           slp.slp_koderak,
           slp.slp_kodesubrak,
           slp.slp_tiperak,
           slp.slp_shelvingrak,
           slp.slp_nourut,
           DATE_TRUNC('day', CURRENT_DATE) - DATE_TRUNC('day', slp.slp_create_dt) AS slp_hari_tertunda,
           FLOOR(24 * (EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - COALESCE(slp.slp_create_dt, CURRENT_TIMESTAMP))) / 3600)) AS slp_jam_tertunda
    FROM   tbtr_slp slp
    LEFT JOIN tbmaster_prodmast prd ON slp.slp_prdcd = prd.prd_prdcd
    WHERE  slp.slp_flag IS NULL
           AND (slp.slp_tiperak <> 'S' OR (slp.slp_tiperak = 'S' AND slp.slp_koderak LIKE '%C'))
";

// Adding a condition based on $slpLokasi
if ($slpLokasi !== 'All') {
    $query .= " AND slp.slp_koderak LIKE '{$slpLokasi}%' ";
}

$query .= " ORDER BY slp.slp_id ";

?>
