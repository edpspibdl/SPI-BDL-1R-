<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil parameter dari URL atau pakai default
$tanggalMulai = $_GET['tanggalMulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggalSelesai'] ?? date('Y-m-d');

// Normalisasi input PLU: buang nol depan, lalu lengkapi jadi 7 digit
$pluInput = $_GET['plu'] ?? '';
$plu = str_pad(ltrim($pluInput, '0'), 7, '0', STR_PAD_LEFT);

// Query SQL
$query = "
SELECT
    CASE
        WHEN H.OBI_RECID IS NULL THEN 'Siap Send HH'
        WHEN H.OBI_RECID = '1' THEN 'Siap Picking'
        WHEN H.OBI_RECID = '2' THEN 'Siap Packing'
        WHEN H.OBI_RECID = '3' THEN 'Siap Draft Struk'
        WHEN H.OBI_RECID = '4' THEN 'Siap Konf. Pembayaran'
        WHEN H.OBI_RECID = '5' THEN 'Siap Struk'
        WHEN H.OBI_RECID = '6' THEN 'Selesai Struk'
        WHEN H.OBI_RECID = '7' THEN 'Set Ongkir'
        ELSE 'Pembatalan / Expired'
    END AS STATUS,
    H.OBI_NOPB nopb,
    H.OBI_TGLPB AS TGL_PB,
    H.OBI_TGLSTRUK AS TGL_STRUK,
    H.OBI_KDMEMBER || ' - ' || C.cus_namamember AS MEMBER,
    D.obi_prdcd AS PLU,
    PRD.prd_deskripsipanjang AS NAMA_BARANG,
    D.obi_qtyorder AS QTY_ORDER,
    D.obi_qtyrealisasi AS QTY_REALISASI,
    COALESCE(P.TIPE_BAYAR_AGGREGATED, 'TUNAI') AS TIPE_BAYAR
FROM tbtr_obi_h H
LEFT JOIN tbmaster_customer C ON C.cus_kodemember = H.OBI_KDMEMBER
LEFT JOIN tbtr_obi_d D ON D.obi_notrans = H.obi_notrans AND D.obi_tgltrans = H.obi_tgltrans
LEFT JOIN tbmaster_prodmast PRD ON D.obi_prdcd = PRD.prd_prdcd
LEFT JOIN (
    SELECT
        NO_PB,
        STRING_AGG(TIPE_BAYAR, ', ' ORDER BY TIPE_BAYAR) AS TIPE_BAYAR_AGGREGATED
    FROM PAYMENT_KLIKIGR
    GROUP BY NO_PB
) P ON P.NO_PB = H.OBI_NOPB
WHERE
    D.obi_prdcd LIKE :pluFilter
    AND H.obi_tglpb BETWEEN :tanggalMulai AND :tanggalSelesai
ORDER BY H.OBI_NOPB ASC
";

$stmt = $conn->prepare($query);
$stmt->execute([
    ':pluFilter' => $plu . '%',
    ':tanggalMulai' => $tanggalMulai,
    ':tanggalSelesai' => $tanggalSelesai,
]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Tabel Umum */
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

    /* Header Tabel */
    #table-1 th {
        border-bottom: none !important;
        background-color: rgb(220, 236, 252);
        font-weight: bold;
        border-bottom: 2px solid #333;
        text-align: center;
        white-space: nowrap;
    }

    /* Kolom Nama Produk */
    #table-1 .nama-column {
        word-wrap: break-word;
        white-space: normal;
        max-width: 300px;
        text-align: left !important;
    }


    /* Tombol DataTables */
    .dt-buttons {
        margin-bottom: 10px;
    }

    /* Align Utilities */
    .text-right {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-left {
        text-align: left !important;
    }

    /* Footer Total */
    #table-1 tfoot tr,
    #table-1 tfoot td {
        font-weight: bold;
        background: #f0f0f0;
    }

    /* Kolom Nama Produk auto lebar */
    .fleksibel {
        white-space: nowrap;
        width: auto !important;
        max-width: none !important;
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Cek Order PLU</h1>
    </div>

    <!-- ✅ Card Wrapper -->
    <div class="card mb-4">
        <div class="card-body">
            <!-- ✅ Form Filter -->
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggalMulai">Tanggal Mulai</label>
                            <input type="date" id="tanggalMulai" name="tanggalMulai" value="<?= htmlspecialchars($tanggalMulai) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggalSelesai">Tanggal Selesai</label>
                            <input type="date" id="tanggalSelesai" name="tanggalSelesai" value="<?= htmlspecialchars($tanggalSelesai) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="plu">PLU</label>
                            <input type="text" id="plu" name="plu" value="<?= htmlspecialchars($pluInput) ?>" class="form-control" placeholder="147569">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ Tabel -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>No PB</th>
                                    <th>Tanggal PB</th>
                                    <th>Tanggal Struk</th>
                                    <th>Member</th>
                                    <th>PLU</th>
                                    <th>Nama Barang</th>
                                    <th>Qty Order</th>
                                    <th>Qty Realisasi</th>
                                    <th>Tipe Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data)) : ?>
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data</td>
                                    </tr>
                                    <?php else :
                                    $no = 1;
                                    foreach ($data as $row) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="fleksibel"><?= htmlspecialchars($row['status']) ?></td>
                                            <td><?= htmlspecialchars($row['nopb']) ?></td>
                                            <td class="fleksibel"><?= htmlspecialchars($row['tgl_pb']) ?></td>
                                            <td class="fleksibel"><?= htmlspecialchars($row['tgl_struk']) ?></td>
                                            <td class="fleksibel"><?= htmlspecialchars($row['member']) ?></td>
                                            <td><?= htmlspecialchars($row['plu']) ?></td>
                                            <td class="fleksibel"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                            <td align="right"><?= number_format($row['qty_order']) ?></td>
                                            <td align="right"><?= number_format($row['qty_realisasi']) ?></td>
                                            <td><?= htmlspecialchars($row['tipe_bayar']) ?></td>
                                        </tr>
                                <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#table-1').DataTable({
            responsive: false,
            lengthMenu: [10, 25, 50, 100],
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
            ],
            dom: 'Bfrtip'
        });

        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>