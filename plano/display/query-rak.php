<?php
	$query = "
		SELECT spb_lokasiasal,
		       COUNT(spb_prdcd) AS spb_jumlah
		FROM (
			SELECT REGEXP_REPLACE(spb_lokasiasal, '([^.]+).*', '\\1') AS spb_lokasiasal,
			       spb_prdcd
			FROM {$viewSpb}
			WHERE spb_prdcd IS NOT NULL
			      AND spb_lokasiasal NOT LIKE '%S%'
			      AND spb_status = '0'
		) AS subquery
		GROUP BY spb_lokasiasal
		ORDER BY spb_lokasiasal
	";
?>
