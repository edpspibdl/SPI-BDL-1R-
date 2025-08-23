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
$query = "SELECT PRD_KODEDIVISI DIV,
  PRD_PRDCD PLU,
  PRD_DESKRIPSIPANJANG DESKRIPSI,
  PRD_FRAC FRAC,
  PRD_UNIT UNIT,
  PRD_KODETAG TAG,
  ST_SALDOAKHIR LPP,
  LCOST LCOST,
  ACOST ACOST_EXC,
  ACOST_INCLUDE ACOST_INC,
  PRD_HRGJUAL HRG_NORMAL,
  PRMD_HRGJUAL HRG_MD,
  MARGIN_A MARGIN_ACOST,
  MARGIN_L MARGIN_LCOST,
  MARGIN_A_MD,
  MARGIN_L_MD
FROM
  (SELECT PRD_KODEDIVISI,
    PRD_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_FRAC,
    PRD_UNIT,
    PRD_KODETAG,
    ST_SALDOAKHIR,
    PRD_HRGJUAL,
    PRMD_HRGJUAL,
    LCOST,
    ACOST,
    ACOST_INCLUDE,
    MARGIN_A,
    MARGIN_L,
    
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRMD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
          ELSE (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
        END AS MARGIN_A_MD,
   
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRMD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
          ELSE (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
        END AS MARGIN_L_MD FROM(SELECT PRD_KODEDIVISI,
  PRD_PRDCD,
  PRD_DESKRIPSIPANJANG,
  PRD_FRAC,
  PRD_UNIT,
  PRD_KODETAG,
  ST_SALDOAKHIR,
  PRD_HRGJUAL,
  ST_LASTCOST,prd_flagbkp2,prd_flagbkp1,ST_AVGCOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN (ST_LASTCOST*PRD_FRAC)/1000
    ELSE ST_LASTCOST *PRD_FRAC
  END AS LCOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN (ST_AVGCOST*PRD_FRAC)/1000
    ELSE ST_AVGCOST *PRD_FRAC
  END AS ACOST,
  CASE
    WHEN PRD_UNIT='KG'
    THEN ((ST_AVGCOST*PRD_FRAC)/1000)*1.11
    ELSE (ST_AVGCOST *PRD_FRAC)*1.11
  END AS ACOST_INCLUDE,
   
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
          ELSE (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
        END AS MARGIN_A,
  
        CASE
          WHEN PRD_UNIT='KG'
          THEN (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
          WHEN COALESCE(prd_flagbkp1,'T') ='Y' and COALESCE(prd_flagbkp2,'T') ='Y'
          THEN (((PRD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
          ELSE (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
        END AS MARGIN_L
  
FROM
(SELECT SUBSTR(PRD_PRDCD,1,6)
  ||0 PLU,
  PRD_PRDCD,
  PRD_KODEDIVISI,
  PRD_KODEDEPARTEMENT,
  PRD_KODEKATEGORIBARANG,
  PRD_KODETAG,
  PRD_DESKRIPSIPANJANG,
  PRD_UNIT,
  PRD_FRAC,
  PRD_HRGJUAL,
  prd_flagbkp1,
  prd_flagbkp2
FROM tbmaster_prodmast
)prd LEFT JOIN
(SELECT ST_PRDCD,
  ST_SALDOAKHIR,
  ST_LASTCOST,
  ST_AVGCOST
FROM tbmaster_Stock
WHERE st_lokasi='01'
)stk ON prd.PLU=stk.st_prdcd 
  WHERE COALESCE (PRD_KODETAG,'0') NOT IN ('N','X','Z') AND ST_SALDOAKHIR <>0 ORDER BY PRD_PRDCD ASC)HRG_N LEFT JOIN 
 (SELECT PRMD_PRDCD AS PLUMD,
  PRMD_HRGJUAL
FROM TBTR_PROMOMD
WHERE CURRENT_DATE BETWEEN DATE(PRMD_TGLAWAL) AND DATE(PRMD_TGLAKHIR)
)PRMD ON HRG_N.PRD_PRDCD=PRMD.PLUMD)MARGINM WHERE (MARGIN_A<0 OR MARGIN_A_MD<0)"; // Ganti dengan query Anda

try {
  $stmt = $conn->prepare($query);
  $stmt->execute();

  // Ambil kolom pertama untuk menentukan header
  $columns = $stmt->fetch(PDO::FETCH_ASSOC);

  // Tentukan nama file dengan tanggal saat ini
  $date = date('Y-m-d');
  $filename = "MARGIN_MINUS_SPI_BDL_1R_$date.xlsx";
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
    $columnNames = ['DIV', 'PLU', 'DESKRIPSI', 'FRAC', 'UNIT', 'TAG', 'LPP', 'LCOST', 'ACOST_EXC', 'ACOST_INC', 'HRG_NORMAL', 'HRG_MD', 'MARGIN_ACOST', 'MARGIN_LCOST', 'MARGIN_A_MD', 'MARGIN_L_MD'];
    $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));
  }

  // Simpan file sementara di server
  if ($writer->writeToFile($filePath)) {
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
    error_log("Gagal menyimpan file di: $filePath");
    die("Gagal menyimpan file di: $filePath");
  }
} catch (PDOException $e) {
  error_log("Query gagal: " . $e->getMessage());
  die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
