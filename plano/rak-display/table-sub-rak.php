<?php

  include '../_/connection.php';
  $stid = oci_parse($conn, $query);
  $r = oci_execute($stid);
?>

<?php
  // buat tombol dari kode sub rak 
  echo "<h4><small>Rak:</small> {$kodeRak}<small> Sub Rak:</small></h4>";
  while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
  	if ($kodeSubRak != $row['LKS_SUB_RAK']) {
		echo '<a href="index.php?kodeRak=' . $kodeRak . '&kodeSubRak=' . $row['LKS_SUB_RAK'] . '" class="btn btn-sm btn-default bayang" role="button">' . $row['LKS_SUB_RAK'] . ' <span class="badge">' . $row['LKS_SHELVING'] . '</span></a> ';  		
  	} else {
  		echo '<a href="index.php?kodeRak=' . $kodeRak . '&kodeSubRak=' . $row['LKS_SUB_RAK'] . '" class="btn btn-sm btn-default bayang active" role="button">' . $row['LKS_SUB_RAK'] . ' <span class="badge">' . $row['LKS_SHELVING'] . '</span></a> ';  		
  	}
    
  } 
  //echo '</h4>';
  echo '<hr>';
?>



