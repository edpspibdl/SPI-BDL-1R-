<?php
$query = "SELECT
    prd_kodedivisi,
    lso_koderak || '.'
    || lso_kodesubrak || '.'
    || lso_tiperak || '.'
    || lso_shelvingrak || '.'
    || lso_nourut AS lks,
    lso_prdcd,
    prd_deskripsipanjang,
    prd_frac,
    prd_unit,
    st_saldoakhir,
    ' ' AS fisik
FROM
    tbtr_lokasi_so
JOIN tbmaster_prodmast ON prd_prdcd = lso_prdcd
JOIN (
    SELECT
        *
    FROM
        tbmaster_stock
    WHERE
        st_lokasi = '01'
) AS stock_table ON stock_table.st_prdcd = lso_prdcd
WHERE
    lso_tglso::date = '2024-06-23'
    AND lso_flagsarana = 'K'
    AND lso_koderak LIKE 'P%'
ORDER BY
    lso_koderak,
    lso_kodesubrak,
    lso_tiperak,
    lso_shelvingrak,
    lso_nourut ASC";

    include "../helper/connection.php";
     $stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<hr>
<div class="table-responsive">
    <table id="GridView" class="table table-bordered table-striped table-hover table table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size: 12px;"> 
<div class="panel-heading text-center" style="font-size: 25px">Input KKSO 01 (Dilakukan Sebelum Copy Master Lokasi Tanggal 17 SEP 2023)</div>
        <thead>
            <tr>
                <th> # </th>
                <th> DIV </th>
                <th> LOKASI </th>
                <th> PLU </th>
                <th> DESC </th>
                <th> FRAC </th>
                <th> UNIT </th>
                <th> LPP PCS </th>
                <th> FISIK PCS </th>
            </tr>
        </thead>
        <tbody>
           <?php
            $no = 0;
            foreach ($rows as $row) {
            $no++;  ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["prd_kodedivisi"] ?> </td>
                    <td> <?= $row["lks"] ?> </td>
                    <td> <?= $row["lso_prdcd"] ?> </td>
                    <td> <?= $row["prd_deskripsipanjang"] ?> </td>
                    <td> <?= $row["prd_unit"] ?> </td>
                    <td> <?= number_format($row["prd_frac"],         0, '.', ',') ?> </td>
                    <td> <?= number_format($row["st_saldoakhir"],         0, '.', ',') ?> </td>
                    <td> <?= $row["fisik"] ?> </td>
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