<?php
$query = "SELECT
st_lokasi,
st_prdcd,
prd_deskripsipanjang,
st_saldoakhir,
st_avgcost
FROM
tbmaster_stock
JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
WHERE
st_avgcost <= 0
AND st_saldoakhir <> 0";

    include "../helper/connection.php";
   $stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-bordered table-striped table-hover table table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size: 12px;">
    <div class="panel-heading text-center" style="font-size: 25px"> Acost 0 Atau Minus</div>
        <thead>
            <tr>
                <th> # </th>
                <th> LOKASI</th>
                <th> PLU </th>
                <th> DESK </th>
                <th> LPP PCS</th>
                <th> ACOST PCS</th>
            </tr>
        </thead>
        <tbody>
           <?php
            $no = 0;
             foreach ($rows as $row) {
            $no++;  ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["ST_LOKASI"] ?> </td>
                    <td> <?= $row["ST_PRDCD"] ?> </td>
                    <td> <?= $row["PRD_DESKRIPSIPANJANG"] ?> </td>
                    <td> <?= number_format($row["ST_SALDOAKHIR"],         0, '.', ',') ?> </td>
                    <td> <?= number_format($row["ST_AVGCOST"],         0, '.', ',') ?> </td>
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