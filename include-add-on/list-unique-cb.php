<!-- list unique cash back -->
<select class="form-control" name="kodeUniqueCB" id="kodeUniqueCB" disabled>
  <option value="All">All Unique Cash Back</option>

  <?php
    $query = "SELECT cbh_kodepromosi,
                     Replace(cbh_kodepromosi
                             || ' '
                             || cbh_namapromosi, 'CB UNIQUE CODE', 'UC') AS cbh_nama_promo,
                     CASE
                       WHEN Trunc(cbh_tglakhir) < Trunc(SYSDATE) THEN 'Selesai'
                       WHEN Trunc(cbh_tglawal) > Trunc(SYSDATE) THEN 'Rencana'
                       ELSE 'Masih promo'
                     END                                                 AS cbh_periode,
                     cbh_tglawal,
                     cbh_tglakhir
              FROM   tbtr_cashback_hdr
              WHERE  cbh_recordid IS NULL
                     AND Trunc(cbh_tglakhir) > Trunc(SYSDATE) - 30
                     AND cbh_namapromosi LIKE '%UNIQUE CODE%'
              ORDER  BY cbh_periode ";
   

    // Create connection to Oracle
    
    require_once '../include/koneksi.php';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $periodeUC = ' ';
    while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
      if ($periodeUC <> $row['CBH_PERIODE']) {
          if ($periodeUC <> ' ') { echo '</optgroup>';}
          $periodeUC = $row['CBH_PERIODE'];
          echo '<optgroup label ="' . $row['CBH_PERIODE'] . '">';
      }
      echo '<option value="' 
      . $row['CBH_KODEPROMOSI'] . '">' 
      . $row['CBH_NAMA_PROMO'] . ' '
      
      . $row['CBH_TGLAWAL']  . ' ~ '
      . $row['CBH_TGLAKHIR']  
      . '</option>';
    }

  ?>
  </optgroup>
</select> <!-- akhir list unique cash back -->