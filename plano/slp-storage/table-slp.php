<div class="list-group bayang">

  <?php
  	//echo '<p>- - - Status SLP</p>';
	// Fetch each row in an associative array
	 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		
		//print '<tr>';

		
		if ($row['slp_progress'] == 'R') {							
			echo '<a href="../slp-detail/index.php?slpStatus=" 
					class="list-group-item">
					Belum Realisasi
  					<span class="badge">' . $row['slp_jumlah'] . '</span>
  				 </a>';
		} elseif ($row['slp_progress'] == 'P') {							
			echo '<a href="../slp-detail/index.php?slpStatus=P" 
				    class="list-group-item">
					Selesai
  					<span class="badge">' . $row['slp_jumlah'] . '</span>
  				 </a>';
		} else {
			echo '<a href="../slp-detail/index.php?slpStatus=C" 
					class="list-group-item">
					Batal
  					<span class="badge">' . $row['slp_jumlah'] . '</span>
  				 </a>';
		}

	} //end while
	   
  ?>

</div><!-- list item -->