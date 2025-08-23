<?php
	include '../views/view_spb.php';

	$query = "
		SELECT LEFT(spb_lokasiasal, 1) AS spb_lokasiasal,
		       COUNT(spb_prdcd) AS spb_jumlah
		FROM (
			SELECT REGEXP_REPLACE(spb_lokasiasal, '([^.]+).*', '\\1') AS spb_lokasiasal,
			       spb_prdcd
			FROM {$viewSpb}
			WHERE spb_prdcd IS NOT NULL
			      AND spb_lokasiasal LIKE '%S%'
			      AND spb_lokasitujuan LIKE '%S%'
			      AND spb_status = '0'
		) AS subquery
		GROUP BY LEFT(spb_lokasiasal, 1)
		ORDER BY spb_lokasiasal
	";
?>
