    <?php
    require_once '../layout/_top.php';
    require_once '../helper/connection.php';

    $kodePLU = isset($_GET['kodePLU']) && $_GET['kodePLU'] !== "" ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
    $data = [];
    $barcodes = [];

    if ($kodePLU !== '') {
        try {
            // Query 1: Informasi produk utama
            $stmt = $conn->prepare("SELECT DISTINCT ON (prd_prdcd)
                prd_prdcd,
                prd_kodetag,
                prd_deskripsipanjang,
                prd_kategoritoko,
                prd_kodecabang,
                prd_flaggudang,
                prd_create_dt,
                prd_kodedivisi || '   ' || COALESCE(div_namadivisi, '') || ' - ' || 
                COALESCE(prd_kodekategoribarang, '') || '  ' || COALESCE(kat_namakategori, '') || ' - ' ||
                COALESCE(prd_kodedepartement, '') || '  ' || COALESCE(dep_namadepartement, '') AS div_dept_kat
            FROM tbmaster_prodmast
            LEFT JOIN tbmaster_divisi ON prd_kodedivisi = div_kodedivisi
            LEFT JOIN tbmaster_departement ON prd_kodedepartement = dep_kodedepartement
            LEFT JOIN tbmaster_kategori ON prd_kodekategoribarang = kat_kodekategori
            WHERE prd_prdcd = :kodePLU");
            $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Query 2: Barcode and Promo Price Data
            $stmt2 = $conn->prepare("SELECT 
        pm.PRD_KODEDIVISI,
        pm.PRD_KODEDEPARTEMENT,
        pm.PRD_PRDCD,
        pm.PRD_DESKRIPSIPANJANG,
        pm.PRD_UNIT,
        pm.PRD_FRAC,
        pm.PRD_HRGJUAL AS PRD_HRGJUAL,  -- harga jual pakai promo jika ada
        md.PRMD_HRGJUAL AS MD_HRGJUAL,  -- harga jual pakai promo jika ada
        pm.PRD_KODETAG,
        pc.PRC_KODETAG,
        pm.PRD_FLAG_AKTIVASI,
        pm.PRD_AVGCOST,
        pm.PRD_LASTCOST,
        pm.PRD_MINJUAL,
        md.PRMD_HRGJUAL,
        md.PRMD_TGLAWAL,
        md.PRMD_TGLAKHIR,
        bc.BRC_BARCODE,

        -- Hitung margin berdasarkan flag BKP pakai harga promo jika ada
        CASE 
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - (pm.PRD_AVGCOST * 1.11)) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
            WHEN pm.PRD_FLAGBKP1 IS NULL AND pm.PRD_FLAGBKP2 IN ('N','C') 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
        END AS MARGIN,

        ROUND(
            CASE         
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100   
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
                WHEN pm.PRD_FLAGBKP1 = 'N' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
            END, 2
        ) AS MARGINACOST, 

        ROUND(
            CASE         
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
                WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
                WHEN pm.PRD_FLAGBKP1 = 'N' 
                    THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100        
            END, 2
        ) AS MARGINLCOST,

        CASE 
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN 1.11
            WHEN pm.PRD_FLAGBKP1 = 'N' AND pm.PRD_FLAGBKP2 IN ('N','C') THEN 1
        END AS KALI,

        -- ST_HARGA_NETTO dari harga jual promo jika ada
        CASE 
            WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
                THEN COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) / 11.1 * 10
            ELSE COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)
        END AS ST_HARGA_NETTO,

        -- ST_MD_NETTO dari md.PRMD_HRGJUAL jika ada
        CASE 
            WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
                THEN md.PRMD_HRGJUAL / 11.1 * 10
            ELSE md.PRMD_HRGJUAL
        END AS ST_MD_NETTO,

        CASE 
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '0' THEN '0'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '1' THEN '1'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '2' THEN '2'
            WHEN RIGHT(pm.PRD_PRDCD, 1) = '3' THEN '3'
            ELSE NULL
        END AS sj,

        pm.PRD_FLAGBKP1,
        pm.PRD_FLAGBKP2

    FROM tbmaster_prodmast pm

    LEFT JOIN (
        SELECT prmd_prdcd, prmd_hrgjual, prmd_tglawal, prmd_tglakhir
        FROM tbtr_promomd
        WHERE CURRENT_DATE BETWEEN prmd_tglawal AND prmd_tglakhir
    ) md ON pm.prd_prdcd = md.prmd_prdcd

    LEFT JOIN tbmaster_prodcrm pc ON pm.prd_prdcd = pc.prc_pluigr

    LEFT JOIN (
        SELECT DISTINCT ON (brc_prdcd) brc_prdcd, brc_barcode
        FROM tbmaster_barcode
        ORDER BY brc_prdcd, brc_barcode
    ) bc ON pm.prd_prdcd = bc.brc_prdcd

    WHERE pm.PRD_PRDCD LIKE :kodePLU

    ORDER BY pm.PRD_MINJUAL, pm.PRD_AVGCOST DESC");

            // Update kodePLU for LIKE clause
            $kodePLU = substr($kodePLU, 0, 6) . '%';
            $stmt2->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
            $stmt2->execute();

            // Fetch all barcode and product data
            $barcodes = $stmt2->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 3 :: TREN SALED

        $stmt3 = $conn->prepare("SELECT a.*,
			  ST_SALES,
			  CASE
			    WHEN p.PRD_UNIT = 'KG'
			    AND p.PRD_FRAC  = 1000
			    THEN (ST_SALES*ST_AVGCOST)/ p.prd_frac
			    ELSE ST_SALES *ST_AVGCOST
			  END HPP
			FROM TBTR_SALESBULANAN a
			LEFT JOIN TBMASTER_STOCK b
			ON a.SLS_PRDCD = b.ST_PRDCD
			LEFT JOIN tbmaster_prodmast p
			ON a.SLS_PRDCD  = p.PRD_PRDCD
			WHERE ST_LOKASI ='01'
			AND SLS_PRDCD LIKE :kodePLU");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt3->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt3->execute();

        // Fetch all barcode and product data
        $trensale = $stmt3->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 4 :: STOCK

        $stmt4 = $conn->prepare("SELECT st_lokasi ,
				  CASE
					WHEN st_lokasi ='01'
					THEN 'BK'
					WHEN st_lokasi ='02'
					THEN 'RT'
					WHEN st_lokasi ='03'
					THEN 'RS'
				  END AS lokasi,
				  st_prdcd ,
				  st_saldoawal ,
				  st_trfin,
				  st_trfout,
				  st_sales ,
				  st_retur ,
				  st_adj ,
				  st_intransit ,
				  st_so + st_selisih_soic as so ,
				  st_saldoakhir,
                  st_saldoakhir_lpp
				FROM tbmaster_stock
				WHERE st_prdcd like :kodePLU
				ORDER BY 1");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt4->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt4->execute();

        // Fetch all barcode and product data
        $stok = $stmt4->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 5 :: STOCK

        $stmt5 = $conn->prepare("SELECT 
    d.cbd_prdcd,  
    d.cbd_kodepromosi,
    h.cbh_namapromosi,
    d.cbd_minstruk,
    h.cbh_minrphprodukpromo,
    h.cbh_mintotbelanja,
    CASE
        WHEN COALESCE(d.cbd_cashback, 0) = 0 THEN h.cbh_cashback
        ELSE d.cbd_cashback
    END AS cbd_cashback,
    COALESCE(a.cba_alokasijumlah, 0) - COALESCE(k.cbk_cashback_qty, 0) AS cbk_sisa,
    d.cbd_maxstruk,
    d.cbd_maxmemberperhari,
    d.cbd_maxfrekperevent,
    d.cbd_maxrphperevent,
    d.cbd_alokasistok,
    h.cbh_tglawal,
    h.cbh_tglakhir,
    d.cbd_flagkelipatan,                    
    a.cba_reguler,
    a.cba_reguler_biruplus,
    a.cba_freepass,
    a.cba_retailer,
    a.cba_silver,
    a.cba_gold1,
    a.cba_gold2,
    a.cba_gold3,
    a.cba_platinum
FROM tbtr_cashback_dtl d
LEFT JOIN tbtr_cashback_hdr h
    ON d.cbd_kodepromosi = h.cbh_kodepromosi
LEFT JOIN (
    SELECT DISTINCT ON (cba_kodepromosi)
        *
    FROM tbtr_cashback_alokasi
    WHERE cba_kodecabang = '1R'
) a ON d.cbd_kodepromosi = a.cba_kodepromosi
LEFT JOIN (
    SELECT kd_promosi AS cbk_kodepromosi, 
           SUM(kelipatan) AS cbk_cashback_qty 
    FROM m_promosi_h 
    GROUP BY kd_promosi
) k ON d.cbd_kodepromosi = k.cbk_kodepromosi
WHERE CURRENT_DATE BETWEEN h.cbh_tglawal AND h.cbh_tglakhir
    AND h.cbh_kodeigr = '1R'
    AND COALESCE(d.cbd_recordid, '2') <> '1'
    AND d.cbd_prdcd LIKE :kodePLU
ORDER BY h.cbh_tglawal ASC");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt5->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt5->execute();

        // Fetch all barcode and product data
        $csbk = $stmt5->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 6 :: HARGA MEMBER BMP

        $stmt6 = $conn->prepare("SELECT PLU,
                     HRGMM,
                     CBMM,
                     HRG_NETMM,
                     HRGBIRU,
                     CBBIRU,
                     HRG_NETBIRU,
                     HRGPLA,
                     CBPLA,
                     HRG_NETPLA
              FROM (       
              SELECT PRD_PRDCD PLU
              FROM TBMASTER_PRODMAST WHERE PRD_PRDCD LIKE :kodePLU  ) subs1
              LEFT JOIN (
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              ---- MEMBER MERAH ----
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              select PLUMM,
                     HRGMM,HRGP,
                     CBMM,
                     HRG_NETMM
              from (
              select pluN plumm, HRGN,
                     HRGP,
                     -- (CASE WHEN PLUN LIKE '%0' THEN HRG  ELSE ( HRG * QTY) END ) HRGMM,
                     (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END ) HRGMM,
                     qty qtymm,
                     cb cbmm,
                     (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG  ELSE ( HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netmm 
              from ( 
              select pluN,  HRGN,
                     HRGP,
                     hrg,qty, 
                     sum((jmlcbh*cbh)+(jmlcbd*cbd)) CB 
              from ( 
              select distinct pluN, 
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC, 
                     cbd, 
                     cbh,
                     HRGN,
                     HRGP,
                     hrg,
                     qty,
                     sum(case when pluN like '%0' 
                     then (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     else (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg*qty) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg*qty)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     end )jmlcbh, 
                     sum(case when COALESCE(MINJUALC,0)<>'0' 
                            then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                            ( case when qty > MAXJUALC
                                   then FLOOR(MAXJUALC/MINJUALC) 
                                   else FLOOR(qty/MINJUALC) 
                                   end )END )
                     else 0 
                     end ) jmlcbd 
              from (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     (CASE WHEN UNIT LIKE '%RCG%'
                     THEN ( 1 * MINJUALN )
                     ELSE ( FRACN*MINJUALN) 
                     END ) QTY,
                     HRGN,
                     HRGP,
                     (CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END ) HRG,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD
              FROM (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     MINJUALN,
                     HRGN,
                     HRGP
              FROM ( 
              SELECT PLUP,HRGP,FLAG FROM 
              (SELECT DISTINCT PRMD_PRDCD PLUP,
                     PRMD_HRGJUAL HRGP,
                     (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
                            WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
                            ELSE 'MERAH' END ) FLAG
              FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
              -- yg tak ganti --
              -- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date -1) --
              WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date)
              AND PRMD_PRDCD LIKE :kodePLU  ) subq8 WHERE FLAG='MERAH' ) subq7
              RIGHT JOIN ( 
              SELECT PRD_PRDCD PLUN,
                     PRD_DESKRIPSIPANJANG DESK,
                     PRD_FRAC FRACN,
                     PRD_UNIT UNIT,
                     PRD_MINJUAL MINJUALN,
                     PRD_HRGJUAL HRGN
              FROM TBMASTER_PRODMAST
              WHERE PRD_PRDCD LIKE :kodePLU  ) subq9 ON PLUN=PLUP ) subq6
              LEFT JOIN (
              SELECT PLUC,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD 
              FROM (
              select cbd_kodepromosi kode, 
                     cbd_prdcd pluC, 
              (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
                     when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
                     else cbh_mintotbelanja 
                     end ) minrphC, 
                     cbd_minstruk minjuALC, 
                     (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
                     (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
                     cbh_cashback cbh, 
                     cbd_cashback cbd 
              from tbtr_cashback_hdr left join tbtr_cashback_dtl on cbh_kodepromosi=cbd_kodepromosi 
                                   left join tbtr_cashback_alokasi on cbh_kodepromosi=cba_kodepromosi 
              where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', current_date) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', current_date)   AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
              and ( COALESCE(cba_retailer,'0')='1' or COALESCE(cba_silver,'0')='1' or COALESCE(cba_gold1,'0')='1' or COALESCE(cba_gold2,'0')='1' or COALESCE(cba_gold2,'0')='1' ) 
              and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%' 
              and COALESCE(cbd_recordid,'2') <>'1' 
              and  COALESCE(cbd_redeempoint,'0')='0' ) subq10 WHERE PLUC LIKE :kodePLU) subq11 ON SUBSTR(PLUN,1,6)||0=PLUC) subq5 
              group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) subq4 group by pluN, HRGN, HRGP, hrg, qty) subq3 ) subq2
              ORDER BY PLUMM ) subq1 ON PLU=PLUMM
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              ---- MEMBER BIRU ----
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              LEFT JOIN (
              select PLUBIRU,
                     HRGBIRU,
                     CBBIRU,
                     HRG_NETBIRU
              from (
              select pluN pluBIRU, HRGN,
                     HRGP,
                     (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG  ELSE ( HRG * QTY) END ) HRGBIRU,
                     qty qtyBIRU,
                     cb cbBIRU,
                     (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netBIRU 
              from ( 
              select pluN,  HRGN,
                     HRGP,
                     hrg,qty, 
                     sum(COALESCE((jmlcbh*cbh),0)+COALESCE((jmlcbd*cbd),0)) CB 
              from ( 
              select distinct pluN, 
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC, 
                     cbd, 
                     cbh,
                     HRGN,
                     HRGP,
                     hrg,
                     qty,
                     sum(case when pluN like '%0' 
                     then (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     else (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg*qty) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg*qty)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     end )jmlcbh, 
                     sum(case when COALESCE(MINJUALC,0)<>'0' 
                     then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                            ( case when qty > MAXJUALC
                                   then FLOOR(MAXJUALC/MINJUALC) 
                                   else FLOOR(qty/MINJUALC) 
                                   end )END )
                     else 0 
                     end ) jmlcbd 
              from (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     (CASE WHEN UNIT LIKE '%RCG%'
                     THEN ( 1 * MINJUALN )
                     ELSE ( FRACN*MINJUALN) 
                     END ) QTY,
                     HRGN,
                     HRGP,
                     COALESCE((CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END ),0) HRG,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD
              FROM (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     MINJUALN,
                     HRGN,
                     HRGP
              FROM ( 
              SELECT PLUP,HRGP,FLAG FROM 
              (SELECT DISTINCT PRMD_PRDCD PLUP,
                     COALESCE(PRMD_HRGJUAL,0) HRGP,
                     (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
                            WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
                            ELSE 'MERAH' END ) FLAG
              FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
              -- yg tak ganti --
              -- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date -1) --
              WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date)
              AND PRMD_PRDCD LIKE :kodePLU  ) subd11 WHERE FLAG='BIRU' ) subd10
              RIGHT JOIN ( 
              SELECT PRD_PRDCD PLUN,
                     PRD_DESKRIPSIPANJANG DESK,
                     PRD_FRAC FRACN,
                     PRD_UNIT UNIT,
                     PRD_MINJUAL MINJUALN,
                     PRD_HRGJUAL HRGN
              FROM TBMASTER_PRODMAST
              WHERE PRD_PRDCD LIKE :kodePLU  ) subd9 ON PLUN=PLUP ) subd8
              LEFT JOIN (
              SELECT PLUC,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD 
              FROM (
              select cbd_kodepromosi kode, 
                     cbd_prdcd pluC, 
              (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
                     when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
                     else cbh_mintotbelanja 
                     end ) minrphC, 
                     cbd_minstruk minjuALC, 
                     (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
                     (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
                     COALESCE(cbh_cashback,0) cbh, 
                     COALESCE(cbd_cashback,0) cbd 
              from tbtr_cashback_hdr left join tbtr_cashback_dtl on cbh_kodepromosi=cbd_kodepromosi 
                                   left join tbtr_cashback_alokasi on cbh_kodepromosi=cba_kodepromosi 
              where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', current_date) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', current_date)  
              and ( COALESCE(cba_REGULER,'0')='1' or COALESCE(cba_REGULER_BIRUPLUS,'0')='1')  
              and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%'  AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
              and COALESCE(cbd_recordid,'2') <>'1' 
              and  COALESCE(cbd_redeempoint,'0')='0' ) subd7 WHERE PLUC LIKE :kodePLU  ) subd6 ON SUBSTR(PLUN,1,6)||0=PLUC) subd5 
              group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) subd4 group by pluN, HRGN, HRGP, hrg, qty) subd3) subd2
              ORDER BY PLUBIRU ) subd1 ON PLU=PLUBIRU
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              ---- MEMBER PLATINUM ----
              ------------------------------------------------------------------------------------------------------------------------------------
              ------------------------------------------------------------------------------------------------------------------------------------
              LEFT JOIN (
              select PLUPLA,
                     HRGPLA,
                     CBPLA,
                     HRG_NETPLA
              from (
              select pluN pluPLA, HRGN,
                     HRGP,
                     (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END ) HRGPLA,
                     qty qtyPLA,
                     cb cbPLA,
                     (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netPLA 
              from ( 
              select pluN,  HRGN,
                     HRGP,
                     hrg,qty, 
                     sum(COALESCE((jmlcbh*cbh),0)+COALESCE((jmlcbd*cbd),0)) CB 
              from ( 
              select distinct pluN, 
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC, 
                     cbd, 
                     cbh,
                     HRGN,
                     HRGP,
                     hrg,
                     qty,
                     sum(case when pluN like '%0' 
                     then (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     else (case when COALESCE(MINRPHC,0)<>'0' 
                                   then  ( case when (hrg*qty) > MAXRPHC 
                                                 then FLOOR(MAXRPHC/MINRPHC) 
                                                 else FLOOR((hrg*qty)/MINRPHC) 
                                                 end ) 
                                   else 0 
                                   end )
                     end )jmlcbh, 
                     sum(case when COALESCE(MINJUALC,0)<>'0' 
                            then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                            ( case when qty > MAXJUALC
                                   then FLOOR(MAXJUALC/MINJUALC) 
                                   else FLOOR(qty/MINJUALC) 
                                   end )END )
                     else 0 
                     end ) jmlcbd 
              from (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     (CASE WHEN UNIT LIKE '%RCG%'
                     THEN ( 1 * MINJUALN )
                     ELSE ( FRACN*MINJUALN) 
                     END ) QTY,
                     HRGN,
                     HRGP,
                     COALESCE((CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END ),0) HRG,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD
              FROM (
              SELECT PLUN,
                     DESK,
                     FRACN,
                     UNIT,
                     MINJUALN,
                     HRGN,
                     HRGP
              FROM ( 
              SELECT PLUP,HRGP,FLAG FROM 
              (SELECT DISTINCT PRMD_PRDCD PLUP,
                     COALESCE(PRMD_HRGJUAL,0) HRGP,
                     (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
                            WHEN (ALK_MEMBER='REGPLAPLUS' OR ALK_MEMBER='REGPLA') THEN 'BIRU'
                            ELSE 'MERAH' END ) FLAG
              FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
              -- yg tak ganti --
              -- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date -1) --
              WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date)
              AND PRMD_PRDCD LIKE :kodePLU  ) subc11 WHERE FLAG='PLATINUM' ) subc10
              RIGHT JOIN ( 
              SELECT PRD_PRDCD PLUN,
                     PRD_DESKRIPSIPANJANG DESK,
                     PRD_FRAC FRACN,
                     PRD_UNIT UNIT,
                     PRD_MINJUAL MINJUALN,
                     PRD_HRGJUAL HRGN
              FROM TBMASTER_PRODMAST
              WHERE PRD_PRDCD LIKE :kodePLU  ) subc9 ON PLUN=PLUP ) subc8
              LEFT JOIN (
              SELECT PLUC,
                     MINRPHC,
                     MINJUALC,
                     MAXJUALC,
                     MAXRPHC,
                     CBH,
                     CBD 
              FROM (
              select cbd_kodepromosi kode, 
                     cbd_prdcd pluC, 
              (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
                     when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
                     else cbh_mintotbelanja 
                     end ) minrphC, 
                     cbd_minstruk minjuALC, 
                     (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
                     (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
                     COALESCE(cbh_cashback,0) cbh, 
                     COALESCE(cbd_cashback,0) cbd 
              from tbtr_cashback_hdr left join tbtr_cashback_dtl on cbh_kodepromosi=cbd_kodepromosi 
                                   left join tbtr_cashback_alokasi on cbh_kodepromosi=cba_kodepromosi 
              where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', current_date) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', current_date)  
              and ( COALESCE(cba_platinum,'0')='1' ) 
              and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%'  AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
              and COALESCE(cbd_recordid,'2') <>'1' 
              and  COALESCE(cbd_redeempoint,'0')='0' ) subc7 WHERE PLUC LIKE :kodePLU  ) subc6 ON SUBSTR(PLUN,1,6)||0=PLUC) subc5 
              group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) subc4 group by pluN, HRGN, HRGP, hrg, qty) subc3) subc2
              ORDER BY PLUPLA ) subc1 ON PLU=PLUPLA");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt6->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt6->execute();

        // Fetch all barcode and product data
        $hrgMember = $stmt6->fetchAll(PDO::FETCH_ASSOC);

        // QUERY 7 :: STOCK

        $stmt7 = $conn->prepare("SELECT * 
                        FROM tbtr_promomd
                        WHERE CURRENT_DATE BETWEEN prmd_tglawal AND prmd_tglakhir
                        and prmd_prdcd like :kodePLU
				        ");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt7->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt7->execute();

        // Fetch all barcode and product data
        $prmMD = $stmt7->fetchAll(PDO::FETCH_ASSOC);

        // QUERY 8 :: PKM

        $stmt8 = $conn->prepare("SELECT 
  DSI,
  PKM_PKMT,
  PKM_MINORDER,
  PKM_MINDISPLAY
FROM TBMASTER_KKPKM
JOIN (
  SELECT 
    ST_PRDCD AS DSI_PLU,
    ST_SALDOAKHIR,
    ROUND(
      CASE
        WHEN ST_SALES > 0 THEN 
          ((((ST_SALDOAWAL + ST_SALDOAKHIR) / 2) / ST_SALES) * EXTRACT(DOY FROM CURRENT_DATE))
        ELSE 0
      END, 0
    ) AS DSI
  FROM TBMASTER_STOCK
  WHERE ST_LOKASI = '01'
) AS stock_data
ON PKM_PRDCD LIKE  :kodePLU
AND PKM_PRDCD = stock_data.DSI_PLU");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt8->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt8->execute();

        // Fetch all barcode and product data
        $pkm = $stmt8->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 9 :: GIFT

        $stmt9 = $conn->prepare("SELECT 
    d.gfd_prdcd            AS gif_prdcd,
    d.gfd_kodepromosi      AS gif_kode_promosi,
    h.gfh_namapromosi      AS gif_nama_promosi,  
    d.gfd_pcs              AS gif_min_beli_pcs,
    d.gfd_rph              AS gif_min_beli_rph,
    h.gfh_tglawal          AS gif_mulai,
    h.gfh_tglakhir         AS gif_selesai,
    h.gfh_jenispromosi     AS gif_jenis_promosi,
    h.gfh_mintotbelanja    AS gif_min_total_struk,
    h.gfh_mintotsponsor    AS gif_min_total_sponsor,
    h.gfh_maxjmlhari       AS gif_max_jml_hari,
    h.gfh_maxfrekhari      AS gif_max_frek_hari,
    h.gfh_maxjmlevent      AS gif_max_jml_event,
    h.GFH_MAXFREKEVENT     AS gif_max_frek_event,
    h.gfh_jenishadiah      AS gif_jenis_hadiah,
    h.gfh_kethadiah        AS gif_plu_hadiah,
    p.prd_deskripsipanjang AS gif_nama_hadian,
    h.gfh_jmlhadiah        AS gif_jumlah_hadiah,
    a.gfa_reguler          AS gif_reguler,
    a.gfa_reguler_biruplus AS gif_reguler_biruplus,
    a.gfa_freepass         AS gif_freepass,
    a.gfa_retailer         AS gif_retailer,
    a.gfa_silver           AS gif_silver,
    a.gfa_gold1            AS gif_gold1,
    a.gfa_gold2            AS gif_gold2,
    a.gfa_gold3            AS gif_gold3,
    a.gfa_platinum         AS gif_platinum
FROM tbtr_gift_dtl d
LEFT JOIN tbtr_gift_hdr h ON d.gfd_kodepromosi = h.gfh_kodepromosi
LEFT JOIN tbtr_gift_alokasi a ON d.gfd_kodepromosi = a.gfa_kodepromosi
LEFT JOIN tbmaster_prodmast p ON h.gfh_kethadiah = p.prd_prdcd
WHERE CURRENT_DATE BETWEEN DATE_TRUNC('day', h.gfh_tglawal) AND DATE_TRUNC('day', h.gfh_tglakhir)
    AND COALESCE(h.GFH_RECORDID, '2') <> '1'
    AND (h.GFH_KODEIGR = '1R' AND a.GFA_KODECABANG = '1R')
    AND d.gfd_prdcd LIKE :kodePLU");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt9->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt9->execute();

        // Fetch all barcode and product data
        $Gift = $stmt9->fetchAll(PDO::FETCH_ASSOC);


        // QUERY 10 :: HJK

        $stmt10 = $conn->prepare("SELECT 
              hgk_prdcd,
              hgk_hrgjual,
              hgk_tglawal,
              hgk_jamawal,
              hgk_tglakhir,
              hgk_jamakhir,
              hgk_hariaktif
          FROM tbtr_hargakhusus
          WHERE CURRENT_DATE BETWEEN hgk_tglawal::date AND hgk_tglakhir::date
              AND hgk_prdcd LIKE :kodePLU");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt10->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt10->execute();

        // Fetch all barcode and product data
        $Hjk = $stmt10->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
?>

<link rel="stylesheet" href="./assets/style.css">


<!-- HTML OUTPUT -->

<head>
    <title>Informasi & History Product</title>
</head>
<div class="promo-section">
    <section class="section">
        <div class="section-header d-flex justify-content-between mb-0">
            <h1>Informasi Promosi dan Produk</h1>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-0">
            <div class="section-title">Deskripsi Produk</div>
            <div class="d-flex" style="gap: 0.5rem;">
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoFull()">Full</button>
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoLokasi()">Lokasi</button>
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoPenerimaan()">Penerimaan</button>
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoPenjualan()">Penjualan</button>
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoPb()">PB</button>
                <button type="button" class="btn btn-primary btn-md" onclick="loadModalInfoSo()">SO</button>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleSection('produkContainer', this)">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>

        <?php include './tabel/tabel_produk.php'; ?>
        <?php include './tabel/tabel_hrgj.php'; ?>
        <?php include './tabel/tabel_hrg_mm_mb_mp.php'; ?>
        <?php include './tabel/tabel_prm_cb.php'; ?>
        <?php include './tabel/tabel_prm_gf.php'; ?>
        <?php include './tabel/tabel_sales.php'; ?>
        <?php include './tabel/tabel_stock.php'; ?>
        <?php include './tabel/tabel_pkm.php'; ?>


    </section>
</div>

<!-- Container untuk menampung modal yang di-load via AJAX -->
<div id="modalInfoTagContainer"></div>

<!-- Container untuk menampung modal yang di-load via AJAX -->
<div id="modalInfoFullContainer"></div>

<!-- Container untuk menampung modal yang di-load via AJAX -->
<div id="modalInfoLokasiContainer"></div>

<!-- Container untuk menampung modal Penerimaan -->
<div id="modalInfoPenerimaanContainer"></div>

<!-- Container untuk menampung modal Penjualan -->
<div id="modalInfoPenjualanContainer"></div>

<!-- Container untuk menampung modal Pb -->
<div id="modalInfoPbContainer"></div>

<!-- Container untuk menampung modal So -->
<div id="modalInfoSoContainer"></div>

<!-- Panggil file JS eksternal -->
<script src="./assets/js/function.js"></script>

<?php
require_once '../layout/_bottom.php';
?>