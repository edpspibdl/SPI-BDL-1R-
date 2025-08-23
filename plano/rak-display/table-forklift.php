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

              if ($row['SPB_JENIS'] == 'MANUAL') {
                $infoJenis = '<span class="badge">Manual</span>';
              } else {
                $infoJenis = '';
              }

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

              $infoTertunda = ' ';
              switch ($row['SPB_HARI_TERTUNDA']) {

                case "0":

                  if ($row['SPB_JAM_TERTUNDA'] >= 2) {
                     $infoTertunda = '<span class="label label-danger"> ' . $row['SPB_JAM_TERTUNDA'] . ' jam yang lalu</span>';
                  } elseif ($row['SPB_JAM_TERTUNDA'] == 1) {
                    $infoTertunda = '<span class="label label-primary"> ' . $row['SPB_JAM_TERTUNDA'] . ' jam yang lalu</span>';
                  }
                  //$infoTertunda = '';
                  break;
                case "1":
                  $infoTertunda = '<span class="label label-danger"> Kemarin</span>';
                  break;
                default:
                  $infoTertunda = '<span class="label label-danger"> ' . $row['SPB_HARI_TERTUNDA'] . ' hari yang lalu</span>';
              }

              print '<tr>';

              echo '<td align="center">'  . $noUrut . '</td>';

              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASIASAL'] . '</h4>' .  $minta . '</td>';
              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASITUJUAN'] . '</h4>' . $infoJenis . '</td>';
              //echo '<td align="left">' . '<h3>'  . $row['SPB_DESKRIPSI'] . '</h3>' . '</td>';
              //echo '<td align="left">' .   '<h4>'  . $row['SPB_DESKRIPSI'] . '</h4>' . $infoProduk . '</td>';
              echo '<td align="left">' .   '<h4>'  . $row['SPB_DESKRIPSI'] . '</h4>' . $infoProduk . ' ' . $infoTertunda  .'</td>';
              
              print '</tr>';
            } //end while
               
            //print '</tr>';
              
              // hitung total nilai disini

          ?>
      	
        
        
        
      </tbody>
    </table>
  </div><!-- table responsive -->
  </div><!-- panel -->
