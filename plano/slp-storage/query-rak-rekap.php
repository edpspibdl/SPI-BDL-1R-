<?php

	$query = "  SELECT substr(slp_koderak,1,1) AS slp_koderak,
				       Count(slp_prdcd) AS slp_jumlah
				FROM   tbtr_slp
				WHERE  slp_flag IS NULL
					   AND slp_tiperak = 'S'
					   AND slp_koderak NOT LIKE '%C'
				GROUP  BY substr(slp_koderak,1,1)
				ORDER  BY slp_koderak ";

?>