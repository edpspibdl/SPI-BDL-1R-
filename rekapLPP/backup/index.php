<?php include '../include/cek.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>LPP vs PLANO Rekap</title>
    <?php include '../include/komponen.php'; ?>
</head>

<body>
    <?php include '../include/nav.php'; ?>
    <div class="container-fluid">
        <div class="panel panel-primary">
            <div class="panel-heading">LPP vs PLANO Rekap</div>
            <div class="panel-body fixed-panel">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table id="" class="table table-striped table-bordered table-responsive table-hover cell-border compact" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>KETERANGAN</th>
                                        <th>PLU</th>
                                        <th>RUPIAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>PLUS</td>
                                        <td id="r-plu-plus" class="text-right"></td>
                                        <td id="r-rph-plus" class="text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>MINUS</td>
                                        <td id="r-plu-minus" class="text-right"></td>
                                        <td id="r-rph-minus" class="text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>TIDAK SELISIH</td>
                                        <td id="r-plu-ts" class="text-right"></td>
                                        <td id="r-rph-ts" class="text-right"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SALDO AKHIR</th>
                                        <th id="r-plu-sa" class="text-right"></th>
                                        <th id="r-rph-sa" class="text-right"></th>
                                    </tr>
									<tr>
                                        <th>PERSEN</th>
                                        <th> </th>
                                        <th> </th>
                                    </tr>
                                </tfoot>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-12">

                    </div>
                </div>

            </div>
        </div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            type: "GET", // mengirim data dengan method POST 
            url: "query.php", // proses update data
            dataType: 'JSON',

            success: function(res) {
                if (res.STATUS === 'OK') {

                    $('#r-plu-plus').text(formatRupiah(res.R_PLU_PLUS))
                    $('#r-plu-minus').text(formatRupiah(res.R_PLU_MINUS))
                    $('#r-plu-ts').text(formatRupiah(res.R_PLU_TS))

                    $('#r-rph-plus').text(formatRupiah(res.R_RPH_PLUS))
                    $('#r-rph-minus').text(formatRupiah(res.R_RPH_MINUS))
                    $('#r-rph-ts').text(res.R_RPH_TS)

                    $('#r-plu-sa').text(formatRupiah(res.R_PLU_ALL))
                    $('#r-rph-sa').text(formatRupiah(res.R_RPH_ALL))
					
                }

            },

            error: function(res) {
                console.log(res.text);
            }
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
</script>