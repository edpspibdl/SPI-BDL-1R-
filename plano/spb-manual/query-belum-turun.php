<?php
	
	include '../views/view_spb.php';
	
	$query = "  SELECT * 
				FROM   {$viewSpb} 
				WHERE  spb_prdcd is not null
					AND spb_status = '0' 
					AND spb_jenis = 'MANUAL' ";

	if ($spbLokasi <> 'ALL') {
		$query .= "	AND spb_lokasiasal like '{$spbLokasi}%' ";
	}

	$query .= "	ORDER BY spb_status, spb_lokasiasal ";


?>