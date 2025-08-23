<?php

  include 'query-rak.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
  $stid = oci_parse($conn, $query);
  $r = oci_execute($stid);
?>

  <div class="list-group bayang">
          <!-- <a href="index.php?spbLokasi=" class="list-group-item active">Lokasi Penurunan Storage</a> -->

          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
                $rak = $row['SPB_LOKASIASAL'];
                $jumlah = $row['SPB_JUMLAH'];

                $active = '';
                if ($spbLokasi == $rak) {
                  $active = 'active';
                }
              echo '<a href="index.php?spbLokasi=' . $rak .  '" class="list-group-item ' . $active .  '">' . $rak . ' <span class="badge">' . $jumlah . '</span></a>';

            } 
          ?>

</div>      	
