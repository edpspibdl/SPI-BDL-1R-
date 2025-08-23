<?php
	include '../views/view_spb.php';
	$query = "  SELECT substr(spb_lokasitujuan, 1, 1) AS spb_lokasitujuan,
				       COUNT(spb_prdcd) AS spb_jumlah
				FROM   (SELECT regexp_matches(spb_lokasitujuan, '[^.]+') AS match_array,
				               spb_prdcd
				        FROM   {$viewSpb}
				        WHERE  spb_prdcd IS NOT NULL
				               AND spb_lokasiasal LIKE '%S%'
				               AND spb_lokasiasal NOT LIKE '%C%'
				               AND spb_status = '3') subquery,
				       LATERAL (SELECT match_array[1] AS spb_lokasitujuan) sub_match
				GROUP  BY substr(spb_lokasitujuan, 1, 1)
				ORDER  BY spb_lokasitujuan ";
?>
