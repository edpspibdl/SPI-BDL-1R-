<?php

	$query = "  SELECT   *
				FROM     (
			                SELECT
			                       CASE Nvl(spb.spb_recordid,'0')
			                              WHEN '0' THEN '0 belum diturunkan'
			                              WHEN '3' THEN '1 sudah turun tapi belum realisasi'
			                              WHEN '1' THEN '2 sudah realisasi'
			                              WHEN '2' THEN '4 batal'
			                              ELSE '5 nggak ngerti'
			                       END AS spb_progress,
			                       CASE
			                              WHEN spb.spb_jenis = 'MANUAL' THEN 'MANUAL'
			                              WHEN spb.spb_jenis = 'OTOMATIS'
			                              AND    Substr(spb.spb_lokasitujuan,1,1) IN ('G',
			                                                                          'D') THEN 'AUTO GUDANG'
			                              ELSE 'AUTO TOKO'
			                       END AS spb_jenis,
			                       spb.spb_prdcd
			                FROM   tbtemp_antrianspb spb ) pivot ( count(spb_prdcd) FOR spb_jenis IN ('AUTO TOKO'   AS auto_toko,
			                                                                                          'AUTO GUDANG' AS auto_gudang,
			                                                                                          'MANUAL'      AS manual) )
				ORDER BY spb_progress ";


	// Create connection to Oracle
	
	include '../_/connection.php';
	$stid = oci_parse($conn, $query);
	$r = oci_execute($stid);

?>