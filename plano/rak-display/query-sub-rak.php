<?php
	
	// include '../views/view_spb.php';
	$query = "  SELECT lks_kodesubrak               AS lks_sub_rak,
				       Count(DISTINCT( lks_tiperak || lks_shelvingrak )) AS lks_shelving
				FROM   tbmaster_lokasi
				WHERE  Substr(lks_tiperak,1,1) IN ( 'B', 'I', 'N' )
				       AND Substr(lks_koderak, 1, 1) NOT IN ( 'D', 'G' )
				       AND lks_koderak = '{$kodeRak}'
				GROUP  BY lks_kodesubrak
				ORDER  BY lks_sub_rak ";


?>