<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// kalau ada input, pakai itu, kalau belum ada kasih kosong
$input_value = isset($_POST['spd_days']) ? (int) $_POST['spd_days'] : '';
?>

<section class="section">
    <div class="section-header">
        <h3>Penerimaan Produk Baru
            <?php if ($input_value !== '') : ?>
                dalam <?php echo htmlspecialchars($input_value); ?> Hari Terakhir
            <?php endif; ?>
        </h3>
    </div>

    <div class="row">
        <!-- Kolom kiri: form -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="spdForm">
                        <div class="form-group">
                            <label for="spd_days">Range (Hari)</label>
                            <input type="number" name="spd_days" id="spd_days"
                                class="form-control" min="1" max="30"
                                value="<?php echo htmlspecialchars($input_value); ?>"
                                placeholder="Masukkan hari">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom kanan: report -->
        <div class="col-md-10">
            <?php
            if ($input_value !== '') {
                include 'report.php';
            } else {
                echo "<div class='alert alert-info'>Silakan masukkan jumlah hari untuk melihat report.</div>";
            }
            ?>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#spdForm').submit(function(e) {
            var spdDays = $('#spd_days').val();
            if (spdDays > 30) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Jumlah SPD Days tidak boleh lebih dari 30!',
                });
            }
        });
    });
</script>

<?php
require_once '../layout/_bottom.php';
?>