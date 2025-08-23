<?php
// FILE: report_by_produk.php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$tanggalMulai = $_GET['tanggalMulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['tanggalSelesai'] ?? date('Y-m-d');
$jenisTransaksi = $_GET['jenisTransaksi'] ?? 'All';

$query = "
SELECT
  bo.trn_type,
  bo.trn_div,
  div.div_namadivisi AS trn_div_nama,
  COUNT(DISTINCT bo.trn_kode_supplier) AS trn_kode_supplier,
  COUNT(DISTINCT bo.trn_prdcd) AS trn_item,
  SUM(bo.trn_qty) AS trn_qty,
  SUM(bo.trn_qty_bonus1 + bo.trn_qty_bonus2) AS trn_qtybonus,
  SUM(bo.trn_gross) AS trn_gross,
  SUM(bo.trn_discount) AS trn_discount,
  SUM(bo.trn_ppn) AS trn_ppn
FROM (
  SELECT
    m.mstd_typetrn AS trn_type,
    m.mstd_tgldoc AS trn_tgldoc,
    p.prd_kodedivisi AS trn_div,
    m.mstd_prdcd AS trn_prdcd,
    m.mstd_qty AS trn_qty,
    COALESCE(m.mstd_qtybonus1, 0) AS trn_qty_bonus1,
    COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus2,
    m.mstd_gross AS trn_gross,
    COALESCE(m.mstd_discrph, 0) AS trn_discount,
    COALESCE(m.mstd_ppnrph, 0) AS trn_ppn,
    m.mstd_kodesupplier AS trn_kode_supplier
  FROM tbtr_mstran_d AS m
  LEFT JOIN tbmaster_prodmast AS p ON m.mstd_prdcd = p.prd_prdcd
  WHERE m.mstd_recordid IS NULL
) AS bo
LEFT JOIN tbmaster_divisi AS div ON bo.trn_div = div.div_kodedivisi
WHERE bo.trn_tgldoc BETWEEN :tglMulai AND :tglSelesai
";

// Jika filter transaksi tidak "All"
if ($jenisTransaksi !== 'All') {
    $query .= " AND bo.trn_type = :jenisTransaksi";
}

$query .= "
GROUP BY
  bo.trn_type,
  bo.trn_div,
  div.div_namadivisi
ORDER BY
  bo.trn_type,
  bo.trn_div
";

// Eksekusi query
$stmt = $conn->prepare($query);
$params = [
    ':tglMulai' => $tanggalMulai,
    ':tglSelesai' => $tanggalSelesai
];
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
        <h1>History BackOffice Per Divisi</h1>
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
                                    <th colspan="2" class="text-center">Divisi</th>
                                    <th rowspan="2" class="text-center">Supplier</th>
                                    <th rowspan="2" class="text-center">Item</th>
                                    <th colspan="2" class="text-center">Quantity</th>
                                    <th colspan="5" class="text-center">Rupiah</th>
                                    <th rowspan="2" class="text-center">Keterangan</th>
                                </tr>
                                <tr class="info">
                                    <th class="text-center">Kode</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Pcs</th>
                                    <th class="text-center">Bonus</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Netto</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noUrut = 1;
                                $rphGross = $rphDiscount = $rphNetto = $rphPPN = $rphTotal = 0;

                                if (empty($data)) {
                                    echo '<tr><td colspan="22" class="text-center">Tidak ada data</td></tr>';
                                } else {
                                    foreach ($data as $row) {
                                        $netto = $row['trn_gross'] - $row['trn_discount'];
                                        $total = $netto + $row['trn_ppn'];

                                        echo '<tr>';
                                        echo '<td align="right">' . $noUrut++ . '</td>';
                                        echo '<td align="center">' . $row['trn_type'] . '</td>';
                                        echo '<td align="center">' . $row['trn_div'] . '</td>';
                                        echo '<td align="left">' . $row['trn_div_nama'] . '</td>';
                                        echo '<td align="right">' . $row['trn_kode_supplier'] . '</td>';
                                        echo '<td align="right">' . $row['trn_item'] . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_qty'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_qtybonus'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_gross'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_discount'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($netto, 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($row['trn_ppn'], 0, '.', ',') . '</td>';
                                        echo '<td align="right">' . number_format($total, 0, '.', ',') . '</td>';
                                        echo '<td align="left">&nbsp;</td>';
                                        echo '</tr>';

                                        // akumulasi total
                                        $rphGross += $row['trn_gross'];
                                        $rphDiscount += $row['trn_discount'];
                                        $rphNetto += $netto;
                                        $rphPPN += $row['trn_ppn'];
                                        $rphTotal += $total;
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-right font-weight-bold">TOTAL</td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphGross, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphDiscount, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphNetto, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphPPN, 0, '.', ','); ?></td>
                                    <td class="text-right font-weight-bold"><?= number_format($rphTotal, 0, '.', ','); ?></td>
                                    <td></td>
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