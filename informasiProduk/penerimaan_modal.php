<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
$data = [];

if ($kodePLU !== '') {
    try {
        $stmt = $conn->prepare("
            SELECT *
            FROM (
                SELECT m.mstd_typetrn,
                       m.mstd_kodesupplier,
                       s.sup_namasupplier AS mstd_namasupplier,
                       m.mstd_qty,
                       m.mstd_qtybonus1,
                       m.mstd_qtybonus2,
                       m.mstd_nodoc,
                       m.mstd_tgldoc,
                       TO_CHAR(m.mstd_create_dt, 'hh24:mi:ss') AS mstd_jam,
                       (m.mstd_gross - m.mstd_discrph) / (m.mstd_qty + 0.00000001) AS mstd_lastcost,
                       m.mstd_avgcost / m.mstd_frac AS mstd_avgcost,
                       m.mstd_create_dt
                FROM tbtr_mstran_d m
                LEFT JOIN tbmaster_supplier s ON s.sup_kodesupplier = m.mstd_kodesupplier
                WHERE m.mstd_prdcd LIKE :kodePLU
                  AND m.mstd_typetrn IN ('B','L')
                  AND m.mstd_recordid IS NULL
                ORDER BY m.mstd_create_dt DESC
                LIMIT 15
            ) AS alias1
            ORDER BY mstd_create_dt DESC
        ");
        $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query gagal: " . $e->getMessage());
    }
}
?>

<!-- Custom Modal Width -->
<style>
  .modal-dialog.custom-width {
    max-width: 85%;
  }
  .table th, .table td {
    font-size: 16px;
    vertical-align: middle;
  }
</style>

<!-- Modal HTML -->
<div class="modal fade" id="modalInfoPenerimaan" tabindex="-1" role="dialog" aria-labelledby="modalInfoPenerimaanLabel" aria-hidden="true">
  <div class="modal-dialog custom-width" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoPenerimaanLabel">Detail Penerimaan Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-sm table-hover">
            <thead class="table-primary text-center">
              <tr>
                <th>#</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Bonus 1</th>
                <th>Bonus 2</th>
                <th>No. Doc</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Last Cost</th>
                <th>Avg Cost</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data)): ?>
                <?php
                  $noUrut = 1;
                  $supplier = '';
                  foreach ($data as $row):
                    $currentSupplier = $row['mstd_kodesupplier'] . ' ' . $row['mstd_namasupplier'];
                ?>
                  <tr>
                    <td class="text-center"><?= $noUrut++ ?></td>
                    <td><?= ($supplier !== $currentSupplier ? $currentSupplier : '') ?></td>
                    <td class="text-right"><?= number_format($row['mstd_qty'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['mstd_qtybonus1'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['mstd_qtybonus2'], 0, '.', ',') ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['mstd_nodoc']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['mstd_tgldoc']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['mstd_jam']) ?></td>
                    <td class="text-right"><?= number_format($row['mstd_lastcost'], 0, '.', ',') ?></td>
                    <td class="text-right"><?= number_format($row['mstd_avgcost'], 0, '.', ',') ?></td>
                  </tr>
                <?php
                  $supplier = $currentSupplier;
                  endforeach;
                ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center">Data tidak ditemukan</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
