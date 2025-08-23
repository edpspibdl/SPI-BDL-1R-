<?php
require_once '../layout/_top.php';
$jenisLaporan = $_GET['jenisLaporan'] ?? '1';
$queryFile = "query_{$jenisLaporan}.php";
$tabelFile = "tabel_{$jenisLaporan}.php";
if (file_exists($queryFile)) require $queryFile;
else die("Query untuk jenis laporan {$jenisLaporan} tidak ditemukan.");
if (file_exists($tabelFile)) require $tabelFile;
else die("Tabel untuk jenis laporan {$jenisLaporan} tidak ditemukan.");
require_once '../layout/_bottom.php';
?>
