<?php
require_once '_obi.class.php';

$obi = new OBI;
$hdr = $obi->headerTgl($_POST['id']);
?>
<div class="row">
    <div class="col-xs-8 col-sm-6 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-totalpb text-center" style="background-color: #2196F3;">
                <b style="color: white;">TOTAL PB</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="PB MASUK" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #4CAF50; color: white;"><?= $row['pb_masuk'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                    <span class="badge" style="background-color: #FF5722; color: white;"><?= $row['pb_batal'] ?></span>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-send text-center" style="background-color: #FF9800;">
                <b style="color: white;">SIAP SEND</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="Siap Send" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #FFC107; color: white;"><?= $row['siap_send'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-pick text-center" style="background-color: #8BC34A;">
                <b style="color: white;">SIAP PICK</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="Siap Picking" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #4CAF50; color: white;"><?= $row['siap_pick'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-pack text-center" style="background-color: #3F51B5;">
                <b style="color: white;">SIAP PACK</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="Siap Packing" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #2196F3; color: white;"><?= $row['siap_pack'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-struk text-center" style="background-color: #9C27B0;">
                <b style="color: white;">SIAP STRUK</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="Siap Struk" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #E91E63; color: white;"><?= $row['siap_struk'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-xs-6 col-sm-4 col-md-2">
        <div class="list-group bayang">
            <a class="list-group-item bg-sstruk text-center" style="background-color: #673AB7;">
                <b style="color: white;">SELESAI STRUK</b>
            </a>
            <?php foreach ($hdr as $row) { ?>
                <a href="#" class="list-group-item open-mdl" data-id="Selesai Struk" data-tgl="<?= $row['obi_tgl'] ?>">
                    <span class="badge" style="background-color: #9C27B0; color: white;"><?= $row['ssai_struk'] ?></span>
                    <small style="font-weight: 900;"><?= $row['obi_tgl'] ?></small>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="color: white; border-radius: 5px 5px 0 0;">
                <h5 class="modal-title" id="myModalLabel">Detail Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <section id="body-dtl"></section> <!-- Content will be dynamically inserted here -->
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<!-- Import jQuery (important to be version 3.6 or latest) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Import DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<!-- Import Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Import SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
  $(".open-mdl").click(function() {
    const st = $(this).attr("data-id");
    const tg = $(this).attr("data-tgl");

    // Show SweetAlert2 loading
    Swal.fire({
        title: 'Menunggu',
        html: 'Memproses data',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Make AJAX request
    $.ajax({
        url: "view_detail.php",
        method: "POST",
        data: {
            tg: tg,
            st: st
        },
        dataType: "html",
        success: function(data) {
            Swal.close();  // Close the loading Swal
            // Show the modal with updated content
            $('#myModal').modal('show');
            // Insert the response data into the modal body
            $("#body-dtl").html(data);

            // Initialize DataTable after content is inserted
            $('#example').DataTable({
                responsive: true, // Optional for responsiveness
                paging: true,
                searching: true,
                ordering: true,
            });
        },
        error: function(res) {
            Swal.fire({
                icon: 'error',
                title: res.status + '<br>' + res.statusText,
                text: res.responseText,
                footer: 'Hubungi EDP!'
            });
        }
    });
});

</script>
