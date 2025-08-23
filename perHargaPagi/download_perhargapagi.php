<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\PERUBAHAN HARGA\\';

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
    prd_kodedivisi,
    prd_kodedepartement,
    prd_kodekategoribarang,
    prd_prdcd,
    prd_deskripsipanjang,
    prd_unit,
    prd_frac,
    sj_lama_nol AS HRG_LAMA_0,
    sj_lama_satu AS HRG_LAMA_1,
    sj_lama_dua AS HRG_LAMA_2,
    sj_lama_tiga AS HRG_LAMA_3,
    sj_baru_0  AS HRG_BARU_0,
    sj_baru_1  AS HRG_BARU_1,
    sj_baru_2 AS HRG_BARU_2,
    sj_baru_3 AS HRG_BARU_3,
    tgl_perubahan,
    jam_perubahan
FROM
    ( SELECT
    prd_kodedivisi,
    prd_kodedepartement,
    prd_kodekategoribarang,
    prd_prdcd,
    prd_deskripsipanjang,
    prd_unit,
    prd_frac,
    coalesce(harga_lama0, 0)                       sj_lama_nol,
    coalesce(harga_lama1, 0)                       sj_lama_satu,
    coalesce(harga_lama2, 0)                       sj_lama_dua,
    coalesce(harga_lama3, 0)                       sj_lama_tiga,
    coalesce(harga_baru0, 0)                       sj_baru_0,
    coalesce(harga_baru1, 0)                       sj_baru_1,
    coalesce(harga_baru2, 0)                       sj_baru_2,
    coalesce(harga_baru3, 0)                       sj_baru_3,
    tgl_perubahan,
    to_char(tgl_perubahan, 'HH: MI: SS')      jam_perubahan
FROM
    (
        SELECT
            prd_prdcd,
            prd_kodedivisi,
            prd_kodedepartement,
            prd_kodekategoribarang,
            prd_deskripsipanjang,
            prd_unit,
            prd_frac,
            prd_modify_dt tgl_perubahan
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
            prd_prdcd LIKE '%0'
    )aa
    LEFT JOIN (
        SELECT
            prd_prdcd                      plu,
            coalesce(prd_hrgjual, 0)       harga_baru0,
            coalesce(prd_hrgjual2, 0)      harga_lama0
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%0'
    )ab ON prd_prdcd = plu
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0                           plu1,
            coalesce(prd_hrgjual, 0)       harga_baru1,
            coalesce(prd_hrgjual2, 0)      harga_lama1
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%1'
    )ac ON prd_prdcd = plu1
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0          plu2,
            prd_hrgjual   harga_baru2,
            prd_hrgjual2  harga_lama2
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%2'
    )ad ON prd_prdcd = plu2
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0          plu3,
            prd_hrgjual   harga_baru3,
            prd_hrgjual2  harga_lama3
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%3'
    )ae ON prd_prdcd = plu3
WHERE
    coalesce(harga_lama0, 0) + coalesce(harga_lama1, 0) + coalesce(harga_lama2, 0) + coalesce(harga_lama3, 0) + coalesce(harga_baru0,
    0) + coalesce(harga_baru1, 0) + coalesce(harga_baru2, 0) + coalesce(harga_baru3, 0) <> '0'
)bc left join
    (
        SELECT
            lks_kodeigr,
            lks_prdcd       plutko,
            lks_koderak
            || '.'
            || lks_kodesubrak
            || '.'
            || lks_tiperak
            || '.'
            || lks_shelvingrak
            || '.'
            || lks_nourut   AS alamat_toko
        FROM
            tbmaster_lokasi
        WHERE
            ( lks_koderak LIKE 'R%' )
            AND lks_koderak NOT LIKE '%C'
            AND lks_tiperak <> 'S'
    )bb on prd_prdcd = plutko
    left join
    (
        SELECT
            lks_kodeigr,
            lks_prdcd       plucounter,
            lks_koderak
            || '.'
            || lks_kodesubrak
            || '.'
            || lks_tiperak
            || '.'
            || lks_shelvingrak
            || '.'
            || lks_nourut   AS alamat_counter
        FROM
            tbmaster_lokasi
        WHERE
            ( lks_koderak LIKE 'O%' )
            AND lks_koderak NOT LIKE '%C'
            AND lks_tiperak <> 'S'
    )ba on prd_prdcd = plucounter
ORDER BY
    1,
    2"; // Ganti dengan query Anda

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
    $filename = "PERUBAHAN_HARGA_PAGI_SPI_BDL_1R_$date.xlsx";
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
