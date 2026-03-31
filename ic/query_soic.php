<?php
function getSoicData($conn, $tglAwal, $tglAkhir) {
    try {
        $sql = "SELECT *, 
                    mso_flagtahap, 
                    mso_flagcetak,
                    CASE 
                        WHEN mso_flagreset = 'Y' THEN 'SO IC SELESAI'
                        WHEN (mso_flagreset IS NULL OR mso_flagreset != 'Y') 
                             AND EXTRACT(MONTH FROM mso_tglso) = EXTRACT(MONTH FROM CURRENT_DATE) 
                             AND EXTRACT(YEAR FROM mso_tglso) = EXTRACT(YEAR FROM CURRENT_DATE) 
                        THEN 'SO IC SEDANG BERJALAN'
                        ELSE 'SO EXPIRED / NON-AKTIF' 
                    END AS status_so_detail
                FROM tbmaster_setting_soic
                WHERE mso_tglso BETWEEN :tglAwal AND :tglAkhir
                ORDER BY mso_tglso DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':tglAwal', $tglAwal);
        $stmt->bindValue(':tglAkhir', $tglAkhir);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}


function getDetailReset($conn, $tglAwal, $tglAkhir) {
    try {
        // Konversi format tanggal untuk filter TO_CHAR (YYYYMMDD)
        $t1 = date('Ymd', strtotime($tglAwal));
        $t2 = date('Ymd', strtotime($tglAkhir));

        $sql = "SELECT 
                    PRD_KODEDIVISI,
                    RSO_PRDCD,
                    PRD_DESKRIPSIPANJANG,
                    PRD_FRAC,
                    PRD_UNIT,
                    SUM(RSO_QTYRESET) AS QTY_RESET,
                    RSO_AVGCOSTRESET
                FROM 
                    TBTR_RESET_SOIC
                LEFT JOIN 
                    tbmaster_prodmast ON prd_prdcd = RSO_PRDCD
                WHERE 
                    RSO_LOKASI = '01'
                    AND TO_CHAR(RSO_TGLSO, 'YYYYMMDD') BETWEEN :t1 AND :t2
                GROUP BY 
                    PRD_KODEDIVISI,
                    RSO_PRDCD,
                    PRD_DESKRIPSIPANJANG,
                    PRD_FRAC,
                    PRD_UNIT,
                    RSO_AVGCOSTRESET
                ORDER BY PRD_KODEDIVISI, RSO_PRDCD";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':t1', $t1);
        $stmt->bindValue(':t2', $t2);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>