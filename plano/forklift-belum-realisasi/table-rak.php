<?php

  include 'query-rak.php'; 

  // Create connection to Oracle
    	include '../_/connection.php';
 $stmt = $conn->prepare($query);
    $stmt->execute();
?>

  <div class="list-group bayang">
          <!--<a href="index.php?spbLokasi=" class="list-group-item active">Lokasi Penurunan Storage</a> -->

          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
               while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rak = $row['spb_lokasitujuan'];
                $jumlah = $row['spb_jumlah'];

                $active = '';
                if ($spbLokasi == $rak) {
                  $active = 'active';
                }
              echo '<a href="index.php?spbLokasi=' . $rak .  '" class="list-group-item ' . $active .  '">' . $rak . ' <span class="badge">' . $jumlah . '</span></a>';

            } 
          ?>

</div>      	
