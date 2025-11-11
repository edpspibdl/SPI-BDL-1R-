<?php
require_once '../helper/connection.php';

try {
        $query = "SELECT
    SQ3.DIV,
    SQ3.DEPT,
    SQ3.KATB,
    SQ3.PLU,
    SQ3.DESK,
    SQ3.FRAC,
    SQ3.TAG,
    TO_CHAR(SQ_EXP.EXP_TERDEKAT, 'YYYY-MM-DD') AS EXP_TERDEKAT,
    SQ3.ALAMAT,
    SQ3.QTY_LKS,
    ROUND((SQ3.QTY_LKS / SQ3.FRAC), 0) AS QTY_LKS_CTN,
    ST.LPP,
    ROUND((ST.LPP / SQ3.FRAC), 0) AS LPP_CTN
FROM
(
    SELECT
        lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut AS ALAMAT,
        LKS_PRDCD AS PLU_LKS,
        LKS_QTY AS QTY_LKS,
        lks_expdate AS EXP_LKS,
        PRD_KODEDIVISI AS DIV,
        PRD_KODEDEPARTEMENT AS DEPT,
        PRD_KODEKATEGORIBARANG AS KATB,
        PRD_PRDCD AS PLU,
        PRD_DESKRIPSIPANJANG AS DESK,
        PRD_FRAC AS FRAC,
        PRD_KODETAG AS TAG
    FROM TBMASTER_LOKASI AS sq1
    LEFT JOIN TBMASTER_PRODMAST AS sq2
        ON sq1.LKS_PRDCD = sq2.PRD_PRDCD
    WHERE sq1.LKS_PRDCD IS NOT NULL
      AND sq2.PRD_PRDCD LIKE '%0'
      AND sq2.PRD_FLAGIGR = 'Y'
) AS SQ3
LEFT JOIN
(
    SELECT
        lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut AS ALAMAT,
        LKS_PRDCD AS PLU_EXP,
        MIN(lks_expdate) AS EXP_TERDEKAT
    FROM TBMASTER_LOKASI
    WHERE LKS_PRDCD IS NOT NULL
    GROUP BY
        LKS_PRDCD,
        lks_koderak, lks_kodesubrak, lks_tiperak, lks_shelvingrak, lks_nourut
) AS SQ_EXP
    ON SQ3.PLU = SQ_EXP.PLU_EXP
   AND SQ3.ALAMAT = SQ_EXP.ALAMAT
LEFT JOIN
(
    SELECT
        ST_PRDCD,
        st_saldoakhir AS LPP
    FROM TBMASTER_STOCK
    WHERE ST_LOKASI = '01'
) AS ST
    ON SQ3.PLU = ST.ST_PRDCD
WHERE
    SQ3.DEPT <> '14'
    AND SQ3.DIV = '1'
    AND SQ3.ALAMAT LIKE '%B%'
    AND SQ3.QTY_LKS <> '0'
ORDER BY SQ_EXP.EXP_TERDEKAT, SQ3.DIV, SQ3.PLU ASC";

          $stmt = $conn->query($query);
} catch (PDOException $e) {
          die("Error: " . $e->getMessage());
}
