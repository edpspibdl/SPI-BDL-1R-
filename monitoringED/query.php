<?php
require_once '../helper/connection.php';

try {
          $query = "SELECT
        DIV,
        DEPT,
        KATB,
        PLU,
        DESK,
        FRAC,
        TAG,
        TO_CHAR(EXP_TERDEKAT,'YYYY-MM-DD') AS EXP_TERDEKAT,
        ALAMAT,
        QTY_LKS,
        ROUND((QTY_LKS / FRAC),0) AS QTY_LKS_CTN,
        LPP,
        ROUND((LPP / FRAC),0) AS LPP_CTN
    FROM (
        SELECT PLU AS PLU_EXP, MIN(EXP) AS EXP_TERDEKAT
        FROM (
            SELECT
                lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut AS ALAMAT,
                LKS_PRDCD AS PLU_LKS,
                LKS_QTY AS QTY_LKS,
                lks_expdate AS EXP
            FROM TBMASTER_LOKASI
            WHERE LKS_PRDCD IS NOT NULL
        ) AS lks
        LEFT JOIN (
            SELECT
                PRD_KODEDIVISI AS DIV,
                PRD_KODEDEPARTEMENT AS DEPT,
                PRD_KODEKATEGORIBARANG AS KATB,
                PRD_PRDCD AS PLU,
                PRD_DESKRIPSIPANJANG AS DESK,
                PRD_FRAC AS FRAC,
                PRD_KODETAG AS TAG
            FROM TBMASTER_PRODMAST
            WHERE PRD_PRDCD LIKE '%0' AND PRD_FLAGIGR = 'Y'
        ) AS prdcd ON PLU_LKS = PLU
        GROUP BY PLU
    ) AS sq
    LEFT JOIN (
        SELECT * FROM (
            SELECT
                lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut AS ALAMAT,
                LKS_PRDCD AS PLU_LKS,
                LKS_QTY AS QTY_LKS,
                lks_expdate AS EXP
            FROM TBMASTER_LOKASI
            WHERE LKS_PRDCD IS NOT NULL
        ) AS sq1
        LEFT JOIN (
            SELECT
                PRD_KODEDIVISI AS DIV,
                PRD_KODEDEPARTEMENT AS DEPT,
                PRD_KODEKATEGORIBARANG AS KATB,
                PRD_PRDCD AS PLU,
                PRD_DESKRIPSIPANJANG AS DESK,
                PRD_FRAC AS FRAC,
                PRD_KODETAG AS TAG
            FROM TBMASTER_PRODMAST
            WHERE PRD_PRDCD LIKE '%0' AND PRD_FLAGIGR = 'Y'
        ) AS sq2 ON PLU_LKS = PLU
    ) AS sq3 ON PLU = PLU_EXP AND EXP = EXP_TERDEKAT
    LEFT JOIN (
        SELECT
            ST_PRDCD,
            ST_SALDOAKHIR AS LPP
        FROM TBMASTER_STOCK
        WHERE ST_LOKASI = '01'
    ) AS st ON PLU = ST_PRDCD
    WHERE DEPT <> '14' AND ALAMAT LIKE '%B%' AND QTY_LKS <> '0'
    ORDER BY 8,1,4 ASC";

          $stmt = $conn->query($query);
} catch (PDOException $e) {
          die("Error: " . $e->getMessage());
}
