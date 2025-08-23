
<span class="badge"><?php echo date("l, j F Y"); ?></span> 
	    <div class="table-responsive">
            <table class="table">
				<thead>
					<tr class="active">
						<!-- <th class="text-center">Tanggal</th> -->
						<th class="text-center">Status</th>
						<th class="text-right">Produk</th>
						<th class="text-right">SLP</th>
						<th colspan = '2' class="text-right">Rata-rata waktu<br>penyelesaian per SLP</th>
				  	</tr>
				</thead> 
				
				<tbody>
				  <?php
				  	

				  	
					// Fetch each row in an associative array
					 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						
						print '<tr>';
						
						if ($row['SLP_PROGRESS'] == 'R') {
							//$statusSLP = '<button type="button" class="btn btn-success btn-block">Belum di Realisasi</button>';
							$statusSLP = '<a href="../slp-detail/index.php?slpStatus=" class="btn btn-warning btn-lg btn-block" role="button">Belum di Realisasi</a>';

						} elseif ($row['SLP_PROGRESS'] == 'P') {
							$statusSLP = '<a href="../slp-detail/index.php?slpStatus=P" class="btn btn-default btn-block" role="button">Selesai</a>';
						} else {
							$statusSLP = '<a href="../slp-detail/index.php?slpStatus=C" class="btn btn-default btn-block" role="button">Batal</a>';
						}
						//echo '<td align="center">' . $row['SLP_TANGGAL'] . '</td>';
						echo '<td align="center">' . $statusSLP . '</td>';
						
						echo '<td align="right">'  . number_format($row['SLP_ITEM'], 0, '.', ',') . '</td>';
						echo '<td align="right">'  . number_format($row['SLP_JUMLAH'], 0, '.', ',') . '</td>';
						//echo '<td align="right">'  . number_format((int) ($row['SLP_WAKTU'] / 60), 0, '.', ',') . '</td>';
						echo '<td align="right"  class="text-nowrap"> '  
						. (int) ($row['SLP_WAKTU'] / 60)
						. ' <small><em> jam &nbsp;  &nbsp;  </em></small>' 
						
						//. '</td>';

						//echo '<td align="right"  class="text-nowrap"> '  
						
						. $row['SLP_WAKTU'] % 60 
						. ' <small><em> menit</em></small>' 
						. '</td>';

						
						

					} //end while
					   
					print '</tr>';
					  
					  // hitung total nilai disini

				  ?>
				</tbody>

            </table>
      	</div><!-- close table-responsive -->

      	<?php //include "tabel-slp-sebelumnya.php"; ?>
	  







      	


    