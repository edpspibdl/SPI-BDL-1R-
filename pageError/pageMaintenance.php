<title>Maintenance</title>
<link rel="icon" href="../assets/img/logo-spi.webp" type="image/png">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
require_once '../layout/_top.php';
?>

<section class="page_maintenance">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-10 col-sm-offset-1 text-center">
                    <div class="maintenance_bg">
                        <h1 class="text-center">🚧</h1>
                    </div>
                    <div class="content_box_maintenance">
                        <h3 class="h2">Under Maintenance</h3>
                        <p>We are currently performing scheduled maintenance. We should be back shortly.</p>
                        <p><strong>Silakan coba lagi nanti.</strong></p>
                        <p>Estimated Time Remaining:</p>
                        <h2 id="countdown">00:00:00</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    // Set waktu selesai maintenance (contoh: 2 jam dari sekarang)
    var maintenanceEnd = new Date();
    maintenanceEnd.setHours(maintenanceEnd.getHours() + 100); // Tambah 2 jam

    function updateCountdown() {
        var now = new Date().getTime();
        var distance = maintenanceEnd - now;

        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML =
            (hours < 10 ? "0" + hours : hours) + ":" +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);

        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "Maintenance Completed";
        }
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>

</body>
</html>
