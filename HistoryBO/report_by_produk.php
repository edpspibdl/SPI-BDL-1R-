<?php
// FILE: report_by_produk.php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$tanggalMulai = $_GET['tanggalMulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggalSelesai'] ?? date('Y-m-d');
$jenisTransaksi = $_GET['jenisTransaksi'] ?? 'All';

// Query dengan agregasi
$query = "SELECT
    trn_type,
    trn_div,
    trn_dept,
    trn_katb,
    trn_prdcd,
    trn_nama_barang,
    trn_unit,
    trn_frac,
    trn_tag,
    SUM(trn_qty) AS trn_qty,
    SUM(trn_qty_bonus1 + trn_qty_bonus2) AS trn_qty_bonus,
    SUM(trn_gross) AS trn_gross,
    SUM(trn_discount) AS trn_discount,
    SUM(trn_ppn) AS trn_ppn,
    MIN(trn_kode_supplier) AS trn_kode_supplier,
    MIN(trn_nama_supplier) AS trn_nama_supplier,
    keterangan
FROM
    (
        SELECT
            m.mstd_typetrn             AS trn_type,
            m.mstd_tgldoc              AS trn_tgldoc,
            m.mstd_nodoc               AS trn_nodoc,
            m.mstd_nopo                AS trn_nopo,
            m.mstd_tglpo               AS trn_tglpo,
            m.mstd_seqno               AS trn_seqno,
            p.prd_kodedivisi           AS trn_div,
            p.prd_kodedepartement      AS trn_dept,
            p.prd_kodekategoribarang   AS trn_katb,
            m.mstd_prdcd               AS trn_prdcd,
            p.prd_deskripsipanjang     AS trn_nama_barang,
            m.mstd_unit                AS trn_unit,
            m.mstd_frac                AS trn_frac,
            coalesce(p.prd_kodetag, ' ') AS trn_tag,
            m.mstd_qty                 AS trn_qty,
            coalesce(m.mstd_qtybonus1, 0) AS trn_qty_bonus1,
            coalesce(m.mstd_qtybonus2, 0) AS trn_qty_bonus2,
            m.mstd_hrgsatuan           AS trn_harga_satuan,
            m.mstd_gross               AS trn_gross,
            coalesce(m.mstd_discrph, 0) AS trn_discount,
            coalesce(m.mstd_ppnrph, 0) AS trn_ppn,
            coalesce(m.mstd_flagdisc1, ' ') AS trn_flag1,
            coalesce(m.mstd_flagdisc2, ' ') AS trn_flag2,
            coalesce(m.mstd_dis4rr, 0) AS trn_dis4rr,
            coalesce(m.mstd_dis4jr, 0) AS trn_dis4jr,
            coalesce(m.mstd_dis4cr, 0) AS trn_dis4cr,
            coalesce(m.mstd_ppnbtlrph, 0) AS trn_ppnbtlrph,
            coalesce(m.mstd_ppnbmrph, 0) AS trn_ppnbmrph,
            m.mstd_kodesupplier        AS trn_kode_supplier,
            s.sup_namasupplier         AS trn_nama_supplier,
            m.mstd_noref3              refrensi,
            m.mstd_keterangan          keterangan
        FROM
            tbtr_mstran_d       m
            LEFT JOIN tbmaster_prodmast   p ON m.mstd_prdcd = p.prd_prdcd
            LEFT JOIN tbmaster_supplier   s ON m.mstd_kodesupplier = s.sup_kodesupplier
        WHERE
            m.mstd_recordid IS NULL
    ) bo
WHERE
    trn_tgldoc BETWEEN :tglMulai AND :tglSelesai
    ";


if ($jenisTransaksi !== 'All') {
    $query .= " AND trn_type = :jenisTransaksi";
}
$query .= " GROUP BY
    keterangan,
    trn_type,
    trn_div,
    trn_dept,
    trn_katb,
    trn_prdcd,
    trn_nama_barang,
    trn_unit,
    trn_frac,
    trn_tag
ORDER BY
    trn_type,
    trn_div,
    trn_dept,
    trn_katb,
    trn_prdcd";


$stmt = $conn->prepare($query);
$params = [':tglMulai' => $tanggalMulai, ':tglSelesai' => $tanggalSelesai];
if ($jenisTransaksi !== 'All') {
    $params[':jenisTransaksi'] = $jenisTransaksi;
}
$stmt->execute($params);
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
        <h1>History BackOffice Produk</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2" class="text-center">#</th>
                                    <th rowspan="2" class="text-center">Tipe</th>
                                    <th colspan="3" class="text-center">Divisi</th>
                                    <th colspan="5" class="text-center">Produk</th>
                                    <th colspan="3" class="text-center">Quantity</th>
                                    <th colspan="5" class="text-center">Rupiah</th>
                                    <th colspan="2" class="text-center">Supplier</th>
                                    <th rowspan="2" class="text-center">Keterangan</th>
                                </tr>
                                <tr class="info">
                                    <th class="text-center">D</th>
                                    <th class="text-center">Dp</th>
                                    <th class="text-center">Kb</th>
                                    <th class="text-center">PLU</th>
                                    <th class="fleksibel">Nama</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Frac</th>
                                    <th class="text-center">Tag</th>
                                    <th class="text-center">CTN</th>
                                    <th class="text-center">Pcs</th>
                                    <th class="text-center">Bonus</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Netto</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Kode</th>
                                    <th class="fleksibel">Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noUrut = 1;
                                $rphGross = $rphDiscount = $rphNetto = $rphPPN = $rphTotal = 0;

                                if (empty($data)) :
                                    echo '<tr><td colspan="22" class="text-center">Tidak ada data</td></tr>';
                                else :
                                    foreach ($data as $row) :
                                        $netto = $row['trn_gross'] - $row['trn_discount'];
                                        $total_row_value = $netto + $row['trn_ppn'];
                                        $ctn = floor($row['trn_qty'] / $row['trn_frac']);
                                        $pcs = $row['trn_qty'] % $row['trn_frac'];

                                        echo '<tr>';
                                        echo '<td align="right">' . $noUrut++ . '</td>';
                                        echo '<td align="center">' . htmlspecialchars($row['trn_type']) . '</td>';
                                        echo '<td align="center">' . htmlspecialchars($row['trn_div']) . '</td>';
                                        echo '<td align="center">' . htmlspecialchars($row['trn_dept']) . '</td>';
                                        echo '<td align="center">' . htmlspecialchars($row['trn_katb']) . '</td>';
                                        echo '<td align="center">' . htmlspecialchars($row['trn_prdcd']) . '</td>';
                                        echo '<td align="left" class="fleksibel">' . htmlspecialchars($row['trn_nama_barang']) . '</td>';
                                        echo '<td align="left">' . htmlspecialchars($row['trn_unit']) . '</td>';
                                        echo '<td align="left">' . htmlspecialchars($row['trn_frac']) . '</td>';
                                        echo '<td align="left">' . htmlspecialchars($row['trn_tag']) . '</td>';

                                        echo '<td align="right">' . number_format($ctn, 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($pcs, 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_qty_bonus'], 0, '.', ',') . '</td>';

                                        echo '<td align="right">' . number_format($row['trn_gross'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_discount'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($netto, 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_ppn'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($total_row_value, 0, '.', ',') . '</td>';

                                        echo '<td align="left">' . htmlspecialchars($row['trn_kode_supplier']) . '</td>';
                                        echo '<td align="left" class="fleksibel">' . htmlspecialchars($row['trn_nama_supplier']) . '</td>';
                                        echo '<td align="left" class="text-nowrap">' . htmlspecialchars($row['keterangan']) . '</td>';
                                        echo '</tr>';

                                        $rphGross += $row['trn_gross'];
                                        $rphDiscount += $row['trn_discount'];
                                        $rphNetto += $netto;
                                        $rphPPN += $row['trn_ppn'];
                                        $rphTotal += $total_row_value;
                                    endforeach;
                                endif;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="13" class="text-right font-weight-bold">TOTAL</td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphGross, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphDiscount, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphNetto, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphPPN, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphTotal, 0, '.', ','); ?></td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
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
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{
                targets: [4],
                orderable: false
            }],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy' // Ubah teks tombol jika diperlukan
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'HIS_BO_BTB_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
                    title: null
                },
            ],
            dom: 'Bfrtip' // Posisi tombol
        });

        // Tambahkan tombol ke wrapper tabel
        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });

    $(document).ready(function() {
        // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
        var table = $('#table-1').DataTable();
        table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
        $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
    });
</script>