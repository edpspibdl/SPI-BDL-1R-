<?php
	
	
	$query = "  SELECT * 
				FROM   {$viewSpb} 
				WHERE  spb_prdcd is not null
				AND spb_lokasiasal NOT like '%S%'
				AND spb_status = '3' ";

	if ($spbLokasi <> 'ALL') {
		$query .= "	AND spb_lokasiasal like '{$spbLokasi}%' ";
	}

	$query .= "	ORDER BY spb_lokasiasal ";


?>