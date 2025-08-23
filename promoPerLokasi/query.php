<?php
require_once '../helper/connection.php';

try {
    $query = "SELECT 
    l.lks_koderak || '.' || l.lks_kodesubrak || '.' || l.lks_tiperak || '.' || l.lks_shelvingrak AS lokasi,
    l.lks_prdcd,
    prd.prd_deskripsipanjang,
    p.pro_cb_gf,
    p.pro_kode_promosi,
    p.pro_nama_promosi,
    p.pro_member,
    p.pro_mulai,
    p.pro_selesai,
    p.pro_cashback,
    p.pro_hadiah,
    p.pro_beli_rph,
    p.pro_beli_qty,
    p.pro_hadiah_qty,
    p.pro_alokasi,
    p.pro_hdh_keluar,
    p.pro_hdh_sisa
FROM tbmaster_lokasi l
JOIN (
    -- Gift Promotion Subquery
    SELECT 
        D.GFD_PRDCD AS PRO_PRDCD,
        'GIFT' AS PRO_CB_GF,
        H.GFH_KODEPROMOSI AS PRO_KODE_PROMOSI,
        H.GFH_NAMAPROMOSI AS PRO_NAMA_PROMOSI,
        CASE
            WHEN A.GFA_REGULER = '1' AND A.GFA_RETAILER = '0' THEN 'BIRU'
            WHEN A.GFA_REGULER = '0' AND A.GFA_RETAILER = '1' THEN 'MERAH'
            WHEN A.GFA_REGULER = '1' AND A.GFA_RETAILER = '1' THEN 'ALL'
        END AS PRO_MEMBER,
        H.GFH_TGLAWAL AS PRO_MULAI,
        H.GFH_TGLAKHIR AS PRO_SELESAI,
        0 AS PRO_CASHBACK,  -- Set to 0 for gift promotions
        H.GFH_KETHADIAH AS PRO_HADIAH,
        H.GFH_MINTOTSPONSOR AS PRO_BELI_RPH,
        D.GFD_PCS AS PRO_BELI_QTY,
        H.GFH_JMLHADIAH AS PRO_HADIAH_QTY,
        A.GFA_ALOKASIJUMLAH AS PRO_ALOKASI,
        COALESCE(HDH.JMLH_HADIAH, 0) AS PRO_HDH_KELUAR,
        A.GFA_ALOKASIJUMLAH - COALESCE(HDH.JMLH_HADIAH, 0) AS PRO_HDH_SISA
    FROM TBTR_GIFT_HDR H
    JOIN TBTR_GIFT_DTL D ON H.GFH_KODEPROMOSI = D.GFD_KODEPROMOSI
    JOIN TBTR_GIFT_ALOKASI A ON H.GFH_KODEPROMOSI = A.GFA_KODEPROMOSI
    LEFT JOIN (
        SELECT KD_PROMOSI, SUM(JMLH_HADIAH) AS JMLH_HADIAH 
        FROM M_GIFT_H 
        WHERE TIPE = 'GF' 
        GROUP BY KD_PROMOSI
    ) HDH ON H.GFH_KODEPROMOSI = HDH.KD_PROMOSI
    WHERE DATE(H.GFH_TGLAKHIR) >= CURRENT_DATE 
      AND DATE(H.GFH_TGLAWAL) <= CURRENT_DATE
    
    UNION ALL
    
    -- Cashback Promotion Subquery
    SELECT 
        D.CBD_PRDCD AS PRO_PRDCD,
        'CASHBACK' AS PRO_CB_GF,
        H.CBH_KODEPROMOSI AS PRO_KODE_PROMOSI,
        H.CBH_NAMAPROMOSI AS PRO_NAMA_PROMOSI,
        CASE
            WHEN CBA_REGULER = '1' AND CBA_RETAILER = '0' THEN 'BIRU'
            WHEN CBA_REGULER = '0' AND CBA_RETAILER = '1' THEN 'MERAH'
            WHEN CBA_REGULER = '1' AND CBA_RETAILER = '1' THEN 'ALL'
        END AS PRO_MEMBER,
        H.CBH_TGLAWAL AS PRO_MULAI,
        H.CBH_TGLAKHIR AS PRO_SELESAI,
        COALESCE(NULLIF(D.CBD_CASHBACK, 0), NULLIF(H.CBH_CASHBACK, 0), 0) AS PRO_CASHBACK,
        '' AS PRO_HADIAH,
        COALESCE(H.CBH_MINRPHPRODUKPROMO, 0) AS PRO_BELI_RPH,
        COALESCE(D.CBD_MINSTRUK, 0) AS PRO_BELI_QTY,
        0 AS PRO_HADIAH_QTY,
        A.CBA_ALOKASIJUMLAH AS PRO_ALOKASI,
        0 AS PRO_HDH_KELUAR,
        A.CBA_ALOKASIJUMLAH AS PRO_HDH_SISA
    FROM TBTR_CASHBACK_HDR H
    JOIN TBTR_CASHBACK_DTL D ON H.CBH_KODEPROMOSI = D.CBD_KODEPROMOSI
    JOIN TBTR_CASHBACK_ALOKASI A ON H.CBH_KODEPROMOSI = A.CBA_KODEPROMOSI
    WHERE DATE(H.CBH_TGLAKHIR) >= CURRENT_DATE 
      AND DATE(H.CBH_TGLAWAL) <= CURRENT_DATE
) p ON l.lks_prdcd = p.pro_prdcd
JOIN tbmaster_prodmast prd ON l.lks_prdcd = prd.prd_prdcd
WHERE (l.lks_koderak LIKE 'GF%' OR l.lks_koderak LIKE 'D%')
  AND l.lks_tiperak <> 'S'
ORDER BY l.lks_koderak, l.lks_kodesubrak, l.lks_tiperak, l.lks_shelvingrak, l.lks_prdcd, p.pro_kode_promosi";

    $stmt = $conn->query($query);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
