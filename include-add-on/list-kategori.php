<!-- list kategori -->
<select class="form-control" name="kodeKategoriBarang" id="kodeKategoriBarang">
  <option value="All">All Kategori</option>

  <?php
    $namaDepartemen = "0. Tidak diketahui";
    $query = "SELECT kat.kat_kodedepartement,
                dep.dep_namadepartement AS kat_namadepartement,
                kat.kat_kodekategori,
                kat.kat_namakategori
              FROM tbmaster_kategori kat
              left join tbmaster_departement dep on kat.kat_kodedepartement = dep.dep_kodedepartement
              ORDER BY kat_kodedepartement,
                kat_kodekategori";
   

    // Create connection to Oracle
    
    include '../helper/connection.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      if ($namaDepartemen <> $row['kat_kodedepartement'] . ' ' . $row['kat_namadepartement'] ) {
          if ($namaDepartemen <> "0. Tidak diketahui") {echo '</optgroup>' ;}
          
          echo '<optgroup label="' . $row['kat_kodedepartement'] . ' ' . $row['kat_namadepartement'] .'">';
          $namaDepartemen = $row['kat_kodedepartement'] . ' ' . $row['kat_namadepartement'];
      }
      
      echo '<option value="' 
          . $row['kat_kodedepartement'] 
          . $row['kat_kodekategori'] 
          . '">' 
          . $row['kat_kodedepartement'] 
          . '-' 
          . $row['kat_kodekategori'] 
          . ' ' 
          . sentence_case($row['kat_namakategori']) 
          . '</option>';
    }

  ?>
  </optgroup>
</select> <!-- akhir list kategori -->