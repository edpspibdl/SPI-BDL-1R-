<?php
	//ambil huruf pertama dari lokasi 
	// misal D, R, O atau G jadi bukan D01
	$slpLokasiRak = SUBSTR($slpLokasi,0,1);

	$query = "  SELECT slp_koderak,
				       Count(slp_prdcd) AS slp_jumlah
				FROM   tbtr_slp
				WHERE  slp_flag IS NULL
					   AND 
					      (slp_tiperak <> 'S' OR (slp_tiperak = 'S' AND slp_koderak LIKE '%C')) ";
	if ($slpLokasi <> 'All') {
		$query .= "	AND slp_koderak LIKE '{$slpLokasiRak}%' ";
	}					     
	
	$query .= "	GROUP  BY slp_koderak
				ORDER  BY slp_koderak ";

?>