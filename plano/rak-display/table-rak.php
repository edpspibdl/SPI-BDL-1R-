<?php

  include 'query-rak.php'; 

  // Create connection to Oracle
  include '../_/connection.php';
  $stid = oci_parse($conn, $query);
  $r = oci_execute($stid);
?>

  <div class="list-group bayang">
          <a href="index.php?kodeRak=" class="list-group-item active">Rak Display</a>

          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
                $rak = $row['LKS_RAK'];
                $jumlah = $row['LKS_SUB_RAK'];

                $active = '';
                if ($kodeRak == $rak) {
                  $active = 'active';
                }
              echo '<a href="index.php?kodeRak=' . $rak .  '" class="list-group-item ' . $active .  '">' . $rak . ' <span class="badge">' . $jumlah . '</span></a>';

            } 
          ?>

</div>      	
