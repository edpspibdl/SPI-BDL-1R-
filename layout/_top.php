<?php
// Logging IP pengunjung ke file TXT (tanpa database)
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$waktu = date("Y-m-d H:i:s");
$halaman = $_SERVER['REQUEST_URI'];
$logBaris = "[$waktu] IP: $ip | Page: $halaman | Agent: $userAgent" . PHP_EOL;

// Simpan ke file log_pengunjung.txt (pastikan folder writable)
@file_put_contents("../log_pengunjung.txt", $logBaris, FILE_APPEND | LOCK_EX);

require_once '../helper/auth.php';
isLogin();

// SweetAlert helper (letakkan di top, cukup panggil sekali)
require_once '../helper/sweetalert_helper.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Web Cabang SPI BDL 1R</title>

  <!-- Favicon -->
  <link rel="icon" href="../assets/img/logo-spi.webp" type="image/png">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet"
    href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet"
    href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/izitoast/css/iziToast.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <!-- Loading animation -->
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- SweetAlert helper -->
  <script src="../assets/js/sweetalert-helper.js"></script>

</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php
      // Header & sidebar
      require_once '_header.php';
      require_once '_sidenav.php';
      ?>

      <!-- Main Content -->
      <div class="main-content">
