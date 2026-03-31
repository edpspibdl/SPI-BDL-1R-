<?php
require_once '../helper/connection.php'; 

if (isset($_POST['pb'])) {
    $pb = $_POST['pb'];

    try {
        $query = "
            SELECT
                no_pb, kode_promo, tipe_promo, 
                cashback_order, cashback_real
            FROM promo_klikigr
            WHERE TO_CHAR(DATE(tgl_trans), 'DDMMYYYY') || no_trans = :pb
            ORDER BY kode_member DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':pb', $pb, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <style>
            .promo-container {
                font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                background-color: #f8fafc;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                overflow: hidden;
            }
            .promo-header {
                background-color: #f1f5f9;
                padding: 12px 20px;
                color: #475569;
                font-size: 14px;
                font-weight: 600;
                border-bottom: 1px solid #e2e8f0;
            }
            .promo-table {
                width: 100%;
                background-color: white;
                border-collapse: collapse;
            }
            .promo-table th {
                color: #94a3b8;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 15px 20px;
                text-align: left;
                border-bottom: 1px solid #f1f5f9;
            }
            .promo-table td {
                padding: 15px 20px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
            }
            .no-col { width: 50px; color: #94a3b8; font-size: 13px; }
            .pb-text { font-weight: 600; color: #334155; font-size: 13px; margin-bottom: 2px; }
            .promo-subtext { color: #94a3b8; font-size: 11px; text-transform: uppercase; }
            .val-order { color: #94a3b8; font-size: 13px; text-align: right; }
            .val-real { color: #4f46e5; font-weight: 600; font-size: 13px; text-align: right; }
            .val-selisih { color: #64748b; font-size: 13px; text-align: right; }
            
            .grand-total-section {
                background-color: #f8fafc;
                font-weight: 700;
                color: #475569;
                font-size: 13px;
            }
            .text-right { text-align: right; }
        </style>

        <div class="promo-container">
            <div class="promo-header">
                <i class="fas fa-percent" style="font-size: 12px; margin-right: 8px;"></i> Rincian Promo & Cashback
            </div>
            
            <table class="promo-table">
                <thead>
                    <tr>
                        <th class="no-col">NO</th>
                        <th>NO PB / PROMO</th>
                        <th class="text-right">ESTIMASI ORDER</th>
                        <th class="text-right">REALISASI</th>
                        <th class="text-right">SELISIH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_order = 0;
                    $total_real = 0;
                    $total_selisih = 0;

                    if ($rows):
                        $no = 1;
                        foreach ($rows as $row):
                            $selisih = $row['cashback_real'] - $row['cashback_order'];
                            $total_order += $row['cashback_order'];
                            $total_real += $row['cashback_real'];
                            $total_selisih += $selisih;
                    ?>
                        <tr>
                            <td class="no-col"><?= $no++ ?></td>
                            <td>
                                <div class="pb-text"><?= htmlspecialchars($row['no_pb']) ?></div>
                                <div class="promo-subtext"><?= htmlspecialchars($row['kode_promo']) ?> | <?= htmlspecialchars($row['tipe_promo']) ?></div>
                            </td>
                            <td class="val-order">Rp <?= number_format($row['cashback_order'], 0, ',', '.') ?></td>
                            <td class="val-real">Rp <?= number_format($row['cashback_real'], 0, ',', '.') ?></td>
                            <td class="val-selisih"><?= number_format($selisih, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                        
                        <tr class="grand-total-section">
                            <td colspan="2" style="text-align: right; padding-right: 50px; text-transform: uppercase; color: #64748b;">Grand Total</td>
                            <td class="text-right">Rp <?= number_format($total_order, 0, ',', '.') ?></td>
                            <td class="text-right" style="color: #4f46e5;">Rp <?= number_format($total_real, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($total_selisih, 0, ',', '.') ?></td>
                        </tr>

                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                PB ini tidak ada cashback.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
    } catch (PDOException $e) {
        echo "<div style='color:red; padding:20px;'>Error: " . $e->getMessage() . "</div>";
    }
}
?>