    <?php
    require_once '../layout/_top.php';
    require_once '../helper/connection.php'; 
    require_once __DIR__ . '/get_sales_monitoring.php';
    ?>

    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>MONITORING SALESMAN</h1>
            <a href="../LaporanLaporan/index.php" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> BACK</a>
        </div>

        <div class="section-body">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row align-items-end">
                        <div class="col-md-3">
                            <label class="font-weight-bold text-small">Tanggal Transaksi</label>
                            <input type="date" name="tgl" class="form-control" value="<?= $tgl_filter ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold text-small">Wilayah Kecamatan</label>
                            <select name="kecamatan" class="form-control selectric">
                                <option value="ALL">SEMUA KECAMATAN</option>
                                <?php foreach ($list_kecamatan as $kec): ?>
                                    <option value="<?= $kec ?>" <?= ($kec_filter == $kec) ? 'selected' : '' ?>><?= $kec ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> FILTER</button>
                            <a href="index.php" class="btn btn-light ml-1">RESET</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-dark text-white"><i class="fas fa-bullseye"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Target</h4></div>
                            <div class="card-body" style="font-size: 14px;">Rp <?= number_format($target_statis, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-primary text-white"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Rupiah Order</h4></div>
                            <div class="card-body" style="font-size: 14px;">Rp <?= number_format($total_order, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-info text-white"><i class="fas fa-file-invoice"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>PB Masuk</h4></div>
                            <div class="card-body" style="font-size: 14px;"><?= number_format($total_pb_masuk, 0, ',', '.') ?> <small>PB</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1 shadow-sm border-bottom border-danger">
                        <div class="card-icon bg-danger text-white"><i class="fas fa-times-circle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>PB Batal</h4></div>
                            <div class="card-body" style="font-size: 14px;"><?= number_format($total_pb_batal, 0, ',', '.') ?> <small>PB</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-warning text-white"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-wrap">
                            <div class="card-header"><h4>Potential Loss</h4></div>
                            <div class="card-body" style="font-size: 14px;">Rp <?= number_format($total_loss, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-4 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h4>Kontribusi Order</h4>
                            <span class="badge badge-primary">
                                <i class="fas fa-map-marker-alt"></i> <?= ($kec_filter == 'ALL') ? 'Semua' : $kec_filter ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="350"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header border-bottom bg-whitesmoke">
                            <h4>Leaderboard Performa Salesman</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-md" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>Salesman</th>
                                            <th class="text-center">Jml PB</th>
                                            <th>Target PB</th>
                                            <th>Real PB</th>
                                            <th>Ach %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td><strong><?= $row['nama_salesman'] ?></strong></td>
                                            <td class="text-center"><span class="badge badge-info"><?= $row['total_pb'] ?></span></td>
                                            <td>Rp <?= number_format($row['nominal_order'], 0, ',', '.') ?></td>
                                            <td class="text-success font-weight-bold">Rp <?= number_format($row['nominal_real'], 0, ',', '.') ?></td>
                                            <td>
                                                <div class="progress" style="height: 10px;" data-toggle="tooltip" title="<?= $row['achieve_pct'] ?>%">
                                                    <div class="progress-bar <?= $row['achieve_pct'] < 80 ? 'bg-danger' : 'bg-success' ?>" 
                                                        style="width: <?= $row['achieve_pct'] ?>%"></div>
                                                </div>
                                                <small class="font-weight-bold"><?= $row['achieve_pct'] ?>%</small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Order PB',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: ['#6777ef', '#63ed7a', '#ffa426', '#fc544b', '#34395e', '#fb167f', '#45e3ff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: {
                        display: true,
                        text: 'Wilayah: <?= ($kec_filter == 'ALL') ? 'Semua Kecamatan' : $kec_filter ?>',
                        font: { size: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    </script>

    <?php require_once '../layout/_bottom.php'; ?>