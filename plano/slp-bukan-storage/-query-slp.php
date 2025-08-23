<?php
	$query = "SELECT * 
			  FROM view_slp_waktu 
			  WHERE slp_tanggal = TRUNC(sysdate) ";

	// Create connection to Oracle	
	include '../_/connection.php';
	$stid = oci_parse($conn, $query);
	$r = oci_execute($stid);
?>