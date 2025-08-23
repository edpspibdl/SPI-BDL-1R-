<?php
	include '../views/view_spb.php';
	$query = "  SELECT LEFT(spb_lokasitujuan, 1) AS spb_lokasitujuan,
		       COUNT(spb_prdcd) AS spb_jumlah
		FROM (
			SELECT REGEXP_REPLACE(spb_lokasitujuan, '([^.]+).*', '\\1') AS spb_lokasitujuan,
			       spb_prdcd
			FROM {$viewSpb}
			WHERE spb_prdcd IS NOT NULL
			      AND spb_lokasiasal LIKE '%S%'
			      AND spb_lokasiasal NOT LIKE '%C%'
			      AND spb_lokasitujuan LIKE '%.S%'
			      AND spb_status = '3'
		) AS subquery
		GROUP BY LEFT(spb_lokasitujuan, 1)
		ORDER BY spb_lokasitujuan ";

?>