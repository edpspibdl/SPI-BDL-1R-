<?php
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/../helper/connection.php';

/* ======================
   MODE JOB (PENTING)
   ====================== */
define('JOB_MODE', true);

/* ======================
   LOG FILE
   ====================== */
$logFile = __DIR__ . '/log_download_all_' . date('Ymd') . '.log';
file_put_contents($logFile, "START " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

/* ======================
   DAFTAR SCRIPT LAPORAN
   ====================== */
$reports = [
    "../diskonminus/download_diskonminus.php",
    "../allitem/download_allitem.php",
    "../poOut/download_poout.php",
    "../allitem/download_allitemigrbdl.php",
    "../Margin/download_marginall.php",
    "../Margin/download_marmin.php",
    "../lppvsPlano/download_data.php",
    "../perHargaPagi/download_perhargapagi.php",
    "../Hj/download_hj.php",
    "../laporanDisc4/download_disc4.php",
    "../btb&retur(ACC)/download_btb_retur.php",
    "../cbPerHari/download_cb_perhari.php"
];

/* ======================
   EKSEKUSI SATU PER SATU
   ====================== */
foreach ($reports as $file) {

    $path = realpath(__DIR__ . '/' . $file);

    if (!$path || !file_exists($path)) {
        file_put_contents($logFile, "NOT FOUND: $file" . PHP_EOL, FILE_APPEND);
        continue;
    }

    file_put_contents($logFile, "RUN: $file" . PHP_EOL, FILE_APPEND);

    try {
        include $path;
        file_put_contents($logFile, "SUCCESS: $file" . PHP_EOL, FILE_APPEND);
    } catch (Throwable $e) {
        file_put_contents(
            $logFile,
            "ERROR: $file | " . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );
    }
}

/* ======================
   FINISH
   ====================== */
file_put_contents($logFile, "FINISH " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

echo "DOWNLOAD ALL LAPORAN SELESAI\n";
