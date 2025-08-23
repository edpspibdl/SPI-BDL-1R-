<?php
	include '../views/view_spb.php';
	$query = "  SELECT substr(spb_lokasiasal,1,1) AS spb_lokasiasal,
				       Count(spb_prdcd) AS spb_jumlah
				FROM   (SELECT Regexp_substr(spb_lokasiasal, '.[^.]+') AS spb_lokasiasal,
				               spb_prdcd
				        FROM   {$viewSpb}
				        WHERE  spb_prdcd IS NOT NULL
				               AND spb_lokasiasal LIKE '%S%'
				               AND spb_lokasiasal NOT LIKE '%C%'
							   AND (spb_lokasitujuan  like 'R%' or spb_lokasitujuan  like 'O%' )
				               AND spb_status = '0')
				GROUP  BY substr(spb_lokasiasal,1,1)
				ORDER  BY spb_lokasiasal ";

?>