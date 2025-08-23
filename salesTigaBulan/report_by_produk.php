<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Fungsi untuk format tanggal Indonesia
function tanggalIND($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tanggal = explode('-', $tanggal);
    return $tanggal[2] . ' ' . $bulan[(int)$tanggal[1]] . ' ' . $tanggal[0];
}

// Ambil data dari form (jika ada)
$tanggalAwal1 = $_POST['tanggalAwal1'] ?? '';
$tanggalAkhir1 = $_POST['tanggalAkhir1'] ?? '';
$tanggalAwal2 = $_POST['tanggalAwal2'] ?? '';
$tanggalAkhir2 = $_POST['tanggalAkhir2'] ?? '';
$tanggalAwal3 = $_POST['tanggalAwal3'] ?? '';
$tanggalAkhir3 = $_POST['tanggalAkhir3'] ?? '';

// Periksa apakah input sudah ada, jika tidak ada, set ke kosong.
$tglMulaiSebelumPromosi = $tanggalAwal1 ? date('Ymd', strtotime($tanggalAwal1)) : '';
$tglSelesaiSebelumPromosi = $tanggalAkhir1 ? date('Ymd', strtotime($tanggalAkhir1)) : '';

$tglMulaiPromosi = $tanggalAwal2 ? date('Ymd', strtotime($tanggalAwal2)) : '';
$tglSelesaiPromosi = $tanggalAkhir2 ? date('Ymd', strtotime($tanggalAkhir2)) : '';

$tglMulaiSetelahPromosi = $tanggalAwal3 ? date('Ymd', strtotime($tanggalAwal3)) : '';
$tglSelesaiSetelahPromosi = $tanggalAkhir3 ? date('Ymd', strtotime($tanggalAkhir3)) : '';

// Query dengan parameter binding
$query = "SELECT
    dtl_k_div,
    dtl_k_dept,
    dtl_prdcd_ctn,
    dtl_nama_barang,
    prd.prd_frac         AS dtl_frac,
    COALESCE(prd.prd_kodetag, ' ') AS dtl_tag,
CASE 
    WHEN prd.prd_PRDCD IN (
        SELECT MPL_PRDCD 
        FROM TBTR_MONITORINGPLU 
        WHERE MPL_KODEMONITORING IN ('F1', 'NF1')
    ) 
    THEN 'V' 
    ELSE '' 
END AS PARETO,
        ROUND(((coalesce(dtl_member,0)+coalesce(dtl_member_2,0)+coalesce(dtl_member_3,0))/3),0) AVG_MEMBER,
        ROUND(((coalesce(dtl_qty_in_pcs,0)+coalesce(dtl_qty_in_pcs_2,0)+coalesce(dtl_qty_in_pcs_3,0))/3),0) AVG_QTY,
        ROUND(((coalesce(dtl_gross,0)+coalesce(dtl_gross_2,0)+coalesce(dtl_gross_3,0))/3),0) AVG_GROSS,
        ROUND(((coalesce(dtl_netto,0)+coalesce(dtl_netto_2,0)+coalesce(dtl_netto_3,0))/3),0) AVG_NETTO,
        ROUND(((coalesce(dtl_margin,0)+coalesce(dtl_margin_2,0)+coalesce(dtl_margin_3,0))/3),0) AVG_MARGIN,
 
    TO_CHAR(
        ( 
            COALESCE(
                CASE WHEN COALESCE(dtl_netto, 0) = 0 THEN 0 
                     ELSE COALESCE(dtl_margin, 0) / NULLIF(COALESCE(dtl_netto, 0), 0) * 100 
                END, 
            0) +
            COALESCE(
                CASE WHEN COALESCE(dtl_netto_2, 0) = 0 THEN 0 
                     ELSE COALESCE(dtl_margin_2, 0) / NULLIF(COALESCE(dtl_netto_2, 0), 0) * 100 
                END, 
            0) +
            COALESCE(
                CASE WHEN COALESCE(dtl_netto_3, 0) = 0 THEN 0 
                     ELSE COALESCE(dtl_margin_3, 0) / NULLIF(COALESCE(dtl_netto_3, 0), 0) * 100 
                END, 
            0)
        ) / 3, 
        'FM999999990.00'
    ) AS rata_persen,
COALESCE(dtl_member, 0) AS dtl_member,
    TRUNC(COALESCE(dtl_qty_in_pcs, 0)) AS LPP_IN_PCS,
    TRUNC(COALESCE(dtl_gross, 0)) AS dtl_gross,
    COALESCE(dtl_netto, 0) AS dtl_netto,
    COALESCE(dtl_margin, 0) AS dtl_margin,
    TO_CHAR(
        CASE 
            WHEN COALESCE(dtl_netto, 0) = 0 THEN 0 
            ELSE COALESCE(dtl_margin, 0) / NULLIF(COALESCE(dtl_netto, 0), 0) * 100 
        END, 
        'FM999999990.00'
    ) AS persen,
    COALESCE(dtl_member_2, 0) AS dtl_member_2,
    TRUNC(COALESCE(dtl_qty_in_pcs_2, 0)) AS LPP_IN_PCS_2,
    TRUNC(COALESCE(dtl_gross_2, 0)) AS dtl_gross_2,
    COALESCE(dtl_netto_2, 0) AS dtl_netto_2,
    COALESCE(dtl_margin_2, 0) AS dtl_margin_2,
    TO_CHAR(
        CASE 
            WHEN COALESCE(dtl_netto_2, 0) = 0 THEN 0 
            ELSE COALESCE(dtl_margin_2, 0) / NULLIF(COALESCE(dtl_netto_2, 0), 0) * 100 
        END, 
        'FM999999990.00'
    ) AS persen_2,

    COALESCE(dtl_member_3, 0) AS dtl_member_3,
    TRUNC(COALESCE(dtl_qty_in_pcs_3, 0)) AS LPP_IN_PCS_3,
    TRUNC(COALESCE(dtl_gross_3, 0)) AS dtl_gross_3,
    COALESCE(dtl_netto_3, 0) AS dtl_netto_3,
    COALESCE(dtl_margin_3, 0) AS dtl_margin_3,
    TO_CHAR(
        CASE 
            WHEN COALESCE(dtl_netto_3, 0) = 0 THEN 0 
            ELSE COALESCE(dtl_margin_3, 0) / NULLIF(COALESCE(dtl_netto_3, 0), 0) * 100 
        END, 
        'FM999999990.00'
    ) AS persen_3,
    pkm.pkm_mindisplay   AS dtl_pkm_mindisplay,
    pkm.pkm_minorder     AS dtl_pkm_minorder,
    pkm.pkm_pkmt         AS dtl_pkmt,
    pkm.pkm_leadtime     AS dtl_pkm_leadtime,
    stk.st_saldoakhir    AS dtl_saldo_in_pcs
FROM ( SELECT
    dtl_k_div,
    dtl_k_dept,
    dtl_k_katb,
    dtl_prdcd_ctn,
    dtl_nama_barang,
    dtl_kodesupplier,
    dtl_namasupplier,
    dtl_kunjungan,
    dtl_member,
    dtl_struk,
    dtl_item,
    dtl_qty_in_pcs,
    dtl_gross,
    dtl_netto,
    dtl_margin,
    dtl_kunjungan_2,
    dtl_member_2,
    dtl_struk_2,
    dtl_item_2,
    dtl_qty_in_pcs_2,
    dtl_gross_2,
    dtl_netto_2,
    dtl_margin_2,
    dtl_kunjungan_3,
    dtl_member_3,
    dtl_struk_3,
    dtl_item_3,
    dtl_qty_in_pcs_3,
    dtl_gross_3,
    dtl_netto_3,
    dtl_margin_3
FROM ( SELECT
    dtl_k_div,
    dtl_k_dept,
    dtl_k_katb,
    dtl_prdcd_ctn,
    dtl_nama_barang,
    dtl_kodesupplier,
    dtl_namasupplier,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_tanggal
        END
    )) AS dtl_kunjungan,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_cusno
        END
    )) AS dtl_member,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_struk
        END
    )) AS dtl_struk,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_gross
        END
    ) AS dtl_gross,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_netto
        END
    )) AS dtl_netto,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_margin
        END
    )) AS dtl_margin,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_tanggal
        END
    )) AS dtl_kunjungan_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_cusno
        END
    )) AS dtl_member_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_struk
        END
    )) AS dtl_struk_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item_2,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs_2,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_gross
        END
    ) AS dtl_gross_2,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_netto
        END
    )) AS dtl_netto_2,
    trunc(SUM(
        CASE WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_margin
        END
    )) AS dtl_margin_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_tanggal
        END
    )) AS dtl_kunjungan_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_cusno
        END
    )) AS dtl_member_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_struk
        END
    )) AS dtl_struk_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item_3,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs_3,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_gross
        END
    ) AS dtl_gross_3,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_netto
        END
    )) AS dtl_netto_3,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_margin
        END
    )) AS dtl_margin_3
FROM (( SELECT
    dtl_rtype,
    dtl_tanggal,
    dtl_jam,
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
    dtl_qty_pcs,
    dtl_qty,
    dtl_harga_jual,
    dtl_diskon,
    CASE
        WHEN dtl_rtype = 'S' THEN
            dtl_gross
        ELSE
            dtl_gross * -1
    END AS dtl_gross,
    CASE
        WHEN dtl_rtype = 'R' THEN
            ( dtl_netto * - 1 )
        ELSE
            dtl_netto
    END AS dtl_netto,
    CASE
        WHEN dtl_rtype = 'R' THEN
            ( dtl_hpp * - 1 )
        ELSE
            dtl_hpp
    END AS dtl_hpp,
    CASE
        WHEN dtl_rtype = 'S' THEN
            dtl_netto - dtl_hpp
        ELSE
            ( dtl_netto - dtl_hpp ) * - 1
    END AS dtl_margin,
    dtl_k_div,
    dtl_nama_div,
    dtl_k_dept,
    dtl_nama_dept,
    dtl_k_katb,
    dtl_nama_katb,
    dtl_cusno,
    dtl_namamember,
    dtl_memberkhusus,
    dtl_outlet,
    dtl_suboutlet,
    dtl_kategori,
    dtl_sub_kategori,
    dtl_tipemember,
    dtl_group_member,
    hgb_kodesupplier   AS dtl_kodesupplier,
    sup_namasupplier   AS dtl_namasupplier
FROM ( SELECT
    date_trunc('day', trjd_transactiondate) AS dtl_tanggal,
    TO_CHAR(trjd_transactiondate, 'hh24:mi:ss') AS dtl_jam,
    TO_CHAR(trjd_transactiondate, 'yyyymmdd')
    || trjd_create_by
    || trjd_transactionno
    || trjd_transactiontype AS dtl_struk,
    trjd_cashierstation    AS dtl_stat,
    trjd_create_by         AS dtl_kasir,
    trjd_transactionno     AS dtl_no_struk,
    substr(trjd_prdcd, 1, 6)
    || '0' AS dtl_prdcd_ctn,
    trjd_prdcd             AS dtl_prdcd,
    prd_deskripsipanjang   AS dtl_nama_barang,
    prd_unit               AS dtl_unit,
    prd_frac               AS dtl_frac,
    coalesce(prd_kodetag, ' ') AS dtl_tag,
    trjd_flagtax1          AS dtl_bkp,
    trjd_transactiontype   AS dtl_rtype,
    TRIM(trjd_divisioncode) AS dtl_k_div,
    div_namadivisi         AS dtl_nama_div,
    substr(trjd_division, 1, 2) AS dtl_k_dept,
    dep_namadepartement    AS dtl_nama_dept,
    substr(trjd_division, 3, 2) AS dtl_k_katb,
    kat_namakategori       AS dtl_nama_katb,
    trjd_cus_kodemember    AS dtl_cusno,
    cus_namamember         AS dtl_namamember,
    cus_flagmemberkhusus   AS dtl_memberkhusus,
    cus_kodeoutlet         AS dtl_outlet,
    upper(cus_kodesuboutlet) AS dtl_suboutlet,
    crm_kategori           AS dtl_kategori,
    crm_subkategori        AS dtl_sub_kategori,
    trjd_quantity          AS dtl_qty,
    trjd_unitprice         AS dtl_harga_jual,
    trjd_discount          AS dtl_diskon,
    trjd_seqno             AS dtl_seqno,
    CASE
        WHEN cus_jenismember = 'T'      THEN
            'TMI'
        WHEN cus_flagmemberkhusus = 'Y' THEN
            'KHUSUS'
        WHEN trjd_create_by IN (
            'IDM',
            'ID1',
            'ID2'
        ) THEN
            'IDM'
        WHEN trjd_create_by IN (
            'OMI',
            'BKL'
        ) THEN
            'OMI'
        ELSE
            'REGULER'
    END AS dtl_tipemember,
    CASE
        WHEN cus_flagmemberkhusus = 'Y' THEN
            'GROUP_1_KHUSUS'
        WHEN trjd_create_by = 'IDM'     THEN
            'GROUP_2_IDM'
        WHEN trjd_create_by IN (
            'OMI',
            'BKL'
        ) THEN
            'GROUP_3_OMI'
        WHEN cus_flagmemberkhusus IS NULL
             AND cus_kodeoutlet = '6' THEN
            'GROUP_4_END_USER'
        ELSE
            'GROUP_5_OTHERS'
    END AS dtl_group_member,
    CASE
        WHEN prd_unit = 'KG' and prd_frac = 1000 THEN
            trjd_quantity
        ELSE
            trjd_quantity * prd_frac
    END AS dtl_qty_pcs,
    CASE
        WHEN trjd_flagtax1 = 'Y'
             AND trjd_create_by IN (
            'IDM',
            'OMI',
            'BKL'
        ) THEN
            trjd_nominalamt * 11.1 / 10
        ELSE
            trjd_nominalamt
    END AS dtl_gross,
    CASE
        WHEN trjd_divisioncode = '5'
             AND substr(trjd_division, 1, 2) = '39' THEN
            case
        WHEN 'Y' = 'Y' THEN
            trjd_nominalamt
    END else case when coalesce(tko_kodesbu, 'z') in ('O', 'I') then case when tko_tipeomi in ('HE', 'HG') then trjd_nominalamt - ( case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') and coalesce(prd_kodetag, 'zz') <> 'Q' then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) else 0 end ) else trjd_nominalamt end else trjd_nominalamt - ( case when substr(trjd_create_by, 1, 2) = 'EX' then 0 else case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') and coalesce(prd_kodetag, 'zz') <> 'Q' then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) else 0 end end ) end end as dtl_netto, case when trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' then case when 'Y' = 'Y' then trjd_nominalamt - ( case when prd_markupstandard is null then (5 * trjd_nominalamt) / 100 else (prd_markupstandard * trjd_nominalamt) / 100 end ) end else (trjd_quantity / case when prd_unit = 'KG' then 1000 else 1 end) * trjd_baseprice end as dtl_hpp from tbtr_jualdetail left join tbmaster_prodmast on trjd_prdcd = prd_prdcd left join tbmaster_tokoigr on trjd_cus_kodemember = tko_kodecustomer left join tbmaster_customer on trjd_cus_kodemember = cus_kodemember left join tbmaster_customercrm on trjd_cus_kodemember = crm_kodemember left join tbmaster_divisi on trjd_division = div_kodedivisi left join tbmaster_departement on substr(trjd_division, 1, 2) = dep_kodedepartement left join tbmaster_kategori on trjd_division = kat_kodedepartement || kat_kodekategori)sls left join (select m.hgb_prdcd hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier from tbmaster_hargabeli m left join tbmaster_supplier s on m.hgb_kodesupplier = s.sup_kodesupplier where m.hgb_tipe = '2' and m.hgb_recordid is null)gb on dtl_prdcd_ctn=hgb_prdcd) ) detailstruk WHERE ( TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal1 and :tanggalAkhir1 OR TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal2 and :tanggalAkhir2 OR TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal3 and :tanggalAkhir3 )GROUP BY dtl_k_div, dtl_k_dept, dtl_k_katb, dtl_prdcd_ctn, dtl_nama_barang, dtl_kodesupplier, dtl_namasupplier ) TEST ) sls LEFT JOIN tbmaster_prodmast prd ON sls.dtl_prdcd_ctn = prd.prd_prdcd LEFT JOIN ( SELECT st_prdcd, st_saldoakhir FROM tbmaster_stock WHERE st_lokasi = '01' ) stk ON sls.dtl_prdcd_ctn = stk.st_prdcd LEFT JOIN ( SELECT pkm_prdcd, pkm_pkmt, pkm_minorder, pkm_leadtime, pkm_mindisplay FROM tbmaster_kkpkm ) pkm ON sls.dtl_prdcd_ctn = pkm.pkm_prdcd
    ";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':tanggalAwal1' => $tglMulaiSebelumPromosi,
        ':tanggalAkhir1' => $tglSelesaiSebelumPromosi,
        ':tanggalAwal2' => $tglMulaiPromosi,
        ':tanggalAkhir2' => $tglSelesaiPromosi,
        ':tanggalAwal3' => $tglMulaiSetelahPromosi,
        ':tanggalAkhir3' => $tglSelesaiSetelahPromosi
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Sales 3 Bulan By Produk</h3>
        <a href="../salesTigaBulan/index.php" class="btn btn-danger">BACK</a>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="GridView" class="table table-bordered">
                    <thead>
                        <tr class="active">
                            <th rowspan="2" class="text-center">#</th>
                            <th colspan="6" class="text-center">Produk</th>
                            <th colspan="6" class="text-center">AVERAGE 3 BULAN</th>
                            <th colspan="6" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiSebelumPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiSebelumPromosi))) ?>
                            </th>
                            <th colspan="6" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiPromosi))) ?>
                            </th>
                            <th colspan="6" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiSetelahPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiSetelahPromosi))) ?>
                            </th>
                            <th rowspan="2" class="text-center">MinDis</th>
                            <th rowspan="2" class="text-center">Minor</th>
                            <th rowspan="2" class="text-center">PKM</th>
                            <th rowspan="2" class="text-center">LT</th>
                            <th rowspan="2" class="text-center">LPP</th>
                        </tr>
                        <tr class="active">
                            <th class="text-center">Div</th>
                            <th class="text-center">Dept</th>
                            <th class="text-center">PLU IGR</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Frac</th>
                            <th class="text-center">Tag</th>

                            <th class="text-center">Member</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Netto</th>
                            <th class="text-center">Margin</th>
                            <th class="text-center">%</th>

                            <th class="text-center">Member</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Netto</th>
                            <th class="text-center">Margin</th>
                            <th class="text-center">%</th>

                            <th class="text-center">Member</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Netto</th>
                            <th class="text-center">Margin</th>
                            <th class="text-center">%</th>

                            <th class="text-center">Member</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Gross</th>
                            <th class="text-center">Netto</th>
                            <th class="text-center">Margin</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $noUrut = 0;
                        foreach ($results as $row) {    
                            $noUrut++;
                            print '<tr>';
                            echo '<td align="right">' . $noUrut . '</td>';
                            echo '<td align="center">' . $row['dtl_k_div'] . '</td>';
                            echo '<td align="center">' . $row['dtl_k_dept'] . '</td>';
                            echo '<td align="center">' . $row['dtl_prdcd_ctn'] . '</td>';
                            echo '<td align="left" class="text-nowrap">' . $row['dtl_nama_barang'] . '</td>';
                            echo '<td align="left">' . $row['dtl_frac'] . '</td>';
                            echo '<td align="left">' . $row['dtl_tag'] . '</td>';

                            // AVG
                            echo '<td align="right">' . number_format($row['avg_member'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['avg_qty'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['avg_gross'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['avg_netto'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['avg_margin'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['rata_persen'], 2, '.', ',') . '</td>';

                            // Sebelum promosi
                            echo '<td align="right">' . number_format($row['dtl_member'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['lpp_in_pcs'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_gross'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_netto'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_margin'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . ($row['dtl_netto'] == 0 ? number_format(0, 2, '.', ',') : number_format($row['dtl_margin'] / $row['dtl_netto'] * 100, 2, '.', ',')) . '</td>';

                            // Promosi
                            echo '<td align="right">' . number_format($row['dtl_member_2'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['lpp_in_pcs_2'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_gross_2'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_netto_2'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_margin_2'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . ($row['dtl_netto_2'] == 0 ? number_format(0, 2, '.', ',') : number_format($row['dtl_margin_2'] / $row['dtl_netto_2'] * 100, 2, '.', ',')) . '</td>';

                            // Setelah promosi
                            echo '<td align="right">' . number_format($row['dtl_member_3'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['lpp_in_pcs_3'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_gross_3'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_netto_3'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_margin_3'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . ($row['dtl_netto_3'] == 0 ? number_format(0, 2, '.', ',') : number_format($row['dtl_margin_3'] / $row['dtl_netto_3'] * 100, 2, '.', ',')) . '</td>';

                            echo '<td align="right">' . number_format($row['dtl_pkm_mindisplay'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_pkm_minorder'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_pkmt'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_pkm_leadtime'], 0, '.', ',') . '</td>';
                            echo '<td align="right">' . number_format($row['dtl_saldo_in_pcs'], 0, '.', ',') . '</td>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php echo $query;
		

		?>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    $(document).ready(function() {
        $('#GridView').DataTable({
            responsive: false,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: false,
            buttons: [
                { extend: 'copy', text: 'Copy' },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'PARETO_3_BULAN_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function () {
                this.api().columns.adjust().draw();
            }
        });
    });
</script>



