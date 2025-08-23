<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';

$dataSo = [];
$dataAdj = [];

if ($kodePLU !== '') {
    try {
        // Query Stock Opname
        $querySo = "
            SELECT 
                SOP_TGLSO,
                ADJ_TGLSO,
                SOP_PRDCD,
                ADJ_PRDCD,
                SOP_LOKASI,
                SOP_QTYSO,
                SOP_QTYLPP,
                (SOP_QTYSO - SOP_QTYLPP) AS SELISIH,
                SOP_NEWAVGCOST,
                (SOP_QTYSO - SOP_QTYLPP) * SOP_NEWAVGCOST AS RUPIAH,
                ADJ_QTY,
                ADJ_KETERANGAN,
                ADJ_CREATE_DT,
                ADJ_SEQ
            FROM TBTR_BA_STOCKOPNAME s
            LEFT JOIN TBTR_ADJUSTSO a
                ON s.SOP_PRDCD = a.ADJ_PRDCD AND s.SOP_TGLSO = a.ADJ_TGLSO
            WHERE s.SOP_PRDCD = :kodePLU
              AND s.SOP_LOKASI = '01'
            ORDER BY s.SOP_TGLSO DESC
            LIMIT 1
        ";

        $stmtSo = $conn->prepare($querySo);
        $stmtSo->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmtSo->execute();
        $dataSo = $stmtSo->fetchAll(PDO::FETCH_ASSOC);

        // Query Adjustment List (multiple rows)
        $queryAdj = "
            SELECT 
                ADJ_SEQ,
                ADJ_QTY,
                ADJ_KETERANGAN,
                ADJ_CREATE_DT
            FROM TBTR_ADJUSTSO
            WHERE ADJ_PRDCD = :kodePLU
            ORDER BY ADJ_CREATE_DT DESC
        ";
        $stmtAdj = $conn->prepare($queryAdj);
        $stmtAdj->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmtAdj->execute();
        $dataAdj = $stmtAdj->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Query gagal: " . $e->getMessage());
    }
}
?>


<!-- Modal HTML -->
<div class="modal fade" id="modalInfoSo" tabindex="-1" role="dialog" aria-labelledby="modalInfoSoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoSoLabel">Detail So Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">

        <!-- Tabel Stock Opname -->
        <table class="table table-bordered table-xl">
          <thead>
           <tr class="text-center table-primary">
              <th>QTY SO</th>
              <th>QTY LPP</th>
              <th>QTY ADJ</th>
              <th>SELISIH</th>
              <th>NEW AVG COST</th>
              <th>RUPIAH</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($dataSo)): ?>
              <?php foreach ($dataSo as $row): ?>
                <tr class="text-center">
                  <td><?= number_format($row['sop_qtyso'], 0, '.', ',') ?></td>
                  <td><?= number_format($row['sop_qtylpp'], 0, '.', ',') ?></td>
                  <td><?= number_format($row['adj_qty'] ?? 0, 0, '.', ',') ?></td>
                  <td><?= number_format($row['selisih'], 0, '.', ',') ?></td>
                  <td><?= number_format($row['sop_newavgcost'], 0, '.', ',') ?></td>
                  <td><?= number_format($row['rupiah'], 2, '.', ',') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <b>KETERANGAN : SELISIH = QTY SO - QTY LPP + QTY ADJ</b>
        <p></p>

        <!-- Tabel Detail Adjustment -->
        <h4 class="modal-title" align="center">Detail Adjustment</h4>
        <hr>
        <table id="lookup" class="table table-bordered table-hover table-striped">
          <thead>
            <tr class="text-center table-primary">
              <th>Seq</th>
              <th>QTY ADJ</th>
              <th>KETERANGAN</th>
              <th>TANGGAL ADJ</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($dataAdj)): ?>
              <?php foreach ($dataAdj as $adj): ?>
                <tr class="text-center">
                  <td><?= htmlspecialchars($adj['adj_seq']) ?></td>
                  <td><?= number_format($adj['adj_qty'], 0, '.', ',') ?></td>
                  <td><?= htmlspecialchars($adj['adj_keterangan']) ?></td>
                  <td><?= htmlspecialchars($adj['adj_create_dt']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">Tidak ada data adjustment</td></tr>
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
