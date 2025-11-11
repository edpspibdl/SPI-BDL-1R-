<!-- list toko idm -->
<select class="form-control" name="kodeTokoIDM" id="kodeTokoIDM">
  <option value="All">Semua Toko IDM</option>
  <?php
      $query = "SELECT tko_kodeomi,tko_namaomi FROM tbmaster_tokoigr WHERE tko_kodesbu = 'I' ORDER BY tko_kodeomi";

      // Create connection to Oracle
      include '../include/koneksi.php';
      $stid = oci_parse($conn, $query);
      $r = oci_execute($stid);
      while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
        echo '<option value="' . $row['TKO_KODEOMI'] . '">' . $row['TKO_KODEOMI'] . ' ' . $row['TKO_NAMAOMI'] . '</option>';
      }

  ?>
</select> <!-- akhir list toko idm -->