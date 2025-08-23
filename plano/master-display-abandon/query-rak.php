<?php

	$query = "  SELECT spb_lokasiasal,
				       Count(spb_prdcd) AS spb_jumlah
				FROM   (SELECT Regexp_substr(spb_lokasiasal, '.[^.]+') AS spb_lokasiasal,
				               spb_prdcd
				        FROM   view_spb
				        WHERE  spb_prdcd IS NOT NULL
				               AND spb_lokasiasal NOT LIKE '%S%'
				               --AND spb_lokasiasal NOT LIKE '%C%'
				               AND spb_status = '0')
				GROUP  BY spb_lokasiasal
				ORDER  BY spb_lokasiasal ";

?>