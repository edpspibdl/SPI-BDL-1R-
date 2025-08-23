<?php
  
  //include 'query-forklift.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
  $stid = oci_parse($conn, $query);
  $r = oci_execute($stid);
?>
  
  


  

  <div class="panel bayang">
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
    <!--
      <thead>
        <tr  class="info">
          <th>#</th>
          <th>PLU</th>
          <th>Nama Barang</th>
          <th>Frac</th>
          <th>Unit</th>
          <th>Tag</th>
          <th>KK</th>
          <th>DB</th>
		  <th>AB</th>
          <th>Disp</th>
          <th>Max Disp</th>
          <th>Qty</th>
          <th>Exp Date</th>
          <th>Min %</th>
          <th>Max Plano</th>
          
          
        </tr>
      </thead>
  -->
      <!--<tbody> -->

          <?php
            $alamatRak = 'Tidak diketahui';
            // Fetch each row in an associative array
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {

              if ($alamatRak != $row['LKS_KODERAK'] . ' ' . $row['LKS_KODESUBRAK'] . ' ' . $row['LKS_TIPERAK'] . ' ' . $row['LKS_SHELVINGRAK'] ) {
              	$alamatRak = $row['LKS_KODERAK'] . ' ' . $row['LKS_KODESUBRAK'] . ' ' . $row['LKS_TIPERAK'] . ' ' . $row['LKS_SHELVINGRAK'] ;
              	

              	echo '<thead>';
              	print '<tr>';
              	//echo '<th colspan="15"><h4><br>' . $alamatRak . '</4></th>';
              	echo '<th colspan="15"><h4><br>' 
              		. '<small>Rak </small>' . $row['LKS_KODERAK'] 
              		. '<small> Sub Rak </small>' . $row['LKS_KODESUBRAK'] 
              		. '<small> Tipe </small>' . $row['LKS_TIPERAK'] 
              		. '<small> Shelving </small>' . $row['LKS_SHELVINGRAK'] 
              		
              		. '</4></th>';
              	print '</tr>';

		        echo ' 	 <tr  class="info">
					          <th>#</th>
					          <th>PLU</th>
					          <th>Nama Barang</th>
					          <th>Frac</th>
					          <th>Unit</th>
					          <th>Tag</th>
					          <th>K K</th>
					          <th>D B</th>
							  <th>A B</th>
					          <th>Disp</th>
					          <th>Max Disp</th>
					          <th>Qty</th>
					          <th>Exp Date</th>
					          <th>Min %</th>
					          <th>Max Plano</th>
				          </tr>';
				


			    echo '</thead>';
              	
              }

              print '<tr>';

              echo '<td align="right">'  . $row['LKS_NOURUT'] . '</td>';
              echo '<td align="left">'  . $row['LKS_PRDCD'] . '</td>';
              echo '<td align="left">'  . $row['LKS_NAMA_BARANG'] . '</td>';
              echo '<td align="left">'  . $row['LKS_UNIT'] . '</td>';
              echo '<td align="right">'  . $row['LKS_FRAC'] . '</td>';
              echo '<td align="center">'  . $row['LKS_TAG'] . '</td>';

              echo '<td align="right">'  . $row['LKS_TIRKIRIKANAN'] . '</td>';
              echo '<td align="right">'  . $row['LKS_TIRDEPANBELAKANG'] . '</td>';
              echo '<td align="right">'  . $row['LKS_TIRATASBAWAH'] . '</td>';

              // kapasitas
              echo '<td align="right">'  . $row['LKS_TIRKIRIKANAN'] * $row['LKS_TIRDEPANBELAKANG'] * $row['LKS_TIRATASBAWAH'] . '</td>';

              echo '<td align="right">'  . $row['LKS_MAXDISPLAY'] . '</td>';
              echo '<td align="right">'  . $row['LKS_QTY'] . '</td>';
              echo '<td align="center">'  . $row['LKS_EXPDATE'] . '</td>';
              echo '<td align="right">'  . $row['LKS_MINPCT'] . '</td>';
              echo '<td align="right">'  . $row['LKS_MAXPLANO'] . '</td>';


              
              print '</tr>';
            } //end while
               
            
          ?>
      	
        
        
        
      <!-- </tbody> -->
    </table>
  </div><!-- table responsive -->
  </div><!-- panel -->
