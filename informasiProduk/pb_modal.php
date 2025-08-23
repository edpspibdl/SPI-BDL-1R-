<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';

$data = [];

if ($kodePLU !== '') {
    try {
        $stmt = $conn->prepare("SELECT 
  PBD_NOPB,
  PBD_NOPO,
  PBH_TGLPB,
  TPOD_PRDCD,
  PBD_KODESUPPLIER,
  PBD_QTYPB,
  PBH_KETERANGANPB,
  TPOH_NOPO,
  TPOH_TGLPO,
  TPOH_KODESUPPLIER,
  TPOD_PRDCD,
  TPOD_QTYPO,
  MSTH_NODOC,
  MSTH_TGLDOC,
  MSTH_NOPO,
  MSTD_PRDCD,
  MSTD_QTY,
  CASE
    WHEN CURRENT_DATE BETWEEN TPOH_TGLPO AND (TPOH_TGLPO + (SUP_JANGKAWAKTUKIRIMBARANG || ' days')::interval)
         AND TPOD_RECORDID IS NULL
         AND PBD_NOPB IS NOT NULL
      THEN 'Brg blm dikirim'
    WHEN TPOD_RECORDID IS NULL
         AND PBD_NOPB IS NOT NULL
         AND MSTD_QTY IS NULL
      THEN 'POmati/Kdlwarsa'
    WHEN TPOD_RECORDID = '1'
      THEN 'PO Alokasi/Mati'
    WHEN TPOD_RECORDID IN ('1','2')
         AND (TPOD_QTYPB = '0' OR TPOD_QTYPB IS NULL)
      THEN 'QTY BPB 0'
    WHEN PBD_NOPB IS NULL
         AND (TPOD_QTYPB = '0' OR TPOD_QTYPB IS NULL)
      THEN 'PO Alokasi/Mati'
    WHEN PBD_NOPB IS NULL
         AND (TPOD_QTYPB <> '0' OR TPOD_QTYPB IS NOT NULL)
         AND MSTH_NOPO IS NULL
      THEN 'PO Alokasi'
  END AS ket
FROM (
  SELECT *
  FROM (
    SELECT 
      PBD_NOPB,
      PBD_NOPO,
      PBH_TGLPB,
      PBD_PRDCD,
      PBD_KODESUPPLIER,
      PBD_QTYPB,
      PBH_KETERANGANPB
    FROM tbtr_pb_h pbh
    JOIN tbtr_pb_d pbd ON pbh.pbh_nopb = pbd.pbd_nopb
    WHERE PBD_PRDCD LIKE :kodePLU
  ) pb
  FULL JOIN (
    SELECT *
    FROM (
      SELECT 
        tpod_recordid,
        tpoh_nopo,
        tpoh_tglpo,
        tpoh_kodesupplier,
        tpod_prdcd,
        tpod_qtypo,
        tpod_qtypb,
        sup_jangkawaktukirimbarang
      FROM (
        SELECT 
          tpod_recordid,
          tpoh_nopo,
          tpoh_tglpo,
          tpoh_kodesupplier,
          tpod_prdcd,
          tpod_qtypo,
          tpod_qtypb
        FROM tbtr_po_h poh
        JOIN tbtr_po_d pod ON poh.tpoh_nopo = pod.tpod_nopo
        WHERE tpod_prdcd LIKE :kodePLU
      ) podh
      LEFT JOIN tbmaster_supplier s ON podh.tpoh_kodesupplier = s.sup_kodesupplier
    ) po
    LEFT JOIN (
      SELECT 
        msth_nodoc,
        msth_tgldoc,
        msth_nopo,
        mstd_prdcd,
        mstd_qty
      FROM tbtr_mstran_d mstd
      JOIN tbtr_mstran_h msth ON mstd.mstd_nodoc = msth.msth_nodoc
      WHERE mstd_recordid IS NULL
        AND mstd_typetrn = 'B'
    ) mst ON po.tpoh_nopo = mst.msth_nopo AND po.tpod_prdcd = mst.mstd_prdcd
  ) pomst
  ON pb.PBD_NOPO = pomst.tpoh_nopo AND pb.PBD_PRDCD = pomst.tpod_prdcd
) final
ORDER BY 9 DESC
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
  /* Atur lebar maksimum modal sesuai kebutuhan */
  .modal-dialog.custom-width {
    max-width: 75%; /* Bisa juga 100%, 1200px, dsb */
    width: 75%;
  }
</style>


<!-- Modal HTML -->
<div class="modal fade" id="modalInfoPb" tabindex="-1" role="dialog" aria-labelledby="modalInfoPbLabel" aria-hidden="true">
<div class="modal-dialog custom-width" role="document">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalInfoPbLabel">Detail Pb Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-md">
            <thead>
              <tr class="table-primary text-center text-nowrap">
                <th colspan="4">PB</th>
                <th colspan="2">PO</th>
                <th colspan="2">BPB</th>
              </tr>
              <tr class="text-nowrap">
                <th style="width: 5%;">No PB</th>
                <th style="width: 10%;">Tanggal PB</th>
                <th style="width: 8%;">Qty PB</th>
                <th style="width: 10%;">Keterangan PB</th>
                <th style="width: 10%;">No PO</th>
                <th style="width: 10%;">Tanggal PO</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr class="text-nowrap">
                    <td><?= htmlspecialchars($row['pbd_nopb']) ?></td>
                    <td><?= htmlspecialchars($row['pbh_tglpb']) ?></td>
                    <td class="text-end"><?= number_format($row['pbd_qtypb'], 0, '.', ',') ?></td>
                    <td><?= htmlspecialchars($row['pbh_keteranganpb']) ?></td>
                    <td><?= htmlspecialchars($row['tpoh_nopo']) ?></td>
                    <td><?= htmlspecialchars($row['tpoh_tglpo']) ?></td>
                    <td><?= htmlspecialchars($row['mstd_qty']) ?></td>
                    <td><?= htmlspecialchars($row['ket']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center">Data tidak ditemukan</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>


      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>
