<?php
$sql = "select * from(
SELECT
    js_cashierid,
    js_cashierstation,
    js_cashierid||'-'||js_cashierstation PK_JH,
    CASE when js_cashdrawerend IS NULL and js_cashierid NOT IN ('IDM','OMI','BKL','ONL') THEN 'OPEN'
        ELSE 'CLOSING'
    END STATUS
FROM
    tbtr_jualsummary
WHERE
    date_trunc('day', js_transactiondate) = date_trunc('day', current_timestamp(0))
) a2
left join (SELECT 
STAT,
SLS.KASIR,
SLS.CUSTOMER,
SLS.STRUK,
SLS.PRODUK,
SLS.NETTO,
SLS.HPP
FROM
(SELECT DTL_STAT                 AS STAT,
DTL_KASIR                      AS KASIR,
COUNT(DISTINCT(DTL_CUSNO))     AS CUSTOMER,
COUNT(DISTINCT(DTL_STRUK))     AS STRUK,
COUNT(DISTINCT(DTL_PRDCD_CTN)) AS PRODUK,
TRUNC(SUM(DTL_NETTO))          AS NETTO,
TRUNC(SUM(DTL_HPP))            AS HPP
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
sls.TRJD_FLAGTAX2                AS dtl_bkp2,
sls.TRJD_QUANTITY * prd.PRD_FRAC AS dtl_qty_pcs,
sls.TRJD_QUANTITY                AS dtl_qty,
sls.TRJD_UNITPRICE               AS dtl_harga_jual,
sls.TRJD_DISCOUNT                AS dtl_diskon,
CASE
when SLS.TRJD_FLAGTAX1  ='Y' AND SLS.TRJD_FLAGTAX2  ='Y'
AND sls.trjd_create_by IN('IDM','OMI','BKL')
THEN sls.TRJD_NOMINALAMT*1.11
ELSE sls.TRJD_NOMINALAMT
END dtl_gross,
CASE
when SLS.TRJD_FLAGTAX1      ='Y' AND SLS.TRJD_FLAGTAX2  ='Y'
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
cus.cus_kodesuboutlet            AS dtl_suboutlet
FROM (SELECT DISTINCT TRJD_RECORDID,TRJD_TRANSACTIONTYPE,TRJD_TRANSACTIONDATE,TRJD_CASHIERSTATION,TRJD_CREATE_BY,TRJD_TRANSACTIONNO,TRJD_SEQNO,TRJD_PRDCD,TRJD_FLAGTAX1,TRJD_FLAGTAX2,TRJD_QUANTITY,TRJD_UNITPRICE,TRJD_DISCOUNT,TRJD_NOMINALAMT,TRJD_BASEPRICE,TRJD_DIVISIONCODE,TRJD_DIVISION,TRJD_CUS_KODEMEMBER
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
from TBTR_JUALDETAIL
WHERE 
DATE_TRUNC('day', trjd_transactiondate)=DATE_TRUNC('day', CURRENT_TIMESTAMP(0))
-- TO_CHAR(trjd_transactiondate,'DDMMYYYY') BETWEEN '15032024' AND '15032024'
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
and TRJD_RECORDID               is null
) s) sls LEFT OUTER JOIN
TBMASTER_PRODMAST prd ON sls.TRJD_PRDCD        = prd.PRD_PRDCD,
tbmaster_customer cus
WHERE
 sls.trjd_cus_kodemember = cus.cus_kodemember
AND sls.trjd_recordid      IS NULL
AND sls.trjd_quantity      <> 0
) s) s
GROUP BY DTL_STAT,
DTL_KASIR
ORDER BY DTL_STAT,
DTL_KASIR
) SLS 
) a1 on js_cashierid = KASIR  and js_cashierstation = STAT
join tbmaster_user on js_cashierid = userid
		LEFT JOIN  (SELECT dpp_create_by, SUBSTRING(dpp_stationkasir FROM 1 FOR 2) AS stat, SUM(dpp_jumlahdeposit) AS ppob 
                     FROM tbtr_deposit_mitraigr
                     WHERE DATE(dpp_create_dt) = CURRENT_DATE
                     GROUP BY dpp_create_by, SUBSTRING(dpp_stationkasir FROM 1 FOR 2)) AS deposit_data 
                    ON js_cashierid = dpp_create_by AND js_cashierstation = js_cashierstation
order by status desc
"; 
include '../helper/connection.php'; 
$stmt = $conn->query($sql);					


?>
<a href="sales.php"><h3><?php echo $title_caption.' BY KASIR' ?></font></h3></a>
<div class="table-responsive">
<table align="center" class="table table-bordered table-striped table-hover  table-nonfluid ">
<thead>
<tr  class="success">
	<th class="text-center"><font size="2">STATION</th>
	<th class="text-center"><font size="2">KASIR</th>
	<th class="text-center"><font size="2">USER</th>
	<th class="text-center"><font size="2">CUSTOMER</th>
	<th class="text-center"><font size="2">STRUK</th>
	<th class="text-center"><font size="2">PRODUCT</th>
	<th class="text-center"><font size="2">NETTO</th>
	<th class="text-center"><font size="2">HPP</th>
	<th class="text-center"><font size="2">PPOB</th>
	<th class="text-center"><font size="2">STATUS</th>
</tr>
</thead>
<tbody>
	<?php
		$no = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$no++;
			if($row['status'] == 'CLOSING') {
				echo "<tr style='color:#c0c0c0'>";
			}else{
				echo "<tr>";
			}

			echo '<td><font size="2">'.$row['js_cashierstation'].'</td>'
				.'<td><font size="2">'.$row['js_cashierid'].'</td>'
				.'<td align="left"><font size="2">'.$row['username'].'</td>'
				.'<td><font size="2">'.$row['customer'].'</td>'
				.'<td><font size="2">'.$row['struk'].'</td>'
				.'<td><font size="2">'.number_format($row['produk'], 0, '.', ',').'</td>'
				.'<td align="right"><font size="2">'.number_format($row['netto'], 0, '.', ',').'</td>'
				.'<td align="right"><font size="2">'.number_format($row['hpp'], 0, '.', ',').'</td>'
				.'<td align="right"><font size="2">'.number_format($row['ppob'], 0, '.', ',').'</td>'
				.'<td><font size="2">'.$row['status'].'</td>';
				
/*				if($row['STATUS'] == 'OPEN') {
					echo '<td align="left" style="background:#ff6666">'.$row['STATUS'].'</td>';
				}else{
					echo '<td>'.$row['STATUS'].'</td>';
				}*/
			echo '</tr>';
		}
	?>
</tr>
</tbody>
</table>
</div>