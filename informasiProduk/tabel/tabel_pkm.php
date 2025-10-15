 <!-- Card PKM -->
                <div class="card mb-4">
                    <h4 class="text-center mb-2 mt-2">PKM</h4>
                    <div class="card-body p-2">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th rowspan="2">DSI</th>
                                    <th rowspan="2">TO</th>
                                    <th rowspan="2">TOP</th>
                                    <th colspan="2">PKMT</th>
                                    <th colspan="2">MINOR</th>
                                    <th colspan="2">MINDIS</th>
                                </tr>
                                <tr>
                                    <th>QTY</th>
                                    <th>TO</th>
                                    <th>QTY</th>
                                    <th>TO</th>
                                    <th>QTY</th>
                                    <th>TO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pkm)): ?>
                                    <?php foreach ($pkm as $row): ?>
                                        <tr>
                                            <td><?= number_format($row['dsi'], 0, '.', ',') ?></td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td><?= number_format($row['pkm_pkmt'], 0, '.', ',') ?></td>
                                            <td>0</td>
                                            <td><?= number_format($row['pkm_minorder'], 0, '.', ',') ?></td>
                                            <td>0</td>
                                            <td><?= number_format($row['pkm_mindisplay'], 0, '.', ',') ?></td>
                                            <td>0</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9">Data tidak ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>