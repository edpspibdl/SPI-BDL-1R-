<?php
$query = "SELECT
    CASE
        WHEN st_lokasi = '01' THEN 'LPP Baik'
        WHEN st_lokasi = '02' THEN 'LPP Retur'
        ELSE 'LPP Rusak'
    END AS st_lokasi,
    prd_kodedivisi,
    st_prdcd,
    prd_deskripsipanjang,
    prd_frac || '/' || prd_unit AS Frac,
    prd_kodetag,
    flag_main,
    st_saldoakhir,
   CASE
    WHEN prd_unit = 'KG' THEN ROUND((st_saldoakhir * st_avgcost::numeric) / prd_frac, 2)
    ELSE ROUND(st_saldoakhir * st_avgcost::numeric, 2)
END AS rph


FROM
    tbmaster_stock
LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
LEFT JOIN (
    SELECT
        PRD_PRDCD AS plu_flag,
        CASE
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IGR+IDM+KLIK+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IGR+KLIK+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IGR+IDM+KLIK'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IGR+IDM+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IDM+KLIK+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IGR+IDM'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IGR+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'IDM+OMI'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IGR+KLIK'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IDM+KLIK'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'OMI+KLIK'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IGR ONLY'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'IDM ONLY'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'Y'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'N'
            THEN 'KLIK ONLY'
            WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N'
                AND COALESCE(PRD_FLAGIDM, 'N') = 'N'
                AND COALESCE(PRD_FLAGOBI, 'N') = 'N'
                AND COALESCE(PRD_FLAGOMI, 'N') = 'Y'
            THEN 'OMI ONLY'
            ELSE 'BLANK'
        END AS FLAG_MAIN
    FROM
        tbmaster_prodmast
    WHERE
        PRD_PRDCD LIKE '%0'
) AS flag_table ON flag_table.plu_flag = st_prdcd
WHERE
    st_saldoakhir < 0
ORDER BY
    st_lokasi,
    st_prdcd
";

    include "../helper/connection.php";
    $stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-bordered table-striped table-hover table table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size: 12px;">
    <div class="panel-heading text-center" style="font-size: 25px">LPP Minus (LPP Tidak Boleh Minus Pada Saat SO, Segera Lakukan Ubah Status / MPP)</div>
        <thead>
            <tr>
                <th> # </th>
                <th> LOKASI </th>
                <th> DIV </th>
                <th> PLU </th>
                <th> DESK </th>
                <th> FRAC </th>
                <th> TAG </th>
                <th> FLAG </th>
                <th> LPP PCS</th>
                <th> RPH</th>
            </tr>
        </thead>
        <tbody>
           <?php
            $no = 0;
             foreach ($rows as $row) {
            $no++;  ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["st_lokasi"] ?> </td>
                    <td> <?= $row["prd_kodedivisi"] ?> </td>
                    <td> <?= $row["st_prdcd"] ?> </td>
                    <td> <?= $row["prd_deskripsipanjang"] ?> </td>
                    <td> <?= $row["frac"] ?> </td>
                    <td> <?= $row["prd_kodetag"] ?> </td>
                    <td> <?= $row["flag_main"] ?> </td>
                    <td> <?= number_format($row["st_saldoakhir"],         0, '.', ',') ?> </td>
                    <td> <?= number_format($row["rph"],         0, '.', ',') ?> </td>
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