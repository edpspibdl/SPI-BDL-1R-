<?php

	include '../views/view_spb.php';

	$query = "  SELECT * 
				FROM   {$viewSpb} 
				WHERE  spb_prdcd is not null
				AND spb_lokasiasal like '%S%'
				AND spb_lokasiasal like '%C%'
				AND spb_status = '0' ";

	if ($spbLokasi <> 'ALL') {
		$query .= "	AND spb_lokasiasal like '{$spbLokasi}%' ";
	}

	$query .= "	ORDER BY spb_lokasiasal ";


?>