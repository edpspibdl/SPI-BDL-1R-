<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$tanggalMulai = $_GET['tanggalMulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggalSelesai'] ?? date('Y-m-d');
$pluInput = $_GET['plu'] ?? '';
$plu = str_pad(ltrim($pluInput, '0'), 7, '0', STR_PAD_LEFT);

$whereClause = "WHERE DATE_TRUNC('DAY', RSO_TGLSO) BETWEEN :tanggalMulai AND :tanggalSelesai";
$params = [
    ':tanggalMulai' => $tanggalMulai,
    ':tanggalSelesai' => $tanggalSelesai,
];

if (!empty($pluInput)) {
    $whereClause .= " AND RSO_PRDCD LIKE :pluFilter";
    $params[':pluFilter'] = $plu . '%';
}

$query = "SELECT
    RSO_TGLSO,
    PRD_KODEDIVISI,
    PRD_KODEDEPARTEMENT,
    PRD_KODEKATEGORIBARANG,
    RSO_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_UNIT,
    PRD_FRAC,
    PRD_KODETAG,
    RSO_QTYSO,
    RSO_QTYPLANO,
    RSO_QTYLPP,
    RSO_CREATE_BY,
    RSO_QTYRESET,
    RSO_AVGCOSTRESET,
    RSO_QTYRESET * RSO_AVGCOSTRESET AS RPH_RESET
FROM TBTR_RESET_SOIC
LEFT JOIN TBMASTER_PRODMAST ON RSO_PRDCD = PRD_PRDCD
{$whereClause}
ORDER BY RSO_TGLSO";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Styles -->
<style>
    #table-1 {
        width: 100%;
        border-collapse: collapse;
    }

    #table-1 th,
    #table-1 td {
        padding: 8px;
        border: 1px solid #ddd;
        vertical-align: top;
    }

    #table-1 th {
        background-color: rgb(220, 236, 252);
        text-align: center;
        white-space: nowrap;
    }

    .text-right {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-left {
        text-align: left !important;
    }

    #table-1 tfoot td {
        font-weight: bold;
        background: #f0f0f0;
    }

    .nama-column {
        max-width: 300px;
        word-wrap: break-word;
    }
</style>

<!-- Content -->
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Cek Order PLU</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="tanggalMulai">Tanggal Mulai</label>
                        <input type="date" id="tanggalMulai" name="tanggalMulai" value="<?= htmlspecialchars($tanggalMulai) ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggalSelesai">Tanggal Selesai</label>
                        <input type="date" id="tanggalSelesai" name="tanggalSelesai" value="<?= htmlspecialchars($tanggalSelesai) ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="plu">PLU</label>
                        <input type="text" id="plu" name="plu" value="<?= htmlspecialchars($pluInput) ?>" class="form-control" placeholder="147569">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Display -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>TANGGAL SO</th>
                                    <th>DIV</th>
                                    <th>DEPT</th>
                                    <th>KATB</th>
                                    <th>PLU</th>
                                    <th>DESKRIPSI</th>
                                    <th>UNIT</th>
                                    <th>FRAC</th>
                                    <th>TAG</th>
                                    <th>QTY SO</th>
                                    <th>QTY PLANO</th>
                                    <th>QTY LPP</th>
                                    <th>ID</th>
                                    <th>QTY RESET</th>
                                    <th>ACOST RESET</th>
                                    <th>RPH SELISIH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($data)) :
                                    echo "<tr><td colspan='17' class='text-center'>Tidak ada data</td></tr>";
                                else :
                                    $no = 1;
                                    $sum_qtyreset = 0;
                                    $sum_rphreset = 0;
                                    foreach ($data as $row) :
                                        $sum_qtyreset += $row['rso_qtyreset'];
                                        $sum_rphreset += $row['rph_reset'];
                                ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['rso_tglso']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_kodedivisi']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_kodedepartement']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_kodekategoribarang']) ?></td>
                                            <td><?= htmlspecialchars($row['rso_prdcd']) ?></td>
                                            <td class="nama-column"><?= htmlspecialchars($row['prd_deskripsipanjang']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_unit']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_frac']) ?></td>
                                            <td><?= htmlspecialchars($row['prd_kodetag']) ?></td>
                                            <td class="text-right"><?= number_format($row['rso_qtyso'], 0, ".", ",") ?></td>
                                            <td class="text-right"><?= number_format($row['rso_qtyplano'], 0, ".", ",") ?></td>
                                            <td class="text-right"><?= number_format($row['rso_qtylpp'], 0, ".", ",") ?></td>
                                            <td><?= htmlspecialchars($row['rso_create_by']) ?></td>
                                            <td class="text-right"><?= number_format($row['rso_qtyreset'], 0, ".", ",") ?></td>
                                            <td class="text-right"><?= number_format($row['rso_avgcostreset'], 0, ".", ",") ?></td>
                                            <td class="text-right"><?= number_format($row['rph_reset'], 0, ".", ",") ?></td>
                                        </tr>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </tbody>
                        </table>

                        <?php if (!empty($data)) : ?>
                            <br />
                            <table class="table table-bordered w-50">
                                <thead>
                                    <tr>
                                        <th colspan="2">REKAP SO IC : <?= htmlspecialchars($plu) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>TOTAL QTY RESET</td>
                                        <td class="text-right"><?= number_format($sum_qtyreset, 0, ".", ",") ?></td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL RUPIAH RESET</td>
                                        <td class="text-right"><?= number_format($sum_rphreset, 0, ".", ",") ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<!-- DataTables JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#table-1').DataTable({
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'History_Produk_' + new Date().toISOString().split('T')[0],
                    title: null
                },
            ]
        });

        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>