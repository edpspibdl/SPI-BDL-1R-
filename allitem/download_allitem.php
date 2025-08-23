<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\ALL ITEM & PO OUT\\'; // <-- Path diubah kembali ke D:\LAPORAN PAGI\

// Memeriksa apakah folder ada. Jika tidak, buat folder tersebut.
if (!file_exists($tempSavePath)) {
    // Parameter `0777` adalah izin folder (read, write, execute for all).
    // Pertimbangkan untuk menggunakan izin yang lebih ketat seperti `0755` di produksi.
    // Parameter `true` memungkinkan pembuatan direktori secara rekursif.
    if (!mkdir($tempSavePath, 0777, true)) {
        die("Fatal Error: Gagal membuat folder: $tempSavePath. Pastikan PHP memiliki izin untuk membuat folder di lokasi ini.");
    }
    // Opsional: Catat ke log server bahwa folder berhasil dibuat
    error_log("Folder berhasil dibuat: $tempSavePath");
}

// Memeriksa apakah folder dapat ditulisi (setelah dipastikan ada atau dibuat).
if (!is_writable($tempSavePath)) {
    die("Fatal Error: Folder tidak dapat ditulisi: $tempSavePath. Periksa izin folder.");
}

// Query data dari database
$query = "SELECT 
    COALESCE(mpl_kodemonitoring, '-') AS kode_kkpbm,
    prd_kodedivisi AS div,
    prd_kodedepartement AS dep,
    prd_kodekategoribarang AS katb,
    prd_plumcg AS plumcg,
    CAST(prd_prdcd AS NUMERIC) AS pluigr,
    COALESCE(prd_flagigr, 'N') AS igr,
    prd_flagomi AS omi,
    COALESCE(prd_flagidm, 'N') AS idm,
    CASE WHEN mpl_prdcd = prd_prdcd THEN 'Y' ELSE ' ' END AS item_pareto,
    PRD_FLAGHBV AS hbv,
    prd_deskripsipanjang AS deskripsi,
    prd_unit AS unit,
    prd_frac AS frac,
    prd_kodetag AS tagigr,
    COALESCE(prd_flagigr, 'N') AS Flagigr,
    prd_kodetag AS tagigr,
    prd_hrgjual AS hg_jual,
    COALESCE(HRGJ1, 0) AS HRGJ1, 
    COALESCE(HRGJ2, 0) AS HRGJ2, 
    COALESCE(HRGJ3, 0) AS HRGJ3,
    ROUND(st_lastcost) AS lcost,
    CASE prd_flagbkp2
        WHEN 'C' THEN ROUND((prd_hrgjual - (st_lastcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
        WHEN 'N' THEN ROUND((prd_hrgjual - (st_lastcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
        WHEN 'Y' THEN ROUND((prd_hrgjual - (st_lastcost * prd_frac * 1.11))::numeric / prd_hrgjual * 100, 2)
        ELSE ROUND((prd_hrgjual - (st_lastcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
    END AS m_lcost,
    CASE
    WHEN substr(prd_prdcd, 7, 1)::integer = 0 THEN
        ROUND(st_avgcost * prd_frac)
    WHEN substr(prd_prdcd, 7, 1)::integer = 1 THEN
        ROUND(st_avgcost * prd_frac)
    WHEN substr(prd_prdcd, 7, 1)::integer = 2 THEN
        ROUND(st_avgcost * prd_frac)
    WHEN substr(prd_prdcd, 7, 1)::integer = 3 THEN
        ROUND(st_avgcost * prd_frac)
END AS avgcost,
    CASE prd_flagbkp2
        WHEN 'C' THEN ROUND((prd_hrgjual - (st_avgcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
        WHEN 'N' THEN ROUND((prd_hrgjual - (st_avgcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
        WHEN 'Y' THEN ROUND((prd_hrgjual - (st_avgcost * prd_frac * 1.11))::numeric / prd_hrgjual * 100, 2)
        ELSE ROUND((prd_hrgjual - (st_avgcost * prd_frac))::numeric / prd_hrgjual * 100, 2)
    END AS m_acost,
    CONCAT_WS(
        '.', 
        lks_koderak, 
        lks_kodesubrak, 
        lks_tiperak, 
        lks_shelvingrak, 
        lks_nourut
    ) AS lokasi,
    st_saldoawal AS stock_awal,
    st_saldoakhir AS stock_akhir,
    CASE
        WHEN st_sales != 0 THEN
            ROUND((((st_saldoawal + st_saldoakhir) / 2)::numeric / st_sales) * EXTRACT(DAY FROM CURRENT_DATE), 2)
        ELSE 0
    END AS dsi,
    ROUND(
    CASE
        WHEN prd_unit = 'KG' THEN (st_saldoakhir * st_avgcost)::numeric / prd_frac
        ELSE st_saldoakhir * st_avgcost
    END
) AS saldo_rp,
    hg_promo,
    mulai,
    selesai,
    berlaku,
    hgb_persendisc01 AS disk1, hgb_rphdisc01 AS rph_disk1,
    TO_CHAR(hgb_tglmulaidisc01, 'dd-MON-yy') AS mulai_disk1,
    TO_CHAR(hgb_tglakhirdisc01, 'dd-MON-yy') AS selesai_disk1,
    hgb_persendisc02 AS disk2, hgb_rphdisc02 AS rph_disk2,
    TO_CHAR(hgb_tglmulaidisc02, 'dd-MON-yy') AS mulai_disk2,
    TO_CHAR(hgb_tglakhirdisc02, 'dd-MON-yy') AS selesai_disk2,
    lokasi1,
    maxplano_lok1,
    minpct_lok1,
    lokasi2,
    maxplano_lok2,
    minpct_lok2,
    kkdbab,
    CONCAT(prd_dimensipanjang, 'x', prd_dimensilebar, 'x', prd_dimensitinggi) AS pxlxt,
    (prd_dimensipanjang * prd_dimensilebar * prd_dimensitinggi) AS volume,
    maxpalet,
    mindis,
    maxdis,
    prd_minorder,   
    bpb.bpb_pertama,
    pb_terakhir,
    po_terakhir,
    bpb.bpb_terakhir,
    lt,
    koef,
    kph,
    kkpkm.pkm AS pkm,
    kkpkm.pkmt AS pkmt,
    kkpkm.mpkm AS mpkm,
    hgb_nilaidpp as hrgomi,
    qty_1,
    qty_2,
    qty_3,
    avgqty,
    rph_1,
    rph_2,
    rph_3,
    avgrph,
    perday,
    hgb_kodesupplier,
    sup_namasupplier,
    hgb_statusbarang,
    sup_jangkawaktukirimbarang AS jwpb,
    (sup_jangkawaktukirimbarang + 2) AS LT_2,
    CASE sup_harikunjungan
        WHEN 'YY     ' THEN 'MINGGU-SENIN'
        WHEN 'Y Y  Y ' THEN 'MINGGU-SELASA-JUMAT'
        WHEN 'Y   Y  ' THEN 'MINGGU-KAMIS'
        WHEN 'Y  Y   ' THEN 'MINGGU-RABU'
        WHEN 'Y      ' THEN 'MINGGU'
        WHEN ' YYYYYY' THEN 'SENIN-SELASA-RABU-KAMIS-JUMAT-SABTU'
        WHEN ' YYYYY ' THEN 'SENIN-SELASA-RABU-KAMIS-JUMAT'
        WHEN ' Y Y Y ' THEN 'SENIN-RABU-JUMAT'
        WHEN ' Y Y   ' THEN 'SENIN-RABU'
        WHEN ' Y  Y  ' THEN 'SENIN-KAMIS'
        WHEN ' Y   Y ' THEN 'SENIN-JUMAT'
        WHEN ' Y     ' THEN 'SENIN'
        WHEN '  YY   ' THEN 'SELASA-RABU'
        WHEN '  Y Y Y' THEN 'SELASA-KAMIS-SABTU'
        WHEN '  Y Y  ' THEN 'SELASA-KAMIS'
        WHEN '  Y  Y ' THEN 'SELASA-JUMAT'
        WHEN '  Y   Y' THEN 'SELASA-SABTU'
        WHEN '  Y    ' THEN 'SELASA'
        WHEN '   Y  Y' THEN 'RABU-SABTU'
        WHEN '   Y   ' THEN 'RABU'
        WHEN '    Y  ' THEN 'KAMIS'
        WHEN '     Y ' THEN 'JUMAT'
        WHEN '      Y' THEN 'SABTU'
        ELSE 'PERBARUI QUERRY'
    END AS hari_kunjungan
FROM tbmaster_prodmast
LEFT JOIN tbmaster_stock 
    ON st_prdcd = prd_prdcd 
    AND st_lokasi = '01'
LEFT JOIN tbmaster_lokasi 
    ON lks_prdcd = prd_prdcd 
    AND lks_tiperak = 'B'
    AND LKS_KODERAK NOT IN ('HDH')
 LEFT JOIN (
        SELECT lks_prdcd AS plulks1,
            CONCAT(lks_koderak, '.', lks_kodesubrak, '.', lks_tiperak, '.', lks_shelvingrak, '.', lks_nourut) AS lokasi1,
            lks_maxdisplay AS maxdis,
            CONCAT(lks_tirkirikanan, '-', lks_tirdepanbelakang, '-', lks_tiratasbawah) AS kkdbab,
            lks_minpct AS minpct_lok1,
            lks_maxplano AS maxplano_lok1
        FROM tbmaster_lokasi
        WHERE (lks_tiperak = 'B' OR lks_tiperak LIKE 'I%')
            AND lks_prdcd IS NOT NULL
            AND lks_noid IS NULL
            AND lks_koderak <> 'DNEW'
            AND lks_koderak <> 'HDH'
            AND lks_koderak <> 'DVOC'
            AND lks_koderak NOT LIKE '%TAG%'
    ) lokasi2 ON prd_prdcd = plulks1
    LEFT JOIN (
        SELECT lks_prdcd AS plulks2,
            CONCAT(lks_koderak, '.', lks_kodesubrak, '.', lks_tiperak, '.', lks_shelvingrak, '.', lks_nourut) AS lokasi2,
            lks_maxdisplay AS maxdis2,
            lks_minpct AS minpct_lok2,
            lks_maxplano AS maxplano_lok2
        FROM tbmaster_lokasi
        WHERE (lks_tiperak = 'B' OR lks_tiperak LIKE 'I%')
            AND lks_prdcd IS NOT NULL
            AND lks_noid IS NOT NULL
            AND lks_koderak NOT LIKE '%DNEW%'
    ) lokasi3 ON prd_prdcd = plulks2
LEFT JOIN tbmaster_hargabeli 
    ON prd_prdcd = hgb_prdcd
LEFT JOIN tbmaster_supplier 
    ON hgb_kodesupplier = sup_kodesupplier
LEFT JOIN (
    SELECT 
        pkm_prdcd, 
        pkm_pkm AS pkm, 
        pkm_pkmt AS pkmt,
        pkm_mpkm AS mpkm, 
        pkm_mindisplay AS mindis,
        pkm_leadtime AS lt, 
        pkm_koefisien AS koef
    FROM tbmaster_kkpkm
) AS kkpkm 
    ON prd_prdcd = kkpkm.pkm_prdcd
LEFT JOIN tbtr_monitoringplu 
    ON mpl_prdcd = prd_prdcd
LEFT JOIN (
    SELECT 
        CONCAT(SUBSTR(prd_prdcd, 1, 6), '0') AS PLU1, 
        prd_avgcost AS ACOST1, 
        prd_hrgjual AS HRGJ1
     FROM tbmaster_prodmast 
     WHERE SUBSTR(prd_prdcd, 7, 1) = '1'
) AS t1 
    ON PLU1 = PRD_PRDCD
LEFT JOIN (
    SELECT 
        CONCAT(SUBSTR(prd_prdcd, 1, 6), '0') AS PLUHG2, 
        prd_avgcost AS ACOST2, 
        prd_hrgjual AS HRGJ2
     FROM tbmaster_prodmast 
     WHERE SUBSTR(prd_prdcd, 7, 1) = '2'
) AS t2 
    ON PLUHG2 = PRD_PRDCD
LEFT JOIN (
    SELECT 
        CONCAT(SUBSTR(prd_prdcd, 1, 6), '0') AS PLU3, 
        prd_avgcost AS ACOST3, 
        prd_hrgjual AS HRGJ3
     FROM tbmaster_prodmast 
     WHERE SUBSTR(prd_prdcd, 7, 1) = '3'
) AS t3 
    ON PLU3 = PRD_PRDCD
LEFT JOIN (
    SELECT 
        mstd_prdcd, 
        MIN(mstd_tgldoc)::DATE AS bpb_pertama, 
        MAX(mstd_tgldoc)::DATE AS bpb_terakhir
    FROM tbtr_mstran_d
    WHERE mstd_typetrn = 'B'
    GROUP BY mstd_prdcd
) AS bpb 
    ON prd_prdcd = mstd_prdcd
LEFT JOIN (
    SELECT 
        tpod_prdcd, 
        MAX(tpod_tglpo)::DATE AS po_terakhir, 
        pb.pb_terakhir
    FROM tbtr_po_d
    LEFT JOIN (
        SELECT 
            pbd_prdcd, 
            MAX(pbd_create_dt)::DATE AS pb_terakhir
        FROM tbtr_pb_d
        GROUP BY pbd_prdcd
    ) AS pb 
        ON tpod_prdcd = pb.pbd_prdcd
    GROUP BY tpod_prdcd, pb.pb_terakhir
) AS po 
    ON prd_prdcd = tpod_prdcd
    LEFT JOIN (
        SELECT prmd_prdcd, prmd_hrgjual AS hg_promo,
            TO_CHAR(prmd_tglawal, 'dd-MON-yy') AS mulai,
            TO_CHAR(prmd_tglakhir, 'dd-MON-yy') AS selesai,
            prmd_kelompokmember AS berlaku
        FROM tbtr_promomd
        WHERE prmd_tglakhir >= CURRENT_DATE
    ) promo ON prd_prdcd = prmd_prdcd
     LEFT JOIN (
        SELECT mpt_prdcd, mpt_maxqty AS maxpalet
        FROM tbmaster_maxpalet
    ) maxpalet ON prd_prdcd = mpt_prdcd
     LEFT JOIN (
        SELECT plu_prodmast, prdcd, ksl_mean AS kph
        FROM tbmaster_kph
        JOIN (
            SELECT prd_prdcd AS plu_prodmast, prd_plumcg AS plu_mcg
            FROM tbmaster_prodmast
            WHERE SUBSTRING(prd_prdcd, 7, 1) = '0'
        ) prodmast ON plu_mcg = prdcd
        WHERE pid = (
            WITH bulan_tahun AS (
                SELECT 
                    CASE 
                        WHEN EXTRACT(MONTH FROM CURRENT_DATE) = 1 THEN 12
                        ELSE EXTRACT(MONTH FROM CURRENT_DATE) - 1 
                    END AS bulan,
                    CASE 
                        WHEN EXTRACT(MONTH FROM CURRENT_DATE) = 1 THEN EXTRACT(YEAR FROM CURRENT_DATE) - 1
                        ELSE EXTRACT(YEAR FROM CURRENT_DATE) 
                    END AS tahun
            )
            SELECT CONCAT(bulan::text, tahun::text)
            FROM bulan_tahun
        )
    ) kph ON prd_prdcd = plu_prodmast
    LEFT JOIN (
        SELECT
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_qty_10
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_qty_11
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_qty_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_qty_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_qty_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_qty_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_qty_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_qty_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_qty_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_qty_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_qty_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_qty_09
            END AS qty_1,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_qty_11
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_qty_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_qty_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_qty_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_qty_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_qty_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_qty_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_qty_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_qty_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_qty_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_qty_09
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_qty_10
            END AS qty_2,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_qty_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_qty_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_qty_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_qty_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_qty_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_qty_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_qty_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_qty_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_qty_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_qty_09
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_qty_10
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_qty_11
            END AS qty_3,
            sls_prdcd,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN ROUND((COALESCE(sls_qty_12, 0) + COALESCE(sls_qty_11, 0) + COALESCE(sls_qty_10, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN ROUND((COALESCE(sls_qty_01, 0) + COALESCE(sls_qty_12, 0) + COALESCE(sls_qty_11, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN ROUND((COALESCE(sls_qty_02, 0) + COALESCE(sls_qty_01, 0) + COALESCE(sls_qty_12, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN ROUND((COALESCE(sls_qty_03, 0) + COALESCE(sls_qty_02, 0) + COALESCE(sls_qty_01, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN ROUND((COALESCE(sls_qty_04, 0) + COALESCE(sls_qty_03, 0) + COALESCE(sls_qty_02, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN ROUND((COALESCE(sls_qty_05, 0) + COALESCE(sls_qty_04, 0) + COALESCE(sls_qty_03, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN ROUND((COALESCE(sls_qty_06, 0) + COALESCE(sls_qty_05, 0) + COALESCE(sls_qty_04, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN ROUND((COALESCE(sls_qty_07, 0) + COALESCE(sls_qty_06, 0) + COALESCE(sls_qty_05, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN ROUND((COALESCE(sls_qty_08, 0) + COALESCE(sls_qty_07, 0) + COALESCE(sls_qty_06, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN ROUND((COALESCE(sls_qty_09, 0) + COALESCE(sls_qty_08, 0) + COALESCE(sls_qty_07, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN ROUND((COALESCE(sls_qty_10, 0) + COALESCE(sls_qty_09, 0) + COALESCE(sls_qty_08, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN ROUND((COALESCE(sls_qty_11, 0) + COALESCE(sls_qty_10, 0) + COALESCE(sls_qty_09, 0))::numeric / 3, 2)
            END AS avgqty,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_rph_10
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_rph_11
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_rph_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_rph_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_rph_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_rph_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_rph_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_rph_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_rph_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_rph_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_rph_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_rph_09
            END AS rph_1,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_rph_11
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_rph_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_rph_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_rph_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_rph_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_rph_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_rph_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_rph_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_rph_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_rph_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_rph_09
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_rph_10
            END AS rph_2,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN sls_rph_12
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN sls_rph_01
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN sls_rph_02
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN sls_rph_03
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN sls_rph_04
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN sls_rph_05
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN sls_rph_06
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN sls_rph_07
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN sls_rph_08
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN sls_rph_09
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN sls_rph_10
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN sls_rph_11
            END AS rph_3,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN ROUND((COALESCE(sls_rph_12, 0) + COALESCE(sls_rph_11, 0) + COALESCE(sls_rph_10, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN ROUND((COALESCE(sls_rph_01, 0) + COALESCE(sls_rph_12, 0) + COALESCE(sls_rph_11, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN ROUND((COALESCE(sls_rph_02, 0) + COALESCE(sls_rph_01, 0) + COALESCE(sls_rph_12, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN ROUND((COALESCE(sls_rph_03, 0) + COALESCE(sls_rph_02, 0) + COALESCE(sls_rph_01, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN ROUND((COALESCE(sls_rph_04, 0) + COALESCE(sls_rph_03, 0) + COALESCE(sls_rph_02, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN ROUND((COALESCE(sls_rph_05, 0) + COALESCE(sls_rph_04, 0) + COALESCE(sls_rph_03, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN ROUND((COALESCE(sls_rph_06, 0) + COALESCE(sls_rph_05, 0) + COALESCE(sls_rph_04, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN ROUND((COALESCE(sls_rph_07, 0) + COALESCE(sls_rph_06, 0) + COALESCE(sls_rph_05, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN ROUND((COALESCE(sls_rph_08, 0) + COALESCE(sls_rph_07, 0) + COALESCE(sls_rph_06, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN ROUND((COALESCE(sls_rph_09, 0) + COALESCE(sls_rph_08, 0) + COALESCE(sls_rph_07, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN ROUND((COALESCE(sls_rph_10, 0) + COALESCE(sls_rph_09, 0) + COALESCE(sls_rph_08, 0))::numeric / 3, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN ROUND((COALESCE(sls_rph_11, 0) + COALESCE(sls_rph_10, 0) + COALESCE(sls_rph_09, 0))::numeric / 3, 2)
            END AS avgrph,
            CASE
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '01' THEN ROUND(((COALESCE(sls_rph_12, 0) + COALESCE(sls_rph_11, 0) + COALESCE(sls_rph_10, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '02' THEN ROUND(((COALESCE(sls_rph_01, 0) + COALESCE(sls_rph_12, 0) + COALESCE(sls_rph_11, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '03' THEN ROUND(((COALESCE(sls_rph_02, 0) + COALESCE(sls_rph_01, 0) + COALESCE(sls_rph_12, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '04' THEN ROUND(((COALESCE(sls_rph_03, 0) + COALESCE(sls_rph_02, 0) + COALESCE(sls_rph_01, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '05' THEN ROUND(((COALESCE(sls_rph_04, 0) + COALESCE(sls_rph_03, 0) + COALESCE(sls_rph_02, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '06' THEN ROUND(((COALESCE(sls_rph_05, 0) + COALESCE(sls_rph_04, 0) + COALESCE(sls_rph_03, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '07' THEN ROUND(((COALESCE(sls_rph_06, 0) + COALESCE(sls_rph_05, 0) + COALESCE(sls_rph_04, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '08' THEN ROUND(((COALESCE(sls_rph_07, 0) + COALESCE(sls_rph_06, 0) + COALESCE(sls_rph_05, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '09' THEN ROUND(((COALESCE(sls_rph_08, 0) + COALESCE(sls_rph_07, 0) + COALESCE(sls_rph_06, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '10' THEN ROUND(((COALESCE(sls_rph_09, 0) + COALESCE(sls_rph_08, 0) + COALESCE(sls_rph_07, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '11' THEN ROUND(((COALESCE(sls_rph_10, 0) + COALESCE(sls_rph_09, 0) + COALESCE(sls_rph_08, 0))::numeric / 3) / 30, 2)
                WHEN TO_CHAR(CURRENT_DATE, 'MM') = '12' THEN ROUND(((COALESCE(sls_rph_11, 0) + COALESCE(sls_rph_10, 0) + COALESCE(sls_rph_09, 0))::numeric / 3) / 30, 2)
            END AS perday
        FROM tbtr_salesbulanan
    ) sales ON prd_prdcd = sls_prdcd
WHERE prd_recordid IS NULL
 AND hgb_tipe = '2' AND hgb_tipe = '2'
"; // Ganti dengan query Anda

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Ambil kolom pertama untuk menentukan header
    $columns = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$columns) {
        die("Tidak ada data yang ditemukan dari query. Pastikan query mengembalikan hasil.");
    }

    // Ambil nama kolom untuk header Excel
    $columnNames = array_map('strtoupper', array_keys($columns));

    // Tentukan nama file dengan tanggal saat ini
    $date = date('Y-m-d');
    $filename = "ALL_ITEM_SPI_BDL_1R_$date.xlsx";
    $filePath = $tempSavePath . $filename; // Menentukan lokasi penyimpanan file sementara

    // Inisialisasi objek writer
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));

    // Loop untuk menulis setiap row ke Excel
    do {
        $writer->writeSheetRow('Sheet1', $columns);
    } while ($columns = $stmt->fetch(PDO::FETCH_ASSOC));


    // Simpan file sementara di server
    if ($writer->writeToFile($filePath)) {
        // Cek apakah file berhasil disimpan
        if (file_exists($filePath)) {
            // Log server untuk memastikan file sudah ditemukan
            error_log("File berhasil disimpan di: " . $filePath);

            // Mulai pengunduhan file
            ob_end_flush(); // Flush output buffer

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            // Baca file dan kirimkan ke browser
            readfile($filePath);

            // Hapus file sementara setelah pengunduhan
            unlink($filePath);
            exit;
        } else {
            // Log error jika file tidak ditemukan
            error_log("File tidak ditemukan di: " . $filePath);
            die("File tidak ditemukan di: " . $filePath);
        }
    } else {
        // Log error jika gagal menyimpan file
        error_log("Gagal menyimpan file di: " . $filePath);
        die("Gagal menyimpan file di: " . $filePath);
    }
} catch (PDOException $e) {
    error_log("Query gagal: " . $e->getMessage());
    die("Query database gagal: " . $e->getMessage());
}

$conn = null;
exit;
