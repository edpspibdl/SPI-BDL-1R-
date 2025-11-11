<!-- list kode tag -->
<select class="form-control" name="kodeTag" id="kodeTag">


  <option value="All"> All Tag</option>

  <?php
    $query = "SELECT DISTINCT coalesce(p.prd_kodetag,' ') AS tag_kode,
                coalesce(t.tag_keterangan,' ')            AS tag_keterangan
              FROM tbmaster_prodmast p
              left join tbmaster_tag t on coalesce(p.prd_kodetag,' ') = t.tag_kodetag
              ORDER BY tag_kode ";
 

    // Create connection to Oracle
    include '../helpepr/connection.php';
    try {
      $query = $conn->prepare($query);
      $query->execute();
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }

  while ($row = $query->fetch(PDO::FETCH_ASSOC)) { 
      echo '<option value="' . $row['tag_kode'] . '">' . $row['tag_kode'] . ' ' . sentence_case($row['tag_keterangan']) . '</option>';
    }

  ?>
  
</select> <!-- kode tag-->