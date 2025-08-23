<?php
	$query = "SELECT * 
			  FROM view_slp_waktu 
			  WHERE slp_tanggal = TRUNC(sysdate) ";

	// Create connection to Oracle	
include '../_/connection.php';
 	$stmt = $conn->prepare($query);
	 $stmt->execute();
?>