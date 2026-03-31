<?php
require_once '../helper/connection.php'; 

if (isset($_POST['pb'])) {
    $pb = $_POST['pb'];

    try {
        $query = "
        SELECT
            PK_D, OBI_PRDCD, PRD_DESKRIPSIPANJANG, OBI_HARGASATUAN,
            OBI_QTYORDER, OBI_QTYREALISASI,
            OBI_QTYORDER * OBI_HARGASATUAN AS RPH_ORDER_DET,
            OBI_QTYREALISASI * OBI_HARGASATUAN AS RPH_REAL_DET
        FROM (
            SELECT
                TO_CHAR(DATE(obi_tgltrans), 'DDMMYYYY') || OBI_NOTRANS AS PK_D,
                OBI_PRDCD, PRD_DESKRIPSIPANJANG, OBI_QTYORDER,
                COALESCE(OBI_QTYREALISASI, 0) AS OBI_QTYREALISASI,
                OBI_HARGASATUAN + OBI_PPN AS OBI_HARGASATUAN
            FROM tbtr_obi_d
            LEFT JOIN tbmaster_prodmast ON prd_prdcd = obi_prdcd
        ) AS subquery
        WHERE PK_D = :pb";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':pb', $pb, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            ?>
            <style>
                .detail-container {
                    background: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
                    border: 1px solid #edf2f7;
                }
                .detail-header {
                    background: #f8fafc;
                    padding: 20px 25px;
                    border-bottom: 1px solid #edf2f7;
                }
                .table-modern {
                    margin-bottom: 0;
                }
                .table-modern thead th {
                    background: #ffffff;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                    letter-spacing: 0.05em;
                    font-weight: 700;
                    color: #64748b;
                    border-top: none;
                    border-bottom: 2px solid #f1f5f9;
                    padding: 15px 25px;
                }
                .table-modern tbody td {
                    padding: 18px 25px;
                    vertical-align: middle;
                    color: #334155;
                    font-size: 0.9rem;
                    border-bottom: 1px solid #f1f5f9;
                }
                .plu-badge {
                    background: #f1f5f9;
                    color: #475569;
                    padding: 4px 8px;
                    border-radius: 6px;
                    font-family: 'Monaco', monospace;
                    font-size: 0.8rem;
                }
                .text-amount {
                    font-family: 'Inter', sans-serif;
                    font-weight: 600;
                }
                .footer-summary {
                    background: #f8fafc;
                    padding: 20px 25px;
                }
                .total-label {
                    color: #64748b;
                    font-size: 0.85rem;
                    font-weight: 500;
                }
                .total-value {
                    color: #0f172a;
                    font-size: 1.25rem;
                    font-weight: 700;
                }
            </style>

            <div class="detail-container">
                <div class="detail-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1" style="font-weight: 700; color: #1e293b;">Detail Pengiriman</h6>
                        <small class="text-muted">Nomor Transaksi: <strong><?php echo htmlspecialchars($pb); ?></strong></small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary shadow-sm" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Cetak Laporan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th class="text-right">Harga</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $total_order = 0;
                            $total_real = 0;
                            foreach ($rows as $row): 
                                $total_order += $row['rph_order_det'];
                                $total_real += $row['rph_real_det'];
                            ?>
                            <tr>
                                <td width="50" class="text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <span class="plu-badge mb-1 d-inline-block"><?php echo htmlspecialchars($row['obi_prdcd']); ?></span>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($row['prd_deskripsipanjang']); ?></div>
                                </td>
                                <td class="text-right text-amount">
                                    Rp <?php echo number_format($row['obi_hargasatuan'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="px-2 text-center">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Order</small>
                                            <span class="font-weight-bold"><?php echo number_format($row['obi_qtyorder']); ?></span>
                                        </div>
                                        <div style="width: 1px; height: 20px; background: #e2e8f0;"></div>
                                        <div class="px-2 text-center">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Real</small>
                                            <span class="text-primary font-weight-bold"><?php echo number_format($row['obi_qtyrealisasi']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right text-amount text-primary">
                                    Rp <?php echo number_format($row['rph_real_det'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="footer-summary border-top">
                    <div class="row justify-content-end text-right">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="total-label">Total Estimasi Order</span>
                                <span class="text-muted">Rp <?php echo number_format($total_order, 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="total-label">Total Realisasi</span>
                                <span class="total-value text-primary">Rp <?php echo number_format($total_real, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            echo "<div class='alert alert-light border text-center p-5'>
                    <i class='fas fa-box-open fa-3x text-muted mb-3 d-block'></i>
                    <span class='text-muted'>Data detail tidak ditemukan untuk transaksi ini.</span>
                  </div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>