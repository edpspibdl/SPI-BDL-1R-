<?php
	
$query = "SELECT STI_DRIVERNAME,
  COUNT(STI_NOSERAHTERIMA) AS TOTAL_SERTIM
FROM TBTR_SERAHTERIMA_IPP
LEFT JOIN TBTR_OBI_H ON OBI_NOPB = STI_CODPAYMENTCODE
WHERE OBI_RECID = '5' and sti_pin not like 'B%'
GROUP BY STI_DRIVERNAME
ORDER BY STI_DRIVERNAME";

// Create connection to Oracle
  require_once '../helper/connection.php';
  $stmt = $conn->query($query); // Eksekusi query dengan PDO


?>