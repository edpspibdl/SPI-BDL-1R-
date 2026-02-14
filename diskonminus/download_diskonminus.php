<?php
date_default_timezone_set('Asia/Jakarta');

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php';
require_once '../helper/connection.php';

/* ======================
   DETEKSI MODE
   ====================== */
$isJob = defined('JOB_MODE') && JOB_MODE === true;

/* ======================
   PATH FILE
   ====================== */
$tempSavePath = 'D:\\Laporan\\Laporan Pagi\\DISKON MINUS\\';

if (!file_exists($tempSavePath)) {
    if (!mkdir($tempSavePath, 0777, true)) {
        error_log("Gagal membuat folder: $tempSavePath");
        if (!$isJob) die("Gagal membuat folder");
        return;
    }
}

if (!is_writable($tempSavePath)) {
    error_log("Folder tidak writable: $tempSavePath");
    if (!$isJob) die("Folder tidak dapat ditulisi");
    return;
}

/* ======================
   QUERY
   ====================== */
$query = "
SELECT
    prd_kodedivisi AS div,
    prmd_prdcd AS plu,
    prd_deskripsipanjang AS deskripsi,
    prd_kodetag AS tag,
    prd_frac AS frac,
    prd_unit AS unit,
    st_saldoakhir AS stok,
    prd_hrgjual AS hrg_normal,
    prmd_hrgjual AS hrg_promo,
    prd_hrgjual - prmd_hrgjual AS diskon
FROM tbtr_promomd
LEFT JOIN tbmaster_prodmast ON prmd_prdcd = prd_prdcd
LEFT JOIN tbmaster_stock ON st_prdcd = prmd_prdcd
WHERE st_lokasi = '01'
AND date_trunc('day', prmd_tglakhir) >= current_date
AND prd_hrgjual - prmd_hrgjual < 0
ORDER BY deskripsi, plu
";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $date = date('Y-m-d');
    $filename = "DISKON_MINUS_SPI_BDL_1R_$date.xlsx";
    $filePath = $tempSavePath . $filename;

    $writer = new XLSXWriter();

    $headers = [
        'DIV','PLU','DESKRIPSI','TAG','FRAC','UNIT',
        'STOK','HRG_NORMAL','HRG_PROMO','DISKON'
    ];

    $writer->writeSheetHeader(
        'Sheet1',
        array_combine($headers, array_fill(0, count($headers), 'string'))
    );

    if ($row) {
        do {
            $writer->writeSheetRow('Sheet1', $row);
        } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
    }

    if (!$writer->writeToFile($filePath)) {
        error_log("Gagal menyimpan file: $filePath");
        if (!$isJob) die("Gagal menyimpan file");
        return;
    }

    error_log("DISKON MINUS OK: $filePath");

    /* ======================
       MODE USER
       ====================== */
    if (!$isJob) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        readfile($filePath);
        unlink($filePath);
        exit;
    }

    /* ======================
       MODE JOB
       ====================== */
    return;

} catch (Throwable $e) {
    error_log("DISKON MINUS ERROR: " . $e->getMessage());
    if (!$isJob) die($e->getMessage());
    return;
}
