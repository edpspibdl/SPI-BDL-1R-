<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\DISKON MINUS\\';

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
$query = "SELECT prd_kodedivisi AS div, prmd_prdcd AS plu, prd_deskripsipanjang AS deskripsi, prd_kodetag AS tag, prd_frac AS frac, prd_unit AS unit, st_saldoakhir AS stok,
                prd_hrgjual AS hrg_normal, prmd_hrgjual AS hrg_promo, prd_hrgjual - prmd_hrgjual AS diskon
                FROM tbtr_promomd
                LEFT JOIN tbmaster_prodmast ON prmd_prdcd = prd_prdcd
                LEFT JOIN tbmaster_stock ON st_prdcd = prmd_prdcd
                WHERE st_lokasi = '01' AND date_trunc('day' , prmd_tglakhir) >=(current_date) AND prd_hrgjual - prmd_hrgjual < 0
                ORDER BY deskripsi, plu"; // Ganti dengan query Anda


try {
  $stmt = $conn->prepare($query);
  $stmt->execute();

  // Ambil kolom pertama untuk menentukan header
  $columns = $stmt->fetch(PDO::FETCH_ASSOC);

  // Tentukan nama file dengan tanggal saat ini
  $date = date('Y-m-d');
  $filename = "DISKON_MINUS_SPI_BDL_1R_$date.xlsx";
  $filePath = $tempSavePath . $filename;  // Menentukan lokasi penyimpanan file sementara

  // Inisialisasi objek writer
  $writer = new XLSXWriter();

  // Jika data ditemukan
  if ($columns) {
    // Ambil nama kolom untuk header Excel
    $columnNames = array_map('strtoupper', array_keys($columns));
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));

    // Loop untuk menulis setiap row ke Excel
    do {
      $writer->writeSheetRow('Sheet1', $columns);
    } while ($columns = $stmt->fetch(PDO::FETCH_ASSOC));
     } else {
    // Jika data kosong, buat file dengan hanya header
    $columnNames = ['DIV', 'PLU', 'DESKRIPSI', 'TAG', 'FRAC', 'UNIT', 'STOK', 'HRG_NORMAL', 'HRG_PROMO', 'DISKON'];
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));
  }

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
            error_log("File tidak ditemukan di: $filePath");
            die("File tidak ditemukan di: $filePath");
        }
    } else {
        // Log error jika gagal menyimpan file
        error_log("Gagal menyimpan file di: $filePath");
        die("Gagal menyimpan file di: $filePath");
    }
} catch (PDOException $e) {
    error_log("Query gagal: " . $e->getMessage());
    die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
