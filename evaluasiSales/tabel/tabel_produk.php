<link rel="stylesheet" href="./style/tabel.css">
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Sales Per Produk</h3>
        <a href="../salesPromo/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead class="text-white" style="background-color: #7bcbf0ff;">
                                <tr>
                                    <th>#</th>
                                    <th>D</th>
                                    <th>Dp</th>
                                    <th>Kb</th>
                                    <th>PLU</th>
                                    <th>Nama Barang</th>
                                    <th>Kunjungan</th>
                                    <th>Member</th>
                                    <th>Struk</th>
                                    <th class="text-nowrap">Qty in pcs</th>
                                    <th>Gross</th>
                                    <th>Netto</th>
                                    <th>Margin</th>
                                    <th>Persen</th>
                                    <th>PKMT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row) :
                                    $noUrut++;

                                    // Calculate totals
                                    $totalKunjungan += $row['kunjungan'];
                                    $totalMember += $row['jml_member'];
                                    $totalStruk += $row['struk'];
                                    $totalQtyInPcs += $row['qty_in_pcs'];
                                    $totalGross += $row['dtl_gross'];
                                    $totalNetto += $row['dtl_netto'];
                                    $totalMargin += $row['dtl_margin'];
                                ?>
                                    <tr>
                                        <td align="right"><?= $noUrut ?></td>
                                        <td align="center"><?= htmlspecialchars($row['dtl_k_div']) ?></td>
                                        <td align="center"><?= htmlspecialchars($row['dtl_k_dept']) ?></td>
                                        <td align="center"><?= htmlspecialchars($row['dtl_k_katb']) ?></td>
                                        <td align="center"><?= htmlspecialchars($row['dtl_prdcd_ctn']) ?></td>
                                        <td align="left"><?= htmlspecialchars($row['dtl_nama_barang']) ?></td>
                                        <td align="right"><?= number_format($row['kunjungan'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['jml_member'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['struk'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['qty_in_pcs'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['dtl_gross'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['dtl_netto'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['dtl_margin'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['dtl_margin_persen'], 2, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['pkm_pkmt'], 2, '.', ',') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" align="right"><strong>Total</strong></td>
                                    <?php
                                    $totalNetto_calc = $totalNetto == 0 ? 1 : $totalNetto; // Avoid division by zero for total margin %
                                    $totalMarginPersen = ($totalNetto > 0) ? ($totalMargin / $totalNetto * 100) : 0;
                                    ?>
                                    <td align="right"><strong><?= number_format($totalKunjungan, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMember, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalStruk, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalQtyInPcs, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalGross, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalNetto, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMargin, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMarginPersen, 2, '.', ',') ?></strong></td>
                                    <td align="right"></td> </tr>

                                <tr>
                                    <td colspan="6" align="right"><strong>Average per Produk</strong></td>
                                    <?php
                                    $noUrut_calc = $noUrut == 0 ? 1 : $noUrut; // Avoid division by zero for averages
                                    ?>
                                    <td align="right"><strong><?= number_format($totalKunjungan / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMember / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalStruk / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalQtyInPcs / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalGross / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalNetto / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMargin / $noUrut_calc, 0, '.', ',') ?></strong></td>
                                    <td align="right"><strong><?= number_format($totalMarginPersen, 2, '.', ',') ?></strong></td>
                                    <td align="right"></td> </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>