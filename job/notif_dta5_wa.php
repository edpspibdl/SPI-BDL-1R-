<?php
require_once '../helper/connection.php';

// Query monitoring
$sql = <<<SQL
WITH date_series AS (
    SELECT GENERATE_SERIES(CURRENT_DATE - INTERVAL '6 days', CURRENT_DATE, INTERVAL '1 day')::DATE AS tanggal
),
detailed_data AS (
    SELECT
        ts.tanggal,
        tf.trf_namafile AS NamaFile,
        tf.trf_namadbf AS NamaDBF, -- Added trf_namadbf
        tf.trf_create_by,
        tf.trf_jammulai,
        tf.trf_jamakhir,
        tf.trf_create_dt,
        CASE
            WHEN (tf.trf_jammulai IS NULL OR tf.trf_jamakhir IS NULL OR TRIM(tf.trf_jammulai) = '' OR TRIM(tf.trf_jamakhir) = '')
            THEN 'Kolom trf_jammulai atau trf_jamakhir kosong atau NULL, perlu pengecekan lebih lanjut.'
            ELSE NULL
        END AS keterangan
    FROM
        date_series ts
    LEFT JOIN
        tbtr_transferfile tf ON DATE(tf.trf_create_dt) = ts.tanggal
),
namadbf_counts AS ( -- New CTE to count trf_namadbf based on trf_namafile
    SELECT
        tanggal,
        NamaFile,
        COUNT(NamaDBF) AS NamaDBF_Count
    FROM
        detailed_data
    GROUP BY
        tanggal, NamaFile
),
aggregated_data AS (
    SELECT
        dd.tanggal,
        dd.trf_create_by,
        MAX(dd.NamaFile) AS NamaFile,
        MAX(dd.keterangan) AS keterangan,
        COUNT(*) FILTER (
            WHERE dd.trf_create_by = 'JOB'
        ) AS job_count,
        COUNT(*) FILTER (
            WHERE dd.trf_create_by != 'JOB'
        ) AS bypass_count,
        COUNT(*) FILTER (
            WHERE dd.keterangan IS NOT NULL
        ) AS problematic_count,
        MAX(ndc.NamaDBF_Count) AS NamaDBF_TotalCount -- Added NamaDBF_TotalCount
    FROM
        detailed_data dd
    LEFT JOIN
        namadbf_counts ndc ON dd.tanggal = ndc.tanggal AND dd.NamaFile = ndc.NamaFile
    GROUP BY
        dd.tanggal, dd.trf_create_by
),
final_status AS (
    SELECT
        ds.tanggal,
        MAX(CASE
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.tanggal = ds.tanggal AND ad.problematic_count > 1
            ) THEN 'LAKUKAN PENGECEKAN'
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.trf_create_by != 'JOB' AND ad.tanggal = ds.tanggal
            ) THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad2
                        WHERE ad2.trf_create_by = 'JOB' AND ad2.tanggal = ds.tanggal
                    ) THEN 'PROSES BYPASS DAN BYJOB BERHASIL'
                    ELSE 'BYPASS BERHASIL'
                END
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME < TIME '21:15:00' THEN 'JOB BELUM DIJALANKAN'
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME >= TIME '21:15:00' AND CURRENT_TIME < TIME '22:00:00' THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad
                        WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by = 'JOB'
                    ) THEN 'JOB OK'
                    ELSE 'JOB BELUM DIJALANKAN'
                END
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME >= TIME '22:00:00' THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad
                        WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by = 'JOB'
                    ) THEN 'JOB OK'
                    ELSE 'TIDAK JALAN'
                END
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.trf_create_by = 'JOB'
                  AND ad.tanggal = ds.tanggal
            ) THEN 'JOB OK'
            ELSE 'TIDAK JALAN'
        END) AS status,
        STRING_AGG(DISTINCT trf_create_by, ', ') AS created_by,
        STRING_AGG(DISTINCT NamaFile, ', ') AS NamaFile,
        MAX(NamaDBF_TotalCount) AS NamaDBF_Count, -- Added NamaDBF_Count to final_status
        CASE
            WHEN MAX(CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM aggregated_data ad
                            WHERE ad.tanggal = ds.tanggal AND ad.problematic_count > 1
                        ) THEN 'LAKUKAN PENGECEKAN'
                        ELSE NULL
                    END) = 'LAKUKAN PENGECEKAN'
            THEN STRING_AGG(DISTINCT keterangan, ', ')
            ELSE NULL
        END AS keterangan
    FROM
        date_series ds
    LEFT JOIN
        aggregated_data ad ON ad.tanggal = ds.tanggal
    GROUP BY
        ds.tanggal
)
SELECT
    tanggal,
    NamaFile,
    NamaDBF_Count, -- Displayed NamaDBF_Count
    status,
    created_by AS created,
    keterangan
FROM
    final_status
ORDER BY
    tanggal DESC
SQL;

// Ambil hasil query
$stmt = $conn->query($sql);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    // Membangun pesan WhatsApp utama
    $pesan = "NOTIFIKASI OTOMATIS JOB DTA5 SPI BDL 1R" . "\n"
        . "\n"
        . "📅 *Monitoring JOB DTA5 " . $data['tanggal'] . "*\n"
        . "📝 Status: *" . $data['status'] . "*\n"
        . "📂 File: " . ($data['namafile'] ? $data['namafile'] : 'Tidak ada') . "\n"
        . "📂 File: " . ($data['namadbf_count'] ? $data['namadbf_count'] : '0') . "\n"
        . "👤 Dibuat oleh: " . ($data['created'] ? $data['created'] : 'Tidak diketahui') . "\n";

    if (!empty($data['keterangan'])) {
        $pesan .= "⚠️ Catatan: " . $data['keterangan'] . "\n";
    }

    // --- Bagian untuk menambahkan detail dari query ke pesan (tanpa membuat file) ---
    $detailPesan = "\n--- Detail Laporan Hari Ini ---\n";
    $detailPesan .= "Tanggal: " . $data['tanggal'] . "\n";
    $detailPesan .= "Status Umum: " . $data['status'] . "\n";
    $detailPesan .= "Nama File Terkait: " . ($data['namafile'] ? $data['namafile'] : 'N/A') . "\n";
    $detailPesan .= "Dibuat Oleh: " . ($data['created'] ? $data['created'] : 'N/A') . "\n";
    if (!empty($data['keterangan'])) {
        $detailPesan .= "Keterangan Tambahan: " . $data['keterangan'] . "\n";
    } else {
        $detailPesan .= "Keterangan Tambahan: Tidak ada masalah terdeteksi pada kolom waktu.\n";
    }
    // Anda bisa menambahkan lebih banyak baris detail di sini jika diperlukan
    // misalnya, detail dari tabel tbtr_transferfile jika Anda mengambil semua barisnya

    $pesan .= $detailPesan; // Gabungkan detail ke pesan utama


    // Daftar nomor WA tujuan
    $targets = [
        "6282180488184",
        "628972569035",
        // Tambahkan nomor lain jika perlu
    ];

    $url = "https://api.fonnte.com/send";
    $token = "KKVJZ5ZraZxuJRxW5Hsg"; // Ganti dengan token Fonnte kamu

    foreach ($targets as $target) {
        $curl = curl_init();

        // Parameter untuk Fonnte API, HANYA berisi target dan message
        $postData = [
            'target' => $target,
            'message' => $pesan, // Pesan sudah mengandung semua detail
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($postData), // Menggunakan http_build_query untuk POST biasa
            CURLOPT_HTTPHEADER => [
                "Authorization: $token", // Token dikirim melalui header Authorization
                "Content-Type: application/x-www-form-urlencoded", // Penting untuk POST biasa
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "❌ Gagal mengirim notifikasi ke $target: " . $err . "<br>";
        } else {
            echo "✅ Notifikasi dikirim ke: $target. Respon API: " . $response . "<br>";
        }
    }
} else {
    echo "⚠️ Tidak ada data monitoring untuk hari ini.";
}
