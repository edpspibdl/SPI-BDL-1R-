<span class="badge"><?php echo date("l, j F Y"); ?></span>



          
    
	
      
    


	    
	    <div class="table-responsive table table-bordered table-hover">
            <table class="table">
            	

				<thead>

					<tr class="active">
						<th class="text-center">Status</th>
						<th class="text-center">Toko</th>
						<th class="text-center">Gudang</th>
						<th class="text-center">Manual</th>
				  	</tr>

					
				</thead> 
				
				<tbody>
				  <?php
				  	

				  	
					// Fetch each row in an associative array
					while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
						
						print '<tr>';

						
						
						$link = '9';

						if ($row['SPB_PROGRESS'] == '0 belum diturunkan') {	
							$textButton = 'Belum diturunkan';
							$link = '0';
						} elseif ($row['SPB_PROGRESS'] == '1 sudah turun tapi belum realisasi') {
							$textButton = 'Sudah turun <br>tapi belum realisasi';
							$link = '3';
						} elseif ($row['SPB_PROGRESS'] == '2 sudah realisasi') {
							$textButton = 'Sudah realisasi';
							$link = '1';
						} elseif ($row['SPB_PROGRESS'] == '4 batal') {
							$textButton = 'Batal';
							$link = '2';
						} else {
							$textButton = 'nggak ngerti';
						}


						echo '<td align="left">' . $textButton . '</td>';
						/*
						$statusSLP = '<a href="#" class="btn btn-warning btn-lg btn-block" role="button">Belum di turunkan</a>';
						*/
						//$statusSLP = '<a href="#" class="btn btn-default btn-block" role="button">' . $textButton . $row['AUTO_TOKO'] . '</a>';
						$linkSPB = '../spb-detail/index.php?jenisSPB=T&statusSPB=' . $link;
						$statusSPB = '<a href="' . $linkSPB . '" class="btn btn-block btn-primary" role="button">'  . $row['AUTO_TOKO'] . '</span>' . '</a>';
						echo '<td align="center">' . $statusSPB . '</td>';

						$linkSPB = '../spb-detail/index.php?jenisSPB=G&statusSPB=' . $link;
						$statusSPB = '<a href="' . $linkSPB . '" class="btn btn-block btn-primary" role="button">'   . $row['AUTO_GUDANG'] . '</span>' . '</a>';
						echo '<td align="center">' . $statusSPB . '</td>';

						
						$linkSPB = '../spb-detail/index.php?jenisSPB=M&statusSPB=' . $link;
						$statusSPB = '<a href="' . $linkSPB . '" class="btn btn-block btn-primary" role="button">'  . $row['MANUAL'] . '</span>' . '</a>';
						echo '<td align="center">' . $statusSPB . '</td>';

						
						


					} //end while
					   
					print '</tr>';
					  
					  // hitung total nilai disini

				  ?>
				</tbody>

            </table>
      	</div><!-- close table-responsive -->