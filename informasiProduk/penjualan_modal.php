<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
$data = [];

if ($kodePLU !== '') {
    try {
        $stmt = $conn->prepare("
            SELECT * 
            FROM tbtr_rekapsalesbulanan 
            WHERE rsl_prdcd = :kodePLU
            ORDER BY rsl_group DESC
        ");
        $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query gagal: " . $e->getMessage());
    }
}
?>

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
<div class="modal fade" id="modalInfoPenjualan" tabindex="-1" role="dialog" aria-labelledby="modalInfoPenjualanLabel" aria-hidden="true">
  <div class="modal-dialog custom-width" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoPenjualanLabel">Detail Penjualan Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <table class="table table-bordered table-sm table-hover">
          <thead class="thead-light">
            <tr>
              <th rowspan="2" class="text-center align-middle">#</th>
              <th rowspan="2" class="text-center align-middle">Group</th>
              <th rowspan="2" class="text-center align-middle">Keterangan</th>
              <th colspan="12" class="text-center">Bulan</th>
            </tr>
            <tr>
              <?php for ($i = 1; $i <= 12; $i++): ?>
                <th class="text-center"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></th>
              <?php endfor; ?>
            </tr>
          </thead> 
          <tbody>
            <?php if (!empty($data)): ?>
              <?php
                $noUrut = 0;
                foreach ($data as $row):
                  $noUrut++;
                  $groupmember = match ($row['rsl_group']) {
                    '01' => 'Member Biru',
                    '02' => 'OMI',
                    default => 'Member Merah'
                  };

                  echo '<tr>';
                  echo '<td rowspan="4" align="center">' . $noUrut . '</td>';
                  echo '<td rowspan="4" align="left">' . htmlspecialchars($groupmember) . '</td>';
                  echo '<td align="left">Qty</td>';
                  for ($i = 1; $i <= 12; $i++) {
                    $qtyKey = 'rsl_qty_' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $qty = number_format($row[$qtyKey] ?? 0, 0, '.', ',');
                    echo '<td align="right">' . $qty . '</td>';
                  }
                  echo '</tr>';

                  echo '<tr>';
                  echo '<td align="left">Rupiah x 1000</td>';
                  for ($i = 1; $i <= 12; $i++) {
                    $rphKey = 'rsl_rph_' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $rph = number_format(($row[$rphKey] ?? 0) / 1000, 0, '.', ',');
                    echo '<td align="right">' . $rph . '</td>';
                  }
                  echo '</tr>';

                  echo '<tr>';
                  echo '<td align="left">Margin x 1000</td>';
                  for ($i = 1; $i <= 12; $i++) {
                    $rph = $row['rsl_rph_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                    $hpp = $row['rsl_hpp_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                    $margin = number_format(($rph - $hpp) / 1000, 0, '.', ',');
                    echo '<td align="right">' . $margin . '</td>';
                  }
                  echo '</tr>';

                  echo '<tr>';
                  echo '<td align="left">Margin (%)</td>';
                  for ($i = 1; $i <= 12; $i++) {
                    $rph = $row['rsl_rph_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                    $hpp = $row['rsl_hpp_' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                    $percent = ($hpp != 0) ? number_format((($rph - $hpp) / $hpp) * 100, 2, '.', ',') : '0.00';
                    echo '<td align="right">' . $percent . '</td>';
                  }
                  echo '</tr>';
                endforeach;
              ?>
            <?php else: ?>
              <tr><td colspan="15" align="center">Tidak ada data</td></tr>
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
