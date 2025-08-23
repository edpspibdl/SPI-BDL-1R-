<?php

    //require_once '../includes/my-function.php'; 
    
      
    // atur nilai default
    $slpStatus   = ' '; 
    $judulPanel  = "SLP Belum Realisasi";
    // null: belum realisasi 
    //    c: batal
    //    p: sudah diproses

    $jenisSPB  = 'T' ; // M:manual T:toko G:gudang
    $statusSPB = '0' ; /* 0: belum diturunkan
                          1: sudah realisasi
                          2: batal
                          3: sudah diturunkan tapi belum realisasi
                       */ 

    
	// atur nilai sesuai dengan request dari form
	if(isset($_GET['jenisSPB'])) {if ($_GET['jenisSPB'] !=""){$jenisSPB = $_GET['jenisSPB']; }}
  if(isset($_GET['statusSPB'])) {if ($_GET['statusSPB'] !=""){$statusSPB = $_GET['statusSPB']; }}
  
    
  	//validasi
  	$jenisSPB = strtoupper($jenisSPB);




  include 'query-spb-detail.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
  $stid = oci_parse($conn, $query);

  // binding all variable
  //require 'binding-query.php';

  $r = oci_execute($stid);
?>

  <h3><?php echo $judulPanel; ?></h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
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
              



              echo '<td align="right"><span class="badge">'  . $noUrut . '</span></td>';

              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASIASAL'] . '</h4>' . '</td>';
              echo '<td align="left">' . '<h4>'  . $row['SPB_LOKASITUJUAN'] . '</h4>' . '</td>';
              //echo '<td align="left">' . '<h3>'  . $row['SPB_DESKRIPSI'] . '</h3>' . '</td>';
              echo '<td align="left">' . $infoProduk .  '<h4>'  . $row['SPB_DESKRIPSI'] . '</h4>' . '</td>';
              
              print '</tr>';
            } //end while
               
            //print '</tr>';
              
              // hitung total nilai disini

          ?>
      	
        
        
        
      </tbody>
    </table>
  </div>