<?php

	$query = "  SELECT lks_koderak,
				  COUNT(distinct(lks_kodesubrak)) AS lks_kodesubrak
				FROM tbmaster_lokasi
				WHERE SUBSTR(lks_koderak,1,1) IN ('R','O')
				AND lks_prdcd is not null
				GROUP BY lks_koderak
				ORDER BY lks_koderak ";

?>