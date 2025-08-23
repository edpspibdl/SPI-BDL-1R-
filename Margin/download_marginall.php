<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\MARGIN\\';

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
            PRD_KODEDIVISI AS DIV,        
            PRD_KODEDEPARTEMENT AS DEP,        
            PRD_KODEKATEGORIBARANG AS KAT,        
            CAST(PRD_PRDCD AS NUMERIC) AS PLU,       
            PRD_DESKRIPSIPANJANG AS DESK,        
            PRD_FRAC AS FRAC,        
            PRD_UNIT AS UNIT,        
            ROUND(PRD_AVGCOST, 2) AS AVGCOST,        
            ROUND(PRD_LASTCOST, 2) AS LCOST,     
            LPP,        
            ROUND(
                CASE         
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 = 'Y' THEN ((HRGJUAL - PRD_AVGCOST * 1.11) / HRGJUAL) * 100   
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 <> 'Y' THEN ((HRGJUAL - PRD_AVGCOST) / HRGJUAL) * 100    
                    WHEN PRD_FLAGBKP1 = 'N' THEN ((HRGJUAL - PRD_AVGCOST) / HRGJUAL) * 100    
                END, 2
            ) AS MARGINACOST,        
            ROUND(
                CASE         
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 = 'Y' THEN ((HRGJUAL - PRD_LASTCOST * 1.11) / HRGJUAL) * 100  
                    WHEN PRD_FLAGBKP1 = 'Y' AND PRD_FLAGBKP2 <> 'Y' THEN ((HRGJUAL - PRD_LASTCOST) / HRGJUAL) * 100  
                    WHEN PRD_FLAGBKP1 = 'N' THEN ((HRGJUAL - PRD_LASTCOST) / HRGJUAL) * 100        
                END, 2
            ) AS MARGINLCOST, 
            COALESCE(QTY_PO_OUT, 0) AS PO_OUT 
        FROM (
            SELECT        
                PRD_KODECABANG,   
                prd_recordid,			
                PRD_KATEGORITOKO,     
                PRD_KODEDIVISI,        
                PRD_KODEDEPARTEMENT,        
                PRD_KODEKATEGORIBARANG,        
                PRD_PRDCD,        
                PRD_DESKRIPSIPANJANG,        
                PRD_FRAC,        
                PRD_UNIT,        
                CASE        
                    WHEN PRMD_HRGJUAL IS NULL THEN PRD_HRGJUAL        
                    ELSE PRMD_HRGJUAL        
                END AS HRGJUAL,        
                ROUND(PRD_AVGCOST, 2) AS PRD_AVGCOST,        
                ROUND(PRD_LASTCOST, 2) AS PRD_LASTCOST,        
                ST_SALDOAKHIR AS LPP,        
                PRD_FLAGBKP1,        
                PRD_FLAGBKP2        
            FROM tbmaster_prodmast        
            LEFT JOIN (
                SELECT * 
                FROM TBTR_PROMOMD 
                WHERE DATE_TRUNC('day', PRMD_TGLAKHIR) >= DATE_TRUNC('day', CURRENT_DATE)
            ) prm ON PRD_PRDCD = PRMD_PRDCD        
            LEFT JOIN (
                SELECT * 
                FROM TBMASTER_STOCK 
                WHERE ST_LOKASI = '01'
            ) st ON PRD_PRDCD = ST_PRDCD
        ) SUB1       
        LEFT JOIN (
            SELECT TPOD_PRDCD, SUM(QTY_PO_OUT) AS QTY_PO_OUT 
            FROM (
                SELECT 
                    d.tpod_prdcd,
                    SUM(d.tpod_qtypo) AS qty_po_out
                FROM tbtr_po_d d
                JOIN tbtr_po_h h ON d.tpod_nopo = h.tpoh_nopo
                WHERE (d.tpod_qtypb = '0' OR d.tpod_qtypb IS NULL)
                  AND DATE_TRUNC('day', h.tpoh_tglpo + (h.tpoh_jwpb || ' days')::interval) >= DATE_TRUNC('day', CURRENT_DATE)
                  AND h.tpoh_recordid IS NULL
                GROUP BY d.tpod_prdcd
                ORDER BY d.tpod_prdcd
            ) po_out
            GROUP BY TPOD_PRDCD
        ) po_out ON PRD_PRDCD = TPOD_PRDCD 
        WHERE PRD_PRDCD LIKE '%0' 
        AND prd_recordid IS NULL
        ORDER BY 8 DESC"; // Ganti dengan query Anda


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
    $filename = "MARGIN_ALL_SPI_BDL_1R_$date.xlsx";
    $filePath = $tempSavePath . $filename;  // Menentukan lokasi penyimpanan file sementara

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
?>
