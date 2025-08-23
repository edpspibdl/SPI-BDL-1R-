<?php
require_once '../layout/_top.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>History SO</h1>
    </div>

    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Get PLU History SO</h5>
            </div>
            <div class="card-body">
                <form id="search-form">
                    <div class="input-group">
                        <input type="text" class="form-control plu" name="plu" placeholder="Masukkan PLU" required>
                        <div class="input-group-append">
                            <button class="btn btn-danger" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Animasi Loading -->
                <div id="loading" class="text-center mt-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Sedang mencari...</p>
                </div>
            </div>
        </div>

        <!-- CARD SENSII -->
        <div id="data-container" class="mt-3"></div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".plu").focus();

        $("#search-form").submit(function(e) {
            e.preventDefault();
            let plu = $(".plu").val();

            $("#loading").show();
            $("#data-container").html("");

            $.post("get_data.php", {
                plu: plu
            }, function(response) {
                $("#loading").hide();

                if (response.trim() === "") {
                    $("#data-container").html(`
                    <div class="card border-danger">
                        <div class="card-body text-center text-danger">
                            <h5 class="card-title">Data Tidak Ditemukan</h5>
                            <p class="card-text">PLU yang Anda cari tidak tersedia dalam database.</p>
                        </div>
                    </div>
                `);
                } else {
                    $("#data-container").html(`
                    <div class="card shadow">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">Data History SO</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">${response}</div>
                        </div>
                    </div>
                `);
                }
            }).fail(function(xhr) {
                $("#loading").hide();
                $("#data-container").html(`
                <div class="card border-danger">
                    <div class="card-body text-center text-danger">
                        <h5 class="card-title">Terjadi Kesalahan</h5>
                        <p class="card-text">${xhr.responseText}</p>
                    </div>
                </div>
            `);
            });
        });
    });
</script>

<?php
require_once '../layout/_bottom.php';
?>