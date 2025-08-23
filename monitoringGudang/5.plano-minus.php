<?php
$query = "SELECT
    lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak AS lokasi,
    prd_kodedivisi,
    lks_prdcd,
    prd_deskripsipanjang,
    CONCAT(prd_frac, '/', prd_unit) AS Frac,
    prd_kodetag,
    flag_main,
    lks_qty
FROM
    tbmaster_lokasi
JOIN tbmaster_prodmast ON prd_prdcd = lks_prdcd
JOIN (
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
) AS flag_table ON flag_table.PLU_flag = lks_prdcd
WHERE
    lks_qty < 0
";

    include "../helper/connection.php";
    $stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-bordered table-striped table-hover table table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size: 12px;">
    <div class="panel-heading text-center" style="font-size: 25px">Plano Minus</div>
        <thead>
            <tr>
                <th> # </th>
                <th> LOKASI PLANO </th>
                <th> DIV </th>
                <th> PLU </th>
                <th> DESK </th>
                <th> FRAC </th>
                <th> TAG </th>
                <th> FLAG </th>
                <th> PLANO PCS</th>
            </tr>
        </thead>
        <tbody>
           <?php
            $no = 0;
           foreach ($rows as $row) {
            $no++;  ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["lokasi"] ?> </td>
                    <td> <?= $row["prd_kodedivisi"] ?> </td>
                    <td> <?= $row["lks_prdcd"] ?> </td>
                    <td> <?= $row["prd_deskripsipanjang"] ?> </td>
                    <td> <?= $row["frac"] ?> </td>
                    <td> <?= $row["prd_kodetag"] ?> </td>
                    <td> <?= $row["flag_main"] ?> </td>
                    <td> <?= number_format($row["lks_qty"],         0, '.', ',') ?> </td>
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