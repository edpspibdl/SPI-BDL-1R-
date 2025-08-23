<?php

try {
  $sql = "SELECT
  CASE DTL_TIPEMEMBER
    WHEN 1
    THEN 'MEMBER MERAH'
    WHEN 2
    THEN 'END USER'
    WHEN 3
    THEN 'IDM'
    WHEN 4
    THEN 'OMI'
    ELSE 'OTHER'
  END TIPE,COUNT(DISTINCT(sls.DTL_CUSNO))                          AS CUSTOMER,
  COUNT(DISTINCT(DTL_STRUK))                          AS STRUK,
  COUNT(DISTINCT(DTL_PRDCD_CTN))                      AS PRODUK,
  TRUNC(SUM(sls.DTL_NETTO))                               AS NETTO,
  TRUNC(SUM(sls.DTL_GROSS))                               AS GROSS,
  TRUNC(SUM(sls.DTL_HPP))                                 AS HPP,
  TRUNC(SUM(sls.DTL_MARGIN))                              AS MARGIN,
  TRUNC((SUM(sls.DTL_MARGIN)/SUM(sls.DTL_NETTO))*100,2)       AS MARGIN_PERSEN,
  TRUNC((SUM(sls.DTL_NETTO) /COUNT(DISTINCT(sls.DTL_CUSNO)))) AS APC,
  TRUNC((SUM(DTL_NETTO) /COUNT(DISTINCT(DTL_STRUK)))) AS APS

FROM
  (SELECT dtl_rtype,
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
    -- quantity in pcs
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_qty_pcs
      ELSE dtl_qty_pcs * -1
    END dtl_qty_pcs,
    -- quantity
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_qty
      ELSE dtl_qty*-1
    END dtl_qty,
    dtl_harga_jual,
    dtl_diskon,
    -- gross
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_gross
      ELSE dtl_gross*-1
    END dtl_gross,
    -- netto
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_netto
      ELSE dtl_netto*-1
    END dtl_netto,
    -- hpp
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_hpp
      ELSE dtl_hpp*-1
    END dtl_hpp,
    -- margin = netto - hpp
    CASE
      WHEN dtl_rtype='S'
      THEN dtl_netto  - dtl_hpp
      ELSE (dtl_netto - dtl_hpp) * -1
    END dtl_margin,
    dtl_k_div,
    dtl_k_dept,
    dtl_k_katb,
    dtl_cusno,
    dtl_namamember,
    dtl_memberkhusus,
    dtl_outlet,
    dtl_suboutlet,
    CASE
      WHEN dtl_memberkhusus='Y'
      THEN 1 -- member khusus
      WHEN dtl_memberkhusus IS NULL and dtl_outlet ='6'
      AND dtl_kasir         <>'IDM'
      AND dtl_kasir         <>'OMI'
      AND dtl_kasir         <>'BKL'
      THEN 2 -- end user
      when DTL_CUSNO in (select TKO_KODECUSTOMER from tbmaster_tokoigr where TKO_KODESBU ='I')
      then 3 -- IDM
      when DTL_CUSNO in (select TKO_KODECUSTOMER from tbmaster_tokoigr where TKO_KODESBU ='O')
      then 4 -- OMI
      ELSE 5

    END dtl_tipemember
  FROM
    (SELECT sls.TRJD_TRANSACTIONTYPE  AS dtl_rtype,
     DATE_TRUNC('day', sls.TRJD_TRANSACTIONDATE) AS dtl_tanggal,
      TO_CHAR(sls.TRJD_TRANSACTIONDATE,'yyyymmdd')
      || sls.TRJD_CASHIERSTATION
      ||sls.TRJD_CREATE_BY
      || sls.TRJD_TRANSACTIONNO
      ||sls.TRJD_TRANSACTIONTYPE AS dtl_struk,
      sls.TRJD_CASHIERSTATION    AS dtl_stat,
      sls.TRJD_CREATE_BY         AS dtl_kasir,
      sls.TRJD_TRANSACTIONNO     AS dtl_no_struk,
      sls.TRJD_SEQNO             AS dtl_seqno,
      SUBSTR(sls.TRJD_PRDCD,1,6)
      || '0'                           AS dtl_prdcd_ctn,
      sls.TRJD_PRDCD                   AS dtl_prdcd,
      prd.PRD_DESKRIPSIPANJANG         AS dtl_nama_barang,
      prd.PRD_UNIT                     AS dtl_unit,
      prd.PRD_FRAC                     AS dtl_frac,
      prd.prd_kodetag                  AS dtl_tag,
      sls.TRJD_FLAGTAX1                AS dtl_bkp,
      sls.TRJD_QUANTITY * prd.PRD_FRAC AS dtl_qty_pcs,
      sls.TRJD_QUANTITY                AS dtl_qty,
      sls.TRJD_UNITPRICE               AS dtl_harga_jual,
      sls.TRJD_DISCOUNT                AS dtl_diskon,
      CASE
        WHEN SLS.TRJD_FLAGTAX1  ='Y' and coalesce(sls.TRJD_FLAGTAX2, 'z') IN ( 'Y', 'z' )
        AND sls.trjd_create_by IN('IDM','OMI','BKL')

        THEN sls.TRJD_NOMINALAMT*11.1/10
        ELSE sls.TRJD_NOMINALAMT
      END dtl_gross,
      CASE
        WHEN SLS.TRJD_FLAGTAX1      ='Y' and coalesce(sls.TRJD_FLAGTAX2, 'z') IN ( 'Y', 'z' )
        AND sls.trjd_create_by NOT IN('IDM','OMI','BKL')
        THEN sls.TRJD_NOMINALAMT    /11.1*10
        ELSE sls.TRJD_NOMINALAMT
      END dtl_netto,
      --sls.TRJD_BASEPRICE AS dtl_acost,
      CASE
        WHEN PRD.PRD_UNIT = 'KG'
        THEN sls.TRJD_QUANTITY * sls.TRJD_BASEPRICE/1000
        ELSE sls.TRJD_QUANTITY * sls.TRJD_BASEPRICE
      END dtl_hpp,
      sls.TRJD_DIVISIONCODE         AS dtl_k_div,
      SUBSTR(sls.trjd_division,1,2) AS dtl_k_dept,
      SUBSTR(sls.trjd_division,3,2) AS dtl_k_katb,
      sls.TRJD_CUS_KODEMEMBER       AS dtl_cusno,
      cus.cus_namamember            AS dtl_namamember,
      cus.cus_flagmemberkhusus      AS dtl_memberkhusus,
      cus.cus_kodeoutlet            AS dtl_outlet,
      cus.cus_kodesuboutlet         AS dtl_suboutlet
    FROM (SELECT DISTINCT TRJD_RECORDID,TRJD_TRANSACTIONTYPE,TRJD_TRANSACTIONDATE,trjd_flagtax2,TRJD_CASHIERSTATION,TRJD_CREATE_BY,TRJD_TRANSACTIONNO,TRJD_SEQNO,TRJD_PRDCD,TRJD_FLAGTAX1,TRJD_QUANTITY,TRJD_UNITPRICE,TRJD_DISCOUNT,TRJD_NOMINALAMT,TRJD_BASEPRICE,TRJD_DIVISIONCODE,TRJD_DIVISION,TRJD_CUS_KODEMEMBER
FROM
  (SELECT trjd_recordid,
    trjd_transactiontype,
    trjd_transactiondate,
    trjd_cashierstation,
    trjd_create_by,
    trjd_transactionno,
    trjd_seqno,
    trjd_prdcd,
    trjd_flagtax1,trjd_flagtax2,
    trjd_quantity,
    trjd_unitprice,
    trjd_discount,
    trjd_nominalamt,
    trjd_baseprice,
    trjd_divisioncode,
    trjd_division,
    trjd_cus_kodemember
  from TBTR_JUALDETAIL
  WHERE DATE_TRUNC('day', trjd_transactiondate)=DATE_TRUNC('day', CURRENT_TIMESTAMP(0))
  AND trjd_recordid               IS NULL
  UNION ALL
  SELECT trjd_recordid,
    trjd_transactiontype,
    trjd_transactiondate,
    trjd_cashierstation,
    trjd_create_by,
    trjd_transactionno,
    trjd_seqno,
    trjd_prdcd,
    trjd_flagtax1,trjd_flagtax2,
    trjd_quantity,
    trjd_unitprice,
    trjd_discount,
    trjd_nominalamt,
    trjd_baseprice,
    trjd_divisioncode,
    trjd_division,
    trjd_cus_kodemember
  FROM tbtr_jualdetail_interface
  WHERE DATE_TRUNC('day', trjd_transactiondate)=DATE_TRUNC('day', CURRENT_TIMESTAMP(0))
  and TRJD_RECORDID               is null
  )s) sls left join 
      TBMASTER_PRODMAST prd on sls.TRJD_PRDCD        = prd.PRD_PRDCD left join
      tbmaster_customer cus on 

    sls.trjd_cus_kodemember = cus.cus_kodemember
    where sls.trjd_recordid      IS NULL
    AND sls.trjd_quantity      <> 0
    )
  s)sls group by CASE DTL_TIPEMEMBER
    WHEN 1
    THEN 'MEMBER MERAH'
    WHEN 2
    THEN 'END USER'
    WHEN 3
    THEN 'IDM'
    WHEN 4
    THEN 'OMI'
    ELSE 'OTHER'
  END ";

  $stmt = $conn->prepare($sql);
  $stmt->execute();
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}


try {
  $sql2 = "SELECT SUM (CUSTOMER)                                   AS TOT_CUS,
  SUM (STRUK)                                           AS TOT_STRUK,
  SUM (PRODUK)                                          AS TOT_PROD,
  SUM (NETTO)                                           AS TOT_NET,
  SUM (GROSS)                                           AS TOT_GROSS,
  SUM (HPP)                                             AS TOT_HPP,
  SUM (MARGIN)                                          AS TOT_MARGIN,
  TO_CHAR(SUM(MARGIN) / SUM(NETTO) *100, 'fm999999.99') AS TOT_PERSEN,
  SUM(NETTO)          / SUM(CUSTOMER)                   AS TOT_APC,
  SUM(NETTO)          / SUM(STRUK)                      AS TOT_APS
FROM
  (SELECT
    CASE dtl_tipemember
      WHEN 1
      THEN 'Member Merah'
      WHEN 2
      THEN 'Member Biru'
      ELSE 'IDM'
    END tipe,
    COUNT(DISTINCT(dtl_cusno))                          AS customer,
    COUNT(DISTINCT(dtl_struk))                          AS struk,
    COUNT(DISTINCT(dtl_prdcd_ctn))                      AS produk,
    TRUNC(SUM(dtl_netto))                               AS netto,
    TRUNC(SUM(DTL_GROSS))                               AS gross,
    TRUNC(SUM(dtl_hpp))                                 AS hpp,
    TRUNC(SUM(dtl_margin))                              AS margin,
    TRUNC((SUM(dtl_margin)/SUM(dtl_netto))*100)         AS margin_persen,
    TRUNC((SUM(dtl_netto) /COUNT(DISTINCT(dtl_cusno)))) AS APC,
    TRUNC((SUM(dtl_netto) /COUNT(DISTINCT(dtl_struk)))) AS APS
  FROM
    (SELECT dtl_rtype,
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
      dtl_bkp2,
      -- qu... SQLINES DEMO ***
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_qty_pcs
        ELSE dtl_qty_pcs * -1
      END dtl_qty_pcs,
      -- qu... SQLINES DEMO ***
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_qty
        ELSE dtl_qty*-1
      END dtl_qty,
      dtl_harga_jual,
      dtl_diskon,
      -- gr... SQLINES DEMO ***
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_gross
        ELSE dtl_gross*-1
      END dtl_gross,
      -- ne... SQLINES DEMO ***
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_netto
        ELSE dtl_netto*-1
      END dtl_netto,
      -- hp... SQLINES DEMO ***
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_hpp
        ELSE dtl_hpp*-1
      END dtl_hpp,
      -- SQLINES DEMO *** hpp
      CASE
        WHEN dtl_rtype='S'
        THEN dtl_netto  - dtl_hpp
        ELSE (dtl_netto - dtl_hpp) * -1
      END dtl_margin,
      dtl_k_div,
      dtl_k_dept,
      dtl_k_katb,
      dtl_cusno,
      dtl_namamember,
      dtl_memberkhusus,
      dtl_outlet,
      dtl_suboutlet,
      CASE
        WHEN dtl_memberkhusus='Y'
        THEN 1 -- me... SQLINES DEMO ***
        WHEN dtl_memberkhusus IS NULL
        AND dtl_kasir         <>'IDM'
        AND dtl_kasir         <>'OMI'
        AND dtl_kasir         <>'BKL'
        THEN 2 -- me... SQLINES DEMO ***
        ELSE 3 -- me... SQLINES DEMO ***
      END dtl_tipemember
    FROM
      (SELECT sls.TRJD_TRANSACTIONTYPE              AS dtl_rtype,
        DATE_TRUNC('day', sls.TRJD_TRANSACTIONDATE) AS dtl_tanggal,
        TO_CHAR(sls.TRJD_TRANSACTIONDATE,'yyyymmdd')
        || sls.TRJD_CASHIERSTATION
        ||sls.TRJD_CREATE_BY
        || sls.TRJD_TRANSACTIONNO
        ||sls.TRJD_TRANSACTIONTYPE AS dtl_struk,
        sls.TRJD_CASHIERSTATION    AS dtl_stat,
        sls.TRJD_CREATE_BY         AS dtl_kasir,
        sls.TRJD_TRANSACTIONNO     AS dtl_no_struk,
        sls.TRJD_SEQNO             AS dtl_seqno,
        SUBSTR(sls.TRJD_PRDCD,1,6)
        || '0'                           AS dtl_prdcd_ctn,
        sls.TRJD_PRDCD                   AS dtl_prdcd,
        prd.PRD_DESKRIPSIPANJANG         AS dtl_nama_barang,
        prd.PRD_UNIT                     AS dtl_unit,
        prd.PRD_FRAC                     AS dtl_frac,
        prd.prd_kodetag                  AS dtl_tag,
        sls.TRJD_FLAGTAX1                AS dtl_bkp,
        sls.TRJD_FLAGTAX2                AS dtl_bkp2,
        sls.TRJD_QUANTITY * prd.PRD_FRAC AS dtl_qty_pcs,
        sls.TRJD_QUANTITY                AS dtl_qty,
        sls.TRJD_UNITPRICE               AS dtl_harga_jual,
        sls.TRJD_DISCOUNT                AS dtl_diskon,
        CASE
          WHEN SLS.TRJD_FLAGTAX1  ='Y'
          AND SLS.TRJD_FLAGTAX2   ='Y'
          AND sls.trjd_create_by IN('IDM','OMI','BKL')
          THEN sls.TRJD_NOMINALAMT*1.11
          ELSE sls.TRJD_NOMINALAMT
        END dtl_gross,
        CASE
          WHEN SLS.TRJD_FLAGTAX1      ='Y'
          AND SLS.TRJD_FLAGTAX2       ='Y'
          AND sls.trjd_create_by NOT IN('IDM','OMI','BKL')
          THEN sls.TRJD_NOMINALAMT    /1.11
          ELSE sls.TRJD_NOMINALAMT
        END dtl_netto,
        -- SQLINES DEMO ***  AS dtl_acost,
        CASE
          WHEN PRD.PRD_UNIT = 'KG'
          THEN sls.TRJD_QUANTITY * sls.TRJD_BASEPRICE/1000
          ELSE sls.TRJD_QUANTITY * sls.TRJD_BASEPRICE
        END dtl_hpp,
        sls.TRJD_DIVISIONCODE         AS dtl_k_div,
        SUBSTR(sls.trjd_division,1,2) AS dtl_k_dept,
        SUBSTR(sls.trjd_division,3,2) AS dtl_k_katb,
        sls.TRJD_CUS_KODEMEMBER       AS dtl_cusno,
        cus.cus_namamember            AS dtl_namamember,
        cus.cus_flagmemberkhusus      AS dtl_memberkhusus,
        cus.cus_kodeoutlet            AS dtl_outlet,
        cus.cus_kodesuboutlet         AS dtl_suboutlet
      FROM
        (SELECT DISTINCT TRJD_RECORDID,
          TRJD_TRANSACTIONTYPE,
          TRJD_TRANSACTIONDATE,
          TRJD_CASHIERSTATION,
          TRJD_CREATE_BY,
          TRJD_TRANSACTIONNO,
          TRJD_SEQNO,
          TRJD_PRDCD,
          TRJD_FLAGTAX1,
          TRJD_FLAGTAX2,
          TRJD_QUANTITY,
          TRJD_UNITPRICE,
          TRJD_DISCOUNT,
          TRJD_NOMINALAMT,
          TRJD_BASEPRICE,
          TRJD_DIVISIONCODE,
          TRJD_DIVISION,
          TRJD_CUS_KODEMEMBER
        FROM
          (SELECT trjd_recordid,
            trjd_transactiontype,
            trjd_transactiondate,
            trjd_cashierstation,
            trjd_create_by,
            trjd_transactionno,
            trjd_seqno,
            trjd_prdcd,
            trjd_flagtax1,
            trjd_flagtax2,
            trjd_quantity,
            trjd_unitprice,
            trjd_discount,
            trjd_nominalamt,
            trjd_baseprice,
            trjd_divisioncode,
            trjd_division,
            trjd_cus_kodemember
          FROM TBTR_JUALDETAIL
          WHERE
            DATE_TRUNC('day', trjd_transactiondate)=DATE_TRUNC('day', CURRENT_TIMESTAMP(0))
            -- TO_CHAR(trjd_transactiondate,'DDMMYYYY') BETWEEN '15032024' AND '15032024'
          AND trjd_recordid IS NULL
          UNION ALL
          SELECT trjd_recordid,
            trjd_transactiontype,
            trjd_transactiondate,
            trjd_cashierstation,
            trjd_create_by,
            trjd_transactionno,
            trjd_seqno,
            trjd_prdcd,
            trjd_flagtax1,
            trjd_flagtax2,
            trjd_quantity,
            trjd_unitprice,
            trjd_discount,
            trjd_nominalamt,
            trjd_baseprice,
            trjd_divisioncode,
            trjd_division,
            trjd_cus_kodemember
          FROM tbtr_jualdetail_interface
          WHERE
            DATE_TRUNC('day', trjd_transactiondate)=DATE_TRUNC('day', CURRENT_TIMESTAMP(0))
            -- TO_CHAR(trjd_transactiondate,'DDMMYYYY') BETWEEN '15032024' AND '15032024'
          AND TRJD_RECORDID IS NULL
          ) s
        ) sls
      LEFT OUTER JOIN TBMASTER_PRODMAST prd
      ON sls.TRJD_PRDCD = prd.PRD_PRDCD,
        tbmaster_customer cus
      WHERE sls.trjd_cus_kodemember = cus.cus_kodemember
      AND sls.trjd_recordid        IS NULL
      AND sls.trjd_quantity        <> 0
      ) s
    ) s
  GROUP BY dtl_tipemember
  ORDER BY dtl_tipemember
  ) s";
  $stmt2 = $conn->prepare($sql2);
  $stmt2->execute();
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}

?>
<h3><?php echo $title_caption . ' BY MEMBER' ?></h3>
<div class="table-responsive">
  <table align="center" class="table table-bordered table-striped table-hover table-nonfluid bayang">
    <thead>
      <tr class="success">
        <th class="text-center">
          <font size="2">TYPE
        </th>
        <th class="text-center">
          <font size="2">CUSTOMER
        </th>
        <th class="text-center">
          <font size="2">STRUK
        </th>
        <th class="text-center">
          <font size="2">PRODUCT
        </th>
        <th class="text-center">
          <font size="2">GROSS
        </th>
        <th class="text-center">
          <font size="2">NETTO
        </th>
        <th class="text-center">
          <font size="2">HPP
        </th>
        <th class="text-center">
          <font size="2">MARGIN RPH
        </th>
        <th class="text-center">
          <font size="2">MARGIN %
        </th>
        <th class="text-center">
          <font size="2">APC
        </th>
        <th class="text-center">
          <font size="2">APS
        </th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 0;
      $summargin = 0;
      $sumcuss = 0;
      $sumstruck = 0;
      $sumprod = 0;
      $sumgross = 0;
      $sumnetto = 0;
      $sumhpp = 0;
      $sumpersen = 0;
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $no++;
        // variabel total
        $sumcuss += $row['customer'];
        $sumstruck += $row['struk'];
        $sumprod += $row['produk'];
        $sumgross += $row['gross'];
        $sumnetto += $row['netto'];
        $sumhpp += $row['hpp'];
        $summargin += $row['margin'];
        $sumpersen += $row['margin_persen'];
        $persen = $summargin / $sumnetto * 100;
        $totapc = $sumnetto / $sumcuss;
        $totaps = $sumnetto / $sumstruck;
        echo '<tr>';
        if ($row['tipe'] == 'MEMBER MERAH') {
          echo '<td align="left" style="color:red"><font size="2">' . $row['tipe'] . '</td>';
        } elseif ($row['tipe'] == 'END USER') {
          echo '<td align="left" style="color:blue"><font size="2">' . $row['tipe'] . '</td>';
        } else {
          echo '<td align="left"><font size="2">' . $row['tipe'] . '</td>';
        }

        echo
        '<td align="right"><font size="2">' . number_format($row['customer'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['struk'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['produk'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['gross'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['netto'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['hpp'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['margin'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . $row['margin_persen'] . ' %</td>'
          . '<td align="right"><font size="2">' . number_format($row['apc'], 0, '.', ',') . '</td>'
          . '<td align="right"><font size="2">' . number_format($row['aps'], 0, '.', ',') . '</td>';
      }
      ?>
      </tr>
      <tr class="success">
    <tfoot>

      <tr class="active">
        <td align="left">
          <font size="2"><b>TOTAL</b>
        </td>
        <?php
        echo '<td align="right"><font size="2"><b>'  . number_format($sumcuss, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($sumstruck, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($sumprod, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($sumgross, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($sumnetto, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($sumhpp, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format($summargin, 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>' . number_format((float)$persen = isset($persen) ? $persen : '', 2, '.', '.') . ' %<b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format((float)$totapc = isset($totapc) ? $totapc : '', 0, '.', ',') . '</b></td>';
        echo '<td align="right"><font size="2"><b>'  . number_format((float)$totaps = isset($totaps) ? $totaps : '', 0, '.', ',') . '</b></td>';
        ?>

      </tr>

    </tfoot>
    </tr>
    </tbody>
  </table>
</div>