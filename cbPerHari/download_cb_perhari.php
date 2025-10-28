<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php';
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAP RUTIN\\ME\\LAP GUDANG\\';

// Cek apakah folder sudah ada, kalau belum buat
if (!file_exists($tempSavePath)) {
    if (!mkdir($tempSavePath, 0777, true)) {
        die("Gagal membuat folder: $tempSavePath. Pastikan PHP memiliki izin untuk membuat folder.");
    }
    // error_log("Folder berhasil dibuat: $tempSavePath");
}

// Cek izin tulis folder
if (!is_writable($tempSavePath)) {
    die("Folder tidak dapat ditulisi: $tempSavePath. Periksa izin folder.");
}

// Query data
// Query telah DIBERSIHKAN dari karakter spasi/tab yang tidak valid
$query = "
SELECT 
    TGL, 
    ROUND(SUM(rp_cb), 0) AS rp_cb, 
    ROUND(SUM(dpp), 0) AS dpp, 
    ROUND(SUM(ppn), 0) AS ppn
FROM (
    SELECT 
        KD_PROMOSI AS kdpromosi, 
        CBH_NAMAPROMOSI AS nama_promosi, 
        CBH_TGLAWAL AS tgl_mulai, 
        CBH_TGLAKHIR AS tgl_akhir, 
        CBH_KODEPERJANJIAN AS kode_perjanjian, 
        SUM(KELIPATAN) AS kelipatan, 
        SUM(CASHBACK) AS rp_cb, 
        COUNT(DISTINCT CUS_KODEMEMBER) AS jum_member, 
        CBH_MEKANISME AS mekanisme 
    FROM (
        SELECT 
            KD_PROMOSI, 
            CBH_NAMAPROMOSI, 
            CBH_TGLAWAL, 
            CBH_TGLAKHIR, 
            CBH_KODEPERJANJIAN, 
            KELIPATAN, 
            CASHBACK, 
            CUS_KODEMEMBER, 
            CBH_MEKANISME 
        FROM M_PROMOSI_H 
        LEFT JOIN TBMASTER_CUSTOMER ON KD_MEMBER = CUS_KODEMEMBER 
        LEFT JOIN TBTR_CASHBACK_HDR ON KD_PROMOSI = CBH_KODEPROMOSI 
        WHERE TO_CHAR(TGL_TRANS, 'MMYYYY') = TO_CHAR(CURRENT_DATE, 'MMYYYY')
    ) AS subquery 
    GROUP BY KD_PROMOSI, CBH_NAMAPROMOSI, CBH_TGLAWAL, CBH_TGLAKHIR, CBH_KODEPERJANJIAN, CBH_MEKANISME 
) AS promosi 
LEFT JOIN (
    SELECT 
        KODE, 
        SUM(CB) AS cb, 
        SUM(DPP) AS dpp, 
        SUM(PPN) AS ppn, 
        TGL 
    FROM (
        SELECT 
            TRP_TRANSACTIONDATE::date AS tgl, 
            TRP_KODEPROMOSI AS kode, 
            TRP_CASHBACK AS cb, 
            TRP_KODEMEMBER AS kodemember, 
            CASE 
                WHEN TRP_FLAGBKP = 'Y' THEN (TRP_CASHBACK / 1.11) 
                ELSE TRP_CASHBACK 
            END AS dpp, 
            CASE 
                WHEN TRP_FLAGBKP = 'Y' THEN TRP_CASHBACK - (TRP_CASHBACK / 1.11) 
                ELSE 0 
            END AS ppn 
        FROM TBTR_TRANSAKSI_PROMOSI 
    ) AS transaksi 
    LEFT JOIN TBMASTER_CUSTOMER ON kodemember = CUS_KODEMEMBER 
    WHERE TO_CHAR(TGL, 'MMYYYY') = TO_CHAR(CURRENT_DATE, 'MMYYYY') 
    GROUP BY kode, tgl 
) AS transaksi ON promosi.kdpromosi = transaksi.kode 
GROUP BY TGL 
ORDER BY TGL
";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Nama file dan path
    $filename = "CB_PERHARI_SPI_BDL_1R.xlsx";
    $filePath = $tempSavePath . $filename;

    // Inisialisasi writer
    $writer = new XLSXWriter();

    // Definisikan header + tipe kolom
    $headerTypes = [
        'TGL'   => 'date',
        'RP CB' => '#,##0',
        'DPP'   => '#,##0',
        'PPN'   => '#,##0'
    ];

    // Definisikan kolom teks untuk header
    $headerTitles = array_keys($headerTypes);

    // Tulis header sheet (Perbaikan Double Header dari permintaan sebelumnya)
    $writer->writeSheetHeader('Sheet1', $headerTypes, ['font-style' => 'bold', 'suppress_row' => false, 'widths' => [15, 15, 15, 15]], $headerTitles);

    // Ambil data dan tulis ke Excel
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $writer->writeSheetRow('Sheet1', [
            $row['tgl'],
            (int)$row['rp_cb'],
            (int)$row['dpp'],
            (int)$row['ppn']
        ]);
    }

    // Simpan file sementara
    if ($writer->writeToFile($filePath)) {
        if (file_exists($filePath)) {
            
            if (ob_get_level()) {
                ob_end_flush();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            header('Expires: 0');
            header('Pragma: public');

            readfile($filePath);

            // Hapus file sementara
            unlink($filePath);
            exit;
        } else {
            die("File tidak ditemukan di: $filePath");
        }
    } else {
        die("Gagal menyimpan file di: $filePath");
    }
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
?>