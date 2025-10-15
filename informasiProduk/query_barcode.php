<?php
require_once '../helper/connection.php';

$barcodes = [];

if ($kodePLU !== '') {
    $stmt2 = $conn->prepare("SELECT 
        pm.PRD_KODEDIVISI,
        pm.PRD_KODEDEPARTEMENT,
        pm.PRD_PRDCD,
        pm.PRD_DESKRIPSIPANJANG,
        pm.PRD_UNIT,
        pm.PRD_FRAC,
        pm.PRD_HRGJUAL AS PRD_HRGJUAL,
        md.PRMD_HRGJUAL AS MD_HRGJUAL,
        pm.PRD_KODETAG,
        pc.PRC_KODETAG,
        pm.PRD_FLAG_AKTIVASI,
        pm.PRD_AVGCOST,
        pm.PRD_LASTCOST,
        pm.PRD_MINJUAL,
        md.PRMD_HRGJUAL,
        md.PRMD_TGLAWAL,
        md.PRMD_TGLAKHIR,
        bc.BRC_BARCODE,

        CASE 
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - (pm.PRD_AVGCOST * 1.11)) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
            WHEN pm.PRD_FLAGBKP1 IS NULL AND pm.PRD_FLAGBKP2 IN ('N','C') 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
        END AS MARGIN,

        ROUND(
            CASE         
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100   
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
                WHEN pm.PRD_FLAGBKP1 = 'N' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
            END, 2
        ) AS MARGINACOST, 

        ROUND(
            CASE         
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
                WHEN pm.PRD_FLAGBKP1 = 'N' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100        
            END, 2
        ) AS MARGINLCOST,

        CASE 
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN 1.11
            WHEN pm.PRD_FLAGBKP1 = 'N' AND pm.PRD_FLAGBKP2 IN ('N','C') THEN 1
        END AS KALI,

        CASE 
            WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
                THEN COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) / 11.1 * 10
            ELSE COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)
        END AS ST_HARGA_NETTO,

        CASE 
            WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
                THEN md.PRMD_HRGJUAL / 11.1 * 10
            ELSE md.PRMD_HRGJUAL
        END AS ST_MD_NETTO,

        CASE 
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '0' THEN '0'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '1' THEN '1'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '2' THEN '2'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '3' THEN '3'
            ELSE NULL
        END AS sj,

        pm.PRD_FLAGBKP1,
        pm.PRD_FLAGBKP2

    FROM tbmaster_prodmast pm

    LEFT JOIN (
        SELECT prmd_prdcd, prmd_hrgjual, prmd_tglawal, prmd_tglakhir
        FROM tbtr_promomd
        WHERE CURRENT_DATE BETWEEN prmd_tglawal AND prmd_tglakhir
    ) md ON pm.prd_prdcd = md.prmd_prdcd

    LEFT JOIN tbmaster_prodcrm pc ON pm.prd_prdcd = pc.prc_pluigr

    LEFT JOIN (
        SELECT DISTINCT ON (brc_prdcd) brc_prdcd, brc_barcode
        FROM tbmaster_barcode
        ORDER BY brc_prdcd, brc_barcode
    ) bc ON pm.prd_prdcd = bc.brc_prdcd

    WHERE pm.PRD_PRDCD LIKE :kodePLU
    ORDER BY pm.PRD_MINJUAL, pm.PRD_AVGCOST DESC");

    $kodePLU = substr($kodePLU, 0, 6) . '%';
    $stmt2->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
    $stmt2->execute();
    $barcodes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>
