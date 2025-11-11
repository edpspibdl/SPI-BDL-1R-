<!-- list zona -->
<select class="form-control" name="zona" id="zona">
  <option value="All">All Zona</option>

  <?php
    $Zona = "0. Tidak diketahui";
    $query = "SELECT 
                DMI_KODEJALUR AS GRAK, 
                'ZONA ' || SUBSTR(DMI_KODEJALUR, 3, 1) AS ZONA, 
                'JALUR ' || SUBSTR(DMI_KODEJALUR, 5, 1) AS JALUR 
              FROM dpd_master_igr 
              ORDER BY DMI_KODEJALUR";

    // Create connection to PostgreSQL
    include '../include/connection.php';
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Error in query: " . pg_last_error($conn);
    } else {
        while ($row = pg_fetch_assoc($result)) {
            if ($Zona != $row['zona'] . ' ' . $row['zona']) {
                if ($Zona != "0. Tidak diketahui") {
                    echo '</optgroup>';
                }
                echo '<optgroup label="' . htmlspecialchars($row['zona']) . '">';
                $Zona = $row['zona'] . ' ' . $row['zona'];
            }
            echo '<option value="' . htmlspecialchars($row['grak']) . '">' . htmlspecialchars($row['zona']) . ' ' . sentence_case($row['jalur']) . '</option>';
        }
        echo '</optgroup>'; // Closing the last optgroup
    }
  ?>
</select> <!-- akhir list departemen -->
