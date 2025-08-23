<?php
  
  //include 'query-forklift.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
 $stmt = $conn->prepare($query);
   $stmt->execute();
?>
 

  <div class="panel bayang">
  <div class="table-responsive">
    <table id= "GridView" class="table table-bordered table-striped table-hover">
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
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              
              $noUrut ++;

              if ($row['spb_jenis'] == 'MANUAL') {
                $infoJenis = '<span class="badge">Manual</span>';
              } else {
                $infoJenis = '';
              }
              

              $infoProduk  = $row['spb_prdcd'] . ' - ';
              $infoProduk .= $row['spb_unit'] . ' / ';
              $infoProduk .= $row['spb_frac'] . ' ';
              $infoProduk .= $row['spb_kodetag'] ;

              $infoTertunda = ' ';

              switch ($row['spb_hari_tertunda']) {

                case 0:

                  if ($row['spb_jam_tertunda'] >= 2) {
                     $infoTertunda = '<span class="label label-danger"> ' . $row['spb_jam_tertunda'] . ' jam yang lalu</span>';
                  } elseif ($row['spb_jam_tertunda'] == 1) {
                    $infoTertunda = '<span class="label label-primary"> ' . $row['spb_jam_tertunda'] . ' jam yang lalu</span>';
                  } elseif ($row['spb_jam_tertunda'] == 0) {
                    $infoTertunda = ' ';
                  }
                  break;
                case 1:
                  $infoTertunda = '<span class="label label-danger"> Kemarin</span>';
                  break;
                default:
                  $infoTertunda = '<span class="label label-danger"> ' . $row['spb_hari_tertunda'] . ' hari yang lalu</span>';
              }

              print '<tr>';
    
              echo '<td align="center">' . '<h4>' . $noUrut . '</h4>' .  '</td>';

              echo '<td align="left">' . '<h4>'  . $row['spb_lokasiasal'] . '</h4>' .  $row['spb_minta_ctn'] . ' - ' . $row['spb_unit'] . '</td>';
              echo '<td align="left">' . '<h4>'  . $row['spb_lokasitujuan'] . '</h4>' . $infoJenis . '</td>';
              //echo '<td align="left">' . '<h3>'  . $row['SPB_DESKRIPSI'] . '</h3>' . '</td>';
              echo '<td align="left">' .   '<h4>'  . $row['spb_deskripsi'] . '</h4>' . $infoProduk . ' ' . $infoTertunda .  '</td>';
              
              print '</tr>';
            } //end while
               
            //print '</tr>';
              
              // hitung total nilai disini

          ?>
      	
        
        
        
      </tbody>
    </table>
	
  </div>
  </div>
