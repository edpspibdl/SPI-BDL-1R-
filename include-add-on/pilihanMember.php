<!-- pilihan member -->

<div class="panel panel-default">
  <div class="panel-heading">Member</div>
  <div class="panel-body">
    <input type="text" class="form-control mb-2" name="namaMember" id="namaMember" placeholder="Nama Member">
    <input type="text" class="form-control mb-2" name="kodeMember" id="kodeMember" placeholder="Kode Member">
    <input type="text" class="form-control mb-2" name="kodeMonitoringMember" id="kodeMonitoringMember" placeholder="Kode Monitoring Member">

    <select class="form-control mb-2" name="jenisMember" id="jenisMember">
      <option value="All">All Member</option>
      <option value="Merah">Member Merah</option>
      <option value="Biru">Member Biru</option>
      <option value="OMI">OMI</option>
      <option value="IDM">Indomaret</option>
      <option value="MerahBiru">Merah dan Biru</option>
      <option value="TMI">TMI</option>
      <option value="MerahBiruOmi">Merah Biru dan OMI</option>
      <option value="MerahKlik">Klik Merah</option>
      <option value="BiruKlik">Klik Biru</option>
    </select>

    <!-- list outlet -->
    <select class="form-control mb-2" name="kodeOutlet" id="kodeOutlet">
      <option value="All">All Outlet</option>
      <?php
      include __DIR__ . '/../helper/connection.php'; // ✅ path aman dari semua lokasi

      try {
          $query = "SELECT out_kodeoutlet, out_namaoutlet FROM tbmaster_outlet ORDER BY out_kodeoutlet";
          $stmt = $conn->query($query);
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<option value="' . htmlspecialchars($row['out_kodeoutlet']) . '">' .
                   htmlspecialchars($row['out_kodeoutlet']) . ' ' . ucwords(strtolower($row['out_namaoutlet'])) .
                   '</option>';
          }
      } catch (PDOException $e) {
          echo '<option disabled>Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
      }
      ?>
    </select>
    <!-- akhir list outlet -->

    <!-- list sub outlet -->
    <select class="form-control" name="kodeSubOutlet" id="kodeSubOutlet">
      <option value="">All Sub Outlet</option>
      <?php
      try {
          $query = "SELECT sub_kodesuboutlet, sub_namasuboutlet FROM tbmaster_suboutlet ORDER BY sub_kodesuboutlet";
          $stmt = $conn->query($query);
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<option value="' . htmlspecialchars($row['sub_kodesuboutlet']) . '">' .
                   htmlspecialchars($row['sub_kodesuboutlet']) . ' ' . ucwords(strtolower($row['sub_namasuboutlet'])) .
                   '</option>';
          }
      } catch (PDOException $e) {
          echo '<option disabled>Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
      }
      ?>
    </select>
    <!-- akhir list sub outlet -->
  </div>
</div>
