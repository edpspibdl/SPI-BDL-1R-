
<?php
// slp belum di-realisasi

	$query = "	SELECT * 
				FROM   view_spb
				WHERE spb_prdcd is not null ";

	$query .= " AND spb_status = {$statusSPB}";

	// tambahkan filter jenis spb
	// M:manual T:toko G:gudang

	switch ($jenisSPB) {

		case "T":
		  $query .= " AND spb_jenis = 'TOKO AUTO' " ;
		  $judulPanel  = "Jenis : TOKO<br>";
		  break;
		case "G":
		  $query .= " AND spb_jenis = 'GUDANG AUTO' " ;
		  $judulPanel  = "Jenis : GUDANG<br>";
		  break;
		case "M":
		  $query .= " AND spb_jenis = 'MANUAL' " ;
		  $judulPanel  = "Jenis : MANUAL<br>";
		  break;
	}

		switch ($statusSPB) {

		case "0":
		  $judulPanel  .= "Status : Belum diturunkan";
		  break;
		case "1":
		  $judulPanel  .= "Status : Sudah realisasi";
		  break;
		case "2":
		  $judulPanel  .= "Status : Batal";
		  break;
		case "3":
		  $judulPanel  .= "Status : Sudah diturunkan tapi belum realisasi";
		  break;
	}


?>