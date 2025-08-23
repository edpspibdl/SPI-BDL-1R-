<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>History SOIC</h1>

    </div>
    <div class="container-fluid">
        <div class="panel panel-primary">
            <div class="panel-heading text-center">
                LPP vs Plano Rekap (QTY Plano Termasuk QTY Yang Sudah Picking IDM/KLIK)
            </div>
            <div class="panel-body fixed-panel">
                <div class="row">
                    <div class="col-md-4 rekap"></div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Tidak Menghitung!</strong>
                                    &nbsp; Produk fraction KG,
                                    &nbsp; Saldo Akhir 0,
                                    &nbsp; AVG Cost 0,
                                    &nbsp; Tag IGR (H, A, N, O, X, U),
                                    &nbsp; Departement (31, 32, 42)
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h4 class="mt-0">5 Plu Plus Terbesar</h4>
                                <section class="pelus"></section>
                            </div>
                            <div class="col-md-12">
                                <h4 class="mt-0">5 Plu Minus Terbesar</h4>
                                <section class="minus"></section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../layout/_bottom.php'; ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.rekap').load('rekap.php');
            loadSection('tbl-pelus.php', '.pelus');
            loadSection('tbl-minus.php', '.minus');
        });

        function loadSection(url, targetSelector) {
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'html',
                beforeSend: function() {
                    $(targetSelector).html('<div class="ld"></div>');
                },
                success: function(response) {
                    $(targetSelector).html(response);
                },
                error: function(xhr) {
                    $(targetSelector).html('<div class="alert alert-danger">Error: ' + xhr.statusText + '</div>');
                }
            });
        }
    </script>