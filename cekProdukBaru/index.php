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

    .iframe-container {
        width: 100%;
        height: 600px;
        border: none;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 10px;
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>FORM CEK PRODUK BARU</h1>
    </div>
    <div id="load"></div>

    <!-- FORM INPUT PLU -->
    <div id="inputContainer" class="container container-custom mt-5">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="mb-0">CEK PRODUK BARU</h3>
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

                    <!-- Upload Excel -->
                    <div class="mb-3">
                        <label for="excelFile" class="form-label fw-bold">Upload Excel (Opsional):</label>
                        <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx">
                        <div class="info-text">
                            File harus berisi daftar PLU di kolom pertama. <br>
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

    <!-- HASIL -->
    <div id="resultCard" class="mt-4" style="display:none;">

        <iframe id="resultFrame" name="resultFrame" class="iframe-container"></iframe>

        <!-- Hidden form untuk POST ke iframe -->
        <form id="hiddenForm" method="POST" target="resultFrame" action="tampildata.php" style="display:none;">
            <input type="hidden" name="plu" id="hiddenPlu">
        </form>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function(){
        $("#load").fadeOut();

        // Submit form utama
        $("#stokOpnameForm").submit(function(event){
            event.preventDefault();
            let pluValue = $("#plu").val().trim();
            if (pluValue === "") {
                alert("Harap masukkan PLU sebelum menampilkan data!");
                return;
            }

            $("#inputContainer").hide();
            $("#resultCard").fadeIn(); 

            // masukkan ke hidden form lalu submit via POST ke iframe
            $("#hiddenPlu").val(pluValue);
            $("#hiddenForm").submit();
        });

        // Tombol kembali
        $("#backButton").click(function(){
            $("#resultCard").hide();
            $("#inputContainer").fadeIn();
        });

        // Tombol cetak
        $("#printButton").click(function(){
            let iframe = document.getElementById("resultFrame").contentWindow;
            if (iframe) {
                iframe.focus();
                iframe.print();
            } else {
                alert("Tidak dapat mencetak. Pastikan data sudah tampil.");
            }
        });

        // Baca file Excel dan masukkan ke textarea
        document.getElementById("excelFile").addEventListener("change", function(e){
            let file = e.target.files[0];
            if (!file) return;

            let reader = new FileReader();
            reader.onload = function(e) {
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, {type: "array"});
                let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                let excelData = XLSX.utils.sheet_to_json(firstSheet, {header: 1});
                
                // Ambil semua PLU dari kolom pertama
                let pluList = excelData.map(row => row[0]).filter(x => x != null);
                $("#plu").val(pluList.join(",")); 
            };
            reader.readAsArrayBuffer(file);
        });
    });
</script>

<?php
require_once '../layout/_bottom.php';
?>
