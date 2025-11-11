<!-- list loyalty promo -->
<select class="form-control" name="kodeLoyalty" id="kodeLoyalty">
  <option value="All">All Loyalty Promo</option>

  <?php
    $query = "SELECT lyh_kodepromo,
       TO_NUMBER(SUBSTR(lyh_kodepromo, 3, 5), '99999')
       || '.'
       || lyh_namapromo AS lyh_nama_promo
FROM   tbtr_loyaltyheader
ORDER  BY lyh_kodepromo";
   

    // Create connection to Oracle
    
    require_once '../include/koneksi.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      echo '<option value="' . $row['lyh_kodepromo'] . '">' . sentence_case($row['lyh_nama_promo']) . '</option>';
    }

  ?>
  
</select> <!-- akhir list loyalty promo -->