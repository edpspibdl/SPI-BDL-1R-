<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAP RUTIN\\BULANAN\\LAPORAN GUDANG\\';

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
$query = "SELECT 
  CASE WHEN mstd_typetrn='B' THEN 'BPB' ELSE 'RETUR' END AS status, 
  sup_kodeigr AS cbg, 
  sup_kodesupplier AS kd_supp, 
  sup_kodesuppliermcg AS kd_supmcg, 
  sup_namasupplier AS nm_sup, 
  mstd_nodoc AS nodoc, 
  TO_CHAR(mstd_tgldoc, 'YYYYMM') AS blndoc, 
  mstd_tgldoc AS tgldoc, 
  mstd_nopo AS no_po, 
  mstd_tglpo AS tglpo, 
  mstd_prdcd AS plu, 
  prd_deskripsipanjang AS deskripsi, 
  mstd_bkp AS bkp, 
  mstd_unit AS unit, 
  mstd_frac AS frac, 
  mstd_qty AS qty, 
  mstd_gross AS gross, 
  mstd_discrph AS disc, 
  mstd_ppnrph AS ppn 
FROM 
  tbmaster_supplier, 
  tbtr_mstran_d, 
  tbmaster_prodmast 
WHERE 
  mstd_typetrn IN ('B','K') 
  and date_trunc('year', mstd_tgldoc) = date_trunc('year', CURRENT_DATE)
 --AND TO_CHAR(mstd_tgldoc, 'YYYY') = '2025' 
  and date_trunc('month', mstd_tgldoc) = date_trunc('month', CURRENT_DATE)
  --AND TO_CHAR(mstd_tgldoc, 'MM') = '10' -- GANTI
  AND mstd_kodesupplier = sup_kodesupplier 
  AND mstd_prdcd = prd_prdcd 
  AND mstd_recordid IS NULL 
ORDER BY 
  mstd_nodoc";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Ambil metadata kolom untuk menentukan header
    $columnCount = $stmt->columnCount();
    $columnNames = [];
    
    // Tentukan kolom header secara eksplisit (sesuai urutan SELECT)
    // Jika data kosong, kita tidak bisa menggunakan fetch, jadi harus menentukan header secara manual.
    // Daftar header eksplisit (sesuai urutan di query SELECT):
    $explicitHeaders = [
    'STATUS',
    'CBG',
    'KD_SUPP',
    'KD_SUPMCG',
    'NM_SUP',
    'NODOC',
    'BLNDOC',
    'TGLDOC',
    'NO_PO',
    'TGLPO',
    'PLU',
    'DESKRIPSI',
    'BKP',
    'UNIT',
    'FRAC',
    'QTY',
    'GROSS',
    'DISC',
    'PPN'
          ];

    $columnNames = $explicitHeaders;
    
    // Ambil baris pertama data
    $columns = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Jika ada data, gunakan nama kolom dari data untuk header (OPSIONAL: untuk memastikan nama kolom benar)
    // if ($columns) {
    //     $columnNames = array_map('strtoupper', array_keys($columns));
    // }

    // Tentukan nama file dengan tanggal saat ini
    $date = date('Y-m-d');
    $filename = "37. BTB_&_RETUR_SPI_BDL_1R.xlsx";
    $filePath = $tempSavePath . $filename;   // Menentukan lokasi penyimpanan file sementara

    // Inisialisasi objek writer
    $writer = new XLSXWriter();
    
    // Tulis Header. Ini dilakukan sebelum looping data, TIDAK peduli apakah data kosong atau tidak.
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));

    // Cek apakah ada data (baris pertama sudah diambil di $columns)
    if ($columns) {
        // Loop untuk menulis setiap row ke Excel
        do {
            $writer->writeSheetRow('Sheet1', $columns);
        } while ($columns = $stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        // Log jika data kosong
        error_log("Query berhasil dieksekusi, tetapi tidak ada data yang ditemukan. File Excel akan dibuat hanya dengan header.");
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