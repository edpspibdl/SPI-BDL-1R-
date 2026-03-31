<?php
require_once '_obi.class.php';

$detail = new OBI;
$temp_d = $detail->detailTgl($_POST['tg'], $_POST['st']);
?>

<!-- Styling untuk Tabel -->
<style>
  /* Styling untuk tabel */
  #table-1 {
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
  }

  #table-1 th {
    background-color: #e0e0e0; /* abu-abu */
  }
  #table-1 td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
  }

  #table-1 th {
    background-color: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #333;
  }

  #table-1 td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  /* Kolom DESK bila dibutuhkan */
  #table-1 .desk-column {
    word-wrap: break-word;
    white-space: normal;
    max-width: 300px;
  }

  /* Responsif */
  .table-responsive {
    overflow-x: auto;
  }
</style>

<h4 class="text-center bg-primary text-white p-2" style="margin-top:0;">
    <?= $_POST['st'] . " " . $_POST['tg'] ?>
</h4>

<div class="table-responsive">
    <table id="table-1" class="display compact">
        <thead>
            <tr>
                <th>NO</th>
                <th>STATUS</th>
                <th>NOPB</th>
                <th>MEMBER</th>
                <th>NAMA</th>
                <th>TIPE MEMBER</th>
                <th>ITEM ORDER</th>
                <th>ITEM REAL</th>
                <th>TOTAL ORDER</th>
                <th>TOTAL REAL</th>
                <th>ONGKIR</th>
                <th>TIPE BAYAR</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            foreach ($temp_d as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['status1'] ?></td>
                    <td><?= $row['obi_nopb'] ?></td>
                    <td><?= $row['kode_member'] ?></td>
                    <td><?= $row['cus_namamember'] ?></td>
                    <td><?= $row['tipe_member'] ?></td>
                    <td class="text-end"><?= number_format($row['item_order'], 0, '.', ',') ?></td>
                    <td class="text-end"><?= number_format($row['item_real'], 0, '.', ',') ?></td>
                    <td class="text-end"><?= number_format($row['total_order'], 0, '.', ',') ?></td>
                    <td class="text-end"><?= number_format($row['total_real'], 0, '.', ',') ?></td>
                    <td><?= $row['ongkir'] ?></td>
                    <td><?= $row['tipe_bayar'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    let table = $('#table-1').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        autoWidth: false,
        ordering: true,
        columnDefs: [
            { targets: [4], orderable: false } // kolom NAMA tidak bisa sorting
        ],
        buttons: [
            { extend: 'copy', text: 'Copy', className: 'btn btn-secondary btn-sm' },
            { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm',
              filename: 'HIS_BO_BTB_' + new Date().toISOString().split('T')[0],
              title: null }
        ],
        dom:
            '<"row mb-2"<"col-md-6"B><"col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row mt-2"<"col-md-6"i><"col-md-6"p>>'
    });

    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
});
</script>