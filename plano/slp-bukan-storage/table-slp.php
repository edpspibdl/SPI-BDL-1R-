<div class="list-group bayang">

  <?php
  	//echo '<p>- - - Status SLP</p>';
	// Fetch each row in an associative array
	while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
		
		//print '<tr>';

		
		if ($row['SLP_PROGRESS'] == 'R') {							
			echo '<a href="../slp-detail/index.php?slpStatus=" 
					class="list-group-item">
					Belum Realisasi
  					<span class="badge">' . $row['SLP_JUMLAH'] . '</span>
  				 </a>';
		} elseif ($row['SLP_PROGRESS'] == 'P') {							
			echo '<a href="../slp-detail/index.php?slpStatus=P" 
				    class="list-group-item">
					Selesai
  					<span class="badge">' . $row['SLP_JUMLAH'] . '</span>
  				 </a>';
		} else {
			echo '<a href="../slp-detail/index.php?slpStatus=C" 
					class="list-group-item">
					Batal
  					<span class="badge">' . $row['SLP_JUMLAH'] . '</span>
  				 </a>';
		}

	} //end while
	   
  ?>

</div><!-- list item -->