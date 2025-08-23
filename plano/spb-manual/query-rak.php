<?php
	include '../views/view_spb.php';

	$query = "
		SELECT spb_lokasiasal,
		       COUNT(spb_prdcd) AS spb_jumlah
		FROM (
			SELECT REGEXP_REPLACE(spb_lokasiasal, '([^.]+).*', '\\1') AS spb_lokasiasal,
			       spb_prdcd
			FROM {$viewSpb}
			WHERE spb_prdcd IS NOT NULL
			      AND spb_status IN ('0', '3')
			      AND spb_jenis = 'MANUAL'
		) AS subquery
		GROUP BY spb_lokasiasal
		ORDER BY spb_lokasiasal
	";
?>
