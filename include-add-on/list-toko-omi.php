<!-- list toko omi -->
<select class="form-control" name="kodeTokoOMI" id="kodeTokoOMI">
  <option value="All">Semua Toko OMI</option>

  <?php
    
    $query = "SELECT tko_kodeomi,tko_namaomi FROM tbmaster_tokoigr WHERE tko_kodesbu = 'O' ORDER BY tko_kodeomi";
              
   

    // Create connection to Oracle
    
    include '../include/koneksi.php';
    $stid = oci_parse($conn, $query);
    oci_execute($stid);

    while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
      echo '<option value="' . $row['TKO_KODEOMI'] . '">' . $row['TKO_KODEOMI'] . ' ' . $row['TKO_NAMAOMI'] . '</option>';
    }

  ?>
</select> <!-- akhir list toko omi -->