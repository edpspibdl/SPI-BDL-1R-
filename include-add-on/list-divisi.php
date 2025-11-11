<!-- list divisi -->
<select class="form-control" name="kodeDivisi" id="kodeDivisi">
  <option value="All">All Divisi</option>

  <?php
    $query = "SELECT div_kodedivisi, div_namadivisi FROM tbmaster_divisi ORDER BY div_kodedivisi ";
   

    // Create connection to Oracle
    
    include '../helper/connection.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      echo '<option value="' . $row['div_kodedivisi'] . '">' . $row['div_kodedivisi'] . ' ' . sentence_case($row['div_namadivisi']) . '</option>';
    }

  ?>
  
</select> <!-- akhir list divisi -->