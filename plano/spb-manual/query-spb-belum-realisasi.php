<?php
	
	include '../views/view_spb.php';

	$query = "  SELECT Count(spb_prdcd) AS spb_belum_realisasi
				FROM   {$viewSpb}
				WHERE  spb_prdcd IS NOT NULL
				       AND spb_lokasiasal LIKE '%S%'
				       AND spb_lokasiasal NOT LIKE '%C%'
				       AND spb_status = '3' ";
	/* 
	spb_status
		null: kondisi awal
		1: sudah realisasi
		2: batal
		3: sudah diturunkan tapi belum realisasi
	*/
	/*
	untuk spb dari display omi ke display toko masih belum bisa langsung realisasi,
	harus penurunan dulu.
	Program lagi direvisi di HO.
	*/

?>

<?php  
	
	require '../_/connection.php';
	$stid = oci_parse($conn, $query);
	oci_execute($stid);

	// buat variable untuk menampung jumlah spb yang sudah diturunkan tapi belum realisasi
	$spbBelumRealisasi = 0;

	while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
		$spbBelumRealisasi = $row['SPB_BELUM_REALISASI'];
	} 
?>