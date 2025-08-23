<?php
	
	$query = "  SELECT Count(slp_prdcd) AS slp_jumlah
				FROM   tbtr_slp
				WHERE  slp_flag IS NULL
					   AND slp_tiperak = 'S' 
					   AND slp_koderak NOT LIKE '%C' ";


?>

<?php  
	
	include '../_/connection.php';
 	$stmt = $conn->prepare($query);
	 $stmt->execute();

	// buat variable untuk menampung jumlah spb yang sudah diturunkan tapi belum realisasi
	$jumlahSlpStorage = 0;

	 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$jumlahSlpStorage = $row['slp_jumlah'];
	} 
?>