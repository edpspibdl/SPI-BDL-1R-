<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Form Input Tanggal SPB</h3>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="report.php" method="POST" id="spbForm">
                        <div class="form-group">
                            <label for="spb_days">SPB Days</label>
                            <input type="date" name="spb_days" id="spb_days" class="form-control" placeholder="Masukkan jumlah SPB Days" required max="30">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    $(document).ready(function() {
        // Initialize Bootstrap Datepicker
        if (typeof $.fn.datepicker !== 'undefined') {
            $('#tanggalMulai').datepicker({
                format: 'dd-M-yyyy',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true
            });
        } else {
            console.log("Datepicker not found");
        }

        // Form validation for SPD Days
        $('#spbForm').submit(function(e) {
            var spbDays = $('#spb_days').val();
            if (spbDays > 30) {
                e.preventDefault(); // Prevent form submission
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