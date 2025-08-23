<?php
// ketergantungan :

$viewSpb = "
( SELECT COALESCE(spb.spb_recordid, '0') AS spb_status,
           spb.spb_lokasiasal,
           spb.spb_lokasitujuan,
           spb.spb_prdcd,
           spb.spb_deskripsi,
           prd.prd_unit AS spb_unit,
           prd.prd_frac AS spb_frac,
           COALESCE(prd.prd_kodetag, ' ') AS spb_kodetag,
           spb.spb_id,
           spb.spb_qty,
           FLOOR(spb.spb_qty / prd.prd_frac) AS spb_rak_ctn,
           MOD(spb.spb_qty::NUMERIC, prd.prd_frac::NUMERIC) AS spb_rak_pcs,
           spb.spb_minus,
           FLOOR(spb.spb_minus / prd.prd_frac) AS spb_minta_ctn,
           MOD(spb.spb_minus::NUMERIC, prd.prd_frac::NUMERIC) AS spb_minta_pcs,
           CASE
               WHEN spb.spb_jenis = 'MANUAL' THEN 'MANUAL'
               WHEN spb.spb_jenis = 'OTOMATIS' AND LEFT(spb.spb_lokasitujuan, 1) IN ('G', 'D') THEN 'GUDANG AUTO'
               ELSE 'TOKO AUTO'
           END AS spb_jenis,
           DATE_TRUNC('day', CURRENT_DATE) - DATE_TRUNC('day', spb.spb_create_dt) AS spb_hari_tertunda,
           CASE
               WHEN EXTRACT(HOUR FROM COALESCE(spb.spb_create_dt, CURRENT_TIMESTAMP)) < 8
               THEN FLOOR(EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - COALESCE(spb.spb_create_dt, CURRENT_TIMESTAMP))) / 3600) - 8
               ELSE FLOOR(EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - COALESCE(spb.spb_create_dt, CURRENT_TIMESTAMP))) / 3600)
           END AS spb_jam_tertunda
    FROM tbtemp_antrianspb spb
    LEFT JOIN tbmaster_prodmast prd ON spb.spb_prdcd = prd.prd_prdcd
    WHERE spb.spb_prdcd IS NOT NULL
      AND spb.spb_prdcd IN (SELECT spb_prdcd FROM (SELECT spb_prdcd FROM tbtemp_antrianspb) AS subquery_alias) 
) AS subquery";	
?>
