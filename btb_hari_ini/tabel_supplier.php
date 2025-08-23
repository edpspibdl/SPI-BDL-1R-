<?php

// Database Connection
include "../helper/connection.php";

$query = "SELECT 
    trn_kode_supplier, 
    trn_nama_supplier, 
    count(DISTINCT trn_prdcd) AS trn_prdcd, 
    count(DISTINCT trn_nodoc) AS trn_nodoc, 
    count(DISTINCT trn_nopo) AS trn_nopo
FROM (
    SELECT 
        m.mstd_typetrn        AS trn_type,
        m.mstd_tgldoc         AS trn_tgldoc,
        m.mstd_nodoc          AS trn_nodoc,
        m.mstd_nopo           AS trn_nopo,
        DATE(m.mstd_tglpo)    AS trn_tglpo,
        m.mstd_seqno          AS trn_seqno,
        p.prd_kodedivisi      AS trn_div,
        p.prd_kodedepartement AS trn_dept,
        p.prd_kodekategoribarang AS trn_katb,
        m.mstd_prdcd          AS trn_prdcd,
        p.prd_deskripsipanjang AS trn_nama_barang,
        m.mstd_unit           AS trn_unit,
        m.mstd_frac           AS trn_frac,
        COALESCE(p.prd_kodetag, ' ') AS trn_tag,
        m.mstd_qty            AS trn_qty,
        COALESCE(m.mstd_qtybonus1, 0) AS trn_qty_bonus1,
        COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus2,
        COALESCE(m.mstd_qtybonus1, 0) + COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus,
        m.mstd_hrgsatuan      AS trn_harga_satuan,
        m.mstd_gross          AS trn_gross,
        COALESCE(m.mstd_discrph, 0) AS trn_discount,
        COALESCE(m.mstd_ppnrph, 0) AS trn_ppn,
        p.prd_kodetag         AS trn_tagigr,
        COALESCE(m.mstd_flagdisc1, ' ') AS trn_flag1,
        COALESCE(m.mstd_flagdisc2, ' ') AS trn_flag2,
        m.mstd_kodesupplier   AS trn_kode_supplier,
        s.sup_namasupplier    AS trn_nama_supplier
    FROM tbtr_mstran_d m
    LEFT JOIN tbmaster_prodmast p ON m.mstd_prdcd = p.prd_prdcd
    LEFT JOIN tbmaster_supplier s ON m.mstd_kodesupplier = s.sup_kodesupplier
    WHERE m.mstd_recordid IS NULL
    AND m.mstd_typetrn = 'B'
    AND DATE(m.mstd_tgldoc) = CURRENT_DATE
) AS subquery
GROUP BY trn_kode_supplier, trn_nama_supplier 
ORDER BY trn_nama_supplier";

include "../helper/connection.php";
$stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-striped table-bordered compact" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>KODE SUPPLIER</th>
                <th>NAMA SUPPLIER</th>
                <th>JML PRODUK</th>
                <th>JML NODOC</th>
                <th>JML NOPO</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($rows as $row) {
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row["trn_kode_supplier"] ?></td>
                    <td><?= $row["trn_nama_supplier"] ?></td>
                    <td class="text-center"><?= $row["trn_prdcd"] ?></td>
                    <td class="text-center"><?= $row["trn_nodoc"] ?></td>
                    <td class="text-center"><?= $row["trn_nopo"] ?></td>
                </tr>
            <?php } ?>
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
            { targets: [0, 1, 2], className: "text-center" },  // Center-align the first three columns
            { targets: [3, 4, 5], className: "text-end" }        // Right-align the rest
        ]
    });
});
</script>
