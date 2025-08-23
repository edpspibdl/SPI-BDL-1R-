<?php
	include '../views/view_spb.php';
	$query = "  SELECT Count(spb_prdcd) AS spb_belum_turun
				FROM   {$viewSpb}
				WHERE  spb_prdcd IS NOT NULL
				       AND spb_lokasiasal LIKE '%S%'
				       AND spb_lokasiasal NOT LIKE '%C%'
				       AND spb_status = '0' ";
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
	
	include '../_/connection.php';
 $stmt = $conn->prepare($query);
  $stmt->execute();

	// buat variable untuk menampung jumlah spb yang sudah diturunkan tapi belum realisasi
	$spbBelumTurun = 0;

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$spbBelumTurun = $row['spb_belum_turun'];
	} 
?>