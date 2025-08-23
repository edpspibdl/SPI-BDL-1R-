<?php
function get_data()
{
    $query = " SELECT 
    coalesce(COUNT (DISTINCT(CASE WHEN coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),0) PLU_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),0) PLU_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),0) PLU_TIDAK_SELISIH,
    coalesce(COUNT (DISTINCT(st_prdcd)),'0') PLU_ALL,
    coalesce(ROUND(SUM(CASE WHEN coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),0) RPH_PLUS,
    coalesce(ROUND(SUM(CASE WHEN coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),0) RPH_MINUS,
    coalesce(ROUND(SUM(sel_rph)),0) SALDO_AKHIR,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '1' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUFOOD_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '1' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUFOOD_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '1' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUFOOD_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '1' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHFOOD_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '1' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHFOOD_MINUS,
    
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '2' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUNFOOD_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '2' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUNFOOD_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '2' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUNFOOD_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '2' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHNFOOD_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '2' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHNFOOD_MINUS,
    
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '3' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUGMS_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '3' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUGMS_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '3' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUGMS_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '3' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHGMS_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '3' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHGMS_MINUS,
    
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '4' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUPERISH_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '4' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUPERISH_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '4' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUPERISH_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '4' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHPERISH_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '4' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHPERISH_MINUS,
    
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '5' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUCNTR_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '5' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUCNTR_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '5' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUCNTR_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '5' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHCNTR_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '5' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHCNTR_MINUS,
    
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '6' AND coalesce(sel_rph,'0')>'0'  THEN st_prdcd END)),'0') PLUFASTFOOD_PLUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '6' AND coalesce(sel_rph,'0')<'0'  THEN st_prdcd END)),'0') PLUFASTFOOD_MINUS,
    coalesce(COUNT (DISTINCT(CASE WHEN prd_kodedivisi = '6' AND coalesce(sel_rph,'0')='0'  THEN st_prdcd END)),'0') PLUFASTFOOD_TDK_SELISIH,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '6' AND coalesce(sel_rph,'0')>'0'  THEN sel_rph END)),'0') RPHFASTFOOD_PLUS,
    coalesce(ROUND(SUM(CASE WHEN prd_kodedivisi = '6' AND coalesce(sel_rph,'0')<'0'  THEN sel_rph END)),'0') RPHFASTFOOD_MINUS
    
    FROM  
    (select 
prd_kodedivisi,
prd_kodedepartement,
prd_kodekategoribarang,
display_toko,
display_omi,
case when display_toko is null and display_omi is not null then 'GUDANG'
     when display_toko is not null and display_omi is null then 'TOKO'
     when display_toko is not null and display_omi is not null then 'TOKO+GUDANG'
end ket_alamat,
st_prdcd,
prd_deskripsipanjang,
prd_unit,
prd_frac,
prd_kodetag,
flag_main,
st_avgcost,
st_saldoakhir, 
case when prd_unit = 'KG' then (st_saldoakhir*st_avgcost)/prd_frac
     else st_saldoakhir*st_avgcost
end rph_lpp,
qty_plano,
COALESCE(omi_recid4,0) omi_recid4,
st_intransit,
(COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) qty_total_plano,
case when prd_unit = 'KG' then ((COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0))*st_avgcost)/prd_frac
     else (qty_plano+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0))*st_avgcost
end rph_total_plano,
(COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) - st_saldoakhir sel_qty,
((COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) - st_saldoakhir) * st_avgcost sel_rph,
case when (COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) > st_saldoakhir then 'PLANO>LPP'
     when (COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) = st_saldoakhir then 'PLANO=LPP'
     else 'PLANO<LPP'
end Kategori,
CASE WHEN ( display_toko IS NULL AND display_omi IS NULL ) THEN 'TDK ADA PLANO'
     ELSE 'ADA PLANO'
END keterangan,
COALESCE(qty_obi_pick,0) qty_obi_pick,
rph_obi_pick
from tbmaster_stock
join tbmaster_prodmast on prd_prdcd = st_prdcd
left join (SELECT
                                plu plu_obi,
                                SUM(qty_final) qty_obi_pick,
                                SUM(qty_final * st_avgcost) rph_obi_pick
                            FROM
                                (
                                    SELECT
                                        status,
                                        obi_tipebayar,
                                        obi_recid,
                                        obi_tglpb,
                                        obi_notrans,
                                        obi_nopb,
                                        obi_tglstruk,
                                        plu,
                                        qty_pick,
                                        qty_pack,
                                        qty_final,
                                        st_avgcost
                                    FROM
                                        (
                                            SELECT
                                                CASE
                                                    WHEN obi_recid = '1' THEN
                                                        'SIAP PICKING'
                                                    WHEN obi_recid = '2' THEN
                                                        'SIAP PACKING'
                                                    WHEN obi_recid = '3' THEN
                                                        'SIAP DRAFT STRUK'
                                                    WHEN obi_recid = '4' THEN
                                                        'KONFIRM PEMBAYARAN'
                                                    WHEN obi_recid = '5' THEN
                                                        'SIAP STRUK'
                                                    WHEN obi_recid = '6' THEN
                                                        'SELESAI STRUK'
                                                    WHEN obi_recid = '7' THEN
                                                        'SET ONGKIR'
                                                END status,
                                                obi_tipebayar,
                                                obi_recid,
                                                obi_tglpb obi_tglpb,
                                                obi_notrans,
                                                obi_nopb,
                                                substr(plu, 1, 6)
                                                || '0' plu,
                                                qty_pick,
                                                qty_pack,
                                                obi_tglstruk,
                                                CASE
                                                    WHEN qty_pick <> qty_pack
                                                         AND qty_pack IS NOT NULL THEN
                                                        qty_pack
                                                    ELSE
                                                        qty_pick
                                                END qty_final
                                            FROM
                                                tbtr_obi_h left
                                                JOIN (
                                                    SELECT
                                                        *
                                                    FROM
                                                        (
                                                            SELECT DISTINCT
                                                                tgl,
                                                                no_pb,
                                                                plu,
                                                                qty_pick,
                                                                qty_pack
                                                            FROM
                                                                (
                                                                    SELECT
                                                                        obi_tgltrans tgl,
                                                                        obi_notrans   no_pb,
                                                                        obi_prdcd     plu
                                                                    FROM
                                                                        tbtr_obi_d
                                                                        where COALESCE(OBI_QTYINTRANSIT,'0') = '0'
                                                                    UNION ALL
                                                                    SELECT
                                                                        pobi_tgltransaksi,
                                                                        pobi_notransaksi,
                                                                        pobi_prdcd
                                                                    FROM
                                                                        tbtr_packing_obi
                                                                ) obid1 left
                                                                JOIN (
                                                                    SELECT
                                                                        obi_tgltrans tgl_obid,
                                                                        obi_notrans,
                                                                        obi_prdcd,
                                                                        obi_qtyrealisasi qty_pick
                                                                    FROM
                                                                        tbtr_obi_d
                                                                ) obid2 ON tgl_obid = tgl
                                                                     AND obi_notrans = no_pb
                                                                     AND obi_prdcd = plu
                                                                LEFT JOIN (
                                                                    SELECT
                                                                        pobi_tgltransaksi pobi_tgltransaksi,
                                                                        pobi_notransaksi,
                                                                        pobi_prdcd,
                                                                        pobi_qty qty_pack
                                                                    FROM
                                                                        tbtr_packing_obi
                                                                ) obid3 ON pobi_tgltransaksi = tgl
                                                                     AND pobi_notransaksi = no_pb
                                                                     AND pobi_prdcd = plu
                                                        ) obid4
                                                ) obid5 ON tgl = obi_tglpb
                                                     AND no_pb = obi_notrans
                                        ) obid6 left
                                        JOIN (
                                            SELECT
                                                st_prdcd,
                                                st_avgcost
                                            FROM
                                                tbmaster_stock
                                            WHERE
                                                st_lokasi = '01'
                                        ) obid7 ON st_prdcd = plu
                                    WHERE
                                        obi_recid IN (
                                            '1',
                                            '2',
                                            '3',
                                            '7'
                                        )
                                ) obid8
                            GROUP BY
                                plu) introbi on plu_obi = st_prdcd
left join (select lks_prdcd, sum(lks_qty) qty_plano from tbmaster_lokasi group by lks_prdcd) plano on lks_prdcd = st_prdcd
left join (SELECT
    lks_prdcd     AS plu_displayomi,
    lks_koderak
    || '.'
    || lks_kodesubrak
    || '.'
    || lks_tiperak
    || '.'
    || lks_shelvingrak
    || '.'
    || lks_nourut AS display_omi
FROM
    tbmaster_lokasi
WHERE
    ( lks_tiperak = 'B'
      OR lks_tiperak LIKE 'I%' )
    AND lks_prdcd IS NOT NULL
    AND lks_noid IS NOT NULL
    AND lks_koderak NOT LIKE '%DNEW%') planodpd on plu_displayomi = st_prdcd
left join (SELECT
                                lks_prdcd AS plu_displaytoko,
                                lks_koderak
                                || '.'
                                || lks_kodesubrak
                                || '.'
                                || lks_tiperak
                                || '.'
                                || lks_shelvingrak
                                || '.'
                                || lks_nourut AS display_toko
                            FROM
                                tbmaster_lokasi
                            WHERE
                                ( lks_tiperak = 'B'
                                  OR lks_tiperak LIKE 'I%' )
                                AND lks_prdcd IS NOT NULL
                                AND lks_noid IS NULL
                                AND lks_koderak <> 'DNEW'
                                AND lks_koderak <> 'HDH'
                                AND lks_koderak <> 'DVOC'
                                AND lks_koderak NOT LIKE '%TAG%')planotoko on plu_displaytoko = st_prdcd
left join (SELECT
                                substr(pbo_pluigr, 0, 6)
                                || '0' plupb,
                                SUM(pbo_qtyrealisasi) omi_recid4
                            FROM
                                tbmaster_pbomi
                            WHERE
                                pbo_nokoli IS NOT NULL
                                and PBO_TGLPB BETWEEN CURRENT_DATE - INTERVAL '1 day' AND CURRENT_DATE
                                AND pbo_nokoli NOT IN (
                                    SELECT
                                        rpb_nokoli
                                    FROM
                                        tbtr_realpb
                                )
                            GROUP BY
                                substr(pbo_pluigr, 0, 6)
                                || '0') recid4 on plupb = st_prdcd
left join (SELECT 
    PRD_PRDCD AS PLU_flag,
    CASE 
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+IDM+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+IDM+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+IDM+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IDM+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+IDM'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IDM+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IDM+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'OMI+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IDM ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'KLIK ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'OMI ONLY'
        
        ELSE 'BLANK'
    END AS FLAG_MAIN
FROM (
    SELECT 
        PRD_PRDCD, 
        PRD_FLAGIGR, 
        PRD_FLAGIDM, 
        PRD_FLAGOBI, 
        PRD_FLAGOMI
    FROM 
        TBMASTER_PRODMAST
    WHERE 
        PRD_PRDCD LIKE '%0'
) prodmast) flag on plu_flag = st_prdcd
where st_lokasi = '01'
and prd_unit <> 'KG'
AND coalesce(st_saldoakhir, 0) <> '0'
AND coalesce(prd_kodetag,' ') NOT IN ( 'H', 'A', 'N', 'O', 'X', 'U' )
AND prd_kodedepartement NOT IN ( '31', '32', '42' )) iclppplano
    ";

    include '../helper/connection.php';
    try {
        $query = $conn->prepare($query);
        $query->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $h['STATUS']       = 'OK';
        $h['R_PLU_PLUS']   = $row['plu_plus'];
        $h['R_PLU_MINUS']  = $row['plu_minus'];
        $h['R_PLU_TS']     = $row['plu_tidak_selisih'];
        $h['R_PLU_ALL']    = $row['plu_all'];

        $h['R_RPH_PLUS']   = $row['rph_plus'];
        $h['R_RPH_MINUS']  = $row['rph_minus'];
        $h['R_RPH_TS']     = 0;
        $h['R_RPH_ALL']    = $row['saldo_akhir'];

        //FOOD
        $h['R_DIVF_PLU_PLUS']   = $row['plufood_plus'];
        $h['R_DIVF_RPH_PLUS']   = $row['rphfood_plus'];
        $h['R_DIVF_PLU_MINUS']   = $row['plufood_minus'];
        $h['R_DIVF_RPH_MINUS']   = $row['rphfood_minus'];
        $h['R_DIVF_PLU_TS']   = $row['plufood_tdk_selisih'];
        $h['R_DIVF_RPH_TS']   = 0;

        //NON FOOD
        $h['R_DIVNF_PLU_PLUS']   = $row['plunfood_plus'];
        $h['R_DIVNF_RPH_PLUS']   = $row['rphnfood_plus'];
        $h['R_DIVNF_PLU_MINUS']   = $row['plunfood_minus'];
        $h['R_DIVNF_RPH_MINUS']   = $row['rphnfood_minus'];
        $h['R_DIVNF_PLU_TS']   = $row['plunfood_tdk_selisih'];
        $h['R_DIVNF_RPH_TS']   = 0;

        //GENERAL MERCHANDISING
        $h['R_DIVGMS_PLU_PLUS']   = $row['plugms_plus'];
        $h['R_DIVGMS_RPH_PLUS']   = $row['rphgms_plus'];
        $h['R_DIVGMS_PLU_MINUS']   = $row['plugms_minus'];
        $h['R_DIVGMS_RPH_MINUS']   = $row['rphgms_minus'];
        $h['R_DIVGMS_PLU_TS']   = $row['plugms_tdk_selisih'];
        $h['R_DIVGMS_RPH_TS']   = 0;

        //PERISHABLE
        $h['R_DIVPRSH_PLU_PLUS']   = $row['pluperish_plus'];
        $h['R_DIVPRSH_RPH_PLUS']   = $row['rphperish_plus'];
        $h['R_DIVPRSH_PLU_MINUS']   = $row['pluperish_minus'];
        $h['R_DIVPRSH_RPH_MINUS']   = $row['rphperish_minus'];
        $h['R_DIVPRSH_PLU_TS']   = $row['pluperish_tdk_selisih'];
        $h['R_DIVPRSH_RPH_TS']   = 0;

        //COUNTER & PROMOTION
        $h['R_DIVCNTR_PLU_PLUS']   = $row['plucntr_plus'];
        $h['R_DIVCNTR_RPH_PLUS']   = $row['rphcntr_plus'];
        $h['R_DIVCNTR_PLU_MINUS']   = $row['plucntr_minus'];
        $h['R_DIVCNTR_RPH_MINUS']   = $row['rphcntr_minus'];
        $h['R_DIVCNTR_PLU_TS']   = $row['plucntr_tdk_selisih'];
        $h['R_DIVCNTR_RPH_TS']   = 0;

        //FAST FOOD
        $h['R_DIVFSF_PLU_PLUS']   = $row['plufastfood_plus'];
        $h['R_DIVFSF_RPH_PLUS']   = $row['rphfastfood_plus'];
        $h['R_DIVFSF_PLU_MINUS']   = $row['plufastfood_minus'];
        $h['R_DIVFSF_RPH_MINUS']   = $row['rphfastfood_minus'];
        $h['R_DIVFSF_PLU_TS']   = $row['plufastfood_tdk_selisih'];
        $h['R_DIVFSF_RPH_TS']   = 0;

        //FOOTER
        $h['R_RPH_ALL']    = $row['saldo_akhir'];
    }
    return $h;
}

echo json_encode(get_data());
