<?php
require_once '_obi.class.php';

$detail = new OBI;
$temp_d = $detail->detailTgl($_POST['tg'], $_POST['st']);
?>

<style>
    table.dataTable {
        font-size: 14px;
        border-collapse: collapse;
    }

    table.dataTable th,
    table.dataTable td {
        text-align: center;
        padding: 10px;
    }

    table.dataTable th {
        background-color: #343a40;
        color: #fff;
        font-weight: bold;
    }

    table.dataTable tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .dataTables_length,
    .dataTables_filter {
        margin: 10px;
    }

    .dataTables_paginate {
        float: right;
    }
</style>

<h4 class="text-center bg-primary text-white p-3" style="margin-top: 0px;">
    <?= $_POST['st'] ?> <?= $_POST['tg'] ?>
</h4>

<div class="table-responsive">
    <table id="GridView" class="table table-hover table-bordered compact" style="width:100%; font-size:12px">
        <thead class="thead-dark">
            <tr>
                <th>NO</th>
                <th class="text-nowrap">STATUS</th>
                <th class="text-nowrap">NOPB</th>
                <th class="text-nowrap">MEMBER</th>
                <th class="text-nowrap">NAMA</th>
                <th class="text-nowrap">TIPE MEMBER</th>
                <th>ITEM ORDER</th>
                <th>ITEM REAL</th>
                <th>TOTAL ORDER</th>
                <th>TOTAL REAL</th>
                <th>ONGKIR</th>
                <th>TIPE BAYAR</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0;
            foreach ($temp_d as $row) {
                $no++; ?>
                <tr>
                    <td class="text-right"><?= $no ?></td>
                    <td class="text-nowrap"><?= $row['status1'] ?></td>
                    <td><?= $row['obi_nopb'] ?></td>
                    <td class="text-nowrap"><?= $row['kode_member'] ?></td>
                    <td class="text-nowrap"><?= $row['cus_namamember'] ?></td>
                    <td class="text-nowrap"><?= $row['tipe_member'] ?></td>
                    <td class="text-nowrap text-right"><?= number_format($row['item_order'], 0, '.', ',') ?></td>
                    <td class="text-nowrap text-right"><?= number_format($row['item_real'], 0, '.', ',') ?></td>
                    <td class="text-nowrap text-right"><?= number_format($row['total_order'], 0, '.', ',') ?></td>
                    <td class="text-nowrap text-right"><?= number_format($row['total_real'], 0, '.', ',') ?></td>
                    <td><?= $row['ongkir'] ?></td>
                    <td><?= $row['tipe_bayar'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        const table = $('#GridView').DataTable({
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{
                targets: [4], // Kolom ke-5 tidak bisa diurutkan
                orderable: false
            }],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'HIS_BO_BTB_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: '<"row mb-2"<"col-md-6"B><"col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row mt-2"<"col-md-6"i><"col-md-6"p>>'
        });

        table.buttons().container().appendTo('#GridView_wrapper .col-md-6:eq(0)');

        table.columns.adjust().draw();
        $("#load").fadeOut(); // Jika ada elemen loading
    });
</script>