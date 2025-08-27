<?php
require_once '../helper/connection.php';

try {
    $query = "
    SELECT 
        PLU, 
        DESK, 
        PRD_FRAC, 
        PRD_UNIT, 
        SUM(SPB_MINTA_CTN) AS SPB_MINTA_CTN, 
        SUM(SPB_MINTA_PCS) AS SPB_MINTA_PCS
    FROM (
        SELECT 
            CASE 
                WHEN COALESCE(SPB_RECORDID, '0') = '2' THEN 'BATAL'
                WHEN COALESCE(SPB_RECORDID, '0') = '0' THEN 'BELUM TURUN'
                WHEN COALESCE(SPB_RECORDID, '0') = '3' THEN 'BELUM REALISASI'
                WHEN COALESCE(SPB_RECORDID, '0') = '1' THEN 'REALISASI'
            END AS KET,
            SPB_CREATE_DT AS TGL,
            TO_CHAR(SPB_CREATE_DT, 'HH24:MI:SS') AS JAM,
            SPB_LOKASIASAL AS LOKASI_ASAL,
            SPB_LOKASITUJUAN AS LOKASI_TUJUAN,
            SPB_PRDCD AS PLU,
            SPB_DESKRIPSI AS DESK,
            PRD_FRAC,
            PRD_UNIT,
            SPB_JENIS AS JENIS,
            FLOOR(SPB_MINUS / PRD_FRAC::numeric) AS SPB_MINTA_CTN,
            (SPB_MINUS::numeric % PRD_FRAC::numeric) AS SPB_MINTA_PCS,
            SPB_QTY,
            COALESCE(SPB_RECORDID, '0') AS SPB_RECORDID,
            CASE 
                WHEN COALESCE(SPB_RECORDID, '0') = '2' THEN 'V'
                ELSE ''
            END AS BATAL,
            CASE 
                WHEN COALESCE(SPB_RECORDID, '0') = '0' THEN 'V'
                ELSE ''
            END AS BELUM_TURUN,
            CASE 
                WHEN COALESCE(SPB_RECORDID, '0') = '3' THEN 'V'
                ELSE ''
            END AS DITURUNKAN,
            CASE 
                WHEN COALESCE(SPB_RECORDID, '0') = '1' THEN 'V'
                ELSE ''
            END AS REALISASI,
            CASE 
                WHEN SUBSTR(SPB_LOKASITUJUAN, 1, 1) IN ('D', 'G') THEN 'SPB GUDANG'
                WHEN SPB_LOKASIASAL LIKE '%C%' THEN 'SPB SK TOKO'
                ELSE 'SPB TOKO'
            END AS SPB_LOKASI,
            SPB_MODIFY_BY,
            USERNAME
        FROM 
            TBTEMP_ANTRIANSPB 
        LEFT JOIN 
            tbmaster_prodmast ON prd_prdcd = SPB_PRDCD
        LEFT JOIN 
            TBMASTER_USER ON USERID = SPB_CREATE_BY
        WHERE 
            SPB_QTY <> 0
    ) AS spb_data
    WHERE 
        PLU IS NOT NULL
        AND KET = 'BELUM REALISASI'
    GROUP BY 
        PLU, 
        DESK, 
        PRD_FRAC, 
        PRD_UNIT";

    $stmt = $conn->query($query);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
