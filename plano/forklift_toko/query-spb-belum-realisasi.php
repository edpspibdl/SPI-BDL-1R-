<?php
	include '../views/view_spb.php';
	$query = "  SELECT Count(spb_prdcd) AS spb_belum_realisasi
				FROM   {$viewSpb} 
				WHERE  spb_prdcd IS NOT NULL
				       AND spb_lokasiasal LIKE '%S%'
				       AND spb_lokasiasal NOT LIKE '%C%'
					   AND (spb_lokasitujuan  like 'R%' or spb_lokasitujuan  like 'O%' )
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
	
	  include '../_/connection.php';
 $stmt = $conn->prepare($query);
   $stmt->execute();

	// buat variable untuk menampung jumlah spb yang sudah diturunkan tapi belum realisasi
	$spbBelumRealisasi = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$spbBelumRealisasi = $row['spb_belum_realisasi'];
	} 
?>