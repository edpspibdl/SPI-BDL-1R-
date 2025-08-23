<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';

$data = [];

if ($kodePLU !== '') {
    try {
        $stmt = $conn->prepare("
            SELECT
                CASE
                    WHEN SUBSTRING(lks_koderak FROM 1 FOR 1) IN ('R','O','F','X','K') THEN '1'
                    ELSE '2'
                END AS lks_lokasi,
                lks_koderak,
                lks_kodesubrak,
                lks_tiperak,
                lks_shelvingrak,
                lks_nourut,
                lks_qty
            FROM tbmaster_lokasi
            WHERE lks_prdcd LIKE :kodePLU
            ORDER BY lks_lokasi,
                     lks_koderak,
                     lks_kodesubrak,
                     lks_tiperak,
                     lks_shelvingrak,
                     lks_nourut
        ");
        $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Perbaikan di sini
    } catch (PDOException $e) {
        die("Query gagal: " . $e->getMessage());
    }
}
?>

<!-- Modal HTML -->
<div class="modal fade" id="modalInfoLokasi" tabindex="-1" role="dialog" aria-labelledby="modalInfoLokasiLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoLokasiLabel">Detail Lokasi Produk </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <table class="table table-bordered table-md">
          <thead class="thead-light">
            <tr>
              <th class="text-center">#</th>
              <th>Rak</th>
              <th>Sub</th>
              <th>Tipe</th>
              <th>Shelving</th>
              <th>Urut</th>
              <th>Qty</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data)): ?>
              <?php foreach ($data as $i => $row): ?>
                <tr>
                  <td class="text-center"><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($row['lks_koderak'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lks_kodesubrak'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lks_tiperak'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lks_shelvingrak'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lks_nourut'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lks_qty'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">Data tidak ditemukan</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>
