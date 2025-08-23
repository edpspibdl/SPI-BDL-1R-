<?php
	include '../views/view_spb.php';

	$spbLokasiRak = substr($spbLokasi, 0, 1);

	$query = "  SELECT substr(spb_lokasiasal, 1, 1) AS spb_lokasiasal,
				       COUNT(spb_prdcd) AS spb_jumlah
				FROM   {$viewSpb}
				WHERE  spb_prdcd IS NOT NULL
				       AND spb_lokasiasal LIKE '%S%'
				       AND spb_lokasiasal NOT LIKE '%C%'
				       AND spb_lokasitujuan LIKE 'D%'
				       AND spb_status = '0' ";

	if ($spbLokasi != 'ALL') {
		$query .= "	AND spb_lokasiasal LIKE '{$spbLokasiRak}%' ";
	}

	$query .= "	GROUP  BY substr(spb_lokasiasal, 1, 1)
				ORDER  BY spb_lokasiasal ";

?>
