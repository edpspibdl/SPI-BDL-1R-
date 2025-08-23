<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil nilai dari form, default ke tanggal hari ini jika kosong
$input_value = isset($_POST['spd_days']) ? $_POST['spd_days'] : date('Y-m-d');

// Query pertama: Data penjualan per hari
$query1 = "
SELECT JS_CASHIERID, JS_CASHIERSTATION, JS_TRANSACTIONDATE, JS_CASHDRAWEREND, 
       CASE 
           WHEN JS_CASHDRAWEREND IS NULL THEN 'BLM CLOSING' 
           ELSE 'SDH CLOSING' 
       END AS KET
FROM tbtr_jualsummary
WHERE JS_CASHIERID <> 'ONL'
AND JS_TRANSACTIONDATE::DATE = :input_value;
";
$stmt1 = $conn->prepare($query1);
$stmt1->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt1->execute();
$result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Query kedua: Data transaksi yang belum closing
$query2 = "
-- MEMBER YANG SUDAH TERIMA KARTU (HANYA UNTUK TANGGAL HARI INI)
SELECT COUNT(cus_kodemember)
FROM TBMASTER_CUSTOMER
WHERE cus_kodeigr = (SELECT prs_kodeigr FROM TBMASTER_PERUSAHAAN)
  AND (
       cus_tglmulai::DATE <= CAST(:input_value AS DATE) 
       OR cus_tglregistrasi::DATE <= CAST(:input_value AS DATE)
      )
  AND COALESCE(cus_flagmemberkhusus, 'N') = 'Y'
  AND cus_recordid IS NULL
  AND cus_kodemember NOT IN (SELECT tko_kodecustomer FROM TBMASTER_TOKOIGR);;
";
$stmt2 = $conn->prepare($query2);
$stmt2->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt2->execute();
$result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Query ketiga : member bulan berjalan
$query3 = "
SELECT 
  COUNT(*) AS hitung,
  COUNT(CASE WHEN kunj = 1 THEN 1 END) AS belanja_1x,
  COUNT(CASE WHEN kunj > 1 THEN 1 END) AS belanja_2x
FROM (
  SELECT jh_cus_kodemember, COUNT(kunj) AS kunj
  FROM (
    SELECT DISTINCT jh_cus_kodemember, DATE_TRUNC('day', JH_TRANSACTIONDATE) AS kunj
    FROM tbtr_jualheader
    LEFT JOIN tbmaster_customer ON cus_kodemember = jh_cus_kodemember
    WHERE COALESCE(cus_flagmemberkhusus, 'N') = 'Y'  -- MEMBER MERAH = 'Y', MEMBER BIRU <> 'Y'
      AND JH_TRANSACTIONTYPE = 'S'
      AND CUS_KODEIGR = '1R'
      AND CUS_KODEMEMBER NOT IN (SELECT TKO_KODECUSTOMER FROM TBMASTER_TOKOIGR)
      AND JH_TRANSACTIONDATE >= DATE_TRUNC('month', CURRENT_DATE)  -- AWAL BULAN OTOMATIS SESUAI BULAN SAAT INI
      AND JH_TRANSACTIONDATE <= TO_DATE(:input_value, 'YYYY-MM-DD') + INTERVAL '1 day' - INTERVAL '1 second'
  ) AS subquery
  GROUP BY jh_cus_kodemember
) AS final_query
";

$stmt3 = $conn->prepare($query3);
$stmt3->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt3->execute();
$result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);


// Query kedua: SALES
$query4 = "
-- SALES --
SELECT
    COUNT(DISTINCT dtl_cusno) AS MEMBER,
    CAST(SUM(dtl_netto) AS INTEGER) AS SPD,
    COUNT(DISTINCT dtl_struk) AS STD,
    CAST(SUM(dtl_margin) AS INTEGER) AS MARGIN,
    COUNT(dtl_prdcd_ctn) AS PRODUKMIX,
    COUNT(DISTINCT dtl_prdcd_ctn) AS PRODUKDIBELI
FROM (
    SELECT
        dtl_rtype,
        dtl_tanggal,
        dtl_struk,
        dtl_stat,
        dtl_kasir,
        dtl_no_struk,
        dtl_seqno,
        dtl_prdcd_ctn,
        dtl_prdcd,
        dtl_nama_barang,
        dtl_unit,
        dtl_frac,
        dtl_tag,
        dtl_bkp,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_qty_pcs
            ELSE dtl_qty_pcs * -1
        END AS dtl_qty_pcs,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_qty
            ELSE dtl_qty * -1
        END AS dtl_qty,
        dtl_harga_jual,
        dtl_diskon,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_gross
            ELSE dtl_gross * -1
        END AS dtl_gross,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_netto
            ELSE dtl_netto * -1
        END AS dtl_netto,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_hpp
            ELSE dtl_hpp * -1
        END AS dtl_hpp,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
            ELSE (dtl_netto - dtl_hpp) * -1
        END AS dtl_margin,
        dtl_k_div,
        dtl_nama_div,
        dtl_k_dept,
        dtl_nama_dept,
        dtl_k_katb,
        dtl_nama_katb,
        dtl_kodetokoomi,
        dtl_cusno,
        dtl_namamember,
        dtl_memberkhusus,
        dtl_outlet,
        dtl_suboutlet,
        CUS_JENISMEMBER,
        CASE
            WHEN (dtl_memberkhusus = 'Y' AND CUS_JENISMEMBER = 'T') THEN 'TMI'
            WHEN dtl_memberkhusus = 'Y' THEN 'KHUSUS'
            WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') AND CUS_JENISMEMBER = 'I' THEN 'IDM'
            WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'OMI'
            ELSE 'REGULER'
        END AS dtl_tipemember,
        CASE
            WHEN dtl_memberkhusus = 'Y' THEN 'GROUP_1_KHUSUS'
            WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') THEN 'GROUP_2_IDM'
            WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'GROUP_3_OMI'
            WHEN dtl_memberkhusus IS NULL AND dtl_outlet = '6' THEN 'GROUP_4_END_USER'
            ELSE 'GROUP_5_OTHERS'
        END AS dtl_group_member,
        dtl_kodesupplier,
        dtl_namasupplier,
        dtl_belanja_pertama,
        dtl_belanja_terakhir
    FROM (
        SELECT
            sls.trjd_transactiontype AS dtl_rtype,
            DATE_TRUNC('day', sls.trjd_transactiondate) AS dtl_tanggal,
            TO_CHAR(DATE_TRUNC('day', sls.trjd_transactiondate), 'yyyy.mm.dd')
                || '-' || sls.trjd_cashierstation
                || '-' || sls.trjd_create_by
                || '-' || sls.trjd_transactionno
                || '-' || sls.trjd_transactiontype AS dtl_struk,
            sls.trjd_cashierstation AS dtl_stat,
            sls.trjd_create_by AS dtl_kasir,
            sls.trjd_transactionno AS dtl_no_struk,
            sls.trjd_seqno AS dtl_seqno,
            Substr(sls.trjd_prdcd, 1, 6) || '0' AS dtl_prdcd_ctn,
            sls.trjd_prdcd AS dtl_prdcd,
            prd.prd_deskripsipanjang AS dtl_nama_barang,
            prd.prd_unit AS dtl_unit,
            prd.prd_frac AS dtl_frac,
            COALESCE(prd.prd_kodetag, ' ') AS dtl_tag,
            sls.trjd_flagtax1 AS dtl_bkp,
            CASE
                WHEN prd.prd_unit = 'KG' AND prd.prd_frac = 1000 THEN sls.trjd_quantity
                ELSE sls.trjd_quantity * prd.prd_frac
            END AS dtl_qty_pcs,
            sls.trjd_quantity AS dtl_qty,
            sls.trjd_unitprice AS dtl_harga_jual,
            sls.trjd_discount AS dtl_diskon,
            CASE
                WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                    AND sls.trjd_create_by IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                    sls.trjd_nominalamt * 11.1 / 10
                ELSE sls.trjd_nominalamt
            END AS dtl_gross,
            CASE
                WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                    AND sls.trjd_create_by NOT IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                    sls.trjd_nominalamt / 11.1 * 10
                ELSE sls.trjd_nominalamt
            END AS dtl_netto,
            CASE
                WHEN prd.prd_unit = 'KG' THEN
                    sls.trjd_quantity * sls.trjd_baseprice / 1000
                ELSE
                    sls.trjd_quantity * sls.trjd_baseprice
            END AS dtl_hpp,
            Trim(sls.trjd_divisioncode) AS dtl_k_div,
            div.div_namadivisi AS dtl_nama_div,
            Substr(sls.trjd_division, 1, 2) AS dtl_k_dept,
            dep.dep_namadepartement AS dtl_nama_dept,
            Substr(sls.trjd_division, 3, 2) AS dtl_k_katb,
            kat.kat_namakategori AS dtl_nama_katb,
            tko.tko_kodeomi AS dtl_kodetokoomi,
            sls.trjd_cus_kodemember AS dtl_cusno,
            cus.cus_namamember AS dtl_namamember,
            cus.cus_flagmemberkhusus AS dtl_memberkhusus,
            cus.cus_kodeoutlet AS dtl_outlet,
            cus.cus_kodesuboutlet AS dtl_suboutlet,
            cus.CUS_JENISMEMBER AS CUS_JENISMEMBER,
            sup.hgb_kodesupplier AS dtl_kodesupplier,
            sup.sup_namasupplier AS dtl_namasupplier,
            akt.jh_belanja_pertama AS dtl_belanja_pertama,
            akt.jh_belanja_terakhir AS dtl_belanja_terakhir
        FROM tbtr_jualdetail sls
        LEFT JOIN tbmaster_prodmast prd ON sls.trjd_prdcd = prd.prd_prdcd
        LEFT JOIN tbmaster_customer cus ON sls.trjd_cus_kodemember = cus.cus_kodemember
        LEFT JOIN tbmaster_tokoigr tko ON sls.trjd_cus_kodemember = tko.tko_kodecustomer
        LEFT JOIN tbmaster_divisi div ON sls.trjd_divisioncode = div.div_kodedivisi
        LEFT JOIN tbmaster_departement dep ON Substr(sls.trjd_division, 1, 2) = dep.dep_kodedepartement
        LEFT JOIN (
            SELECT kat_kodedepartement || kat_kodekategori AS kat_kodekategori, kat_namakategori
            FROM tbmaster_kategori
        ) kat ON sls.trjd_division = kat.kat_kodekategori
        LEFT JOIN (
            SELECT m.hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier
            FROM tbmaster_hargabeli m
            LEFT JOIN tbmaster_supplier s ON m.hgb_kodesupplier = s.sup_kodesupplier
            WHERE m.hgb_tipe = '2' AND m.hgb_recordid IS NULL
        ) sup ON Substr(sls.trjd_prdcd, 1, 6) || '0' = sup.hgb_prdcd
        LEFT JOIN (
            SELECT jh_cus_kodemember, DATE_TRUNC('day', Min(jh_transactiondate)) AS jh_belanja_pertama, DATE_TRUNC('day', Max(jh_transactiondate)) AS jh_belanja_terakhir
            FROM tbtr_jualheader
            WHERE jh_cus_kodemember IS NOT NULL
            GROUP BY jh_cus_kodemember
        ) akt ON sls.trjd_cus_kodemember = akt.jh_cus_kodemember
        WHERE sls.trjd_recordid IS NULL AND sls.trjd_quantity <> 0
    ) AS dtl_inner
) AS dtl_outer
WHERE dtl_tanggal::DATE = TO_DATE(:input_value, 'YYYY-MM-DD')
HAVING COALESCE(SUM(dtl_netto), 0) <> 0
";
$stmt4 = $conn->prepare($query4);
$stmt4->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt4->execute();
$result4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);


// Query kedua: SALES TOBACO
$query5 = "
-- SALES TOBACOO --
SELECT 
       COUNT(DISTINCT(dtl_cusno)) AS MEMBER_BELI_TOBACOO,
       CAST(SUM(dtl_netto)AS INTEGER)      AS SPD_TOBACOO,      
       CAST(SUM(dtl_margin)AS INTEGER)     AS MARGIN_TOBACOO
FROM (
    SELECT
        dtl_rtype,
        dtl_tanggal,
        dtl_struk,
        dtl_stat,
        dtl_kasir,
        dtl_no_struk,
        dtl_seqno,
        dtl_prdcd_ctn,
        dtl_prdcd,
        dtl_nama_barang,
        dtl_unit,
        dtl_frac,
        dtl_tag,
        dtl_bkp,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_qty_pcs
            ELSE dtl_qty_pcs * -1
        END AS dtl_qty_pcs,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_qty
            ELSE dtl_qty * -1
        END AS dtl_qty,
        dtl_harga_jual,
        dtl_diskon,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_gross
            ELSE dtl_gross * -1
        END AS dtl_gross,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_netto
            ELSE dtl_netto * -1
        END AS dtl_netto,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_hpp
            ELSE dtl_hpp * -1
        END AS dtl_hpp,
        CASE
            WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
            ELSE (dtl_netto - dtl_hpp) * -1
        END AS dtl_margin,
        dtl_k_div,
        dtl_nama_div,
        dtl_k_dept,
        dtl_nama_dept,
        dtl_k_katb,
        dtl_nama_katb,
        dtl_kodetokoomi,
        dtl_cusno,
        dtl_namamember,
        dtl_memberkhusus,
        dtl_outlet,
        dtl_suboutlet,
        CUS_JENISMEMBER,
        CASE
            WHEN (dtl_memberkhusus = 'Y' AND CUS_JENISMEMBER = 'T') THEN 'TMI'
            WHEN dtl_memberkhusus = 'Y' THEN 'KHUSUS'
            WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') AND CUS_JENISMEMBER = 'I' THEN 'IDM'
            WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'OMI'
            ELSE 'REGULER'
        END AS dtl_tipemember,
        CASE
            WHEN dtl_memberkhusus = 'Y' THEN 'GROUP_1_KHUSUS'
            WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') THEN 'GROUP_2_IDM'
            WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'GROUP_3_OMI'
            WHEN dtl_memberkhusus IS NULL AND dtl_outlet = '6' THEN 'GROUP_4_END_USER'
            ELSE 'GROUP_5_OTHERS'
        END AS dtl_group_member,
        dtl_kodesupplier,
        dtl_namasupplier,
        dtl_belanja_pertama,
        dtl_belanja_terakhir
    FROM (
        SELECT
            sls.trjd_transactiontype AS dtl_rtype,
            DATE_TRUNC('day', sls.trjd_transactiondate) AS dtl_tanggal,
            TO_CHAR(DATE_TRUNC('day', sls.trjd_transactiondate), 'yyyy.mm.dd')
                || '-' || sls.trjd_cashierstation
                || '-' || sls.trjd_create_by
                || '-' || sls.trjd_transactionno
                || '-' || sls.trjd_transactiontype AS dtl_struk,
            sls.trjd_cashierstation AS dtl_stat,
            sls.trjd_create_by AS dtl_kasir,
            sls.trjd_transactionno AS dtl_no_struk,
            sls.trjd_seqno AS dtl_seqno,
            Substr(sls.trjd_prdcd, 1, 6) || '0' AS dtl_prdcd_ctn,
            sls.trjd_prdcd AS dtl_prdcd,
            prd.prd_deskripsipanjang AS dtl_nama_barang,
            prd.prd_unit AS dtl_unit,
            prd.prd_frac AS dtl_frac,
            COALESCE(prd.prd_kodetag, ' ') AS dtl_tag,
            sls.trjd_flagtax1 AS dtl_bkp,
            CASE
                WHEN prd.prd_unit = 'KG' AND prd.prd_frac = 1000 THEN sls.trjd_quantity
                ELSE sls.trjd_quantity * prd.prd_frac
            END AS dtl_qty_pcs,
            sls.trjd_quantity AS dtl_qty,
            sls.trjd_unitprice AS dtl_harga_jual,
            sls.trjd_discount AS dtl_diskon,
            CASE
                WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                    AND sls.trjd_create_by IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                    sls.trjd_nominalamt * 11.1 / 10
                ELSE sls.trjd_nominalamt
            END AS dtl_gross,
            CASE
                WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                    AND sls.trjd_create_by NOT IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                    sls.trjd_nominalamt / 11.1 * 10
                ELSE sls.trjd_nominalamt
            END AS dtl_netto,
            CASE
                WHEN prd.prd_unit = 'KG' THEN
                    sls.trjd_quantity * sls.trjd_baseprice / 1000
                ELSE
                    sls.trjd_quantity * sls.trjd_baseprice
            END AS dtl_hpp,
            Trim(sls.trjd_divisioncode) AS dtl_k_div,
            div.div_namadivisi AS dtl_nama_div,
            Substr(sls.trjd_division, 1, 2) AS dtl_k_dept,
            dep.dep_namadepartement AS dtl_nama_dept,
            Substr(sls.trjd_division, 3, 2) AS dtl_k_katb,
            kat.kat_namakategori AS dtl_nama_katb,
            tko.tko_kodeomi AS dtl_kodetokoomi,
            sls.trjd_cus_kodemember AS dtl_cusno,
            cus.cus_namamember AS dtl_namamember,
            cus.cus_flagmemberkhusus AS dtl_memberkhusus,
            cus.cus_kodeoutlet AS dtl_outlet,
            cus.cus_kodesuboutlet AS dtl_suboutlet,
            cus.CUS_JENISMEMBER AS CUS_JENISMEMBER,
            sup.hgb_kodesupplier AS dtl_kodesupplier,
            sup.sup_namasupplier AS dtl_namasupplier,
            akt.jh_belanja_pertama AS dtl_belanja_pertama,
            akt.jh_belanja_terakhir AS dtl_belanja_terakhir
        FROM tbtr_jualdetail sls
        LEFT JOIN tbmaster_prodmast prd ON sls.trjd_prdcd = prd.prd_prdcd
        LEFT JOIN tbmaster_customer cus ON sls.trjd_cus_kodemember = cus.cus_kodemember
        LEFT JOIN tbmaster_tokoigr tko ON sls.trjd_cus_kodemember = tko.tko_kodecustomer
        LEFT JOIN tbmaster_divisi div ON sls.trjd_divisioncode = div.div_kodedivisi
        LEFT JOIN tbmaster_departement dep ON Substr(sls.trjd_division, 1, 2) = dep.dep_kodedepartement
        LEFT JOIN (
            SELECT kat_kodedepartement || kat_kodekategori AS kat_kodekategori, kat_namakategori
            FROM tbmaster_kategori
        ) kat ON sls.trjd_division = kat.kat_kodekategori
        LEFT JOIN (
            SELECT m.hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier
            FROM tbmaster_hargabeli m
            LEFT JOIN tbmaster_supplier s ON m.hgb_kodesupplier = s.sup_kodesupplier
            WHERE m.hgb_tipe = '2' AND m.hgb_recordid IS NULL
        ) sup ON Substr(sls.trjd_prdcd, 1, 6) || '0' = sup.hgb_prdcd
        LEFT JOIN (
            SELECT jh_cus_kodemember, DATE_TRUNC('day', Min(jh_transactiondate)) AS jh_belanja_pertama, DATE_TRUNC('day', Max(jh_transactiondate)) AS jh_belanja_terakhir
            FROM tbtr_jualheader
            WHERE jh_cus_kodemember IS NOT NULL
            GROUP BY jh_cus_kodemember
        ) akt ON sls.trjd_cus_kodemember = akt.jh_cus_kodemember
        WHERE sls.trjd_recordid IS NULL AND sls.trjd_quantity <> 0
    ) AS dtl_inner
) AS dtl_outer
where date_trunc('day', dtl_tanggal) =  :input_value
and DTL_K_DEPT = '14'
HAVING COALESCE(SUM(dtl_netto),0) <> 0
";
$stmt5 = $conn->prepare($query5);
$stmt5->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt5->execute();
$result5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengecek perubahan tanggal kasir
$query6 = "SELECT COUNT(*) FROM tbtr_jualsummary 
          WHERE js_resetamt = '0' 
          AND JS_CASHDRAWEREND IS NULL 
          AND date_trunc('day', JS_TRANSACTIONDATE) <> (:input_value)";

$stmt6 = $conn->prepare($query6);
$stmt6->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt6->execute();
$count_perubahan_kasir = $stmt6->fetchColumn(); // Mengambil hasil COUNT langsung


// Cek MyPoin yang gk keupdate --
//Jika recordnya banyak (>100 / >500 / >1000) , restart mypoin service --
$query7 = "select count (*)
from tbtr_perolehanmypoin
where POR_FLAGUPDATE is null
and POR_KODEMYPOIN not like '%+%'";

$stmt7 = $conn->prepare($query7);
$stmt7->execute();
$count_mypoin = $stmt7->fetchColumn(); // Mengambil hasil COUNT langsung

// Query untuk mengecek perubahan tanggal kasir
$query8 = "SELECT COUNT(*) FROM tbtr_jualdetail
WHERE to_char(trjd_transactiondate, 'mm-yyyy') <> :input_value
AND (TRJD_BASEPRICE = '0' OR TRJD_BASEPRICE IS NULL)
AND to_char(trjd_transactiondate, 'mm-yy') = to_char(CURRENT_DATE, 'mm-yy')";


$stmt8 = $conn->prepare($query8);
$stmt8->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt8->execute();
$count_besprice = $stmt8->fetchColumn(); // Mengambil jumlah total baris




// Query untuk mengecek perubahan tanggal kasir
$query9 = "SELECT 
    c.cus_tglmulai, 
    COUNT(DISTINCT j.JH_CUS_KODEMEMBER) AS total_members
FROM tbtr_jualheader j
JOIN tbmaster_customer c ON j.JH_CUS_KODEMEMBER = c.CUS_KODEMEMBER
WHERE c.CUS_KODEIGR = '1R'
AND c.cus_recordid IS NULL
AND c.cus_flagmemberkhusus = 'Y'
AND c.cus_tglmulai >= CURRENT_DATE - INTERVAL '4 days'  -- Hanya 7 hari terakhir
AND c.cus_tglmulai <= CURRENT_DATE                      -- Sampai hari ini
GROUP BY c.cus_tglmulai
ORDER BY c.cus_tglmulai";

$stmt9 = $conn->prepare($query9);
$stmt9->execute();
$result6 = $stmt9->fetchAll(PDO::FETCH_ASSOC);

// Query CEK Kelurahan Coverage Sum
$query10 = "
    SELECT
       CAST(:input_value AS DATE) AS cutoff,
        COUNT(DISTINCT UPPER(cc.crm_alamatusaha4)) AS jumkel
    FROM
        tbmaster_customer c
    LEFT JOIN
        tbmaster_customercrm cc ON c.cus_kodemember = cc.crm_kodemember
    WHERE
        c.cus_recordid IS NULL
        AND c.cus_flagmemberkhusus = 'Y'
        AND c.cus_kodeigr = '1R'
        AND c.cus_namamember <> 'NEW'
        AND c.cus_tglmulai IS NOT NULL
        AND cc.crm_alamatusaha4 IS NOT NULL
        AND DATE_TRUNC('day', c.cus_tglmulai) <= :input_value
";

$stmt10 = $conn->prepare($query10);
$stmt10->bindValue(':input_value', $input_value, PDO::PARAM_STR);
$stmt10->execute();
$result7 = $stmt10->fetchAll(PDO::FETCH_ASSOC);
?>

<?php

$result1 = $result1 ?? [];
$result2 = $result2 ?? [];
$result3 = $result3 ?? [];
$result4 = $result4 ?? [];
$result5 = $result5 ?? [];
$result6 = $result6 ?? [];
$result7 = $result7 ?? [];


$tglTrans = '-';
if (!empty($result1) && isset($result1[0]['js_transactiondate'])) {
    // Mencari tanggal transaksi paling awal di result1 (jika ada data)
    $minDate = null;
    foreach ($result1 as $row) {
        if (isset($row['js_transactiondate'])) {
            $currentDate = strtotime($row['js_transactiondate']);
            if ($minDate === null || $currentDate < $minDate) {
                $minDate = $currentDate;
            }
        }
    }
    $tglTrans = ($minDate !== null) ? date("d M Y", $minDate) : date("d M Y", strtotime(date('Y-m-d'))); // Fallback to current date if no date found
} else {
    // Jika result1 kosong, gunakan tanggal hari ini
    $tglTrans = date("d M Y", strtotime(date('Y-m-d')));
}


// Inisialisasi variabel counter untuk alert box
$count_perubahan_kasir = $count_perubahan_kasir ?? 0;
$count_mypoin = $count_mypoin ?? 0;
$count_besprice = $count_besprice ?? 0; // Pastikan ini diinisialisasi
?>

<body>
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h3 class="w-100">SALES PER DAY (<?= htmlspecialchars($tglTrans); ?>)</h3>
        </div>


        <div class="alert alert-info mt-3 p-2 rounded-lg shadow-sm text-center">
            <div class="d-flex justify-content-around flex-wrap">
                <div class="p-2">
                    <strong><i class="fas fa-calendar-alt"></i> Kasir Ubah Tanggal:</strong><br>
                    <span class="badge bg-primary fs-6 px-2"><?= htmlspecialchars($count_perubahan_kasir); ?></span>
                </div>
                <div class="p-2">
                    <strong><i class="fas fa-coins"></i> My Poin Tidak Naik:</strong><br>
                    <span class="badge bg-warning text-dark fs-6 px-2"><?= htmlspecialchars($count_mypoin); ?></span>
                </div>
                <div class="p-2">
                    <strong><i class="fas fa-tag"></i> Base Price:</strong><br>
                    <span class="badge bg-success fs-6 px-2"><?= htmlspecialchars($count_besprice); ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="">Cek Kasir</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>STATION</th>
                                        <th>TANGGAL TRANSAKSI</th>
                                        <th>DRAWER END</th>
                                        <th>KET</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result1)) : ?>
                                        <?php foreach ($result1 as $row) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['js_cashierid'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($row['js_cashierstation'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($row['js_transactiondate'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($row['js_cashdrawerend'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($row['ket'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data untuk Cek Kasir.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5 class="">Jumlah Member</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-2">
                                        <thead>
                                            <tr>
                                                <th>Jumlah Member</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result2)) : ?>
                                                <?php foreach ($result2 as $row) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['count'] ?? 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td class="text-center">Tidak ada data jumlah member.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="">Jumlah Member Belanja Repeat</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-3">
                                        <thead>
                                            <tr>
                                                <th>Member Bulan Berjalan</th>
                                                <th>Belanja 1X</th>
                                                <th>Belanja 2X</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result3)) : ?>
                                                <?php foreach ($result3 as $row) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['hitung'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($row['belanja_1x'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($row['belanja_2x'] ?? 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data member belanja repeat.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <h5 class="">Sales</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-4">
                                <thead>
                                    <tr>
                                        <th>Member Belanja</th>
                                        <th>SPD</th>
                                        <th>STD</th>
                                        <th>MARGIN</th>
                                        <th>PRODUK MIX</th>
                                        <th>PRODUK DIBELI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result4)) : ?>
                                        <?php foreach ($result4 as $row) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['member'] ?? 0) ?></td>
                                                <td><?= htmlspecialchars($row['spd'] ?? 0) ?></td>
                                                <td><?= htmlspecialchars($row['std'] ?? 0) ?></td>
                                                <td><?= htmlspecialchars($row['margin'] ?? 0) ?></td>
                                                <td><?= htmlspecialchars($row['produkmix'] ?? 0) ?></td>
                                                <td><?= htmlspecialchars($row['produkdibeli'] ?? 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data Sales.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row ">
                            <div class="col-md-6">
                                <h5 class="mb-3">Sales Udud</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-5">
                                        <thead>
                                            <tr>
                                                <th>Member Belanja</th>
                                                <th>SPD</th>
                                                <th>MARGIN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result5)) : ?>
                                                <?php foreach ($result5 as $row) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['member_beli_tobacoo'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($row['spd_tobacoo'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($row['margin_tobacoo'] ?? 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data Sales Udud.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6 m">
                                <h5 class="">Member Aktif Setelah PT.MDIH</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-6">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah Member</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result6)) : ?>
                                                <?php foreach ($result6 as $row) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['cus_tglmulai'] ?? '-') ?></td>
                                                        <td><?= htmlspecialchars($row['total_members'] ?? 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">Tidak ada data member aktif.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-md-6">
                                <h5 class="mb-3">Kelurahan Coverage</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-5">
                                        <thead>
                                            <tr>
                                                <th>Periode</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result7)) : ?>
                                                <?php foreach ($result7 as $row) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['cutoff'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($row['jumkel'] ?? 0) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data Sales Udud.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

<?php require_once '../layout/_bottom.php'; ?>