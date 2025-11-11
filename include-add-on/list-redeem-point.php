<!-- list Redeem Point -->
<select class="form-control" name="kodeRedeem" id="kodeRedeem">
  <option value="All">All Redeem Point</option>

  <?php
    $query = "SELECT cbh_kodepromosi,
                REPLACE( cbh_namapromosi,'CB RDMPOINT', 'CBRP' ) AS cbh_namapromosi,
                to_char(cbh_tglawal,'DD-MON-YY') cbh_tglawal,
                to_char(cbh_tglakhir,'DD-MON-YY') cbh_tglakhir
              FROM tbtr_cashback_hdr
              WHERE cbh_tglakhir >= CURRENT_DATE
              AND cbh_jenispromosi      ='2'
--              AND cbh_kiosk             = 'N'
              AND cbh_recordid         IS NULL
              ORDER BY cbh_namapromosi ";
   

    // Create connection to Oracle
    
    include '../include/koneksi.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      echo '<option value="' . $row['cbh_kodepromosi'] . '">' . $row['cbh_kodepromosi'] . ' ' . sentence_case($row['cbh_namapromosi']) . ' ' . $row['cbh_tglawal'] . ' s.d. ' . $row['cbh_tglakhir'] . '</option>';      
    }

  ?>
  
</select> <!-- akhir list Redeem Point -->