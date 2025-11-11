<?php
// FILE: query/product_query.php

// 🔹 QUERY UTAMA (TIDAK DIUBAH)
// Mendefinisikan query sebagai konstanta atau variabel global
$QUERY_LOKAL_KOMPLEKS = " 
    SELECT 
        subs1.PLU AS prd_prdcd, 
        subs1.DESK AS prd_deskripsipanjang, 
        COALESCE(subq1.hrg_netmm, subs1.HRGN) AS harga_final_lokal 
    FROM (
        SELECT PRD_PRDCD PLU, PRD_DESKRIPSIPANJANG DESK, PRD_HRGJUAL HRGN
        FROM TBMASTER_PRODMAST 
        WHERE PRD_PRDCD LIKE :base_plu 
    ) subs1
    LEFT JOIN (
        select PLUMM, hrg_netmm
        from (
            select pluN plumm, HRGN, HRGP,
                (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END) HRGMM,
                qty qtymm, cb cbmm,
                (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END)),0)-COALESCE(cb,0)) hrg_netmm 
            from ( 
                select pluN, HRGN, HRGP, hrg, qty, sum((jmlcbh*cbh)+(jmlcbd*cbd)) CB 
                from ( 
                    select distinct pluN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty,
                        sum(case when pluN like '%0' 
                            then (case when COALESCE(MINRPHC,0)<>'0' 
                                then (case when (hrg) > MAXRPHC then FLOOR(MAXRPHC/MINRPHC) else FLOOR((hrg)/MINRPHC) end) 
                                else 0 end)
                            else (case when COALESCE(MINRPHC,0)<>'0' 
                                then (case when (hrg*qty) > MAXRPHC then FLOOR(MAXRPHC/MINRPHC) else FLOOR((hrg*qty)/MINRPHC) end)
                                else 0 end)
                        end ) jmlcbh, 
                        sum(case when COALESCE(MINJUALC,0)<>'0' 
                            then (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) 
                                ELSE (case when qty > MAXJUALC then FLOOR(MAXJUALC/MINJUALC) else FLOOR(qty/MINJUALC) end) END)
                            else 0 end ) jmlcbd 
                    from ( 
                        SELECT PLUN, DESK, FRACN, UNIT,
                            (CASE WHEN UNIT LIKE '%RCG%' THEN (1 * MINJUALN) ELSE (FRACN*MINJUALN) END) QTY,
                            HRGN, HRGP,
                            (CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END) HRG,
                            MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, CBH, CBD
                        FROM (
                            SELECT PLUN, DESK, FRACN, UNIT, MINJUALN, HRGN, HRGP
                            FROM ( 
                                SELECT PLUP, HRGP, FLAG FROM 
                                (SELECT DISTINCT PRMD_PRDCD PLUP, PRMD_HRGJUAL HRGP,
                                    (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
                                        WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
                                        ELSE 'MERAH' END) FLAG
                                FROM TBTR_PROMOMD 
                                LEFT JOIN TBTR_PROMOMD_ALOKASI 
                                ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
                                WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=current_date 
                                    AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=current_date
                                    AND PRMD_PRDCD LIKE :base_plu
                                ) subq8 WHERE FLAG='MERAH'
                            ) subq7
                            RIGHT JOIN ( 
                                SELECT PRD_PRDCD PLUN, PRD_DESKRIPSIPANJANG DESK, PRD_FRAC FRACN, PRD_UNIT UNIT, PRD_MINJUAL MINJUALN, PRD_HRGJUAL HRGN
                                FROM TBMASTER_PRODMAST
                                WHERE PRD_PRDCD LIKE :base_plu
                            ) subq9 ON PLUN=PLUP
                        ) subq6
                        LEFT JOIN (
                            SELECT PLUC, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, CBH, CBD 
                            FROM (
                                select cbd_kodepromosi kode, cbd_prdcd pluC,
                                    (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
                                        when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
                                        else cbh_mintotbelanja end) minrphC, 
                                    cbd_minstruk minjuALC,
                                    (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC,
                                    (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC,
                                    cbh_cashback cbh, cbd_cashback cbd
                                from tbtr_cashback_hdr 
                                left join tbtr_cashback_dtl on cbh_kodepromosi=cbd_kodepromosi 
                                left join tbtr_cashback_alokasi on cbh_kodepromosi=cba_kodepromosi 
                                where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', current_date) 
                                    and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', current_date)
                                    and CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
                                    and (COALESCE(cba_retailer,'0')='1' or COALESCE(cba_silver,'0')='1' 
                                        or COALESCE(cba_gold1,'0')='1' or COALESCE(cba_gold2,'0')='1' or COALESCE(cba_gold3,'0')='1')
                                    and cbh_namapromosi not like '%UNIQUE%' 
                                    and cbh_namapromosi not like '%PWP%' 
                                    and cbh_namapromosi not like '%UNICODE%' 
                                    and COALESCE(cbd_recordid,'2') <>'1' 
                                    and COALESCE(cbd_redeempoint,'0')='0'
                            ) subq10 WHERE PLUC LIKE :base_plu
                        ) subq11 ON SUBSTR(PLUN,1,6)||0=PLUC
                    ) subq5 
                    group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty 
                ) subq4 
                group by pluN, HRGN, HRGP, hrg, qty
            ) subq3
        ) subq2
        ORDER BY PLUMM
    ) subq1 ON subs1.PLU = subq1.PLUMM
    ORDER BY subs1.PLU
";