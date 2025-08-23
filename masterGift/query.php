<?php
require_once '../helper/connection.php';

try {
    $query = "    SELECT COALESCE(ALAMAT, 'TDK ADA ALAMAT HDH') AS ALAMAT,
       GFH_KODEPROMOSI,
       GFH_KETHADIAH,
       GFH_NAMAPROMOSI,
       GFH_TGLAWAL,
       GFH_TGLAKHIR AS TGL_BERLAKU_AKHIR,
       COALESCE(LPP, 0) AS LPP,
       COALESCE(PLANO, 0) AS PLANO,
       COALESCE(sum(CAST(JMLH_HADIAH AS numeric)), 0) AS PENGELUARAN
FROM tbtr_gift_hdr
LEFT JOIN (
    SELECT LKS_KODERAK || '.' || LKS_KODESUBRAK || '.' || LKS_TIPERAK || '.' || LKS_SHELVINGRAK || '.' || LKS_NOURUT AS ALAMAT,
           LKS_PRDCD,
           LKS_QTY AS PLANO
    FROM tbmaster_lokasi
    WHERE substring(LKS_KODERAK from 1 for 3) = 'HDH'
) AS lokasi ON lokasi.LKS_PRDCD = COALESCE(substring(GFH_KETHADIAH from 1 for 7), 'POINT')
LEFT JOIN M_GIFT_H ON GFH_KODEPROMOSI = KD_PROMOSI
LEFT JOIN (
    SELECT st_prdcd,
           COALESCE(st_saldoakhir, 0) AS LPP
    FROM tbmaster_stock
    WHERE st_lokasi = '01'
) AS stock ON stock.st_prdcd = COALESCE(substring(GFH_KETHADIAH from 1 for 7), 'POINT')
GROUP BY GFH_KODEPROMOSI, GFH_NAMAPROMOSI, GFH_KETHADIAH, GFH_TGLAWAL, ALAMAT, LPP, PLANO, GFH_TGLAKHIR
ORDER BY GFH_TGLAWAL DESC";

    $stmt = $conn->query($query);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
