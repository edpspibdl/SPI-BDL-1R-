<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\ALL ITEM & PO OUT\\';

// Memeriksa apakah folder ada, jika tidak, buat folder tersebut
if (!file_exists($tempSavePath)) {
    // Parameter ketiga 'true' berarti akan membuat direktori secara rekursif jika parent directory tidak ada
    // Parameter kedua '0777' adalah izin folder (baca, tulis, eksekusi untuk semua).
    // Anda mungkin ingin menggunakan izin yang lebih ketat seperti 0755 di lingkungan produksi.
    if (!mkdir($tempSavePath, 0777, true)) {
        die("Gagal membuat folder: $tempSavePath. Pastikan PHP memiliki izin untuk membuat folder.");
    }
    error_log("Folder berhasil dibuat: $tempSavePath");
}

// Memeriksa apakah folder dapat ditulis (setelah dipastikan ada atau dibuat)
if (!is_writable($tempSavePath)) {
    die("Folder tidak dapat ditulisi: $tempSavePath. Periksa izin folder.");
}

// Query data dari database
$query = "SELECT DISTINCT
    tpod_prdcd AS PLU,
    tpod_qtypo AS QTY,
    tpod_nopo AS NO_PO,
    tpoh_kodesupplier AS KODE_SUPP,
    tms.sup_namasupplier AS NAMA_SUPP,
    DATE_TRUNC('DAY', tpoh_tglpo + (tpoh_jwpb || ' days')::interval) AS TGL_PO_OUT_MATI
FROM tbtr_po_d
LEFT JOIN (
    SELECT tpoh_nopo, tpoh_tglpo, tpoh_jwpb, tpoh_kodesupplier
    FROM tbtr_po_h
    WHERE tpoh_recordid IS NULL OR tpoh_recordid = 'X'
) AS po_header ON tpoh_nopo = tpod_nopo
LEFT JOIN tbmaster_supplier tms ON tms.sup_kodesupplier = tpoh_kodesupplier
WHERE DATE_TRUNC('DAY', tpoh_tglpo + (tpoh_jwpb || ' days')::interval) >= CURRENT_DATE
ORDER BY tpod_prdcd ASC"; // Ganti dengan query Anda

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Ambil kolom pertama untuk menentukan header
    $columns = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$columns) {
        die("Tidak ada data yang ditemukan.");
    }

    // Ambil nama kolom untuk header Excel
    $columnNames = array_map('strtoupper', array_keys($columns));

    // Tentukan nama file dengan tanggal saat ini
    $date = date('Y-m-d');
    $filename = "PO_OUT_TGL_MATI_SPI_BDL_1R_$date.xlsx";
    $filePath = $tempSavePath . $filename; // Menentukan lokasi penyimpanan file sementara

    // Inisialisasi objek writer
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));

    // Loop untuk menulis setiap row ke Excel
    do {
        $writer->writeSheetRow('Sheet1', $columns);
    } while ($columns = $stmt->fetch(PDO::FETCH_ASSOC));

    // Simpan file sementara di server
    if ($writer->writeToFile($filePath)) {
        // Cek apakah file berhasil disimpan
        if (file_exists($filePath)) {
            // Log server untuk memastikan file sudah ditemukan
            error_log("File berhasil disimpan di: $filePath");

            // Mulai pengunduhan file
            ob_end_flush(); // Flush output buffer

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            // Baca file dan kirimkan ke browser
            readfile($filePath);

            // Hapus file sementara setelah pengunduhan
            unlink($filePath);
            exit;
        } else {
            // Log error jika file tidak ditemukan
            error_log("File tidak ditemukan di: " . $filePath);
            die("File tidak ditemukan di: " . $filePath);
        }
    } else {
        // Log error jika gagal menyimpan file
        error_log("Gagal menyimpan file di: " . $filePath);
        die("Gagal menyimpan file di: " . $filePath);
    }
} catch (PDOException $e) {
    error_log("Query gagal: " . $e->getMessage());
    die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
