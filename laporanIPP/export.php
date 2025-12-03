<?php
require_once '../helper/connection.php';
require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php';

$tanggalMulai = $_GET['tanggalMulai'] ?? '';
$tanggalSelesai = $_GET['tanggalSelesai'] ?? '';

if (!$tanggalMulai || !$tanggalSelesai) {
    die("Tanggal tidak boleh kosong.");
}

$query = "SELECT
    *
FROM
    (
        SELECT
            obi_nopb,
            obi_tglpb,
            dtl_tanggal        tglstruk,
            obi_kdmember,
            cus_namamember,
            dtl_struk          no_struk,
            tgl_hari,
            dsp_nolisting,
            dtl_tipemember,
            ( obi_ttlorder + obi_ttlppn ) ttl_rupiah,
            SUM(dtl_gross) AS rph_gross,
            SUM(dtl_margin) AS rph_margin,
            round(SUM(dtl_netto), 2) AS sls_nett,
            obi_jrkekspedisi   km,
            ongkir             f_ongkir,
            pot_ongkir         z_pot_ongkir,
            OBI_KDEKSPEDISI
        FROM
            (
                SELECT
                    obi_nopb,
                    obi_jrkekspedisi,
                    obi_kdmember,
                    cus_namamember,
                    dsp_nolisting,
                    obi_ttlppn,
                    obi_ttlorder, ( obi_ttlorder + obi_ttlppn ) ttl_rupiah,
                    ( obi_realorder + obi_realppn) obi_real,
                    TO_CHAR(obi_tglstruk, 'yyyy.mm.dd')
                    || '-'
                    || obi_kdstation
                    || '-'
                    || obi_cashierid
                    || '-'
                    || obi_nostruk
                    || '-'
                    || obi_tipe struk_obi,
                    TO_CHAR(obi_tglpb,'DD-MON-YY') obi_tglpb,
                    OBI_KDEKSPEDISI,
                    TO_CHAR(obi_tglstruk, 'DD') AS tgl_hari
                FROM
                    tbtr_obi_h left
                    JOIN tbmaster_customer ON cus_kodemember = obi_kdmember
                   LEFT JOIN (
                        SELECT DISTINCT dsp_kodemember, dsp_notrans, dsp_nolisting, dsp_nopb
                        FROM tbtr_dsp_spi
                    ) tbtr_dsp_spi ON obi_nopb = dsp_nopb AND dsp_notrans = obi_notrans
                WHERE
                    to_char(obi_tglstruk,'YYYYMMDD') BETWEEN :tanggalMulai AND :tanggalSelesai
                    and obi_recid = '6'
                    AND OBI_KDEKSPEDISI <> 'Ambil di Stock Point Indogrosir'
            ) obih left
            JOIN (
                SELECT
                    dtl_tanggal,
                    dtl_tipemember,
                    dtl_prdcd,
                    dtl_prdcd_ctn,
                    dtl_cusno,
                    dtl_gross,
                    dtl_margin,
                    dtl_netto,
                    dtl_struk
                FROM
                    (SELECT   dtl_rtype, 
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
		       END dtl_qty_pcs,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_qty
		         ELSE dtl_qty *- 1
		       END dtl_qty,
		       dtl_harga_jual,
		       dtl_diskon,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_gross
		         ELSE dtl_gross *- 1
		       END dtl_gross,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_netto
		         ELSE dtl_netto *- 1
		       END dtl_netto,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_hpp
		         ELSE dtl_hpp *- 1
		       END dtl_hpp,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
		         ELSE ( dtl_netto - dtl_hpp ) * -1
		       END dtl_margin,
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
               dtl_idsegment,
               dtl_segment,
		       CASE
		         WHEN (dtl_memberkhusus = 'Y' and CUS_JENISMEMBER = 'T') THEN 'TMI'
                 WHEN dtl_memberkhusus = 'Y' THEN 'KHUSUS'
		         WHEN dtl_kasir = 'IDM' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'ID1' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'ID2' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'OMI'
		               OR dtl_kasir = 'BKL' THEN 'OMI'
		         ELSE 'REGULER'
		       END dtl_tipemember,
		       CASE
		         WHEN dtl_memberkhusus = 'Y' THEN 'GROUP_1_KHUSUS'
		         WHEN dtl_kasir = 'IDM' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'ID1' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'ID2' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'OMI' OR dtl_kasir = 'BKL' THEN 'GROUP_3_OMI'
             	 WHEN dtl_memberkhusus is null AND dtl_outlet ='6' THEN 'GROUP_4_END_USER'
		         ELSE 'GROUP_5_OTHERS'
		       END dtl_group_member,
		       dtl_kodesupplier,
		       dtl_namasupplier,
		       dtl_belanja_pertama,
		       dtl_belanja_terakhir
		FROM   (SELECT sls.trjd_transactiontype        AS dtl_rtype,
		               sls.trjd_transactiondate AS dtl_tanggal,
		               To_char(sls.trjd_transactiondate, 'yyyy.mm.dd')
		               ||'-'||sls.trjd_cashierstation
		               ||'-'||sls.trjd_create_by
		               ||'-'||sls.trjd_transactionno
		               ||'-'||sls.trjd_transactiontype      AS dtl_struk,
		               sls.trjd_cashierstation         AS dtl_stat,
		               sls.trjd_create_by              AS dtl_kasir,
		               sls.trjd_transactionno          AS dtl_no_struk,
		               sls.trjd_seqno                  AS dtl_seqno,
		               Substr(sls.trjd_prdcd, 1, 6)
		               || '0'                          AS dtl_prdcd_ctn,
		               sls.trjd_prdcd                  AS dtl_prdcd,
		               prd.prd_deskripsipanjang        AS dtl_nama_barang,
		               prd.prd_unit                    AS dtl_unit,
		               prd.prd_frac                    AS dtl_frac,
		               coalesce(prd.prd_kodetag, ' ')       AS dtl_tag,
		               sls.trjd_flagtax1               AS dtl_bkp,
		               CASE
		                 WHEN PRD.prd_unit = 'KG'
		                      AND prd.prd_frac = 1000 THEN sls.trjd_quantity
		                 ELSE sls.trjd_quantity * prd.prd_frac
		               END                             dtl_qty_pcs,
		               sls.trjd_quantity               AS dtl_qty,
		               sls.trjd_unitprice              AS dtl_harga_jual,
		               sls.trjd_discount               AS dtl_diskon,
		               CASE
		                 WHEN sls.trjd_flagtax1 = 'Y' and  sls.trjd_flagtax2 = 'Y'
		                      AND sls.trjd_create_by IN( 'IDM','ID1','ID2', 'OMI', 'BKL' ) THEN
		                 sls.trjd_nominalamt * 11.1 / 10
		                 ELSE sls.trjd_nominalamt
		               END                             dtl_gross,
		               CASE
		                 WHEN sls.trjd_flagtax1 = 'Y' and  sls.trjd_flagtax2 = 'Y'
		                      AND sls.trjd_create_by NOT IN( 'IDM','ID1','ID2', 'OMI', 'BKL' ) THEN
		                 sls.trjd_nominalamt / 11.1 * 10
		                 ELSE sls.trjd_nominalamt
		               END                             dtl_netto,
		               CASE
		                 WHEN PRD.prd_unit = 'KG' THEN
		                 sls.trjd_quantity * sls.trjd_baseprice / 1000
		                 ELSE sls.trjd_quantity * sls.trjd_baseprice
		               END                             dtl_hpp,
		               Trim(sls.trjd_divisioncode)     AS dtl_k_div,
		               div.div_namadivisi              AS dtl_nama_div,
		               Substr(sls.trjd_division, 1, 2) AS dtl_k_dept,
		               dep.dep_namadepartement         AS dtl_nama_dept,
		               Substr(sls.trjd_division, 3, 2) AS dtl_k_katb,
		               kat.kat_namakategori            AS dtl_nama_katb,
                       tko.tko_kodeomi                 AS dtl_kodetokoomi,
		               sls.trjd_cus_kodemember         AS dtl_cusno,
		               cus.cus_namamember              AS dtl_namamember,
		               cus.cus_flagmemberkhusus        AS dtl_memberkhusus,
		               cus.cus_kodeoutlet              AS dtl_outlet,
		               cus.cus_kodesuboutlet           AS dtl_suboutlet,
                       crm_idsegment                   AS dtl_idsegment,
                       seg.seg_nama                    AS dtl_segment,
                       cus.CUS_JENISMEMBER             as CUS_JENISMEMBER,
		               sup.hgb_kodesupplier            AS dtl_kodesupplier,
		               sup.sup_namasupplier            AS dtl_namasupplier,
		               akt.jh_belanja_pertama          AS dtl_belanja_pertama,
		               akt.jh_belanja_terakhir         AS dtl_belanja_terakhir

		        FROM   tbtr_jualdetail sls
		        join tbmaster_prodmast prd on sls.trjd_prdcd = prd.prd_prdcd
		        join tbmaster_customer cus on sls.trjd_cus_kodemember = cus.cus_kodemember
                left join tbmaster_customercrm crm on sls.trjd_cus_kodemember = crm.crm_kodemember
                left join tbmaster_segmentasi seg on crm.crm_idsegment = seg.seg_id 
                left join tbmaster_tokoigr tko on sls.trjd_cus_kodemember = tko.tko_kodecustomer
		        left join tbmaster_divisi div on sls.trjd_divisioncode = div.div_kodedivisi
		        left join tbmaster_departement dep on Substr(sls.trjd_division, 1, 2) = dep.dep_kodedepartement
		        left join (SELECT kat_kodedepartement
		                       || kat_kodekategori AS kat_kodekategori,
		                       kat_namakategori
		                FROM   tbmaster_kategori) kat on sls.trjd_division = kat.kat_kodekategori
		        left join (SELECT m.hgb_prdcd,
		                       m.hgb_kodesupplier,
		                       s.sup_namasupplier
		                FROM   tbmaster_hargabeli m
                        left join tbmaster_supplier s on m.hgb_kodesupplier = s.sup_kodesupplier
		                WHERE  m.hgb_tipe = '2'
		                       AND m.hgb_recordid IS NULL) sup on Substr(sls.trjd_prdcd, 1, 6) || 0 = sup.hgb_prdcd
                left join (SELECT jh_cus_kodemember,
						       to_char(Min(jh_transactiondate),'DD-MON-YY') AS jh_belanja_pertama,
						       to_char(Max(jh_transactiondate),'DD-MON-YY') AS jh_belanja_terakhir
						FROM   tbtr_jualheader
						WHERE  jh_cus_kodemember IS NOT NULL
						GROUP  BY jh_cus_kodemember) akt on sls.trjd_cus_kodemember = akt.jh_cus_kodemember
		        WHERE  sls.trjd_recordid IS NULL
		               AND sls.trjd_quantity <> 0) alias1) view_detail
            ) view_detail_struk_revisi ON dtl_struk = struk_obi
            LEFT JOIN (
    SELECT
        kode_member,
        no_pb,
        no_trans,
        MAX(CASE WHEN ongkir > 0 THEN ongkir ELSE 0 END) AS ongkir,
        SUM(pot_ongkir) AS pot_ongkir
    FROM payment_klikigr
    GROUP BY kode_member, no_pb, no_trans
) payment_klikigr
ON obi_kdmember = payment_klikigr.kode_member
AND obi_nopb = payment_klikigr.no_pb
        GROUP BY
            obi_nopb,
            obi_kdmember,
            cus_namamember,
            ttl_rupiah,
            obi_tglpb,
            dtl_tanggal,
            dtl_cusno,
            dtl_struk,
            dtl_tipemember,
            obi_jrkekspedisi,
            ongkir,
            pot_ongkir,
            ( obi_ttlorder + obi_ttlppn ),
            OBI_KDEKSPEDISI,
            dsp_nolisting,
            tgl_hari
        ORDER BY
            dtl_tanggal ASC
    )uyee
";

$stmt = $conn->prepare($query);
$stmt->bindValue(':tanggalMulai', $tanggalMulai);
$stmt->bindValue(':tanggalSelesai', $tanggalSelesai);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// **Cari duplikat MEMBER + NO LISTING**
$duplikat = [];
$temp = [];

foreach ($rows as $r) {
    $key = $r['cus_namamember'] . '-' . $r['dsp_nolisting'];
    if (!isset($temp[$key])) {
        $temp[$key] = 0;
    }
    $temp[$key]++;
}

foreach ($temp as $key => $count) {
    if ($count > 1) {
        $duplikat[$key] = true;
    }
}

// STYLE Excel
$headerStyle = ['font-style' => 'bold', 'border' => 'left,right,top,bottom'];
$normal = ['border' => 'left,right,top,bottom'];

$redStyle = [
    'fill' => '#FF0000',
    'font-color' => '#FFFFFF',
    'font-style' => 'bold',
    'border' => 'left,right,top,bottom'
];

$writer = new XLSXWriter();
$writer->writeSheetHeader('DATA', [
    'NO PB' => 'string', 'TGL PB' => 'string', 'TGL STRUK' => 'string', 'NO STRUK' => 'string',
    'MEMBER' => 'string', 'TGL' => 'string', 'NAMA MEMBER' => 'string', 'NO LISTING' => 'string',
    'TIPE' => 'string', 'RUPIAH' => 'integer', 'GROSS' => 'integer', 'MARGIN' => 'integer',
    'NETT' => 'integer', 'KM' => 'integer', 'ONGKIR' => 'integer', 'POT ONGKIR' => 'integer',
    'EKSPEDISI' => 'string'
], $headerStyle);

// DATA
foreach ($rows as $r) {
    $key = $r['cus_namamember'] . '-' . $r['dsp_nolisting'];

    $styleTgl = $duplikat[$key] ?? false ? $redStyle : $normal;
    $styleName = $duplikat[$key] ?? false ? $redStyle : $normal;
    $styleListing = $duplikat[$key] ?? false ? $redStyle : $normal;

    $writer->writeSheetRow('DATA', [
        $r['obi_nopb'], $r['obi_tglpb'], $r['tglstruk'], $r['no_struk'],
        $r['obi_kdmember'], $r['tgl_hari'], $r['cus_namamember'], $r['dsp_nolisting'],
        $r['dtl_tipemember'], $r['ttl_rupiah'], $r['rph_gross'], $r['rph_margin'],
        $r['sls_nett'], $r['km'], $r['f_ongkir'], $r['z_pot_ongkir'], $r['obi_kdekspedisi']
    ], [
        $normal, $normal, $normal, $normal,
        $normal, $styleTgl, $styleName, $styleListing,
        $normal, $normal, $normal, $normal,
        $normal, $normal, $normal, $normal, $normal
    ]);
}

$file = "SALES_VS_ONGKIR_" . date("Ymd_His") . ".xlsx";
header('Content-disposition: attachment; filename="' . $file . '"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
$writer->writeToStdOut();
exit;
