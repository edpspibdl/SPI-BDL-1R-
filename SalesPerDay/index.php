<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">SALES PER DAY</h3>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="report.php" method="POST" id="spdForm">
                        <div class="form-group">
                            <label for="spd_days">Pilih Tanggal Sales</label>
                            <input type="date" name="spd_days" id="spd_days" class="form-control" placeholder="Masukkan jumlah SPD Days" required max="30">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Cari Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function () {
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
        $('#spdForm').submit(function (e) {
            var spdDays = $('#spd_days').val();
            if (spdDays > 30) {
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
</section>



<?php
require_once '../layout/_bottom.php';
?>
