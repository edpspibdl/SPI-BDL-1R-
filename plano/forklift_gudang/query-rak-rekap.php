<?php
	include '../views/view_spb.php';
	$query = "  SELECT substr(spb_lokasiasal, 1, 1) AS spb_lokasiasal,
				       COUNT(spb_prdcd) AS spb_jumlah
				FROM   (SELECT regexp_matches(spb_lokasiasal, '[^.]+') AS match_array,
				               spb_prdcd
				        FROM   {$viewSpb}
				        WHERE  spb_prdcd IS NOT NULL
				               AND spb_lokasiasal LIKE '%S%'
				               AND spb_lokasiasal NOT LIKE '%C%'
				               AND spb_lokasitujuan LIKE 'D%'
				               AND spb_status = '0') subquery,
				       LATERAL (SELECT match_array[1] AS spb_lokasiasal) sub_match
				GROUP  BY substr(spb_lokasiasal, 1, 1)
				ORDER  BY spb_lokasiasal ";
?>
