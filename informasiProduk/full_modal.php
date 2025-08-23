<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';

$data = [];

if ($kodePLU !== '') {
          try {
                    $stmt = $conn->prepare("SELECT 
    pm.PRD_KODEDIVISI,
    pm.PRD_KODEDEPARTEMENT,
    pm.PRD_PRDCD,
    pm.PRD_DESKRIPSIPANJANG,
    pm.PRD_UNIT,
    pm.PRD_FRAC,
    COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) AS PRD_HRGJUAL,  -- harga jual pakai promo jika ada
    pm.PRD_KODETAG,
    pc.PRC_KODETAG,
    pm.PRD_FLAG_AKTIVASI,
    pm.PRD_AVGCOST,
    pm.PRD_LASTCOST,
    pm.PRD_MINJUAL,
    md.PRMD_HRGJUAL,
    md.PRMD_TGLAWAL,
    md.PRMD_TGLAKHIR,
    bc.BRC_BARCODE,

    -- Hitung margin berdasarkan flag BKP pakai harga promo jika ada
    CASE 
        WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
            THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - (pm.PRD_AVGCOST * 1.11)) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
        WHEN pm.PRD_FLAGBKP1 IS NULL AND pm.PRD_FLAGBKP2 IN ('N','C') 
            THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) * 100)
    END AS MARGIN,

    ROUND(
        CASE         
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100   
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
            WHEN pm.PRD_FLAGBKP1 = 'N' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_AVGCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100    
        END, 2
    ) AS MARGINACOST, 

    ROUND(
        CASE         
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST * 1.11) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
            WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100  
            WHEN pm.PRD_FLAGBKP1 = 'N' 
                THEN ((COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) - pm.PRD_LASTCOST) / COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)) * 100        
        END, 2
    ) AS MARGINLCOST,

    CASE 
        WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN 1.11
        WHEN pm.PRD_FLAGBKP1 = 'N' AND pm.PRD_FLAGBKP2 IN ('N','C') THEN 1
    END AS KALI,

    -- ST_HARGA_NETTO dari harga jual promo jika ada
    CASE 
        WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
            THEN COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL) / 11.1 * 10
        ELSE COALESCE(md.PRMD_HRGJUAL, pm.PRD_HRGJUAL)
    END AS ST_HARGA_NETTO,

    -- ST_MD_NETTO dari md.PRMD_HRGJUAL jika ada
    CASE 
        WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
            THEN md.PRMD_HRGJUAL / 11.1 * 10
        ELSE md.PRMD_HRGJUAL
    END AS ST_MD_NETTO,

    CASE 
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '0' THEN '0'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '1' THEN '1'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '2' THEN '2'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '3' THEN '3'
        ELSE NULL
    END AS sj,

    pm.PRD_FLAGBKP1,
    pm.PRD_FLAGBKP2

FROM tbmaster_prodmast pm

LEFT JOIN (
    SELECT prmd_prdcd, prmd_hrgjual, prmd_tglawal, prmd_tglakhir
    FROM tbtr_promomd
    WHERE CURRENT_DATE BETWEEN prmd_tglawal AND prmd_tglakhir
) md ON pm.prd_prdcd = md.prmd_prdcd

LEFT JOIN tbmaster_prodcrm pc ON pm.prd_prdcd = pc.prc_pluigr

LEFT JOIN (
    SELECT DISTINCT ON (brc_prdcd) brc_prdcd, brc_barcode
    FROM tbmaster_barcode
    ORDER BY brc_prdcd, brc_barcode
) bc ON pm.prd_prdcd = bc.brc_prdcd

WHERE pm.PRD_PRDCD LIKE :kodePLU

ORDER BY pm.PRD_MINJUAL, pm.PRD_AVGCOST DESC
        ");
                    $kodePLU = substr($kodePLU, 0, 6) . '%'; // modifikasi sebelum bind
                    $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
                    die("Query gagal: " . $e->getMessage());
          }
}
?>

<!-- Modal HTML -->
<!-- Modal HTML -->
<div class="modal fade" id="modalInfoFull" tabindex="-1" role="dialog" aria-labelledby="modalInfoFullLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                              <!-- Modal Header -->
                              <div class="modal-header">
                                        <h5 class="modal-title mb-0" id="modalInfoFullLabel">Detail Lokasi Produk</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                                                  <span aria-hidden="true">&times;</span>
                                        </button>
                              </div>
                              <!-- Modal Body -->
                              <div class="modal-body">
                                        <div class="table-responsive">
                                                  <table class="table table-bordered table-hover table-sm">
                                                            <thead class="table-primary text-center">
                                                                      <tr>
                                                                                <th>Satuan / Frac</th>
                                                                                <th>Barcode</th>
                                                                                <th>Harga Jual</th>
                                                                                <th>LCost</th>
                                                                                <th>ACost</th>
                                                                                <th>Margin Lcost</th>
                                                                                <th>Margin Acost</th>
                                                                                <th>Tag</th>
                                                                                <th>Act</th>
                                                                                <th>MinJual</th>
                                                                                <th>Flag1</th>
                                                                                <th>Flag2</th>
                                                                      </tr>
                                                            </thead>
                                                            <tbody>
                                                                      <?php if (!empty($data)): ?>
                                                                                <?php foreach ($data as $row): ?>
                                                                                          <tr>
                                                                                                    <td><?= htmlspecialchars(($row['prd_unit'] ?? '') . ' / ' . ($row['prd_frac'] ?? '')) ?></td>
                                                                                                    <td><?= htmlspecialchars($row['brc_barcode'] ?? '') ?></td>
                                                                                                    <td class="text-end"><?= number_format($row['prd_hrgjual'] ?? 0) ?></td>
                                                                                                    <td class="text-end"><?= number_format($row['prd_lastcost'] ?? 0) ?></td>
                                                                                                    <td class="text-end"><?= number_format($row['prd_avgcost'] ?? 0) ?></td>
                                                                                                    <td class="text-end"><?= number_format($row['marginlcost'] ?? 0, 2) ?>%</td>
                                                                                                    <td class="text-end"><?= number_format($row['marginacost'] ?? 0, 2) ?>%</td>
                                                                                                    <td class="text-center"><?= htmlspecialchars($row['prd_kodetag'] ?? '') ?></td>
                                                                                                    <td class="text-center"><?= htmlspecialchars($row['prd_flag_aktivasi'] ?? '') ?></td>
                                                                                                    <td class="text-end"><?= htmlspecialchars($row['prd_minjual'] ?? '') ?></td>
                                                                                                    <td class="text-center"><?= htmlspecialchars($row['prd_flagbkp1'] ?? '') ?></td>
                                                                                                    <td class="text-center"><?= htmlspecialchars($row['prd_flagbkp2'] ?? '') ?></td>
                                                                                          </tr>
                                                                                <?php endforeach; ?>
                                                                      <?php else: ?>
                                                                                <tr>
                                                                                          <td colspan="12" class="text-center text-muted">Data tidak ditemukan</td>
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