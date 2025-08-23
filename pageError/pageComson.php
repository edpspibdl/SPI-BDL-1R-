<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <link rel="icon" href="../assets/img/logo-spi.webp" type="image/png">
    <style>
        
        .container {
            background: white;
            justify-content: center;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .coming_soon_bg h1 {
            font-size: 100px;
            color: #ff9800;
            margin: 0;
            text-align: center;
        }

        .content_box_coming_soon h3 {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        .content_box_coming_soon p {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }

        #countdown {
    font-size: 32px;
    font-weight: bold;
    color: #ff5722;
    background: #fff3e0;
    padding: 10px 20px;
    border-radius: 5px;
    display: block; /* Memastikan elemen bisa diatur dengan margin */
    margin: 10px auto; /* Center secara horizontal */
    text-align: center;
    width: fit-content; /* Agar ukurannya sesuai dengan teks di dalamnya */
}

/* Pastikan parent juga memusatkan konten */
.content_box_coming_soon {
    display: flex;
    flex-direction: column;
    align-items: center; /* Memusatkan elemen di dalam */
    justify-content: center;
    text-align: center;
}

    </style>
</head>
<body>
    <?php require_once '../layout/_top.php'; ?>

    <section class="page_coming_soon">
        <div class="container">
            <div class="coming_soon_bg">
                <h1>🚀</h1>
            </div>
            <div class="content_box_coming_soon">
                <h3>Coming Soon</h3>
                <p>We are preparing something exciting. Stay tuned!</p>
                <p><strong>Silakan kembali lagi nanti.</strong></p>
                <p>Estimated Time Until Launch:</p>
                <h2 id="countdown">00:00:00</h2>
            </div>
        </div>
    </section>

    <?php require_once '../layout/_bottom.php'; ?>

    <script>
        // Set waktu peluncuran (7 hari dari sekarang)
        var launchDate = new Date();
        launchDate.setDate(launchDate.getDate() + 7);

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = launchDate - now;

            if (distance <= 0) {
                document.getElementById("countdown").innerHTML = "Launching Soon!";
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML =
                (days > 0 ? days + "d " : "") +
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>
