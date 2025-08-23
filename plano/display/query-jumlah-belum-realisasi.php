<?php
	
	$query = "  SELECT Count(spb_prdcd) AS spb_jumlah
		        FROM   {$viewSpb}
		        WHERE  spb_prdcd IS NOT NULL
		               AND spb_lokasiasal NOT LIKE '%S%'
		               AND spb_status = '3' ";


?>

<?php  
	
	  include '../_/connection.php';
 	$stmt = $conn->prepare($query);
	 $stmt->execute();

	// buat variable untuk menampung jumlah spb yang sudah diturunkan tapi belum realisasi
	$jumlahSpbBelumRealisasi = 0;

	   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$jumlahSpbBelumRealisasi = $row['spb_jumlah'];
	} 
?>