<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<style>
    body {
        background-color: #f8f9fa; 	
    }

    .container-custom {
        max-width: 900px;
        margin: auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .panel-heading {
        background-color: #007bff;
        color: white;
        padding: 10px;
        font-size: 18px;
        border-radius: 6px 6px 0 0;
        text-align: center;
    }

    /* Frame Container */
    .frame-container {
        width: 100%;
        display: none; /* Default: tersembunyi */
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin-top: 20px;
    }

    /* Frame Kertas */
    .frame-kertas {
        width: 210mm;
        height: 297mm;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        position: relative;
        overflow: auto;
        margin: auto; /* Membuatnya selalu di tengah */
    }

    .kop-surat {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .garis {
        border-bottom: 2px solid black;
        width: 100%;
        margin-bottom: 15px;
    }

    .tanggal-cetak {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 14px;
        font-weight: bold;
    }

    .iframe-container {
        width: 100%;
        height: 100%;
        border: none;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    @media print {
        body {
            background: none;
        }
        .frame-container {
            background: none;
            box-shadow: none;
            padding: 0;
        }
        .frame-kertas {
            box-shadow: none;
            width: 100%;
            height: auto;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            display: block !important;
        }
        .tanggal-cetak {
            display: block !important;
        }
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>FORM SO HARIAN</h1>
    </div>
    <div id="load"></div>

    <!-- FORM INPUT PLU -->
    <div id="inputContainer" class="container container-custom mt-5">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="mb-0">STOK OPNAME HARIAN</h3>
                <div class="note mt-2">
                    ⚠️ Harap masukkan pelan-pelan! ⚠️
                </div>
            </div>

            <div class="panel-body p-4">
                <form id="stokOpnameForm" class="form">
                    <div class="mb-3">
                        <label for="plu" class="form-label fw-bold">Masukkan PLU:</label>
                        <textarea class="form-control" id="plu" name="plu" rows="3" placeholder="Contoh: 850,60410,357330" required></textarea>
                        <div class="info-text">
                            Gunakan tanda koma (,) untuk memisahkan PLU tanpa spasi. <br>
                            <i>Contoh: 0000850,0060410,357330,357320</i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="reset" class="btn btn-success btn-round px-4">Bersihkan</button>
                        <button type="submit" class="btn btn-primary btn-round px-4">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- HASIL STOK OPNAME -->
    <div id="resultCard" class="frame-container mt-4">
        
        <!-- TOMBOL CETAK & KEMBALI DI ATAS -->
        <div class="btn-group">
            <button id="backButton" class="fas fa-backward btn btn-light btn-sm"> Kembali</button>
            <button id="printButton" class="fas fa-print btn btn-success btn-sm"> Cetak</button>
        </div>

        <div class="frame-kertas">
            <div class="kop-surat" id="kopCetak">
                STOCK POINT INDOGROSIR METRO 1R
            </div>
            <div class="tanggal-cetak" id="tanggalCetak">
                Tanggal Cetak: <?= date('d-m-Y') ?>
            </div>
            <div class="garis"></div>

            <iframe id="resultFrame" class="iframe-container"></iframe>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $("#load").fadeOut();

        $("#stokOpnameForm").submit(function(event){
            event.preventDefault();
            let pluValue = $("#plu").val().trim();
            if (pluValue === "") {
                alert("Harap masukkan PLU sebelum menampilkan data!");
                return;
            }

            $("#inputContainer").hide();
            $("#resultCard").fadeIn().css("display", "flex"); // Pastikan muncul dengan flexbox
            $("#resultFrame").attr("src", "tampildata.php?plu=" + encodeURIComponent(pluValue));
        });

        $("#backButton").click(function(){
            $("#resultCard").hide();
            $("#inputContainer").fadeIn();
        });

        $("#printButton").click(function(){
            let iframe = document.getElementById("resultFrame").contentWindow;
            if (iframe) {
                iframe.focus();
                iframe.print();
            } else {
                alert("Tidak dapat mencetak. Pastikan data sudah tampil.");
            }
        });
    });
</script>

<?php
require_once '../layout/_bottom.php';
?>
