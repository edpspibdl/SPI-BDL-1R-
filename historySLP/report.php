<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Normalisasi input PLU: buang nol depan, lalu lengkapi jadi 7 digit
$pluInput = $_GET['plu'] ?? '';
$pluFilterValue = '';

if (!empty($pluInput)) {
    // Jika PLU diisi, normalisasi dan tambahkan wildcard di belakang
    $pluFilterValue = str_pad(ltrim($pluInput, '0'), 7, '0', STR_PAD_LEFT) . '%';
} else {
    // Jika PLU kosong, kita ingin mencari semua. Kirim wildcard '%' sebagai filter.
    $pluFilterValue = '%';
}

// Query SQL
// Sekarang, WHERE clause menjadi lebih sederhana karena :pluFilter akan SELALU berupa string dengan wildcard
$query = "SELECT
    SLP_KODERAK || '.' || SLP_KODESUBRAK || '.' || SLP_TIPERAK || '.' || SLP_SHELVINGRAK || '.' || SLP_NOURUT AS ALAMAT,
    SLP_PRDCD,
    SLP_DESKRIPSI,
    SLP_UNIT || '/' || SLP_FRAC AS SATUAN,
    SLP_QTYCRT,
    SLP_QTYPCS,
    SLP_EXPDATE,
    TO_CHAR(SLP_CREATE_DT, 'DD-MON-YY HH24:MI:SS') AS SLP_CREATE_DT,
    SLP_CREATE_DT AS ORDERTGL,
    SLP_CREATE_BY,
    COALESCE(TO_CHAR(SLP_MODIFY_DT, 'DD-MON-YY HH24:MI:SS'), '-') AS SLP_MODIFY_DT,
    COALESCE(SLP_MODIFY_BY || ' | ' || USERNAME, '-') AS SLP_MODIFY_BY,
    SLP_TIPE,
    SLP_JENIS,
    SLP_KODETOKO
FROM
    TBTR_SLP
LEFT JOIN
    TBMASTER_USER ON SLP_MODIFY_BY = USERID
WHERE
    SLP_PRDCD LIKE :pluFilter -- WHERE clause sekarang sederhana
ORDER BY
    ORDERTGL DESC
";

try {
    $stmt = $conn->prepare($query);

    // Sekarang, kita selalu bind sebagai string
    $stmt->bindValue(':pluFilter', $pluFilterValue, PDO::PARAM_STR);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tampilkan pesan error yang lebih informatif jika terjadi kesalahan database
    echo "<div class='alert alert-danger' role='alert'>Error mengambil data: " . $e->getMessage() . "</div>";
    exit; // Hentikan eksekusi script lebih lanjut
}
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

    /* Kolom Deskripsi (sebelumnya Nama Produk) */
    #table-1 .description-column {
        /* Ubah nama kelas agar lebih spesifik */
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

    /* Kolom fleksibel untuk menghindari overflow */
    .fleksibel {
        white-space: nowrap;
        width: auto !important;
        max-width: none !important;
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Cek Order PLU <?= !empty($pluInput) ? '(PLU: ' . htmlspecialchars($pluInput) . ')' : '(Semua PLU)' ?></h1>
        <a href="form_report.php" class="btn btn-primary">Kembali ke Form</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> ALAMAT </th>
                                    <th> PLU </th>
                                    <th> DESKRIPSI </th>
                                    <th> SATUAN </th>
                                    <th> CTN </th>
                                    <th> PCS </th>
                                    <th> EXP </th>
                                    <th> CREATE_DT </th>
                                    <th> DIBUAT </th>
                                    <th> MODIFY_DT </th>
                                    <th> REALISASI </th>
                                    <th> TIPE </th>
                                    <th> JENIS </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data)) : ?>
                                    <tr>
                                        <td colspan="14" class="text-center">Tidak ada data ditemukan untuk PLU ini.</td>
                                    </tr>
                                    <?php else :
                                    $no = 1;
                                    foreach ($data as $row) : ?>
                                        <tr class="text-nowrap">
                                            <td> <?= $no++ ?> </td>
                                            <td> <?= htmlspecialchars($row["alamat"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_prdcd"]) ?> </td>
                                            <td class="description-column"> <?= htmlspecialchars($row["slp_deskripsi"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["satuan"]) ?> </td>
                                            <td class="text-right"> <?= number_format($row["slp_qtycrt"], 0, ',', '.') ?> </td>
                                            <td class="text-right"> <?= number_format($row["slp_qtypcs"], 0, ',', '.') ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_expdate"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_create_dt"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_create_by"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_modify_dt"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_modify_by"]) ?> </td>
                                            <td> <?= htmlspecialchars($row["slp_tipe"]) ?> </td>
                                            <td>
                                                <?= htmlspecialchars($row["slp_jenis"]) == 'O' ? 'OTOMATIS' : (htmlspecialchars($row["slp_jenis"]) == 'M' ? 'MANUAL' : htmlspecialchars($row["SLP_JENIS"])); ?>
                                            </td>
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
            responsive: false, // Atur ke true jika ingin tabel menyesuaikan lebar layar
            lengthChange: true, // Memungkinkan perubahan jumlah entri per halaman
            lengthMenu: [10, 25, 50, 100], // Opsi jumlah entri
            buttons: [{
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'History_Produk_<?= !empty($pluInput) ? htmlspecialchars($pluInput) : "All" ?>_' + new Date().toISOString().split('T')[0],
                    title: null
                },
            ],
            dom: 'Bfrtip' // 'B' for buttons, 'f' for filter, 'r' for processing, 't' for table, 'i' for info, 'p' for pagination
        });

        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>