<?php
// Database Connection
include "../helper/connection.php";

// Query
$query = "SELECT 
    TRN_DIV, TRN_DEPT, TRN_KATB, TRN_PRDCD, TRN_NAMA_BARANG, TRN_UNIT, TRN_FRAC, 
    SUM(TRN_QTY) AS TRN_QTY, 
    SUM(TRN_QTY_BONUS1) AS TRN_QTY_BONUS1, 
    SUM(TRN_QTY_BONUS2) AS TRN_QTY_BONUS2, 
    TRN_TAG  
FROM ( 
    SELECT 
        m.mstd_typetrn AS trn_type,
        m.mstd_tgldoc AS trn_tgldoc,
        m.mstd_nodoc AS trn_nodoc,
        m.mstd_nopo AS trn_nopo,
        DATE(m.mstd_tglpo) AS trn_tglpo,
        m.mstd_seqno AS trn_seqno,
        p.prd_kodedivisi AS trn_div,
        p.prd_kodedepartement AS trn_dept,
        p.prd_kodekategoribarang AS trn_katb,
        m.mstd_prdcd AS trn_prdcd,
        p.prd_deskripsipanjang AS trn_nama_barang,
        m.mstd_unit AS trn_unit,
        m.mstd_frac AS trn_frac,
        COALESCE(p.prd_kodetag, ' ') AS trn_tag,
        m.mstd_qty AS trn_qty,
        COALESCE(m.mstd_qtybonus1, 0) AS trn_qty_bonus1,
        COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus2,
        m.mstd_hrgsatuan AS trn_harga_satuan
    FROM tbtr_mstran_d m
    LEFT JOIN tbmaster_prodmast p ON m.mstd_prdcd = p.prd_prdcd
    WHERE m.mstd_recordid IS NULL
    AND m.mstd_typetrn = 'B'
    AND DATE(m.mstd_tgldoc) = CURRENT_DATE
) AS subquery
GROUP BY TRN_PRDCD, TRN_NAMA_BARANG, TRN_DIV, TRN_DEPT, TRN_KATB, TRN_UNIT, TRN_FRAC, TRN_TAG
ORDER BY TRN_PRDCD";

$stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML Tabel -->
<div class="table-responsive">
    <table id="GridView" class="table table-striped table-bordered compact" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>DIV</th>
                <th>DEPT</th>
                <th>KATB</th>
                <th>PRDCD</th>
                <th>NAMA BARANG</th>
                <th>UNIT</th>
                <th>FRAC</th>
                <th>QTY</th>
                <th>QTY BONUS1</th>
                <th>QTY BONUS2</th>
                <th>TAG</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($rows as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_div"]) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_dept"]) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_katb"]) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_prdcd"]) ?></td>
                    <td><?= htmlspecialchars($row["trn_nama_barang"]) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_unit"]) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row["trn_frac"]) ?></td>
                    <td class="text-end"><?= number_format($row["trn_qty"], 0, ',', '.') ?></td>
                    <td class="text-end"><?= number_format($row["trn_qty_bonus1"], 0, ',', '.') ?></td>
                    <td class="text-end"><?= number_format($row["trn_qty_bonus2"], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row["trn_tag"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- DataTables Initialization -->
<script>
$(document).ready(function() {
    $('#GridView').DataTable({
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ baris",
            info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        columnDefs: [
            { targets: [0, 1, 2, 3, 4, 6, 7], className: "text-center" },
            { targets: [8, 9, 10], className: "text-end" }
        ]
    });
});
</script>
