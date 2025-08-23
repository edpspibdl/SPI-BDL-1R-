<?php
  
  //include 'query-forklift.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
  $stid = oci_parse($conn, $query);
  $r = oci_execute($stid);
?>
  
  


  

  
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr  class="info">
          <th>#</th>
          <th>Asal</th>
          <th>Tujuan</th>
          <th>Nama Barang</th>
        </tr>
      </thead>
      <tbody>

          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
              
              $noUrut ++;

              print '<tr>';

              $infoProduk  = $row['SPB_PRDCD'] . ' - ';
              $infoProduk .= $row['SPB_UNIT'] . ' / ';
              $infoProduk .= $row['SPB_FRAC'] . ' ';
              $infoProduk .= $row['SPB_KODETAG'] ;
              
              $minta = '';

              if ($row['SPB_MINTA_CTN'] <> 0) {
                  $minta = $row['SPB_MINTA_CTN'] . ' ' . $row['SPB_UNIT'] ;                
              }

              if ($row['SPB_MINTA_PCS'] <> 0) {
                  $minta .= ' ' . $row['SPB_MINTA_PCS'] . ' PCS';                
              }


              

              echo '<td align="center">'  . $noUrut . '</td>';

              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASIASAL'] . '</h4>' .  $minta . '</td>';
              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASITUJUAN'] . '</h4>' . '</td>';
              //echo '<td align="left">' . '<h3>'  . $row['SPB_DESKRIPSI'] . '</h3>' . '</td>';
              echo '<td align="left">' .   '<h4>'  . $row['SPB_DESKRIPSI'] . '</h4>' . $infoProduk . '</td>';
              
              print '</tr>';
            } //end while
               
            //print '</tr>';
              
              // hitung total nilai disini

          ?>
      	
        
        
        
      </tbody>
    </table>
  </div>
