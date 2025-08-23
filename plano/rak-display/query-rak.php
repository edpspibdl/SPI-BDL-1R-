<?php
	// include '../views/view_spb.php';
	
	$query = "  SELECT lks_koderak                       AS LKS_RAK,
				       Count(DISTINCT( lks_kodesubrak )) AS LKS_SUB_RAK
				FROM   tbmaster_lokasi
				WHERE  Substr(lks_tiperak,1,1) IN ( 'B', 'I', 'N' )
				       AND Substr(lks_koderak, 1, 1) NOT IN ( 'D', 'G' )
				GROUP  BY lks_koderak
				ORDER  BY lks_rak ";

?>