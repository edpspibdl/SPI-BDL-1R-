<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - BTB</title>

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">

    <!-- JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        .loader {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Monitoring BTB</h1>
        <a href="../pluNoSales/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="section-body">
        <div class="panel-body fixed-panel">
            <div class="row">
                <!-- Card Section -->
                <div class="col-md-12">
                    <div id="card-section" class="mb-3">
                        <?php include 'card.php'; ?>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="col-md-12">
                    <div id="table-section" class="p-3 border rounded">
                        <section id="tabel"></section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function () {
        // Hilangkan loader setelah 100ms
        setTimeout(() => $('#load').fadeOut('slow'), 100);

        // Event handler AJAX untuk tombol
        $(".bt_prod").click(() => loadData('tabel_produk.php'));
        $(".tdk_hgb").click(() => loadData('hgb_tidakada.php'));
        $(".bt_supp").click(() => loadData('tabel_supplier.php'));
        $(".bt_det").click(() => loadData('tabel_detail.php'));

        // Fungsi AJAX
        function loadData(url) {
            $('#tabel').empty().addClass('loader').html('<p>Loading...</p>');

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'html',
                success: function(response) {
                    $('#tabel').html(response).removeClass('loader');
                    $('#tabel .dataTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        footer: `${xhr.statusText}: ${xhr.responseText}`
                    });
                }
            });
        }
    });
</script>

</body>
</html>
<?php
require_once '../layout/_bottom.php';
?>