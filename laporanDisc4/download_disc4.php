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
$query = "SELECT * FROM (
    SELECT 
        i.mstd_recordid,     
        i.mstd_typetrn,      
        i.mstd_nodoc,        
        i.mstd_tgldoc,       
        i.mstd_kodesupplier, 
        i.mstd_prdcd,        
        j.prd_deskripsipendek, 
        j.prd_deskripsipanjang, 
        i.mstd_unit,         
        i.mstd_frac,         
        i.mstd_qty,          
        i.mstd_gross,        
        i.mstd_dis4jp,       
        i.mstd_dis4jr         
    FROM tbtr_mstran_d i      
    JOIN tbmaster_prodmast j      
    ON i.mstd_prdcd = j.prd_prdcd
    -- Filter di inner query tetap sama
    WHERE i.mstd_recordid IS NULL       
    AND i.mstd_typetrn = 'B'      
    AND (j.prd_deskripsipanjang LIKE '%LARISST%'      
          OR j.prd_deskripsipendek LIKE '%LARISST%')      
    ORDER BY i.mstd_tgldoc
) sub1
-- PERBAIKAN DINAMIS UNTUK BULAN BERJALAN
WHERE 
    -- Bandingkan tanggal dokumen dengan bulan/tahun dari tanggal saat ini.
    date_trunc('month', mstd_tgldoc) = date_trunc('month', CURRENT_DATE)
ORDER BY 4 DESC";

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
        'MSTD_RECORDID', 'MSTD_TYPETRN', 'MSTD_NODOC', 'MSTD_TGLDOC', 'MSTD_KODESUPPLIER',
        'MSTD_PRDCD', 'PRD_DESKRIPSIPENDEK', 'PRD_DESKRIPSIPANJANG', 'MSTD_UNIT', 'MSTD_FRAC',
        'MSTD_QTY', 'MSTD_GROSS', 'MSTD_DIS4JP', 'MSTD_DIS4JR'
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
    $filename = "36. DISC4_LARIST_SPI_BDL_1R.xlsx";
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