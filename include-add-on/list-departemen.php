<!-- list departemen -->
<select class="form-control" name="kodeDepartemen" id="kodeDepartemen">
  <option value="All">All Departemen</option>

  <?php
    $namaDivisi = "0. Tidak diketahui";
    $query = "SELECT dep.dep_kodedivisi,
                     div.div_namadivisi AS dep_namadivisi,
                     dep.dep_kodedepartement,
                     dep.dep_namadepartement
              FROM   tbmaster_departement dep
              left join  tbmaster_divisi div on dep.dep_kodedivisi = div.div_kodedivisi
              ORDER  BY dep_kodedivisi,
                        dep_kodedepartement ";
   

    // Create connection to Oracle
    
    include '../helper/connection.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

  while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      if ($namaDivisi <> $row['dep_kodedivisi'] . ' ' . $row['dep_namadivisi'] ) {
          if ($namaDivisi <> "0. Tidak diketahui") {echo '</optgroup>' ;}
          
          echo '<optgroup label="' . $row['dep_kodedivisi'] . ' ' . $row['dep_namadivisi'] .'">';
          $namaDivisi = $row['dep_kodedivisi'] . ' ' . $row['dep_namadivisi'];
      }
      echo '<option value="' . $row['dep_kodedepartement'] . '">' . $row['dep_kodedepartement'] . ' ' . sentence_case($row['dep_namadepartement']) . '</option>';
    }

  ?>
  </optgroup>
</select> <!-- akhir list departemen -->