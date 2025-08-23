<div class="list-group">

  <?php

	// Fetch each row in an associative array
	while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {

		$statusSPB = substr($row['SPB_PROGRESS'],0,1);
		$statusKode = '9';

		switch ($statusSPB) {
		    case "0":
		        $status = "Belum diturunkan";
		        $statusKode = '0';
		        break;
		    case "1":
		        $status = "Sudah diturunkan tapi belum realisasi";
		        $statusKode = '3';
		        break;
		    case "2":
		        $status = "Sudah realisasi";
		        $statusKode = '1';
		        break;
		    default:
		        $status = "Batal";
		        $statusKode = '2';
		}
	
		echo '<div class="panel panel-primary">';
		echo '  <div class="panel-heading">';
		echo '    <h3 class="panel-title">' . $status .  '</h3>';
		echo '  </div>';

		echo '<a href="../spb-detail/index.php?jenisSPB=T&statusSPB=' . $statusKode . '" 
					class="list-group-item">
					Toko
  					<span class="badge">' . $row['AUTO_TOKO'] . '</span>
  				 </a>';

  		echo '<a href="../spb-detail/index.php?jenisSPB=G&statusSPB=' . $statusKode . '" 
					class="list-group-item">
					Gudang
  					<span class="badge">' . $row['AUTO_GUDANG'] . '</span>
  				 </a>';

  		echo '<a href="../spb-detail/index.php?jenisSPB=M&statusSPB=' . $statusKode . '" 
					class="list-group-item">
					Manual
  					<span class="badge">' . $row['MANUAL'] . '</span>
  				 </a>';



		
		echo '</div>'; // close panel
		
	

	} //end while
	
  ?>

</div><!-- list item -->