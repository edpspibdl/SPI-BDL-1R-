<?php
function get_data()
{
    $query = "  SELECT 
                'REKAP' STATUS,
                NVL(COUNT (DISTINCT(CASE WHEN NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLU_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLU_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLU_TIDAK_SELISIH,
                NVL(COUNT (DISTINCT(PLU)),0) PLU_ALL,
                NVL(ROUND(SUM(CASE WHEN NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPH_PLUS,
                NVL(ROUND(SUM(CASE WHEN NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPH_MINUS,
                NVL(ROUND(SUM(SELISIH_RPH)),0) SALDO_AKHIR,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '1' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUFOOD_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '1' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUFOOD_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '1' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUFOOD_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '1' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHFOOD_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '1' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHFOOD_MINUS,
                
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '2' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUNFOOD_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '2' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUNFOOD_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '2' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUNFOOD_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '2' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHNFOOD_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '2' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHNFOOD_MINUS,
                
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '3' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUGMS_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '3' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUGMS_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '3' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUGMS_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '3' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHGMS_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '3' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHGMS_MINUS,
                
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '4' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUPERISH_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '4' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUPERISH_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '4' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUPERISH_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '4' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHPERISH_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '4' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHPERISH_MINUS,
                
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '5' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUCNTR_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '5' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUCNTR_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '5' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUCNTR_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '5' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHCNTR_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '5' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHCNTR_MINUS,
                
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '6' AND NVL(SELISIH_RPH,0)>0  THEN PLU END)),0) PLUFASTFOOD_PLUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '6' AND NVL(SELISIH_RPH,0)<0  THEN PLU END)),0) PLUFASTFOOD_MINUS,
                NVL(COUNT (DISTINCT(CASE WHEN DIV = '6' AND NVL(SELISIH_RPH,0)=0  THEN PLU END)),0) PLUFASTFOOD_TDK_SELISIH,
                NVL(ROUND(SUM(CASE WHEN DIV = '6' AND NVL(SELISIH_RPH,0)>0  THEN SELISIH_RPH END)),0) RPHFASTFOOD_PLUS,
                NVL(ROUND(SUM(CASE WHEN DIV = '6' AND NVL(SELISIH_RPH,0)<0  THEN SELISIH_RPH END)),0) RPHFASTFOOD_MINUS
                
                FROM  
                ( WITH temporar_obi AS (
                SELECT
                        substr(OBI_PRDCD, 1, 6)
                        || '0'                                      obi_prdcd,
                        SUM(OBI_QTYREALISASI)                     obi_qty,
                        SUM(OBI_QTYREALISASI * OBI_HARGASATUAN) obi_rph
                    FROM(
                    select OBID_TGLTRANS, OBID_NOTRANS, OBI_PRDCD ,POBI_QTY,OBI_QTYREALISASI, obi_qty , OBI_HARGASATUAN from(
                    select OBI_TGLTRANS OBID_TGLTRANS, OBI_NOTRANS OBID_NOTRANS, PK_OBID, substr(OBI_PRDCD,1,6)||'0' OBI_PRDCD, OBI_QTYREALISASI, POBI_QTY,OBI_HARGASATUAN,
                    case when POBI_QTY is not null and POBI_QTY <> OBI_QTYREALISASI then POBI_QTY else OBI_QTYREALISASI
                        end obi_qty
                    from (
                        select OBI_TGLTRANS, OBI_NOTRANS, trunc(OBI_TGLTRANS)||'-'||OBI_NOTRANS||'-'||OBI_PRDCD PK_OBID, OBI_PRDCD, OBI_QTYREALISASI, OBI_HARGASATUAN
                        from tbtr_obi_d)
                        left join (select trunc(POBI_TGLTRANSAKSI)||'-'||POBI_NOTRANSAKSI||'-'||POBI_PRDCD PK_PACK, POBI_PRDCD, POBI_QTY from tbtr_packing_obi) on PK_PACK = PK_OBID
                        ) join tbtr_obi_h h on h.obi_notrans = OBID_NOTRANS AND h.obi_tgltrans = OBID_TGLTRANS
                            where substr(h.obi_recid, 0, 1) IN ( '1', '2', '3' ))
                            group by substr(OBI_PRDCD, 1, 6)|| '0'
                ), temporar_omi AS (
                    SELECT
                        substr(pbo_pluigr, 0, 6)
                        || '0'                pbo_prdcd,
                        SUM(pbo_qtyrealisasi) pbo_qty
                    FROM
                        tbmaster_pbomi
                    WHERE
                        pbo_nokoli IS NOT NULL
                        AND NOT EXISTS (
                            SELECT
                                1
                            FROM
                                tbtr_realpb
                            WHERE
                                rpb_nokoli = pbo_nokoli
                        )
                    GROUP BY
                        substr(pbo_pluigr, 0, 6)
                        || '0'
                )
                
                SELECT
                    prd_kodedivisi                                             div,
                    prd_kodedepartement                                        dept,
                    prd_kodekategoribarang                                     katb,
                    prd_prdcd                                                  plu,
                    prd_deskripsipanjang                                       desk,
                    prd_unit                                                   unit,
                    prd_frac                                                   frac,
                    prd_kodetag                                                tag,
                    st_avgcost                                                 acost_pcs,
                    nvl(obi_qty, 0)                                            obi_qty,
                    nvl(pbo_qty, 0)                                            pbo_qty,
                    SUM(lks_qty)                                               plano_qty,
                    st_saldoakhir                                              lpp_qty,
                    CASE WHEN prd_unit = 'KG' THEN ( st_saldoakhir * st_avgcost ) / prd_frac
                    ELSE st_saldoakhir * st_avgcost END                       lpp_rph,
                    nvl(SUM(lks_qty), 0) + nvl(obi_qty, 0) + nvl(pbo_qty, 0)   total_plano,
                    (nvl(SUM(lks_qty), 0) + nvl(obi_qty, 0) + nvl(pbo_qty, 0)) * st_avgcost  rph_plano,
                    (nvl(SUM(lks_qty), 0) + nvl(obi_qty, 0) + nvl(pbo_qty, 0)) - st_saldoakhir AS selisih_qty,
                    ((nvl(SUM(lks_qty), 0) + nvl(obi_qty, 0) + nvl(pbo_qty, 0)) - st_saldoakhir ) * st_avgcost AS selisih_rph
                FROM
                    tbmaster_prodmast
                    LEFT JOIN temporar_obi ON prd_prdcd = obi_prdcd
                    LEFT JOIN tbmaster_stock ON prd_prdcd = st_prdcd AND st_lokasi = '01'
                    LEFT JOIN tbmaster_lokasi ON prd_prdcd = lks_prdcd 
                    LEFT JOIN temporar_omi ON pbo_prdcd = prd_prdcd
                WHERE prd_prdcd like '%0'
                    AND prd_unit <> 'KG'
                    AND NVL(st_saldoakhir, 0) <> '0'
                    AND NVL(st_avgcost, 0) <> '0'
                    AND prd_kodetag NOT IN ( 'H', 'A', 'N', 'O', 'X', 'U' )
                    AND prd_kodedepartement NOT IN ( '31', '32', '42' )
                GROUP BY
                    prd_kodedivisi,
                    prd_kodedepartement,
                    prd_kodekategoribarang,
                    prd_prdcd,
                    prd_deskripsipanjang,
                    prd_unit,
                    prd_frac,
                    prd_kodetag,
                    st_avgcost,
                    st_saldoakhir,
                    obi_qty,
                    pbo_qty,
                    ( st_saldoakhir * st_avgcost ) 
                )   GROUP BY 'REKAP' 
    
    ";

    include '../include/koneksi.php';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS + OCI_ASSOC)) {
        $h['STATUS']       = 'OK';
        $h['R_PLU_PLUS']   = $row['PLU_PLUS'];
        $h['R_PLU_MINUS']  = $row['PLU_MINUS'];
        $h['R_PLU_TS']     = $row['PLU_TIDAK_SELISIH'];
        $h['R_PLU_ALL']    = $row['PLU_ALL'];

        $h['R_RPH_PLUS']   = $row['RPH_PLUS'];
        $h['R_RPH_MINUS']  = $row['RPH_MINUS'];
        $h['R_RPH_TS']     = 0;
        $h['R_RPH_ALL']    = $row['SALDO_AKHIR'];

        //FOOD
        $h['R_DIVF_PLU_PLUS']   = $row['PLUFOOD_PLUS'];
        $h['R_DIVF_RPH_PLUS']   = $row['RPHFOOD_PLUS'];
        $h['R_DIVF_PLU_MINUS']   = $row['PLUFOOD_MINUS'];
        $h['R_DIVF_RPH_MINUS']   = $row['RPHFOOD_MINUS'];
        $h['R_DIVF_PLU_TS']   = $row['PLUFOOD_TDK_SELISIH'];
        $h['R_DIVF_RPH_TS']   = 0;

        //NON FOOD
        $h['R_DIVNF_PLU_PLUS']   = $row['PLUNFOOD_PLUS'];
        $h['R_DIVNF_RPH_PLUS']   = $row['RPHNFOOD_PLUS'];
        $h['R_DIVNF_PLU_MINUS']   = $row['PLUNFOOD_MINUS'];
        $h['R_DIVNF_RPH_MINUS']   = $row['RPHNFOOD_MINUS'];
        $h['R_DIVNF_PLU_TS']   = $row['PLUNFOOD_TDK_SELISIH'];
        $h['R_DIVNF_RPH_TS']   = 0;

        //GENERAL MERCHANDISING
        $h['R_DIVGMS_PLU_PLUS']   = $row['PLUGMS_PLUS'];
        $h['R_DIVGMS_RPH_PLUS']   = $row['RPHGMS_PLUS'];
        $h['R_DIVGMS_PLU_MINUS']   = $row['PLUGMS_MINUS'];
        $h['R_DIVGMS_RPH_MINUS']   = $row['RPHGMS_MINUS'];
        $h['R_DIVGMS_PLU_TS']   = $row['PLUGMS_TDK_SELISIH'];
        $h['R_DIVGMS_RPH_TS']   = 0;

        //PERISHABLE
        $h['R_DIVPRSH_PLU_PLUS']   = $row['PLUPERISH_PLUS'];
        $h['R_DIVPRSH_RPH_PLUS']   = $row['RPHPERISH_PLUS'];
        $h['R_DIVPRSH_PLU_MINUS']   = $row['PLUPERISH_MINUS'];
        $h['R_DIVPRSH_RPH_MINUS']   = $row['RPHPERISH_MINUS'];
        $h['R_DIVPRSH_PLU_TS']   = $row['PLUPERISH_TDK_SELISIH'];
        $h['R_DIVPRSH_RPH_TS']   = 0;

        //COUNTER & PROMOTION
        $h['R_DIVCNTR_PLU_PLUS']   = $row['PLUCNTR_PLUS'];
        $h['R_DIVCNTR_RPH_PLUS']   = $row['RPHCNTR_PLUS'];
        $h['R_DIVCNTR_PLU_MINUS']   = $row['PLUCNTR_MINUS'];
        $h['R_DIVCNTR_RPH_MINUS']   = $row['RPHCNTR_MINUS'];
        $h['R_DIVCNTR_PLU_TS']   = $row['PLUCNTR_TDK_SELISIH'];
        $h['R_DIVCNTR_RPH_TS']   = 0;

        //FAST FOOD
        $h['R_DIVFSF_PLU_PLUS']   = $row['PLUFASTFOOD_PLUS'];
        $h['R_DIVFSF_RPH_PLUS']   = $row['RPHFASTFOOD_PLUS'];
        $h['R_DIVFSF_PLU_MINUS']   = $row['PLUFASTFOOD_MINUS'];
        $h['R_DIVFSF_RPH_MINUS']   = $row['RPHFASTFOOD_MINUS'];
        $h['R_DIVFSF_PLU_TS']   = $row['PLUFASTFOOD_TDK_SELISIH'];
        $h['R_DIVFSF_RPH_TS']   = 0;

        //FOOTER
        $h['R_RPH_ALL']    = $row['SALDO_AKHIR'];
    }
    return $h;
}

echo json_encode(get_data());
