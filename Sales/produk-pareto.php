<?php
$sql = "select * from (SELECT dtl_prdcd_ctn AS PLU,
  dtl_nama_barang    AS DESKRIPSI,
  COUNT(distinct(dtl_cusno))   AS JMLMEMBER,
  SUM(dtl_netto)RUPIAH,
  SUM(dtl_margin)RPH_MARGIN
FROM (SELECT dtl_rtype,
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
  WHERE DATE_TRUNC('day', trjd_transactiondate)=current_date
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
  WHERE DATE_TRUNC('day', trjd_transactiondate)=current_date
  and TRJD_RECORDID               is null
  )s) sls left join 
      TBMASTER_PRODMAST prd on sls.TRJD_PRDCD        = prd.PRD_PRDCD left join
      tbmaster_customer cus on 

    sls.trjd_cus_kodemember = cus.cus_kodemember
    where sls.trjd_recordid      IS NULL
    AND sls.trjd_quantity      <> 0
    )
  s)sls  group by dtl_nama_barang,dtl_prdcd_ctn order by 3 desc )sls  limit  10
"; 
include '../helper/connection.php'; 
$stmt = $conn->query($sql);				


?>
<a></a><h3><?php echo $title_caption.' PRODUCT PARETO' ?></font></h3></a>
<div class="table-responsive">
<table align="center" class="table table-bordered table-striped table-hover table-nonfluid" style="border: 1px solid black;">
<thead>
<tr class="success" style="color: black;"> <!-- Mengubah warna biru mencolok menjadi lebih lembut -->
	<th class="text-center" style="padding: 10px;"><font size="2">PLU</font></th>
	<th class="text-center" style="padding: 10px;"><font size="2">DESKRIPSI</font></th>
	<th class="text-center" style="padding: 10px;"><font size="2">JML MEMBER</font></th>
	<th class="text-center" style="padding: 10px;"><font size="2">RUPIAH</font></th>
	<th class="text-center" style="padding: 10px;"><font size="2">RPH MARGIN</font></th>
</tr>
</thead>
<tbody>
	<?php
			   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				echo '<tr>'
					.'<td style="padding: 8px;"><font size="2">'.$row['plu'].'</font></td>'
					.'<td style="padding: 8px;"><font size="2">'.$row['deskripsi'].'</font></td>'
					.'<td align="left" style="padding: 8px;"><font size="2">'.$row['jmlmember'].'</font></td>'
					.'<td align="right" style="padding: 8px;"><font size="2">'.number_format($row['rupiah'], 0, '.', ',').'</font></td>'
					.'<td align="right" style="padding: 8px;"><font size="2">'.number_format($row['rph_margin'], 0, '.', ',').'</font></td>'
					.'</tr>';
			}
			if (!$stmt) {
        die("Error executing query: " . $conn);
    }
    
	?>
</tbody>
</table>
</div>
</div>