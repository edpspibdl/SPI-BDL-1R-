<?php
	include '../views/view_spb.php';

	//ambil huruf pertama dari lokasi 
	// misal D, R, O atau G jadi bukan D01
	$spbLokasiRak = SUBSTR($spbLokasi,0,1);

	$query = " SELECT spb_lokasiasal,
		       COUNT(spb_prdcd) AS spb_jumlah
		FROM (
			SELECT REGEXP_REPLACE(spb_lokasiasal, '([^.]+).*', '\\1') AS spb_lokasiasal,
			       spb_prdcd
			FROM {$viewSpb}
			WHERE spb_prdcd IS NOT NULL
			      AND spb_lokasiasal LIKE '%S%'
			      AND spb_lokasitujuan LIKE '%S%'
			      AND spb_status = '0'
		) AS subquery ";
	if ($spbLokasi <> 'ALL') {
		
		
		$query .= "	WHERE spb_lokasiasal like '{$spbLokasiRak}%' ";
	}

	$query .= "	GROUP  BY spb_lokasiasal
				ORDER  BY spb_lokasiasal ";


?>