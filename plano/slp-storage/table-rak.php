<?php

  include 'query-rak.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
 	$stmt = $conn->prepare($query);
	 $stmt->execute();
?>
  
  
  <div class="list-group  bayang">
      <!-- <a href="index.php?slpStatus=" class="list-group-item active">SLP Belum terealisasi</a>        -->

          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rak = $row['slp_koderak'];
                $jumlah = $row['slp_jumlah'];

                //$spbLokasi ='99';
                $active = '';
                if ($slpLokasi == $rak) {
                  $active = 'active';
                }
              echo '<a href="index.php?slpStatus=&slpLokasi=' . $rak .  '" class="list-group-item ' . $active .  '">' . $rak . ' <span class="badge">' . $jumlah . '</span></a>';

            } 
          ?>

</div>      	
