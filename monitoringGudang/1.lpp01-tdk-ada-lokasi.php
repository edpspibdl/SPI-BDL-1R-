<?php
$query = "SELECT
    prd_kodedivisi,
    st_prdcd,
    prd_deskripsipanjang,
    prd_frac || '/' || prd_unit AS Frac,
    prd_kodetag,
    flag_main,
    st_saldoakhir
FROM
    tbmaster_stock
    LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
    LEFT JOIN (
        SELECT 
            PRD_PRDCD AS PLU_flag,
            CASE 
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+IDM+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+IDM+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+IDM+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IDM+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+IDM'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IDM+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IDM+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'OMI+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IDM ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'KLIK ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'OMI ONLY'
                ELSE 'BLANK'
            END AS FLAG_MAIN
        FROM 
            TBMASTER_PRODMAST
        WHERE 
            PRD_PRDCD LIKE '%0'
    ) AS flag_table ON flag_table.PLU_flag = st_prdcd
WHERE
    st_lokasi = '01'
    AND st_saldoakhir <> 0
    AND st_prdcd NOT IN (
        SELECT DISTINCT PLU
        FROM (
            SELECT lks_prdcd AS PLU
            FROM tbmaster_lokasi
            WHERE lks_prdcd IS NOT NULL
            UNION
            SELECT lso_prdcd AS PLU
            FROM tbtr_lokasi_so
            WHERE DATE(LSO_TGLSO) = '2024-06-23'
            AND lso_lokasi = '01'
        ) AS combined_plu
    )
ORDER BY st_prdcd ASC
";

    include "../helper/connection.php";
    // Using PDO to execute the query
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-bordered table-striped table-hover table table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size: 12px;"> 
    <div class="panel-heading text-center" style="font-size: 25px">LPP 01 Tidak Ada Lokasi (Semua LPP 01 Harus Ada Lokasi, termasuk divisi Perishable)</div>
        <thead>
            <tr>
                <th> # </th>
                <th> DIV </th>
                <th> PLU </th>
                <th> DESK </th>
                <th> FRAC </th>
                <th> TAG </th>
                <th> FLAG </th>
                <th> LPP PCS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            foreach ($rows as $row) {
                $no++;  ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["PRD_KODEDIVISI"] ?> </td>
                    <td> <?= $row["ST_PRDCD"] ?> </td>
                    <td> <?= $row["PRD_DESKRIPSIPANJANG"] ?> </td>
                    <td> <?= $row["FRAC"] ?> </td>
                    <td> <?= $row["PRD_KODETAG"] ?> </td>
                    <td> <?= $row["FLAG_MAIN"] ?> </td>
                    <td> <?= number_format($row["ST_SALDOAKHIR"], 0, '.', ',') ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
$(document).ready(function(){
    var GridView = $("#GridView").DataTable({
        "language": {
            "search": "Cari",
            "lengthMenu": "_MENU_ Baris per halaman",
            "zeroRecords": "Data tidak ada",
            "info": "Halaman _PAGE_ dari _PAGES_ halaman",
            "infoEmpty": "Data tidak ada",
            "infoFiltered": "(Filter dari _MAX_ data)"
        },
        lengthChange:true,
        lengthMenu: [ 10, 25, 50, 75, 100 ],
        paging: true,
        responsive: true,
        buttons: [ "copy", "excel", "colvis" ],
    });
    GridView.buttons().container()
        .appendTo( "#GridView_wrapper .col-sm-6:eq(0)" );
    $("#GridView").show();
    GridView.columns.adjust().draw();
    $("#load").fadeOut();
});
</script>
