<?php
// Menambahkan header untuk mencegah caching halaman
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>maintenance</title>
      <!-- Favicon -->
    <link rel="icon" href="../assets/img/logo-spi.webp" type="image/png"> <!-- Ganti dengan path ke logo Anda -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #282c34;
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .container {
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            z-index: 10;
            position: relative;
        }

        .icon {
            font-size: 70px;
            margin-bottom: 20px;
            color: #ffe600;
        }

        img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
            border-radius: 10px;
        }

        h3 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
        }

        /* Spinner loading animation */
        .spinner-border {
            width: 4rem;
            height: 4rem;
            margin-top: 20px;
            color: #ffe600;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Background falling basket animation */
        .basket {
            position: absolute;
            width: 50px;
            height: 50px;
            background-image: url('https://img.icons8.com/emoji/48/shopping-cart.png'); /* URL gambar keranjang */
            background-size: contain;
            background-repeat: no-repeat;
            animation: fall linear infinite;
        }

        /* Keyframes animation for falling */
        @keyframes fall {
            0% {
                transform: translateY(-100px) translateX(calc(100vw * var(--start)));
            }
            100% {
                transform: translateY(110vh) translateX(calc(100vw * var(--start)));
            }
        }
    </style>
</head>
<body>


<div class="container">
    <div class="alert">
        <div class="icon">
            <i class="fa fa-cogs"></i>
        </div>
        <h3>Halaman Sedang Dalam Proses Deployment</h3>
        <p>Maaf, halaman yang Anda cari belum tersedia. Harap coba lagi nanti.</p>
        <!-- Menambahkan gambar -->
        <img src="../assets/img/spi.png" alt="Sedang Deployment" class="img-fluid">
        <!-- Menambahkan loading spinner -->
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <footer>&copy; 2025 - EDP SPIBDL</footer>
</div>

<!-- Menambahkan ikon untuk memberi efek lebih menarik -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

</body>
</html>
