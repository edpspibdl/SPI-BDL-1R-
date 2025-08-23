<?php
	include '../views/view_spb.php';

	//ambil huruf pertama dari lokasi 
	// misal D, R, O atau G jadi bukan D01
	$spbLokasiRak = SUBSTR($spbLokasi,0,1);

	$query = "  SELECT spb_lokasiasal,
				       Count(spb_prdcd) AS spb_jumlah
				FROM   (SELECT Regexp_substr(spb_lokasiasal, '.[^.]+') AS spb_lokasiasal,
				               spb_prdcd
				        FROM   {$viewSpb}
				        WHERE  spb_prdcd IS NOT NULL
				               AND spb_lokasiasal LIKE '%S%'
				               AND spb_lokasiasal NOT LIKE '%C%'
							   AND (spb_lokasitujuan  like 'D%')
				               AND spb_status = '0') ";
	if ($spbLokasi <> 'ALL') {
		
		
		$query .= "	WHERE spb_lokasiasal like '{$spbLokasiRak}%' ";
	}

	$query .= "	GROUP  BY spb_lokasiasal
				ORDER  BY spb_lokasiasal ";


?>