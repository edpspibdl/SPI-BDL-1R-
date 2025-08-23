<?php
	include '../views/view_spb.php';
	$query = "  SELECT * 
				FROM   {$viewSpb} 
				WHERE  spb_prdcd is not null
				AND spb_lokasiasal like '%S%'
				AND spb_lokasiasal not like '%C%'
				AND spb_lokasitujuan LIKE '%.S%'
				AND spb_status = '3' ";

	if ($spbLokasi <> 'ALL') {
		$query .= "	AND spb_lokasitujuan like '{$spbLokasi}%' ";
	}

	$query .= "	ORDER BY spb_lokasitujuan ";
	//$query .= "	ORDER BY spb_lokasiasal ";


?>