<?php
require_once __DIR__ . '/../helper/connection.php';

/**
 * ===============================
 * QUERY MONITORING
 * ===============================
 */
$sql = <<<SQL
WITH date_series AS (
    SELECT GENERATE_SERIES(CURRENT_DATE - INTERVAL '6 days', CURRENT_DATE, INTERVAL '1 day')::DATE AS tanggal
),
detailed_data AS (
    SELECT
        ts.tanggal,
        tf.trf_namafile AS NamaFile,
        tf.trf_namadbf AS NamaDBF,
        tf.trf_create_by,
        tf.trf_jammulai,
        tf.trf_jamakhir,
        tf.trf_create_dt,
        CASE
            WHEN (tf.trf_jammulai IS NULL OR tf.trf_jamakhir IS NULL
                  OR TRIM(tf.trf_jammulai) = '' OR TRIM(tf.trf_jamakhir) = '')
            THEN 'Kolom trf_jammulai atau trf_jamakhir kosong / NULL.'
            ELSE NULL
        END AS keterangan
    FROM date_series ts
    LEFT JOIN tbtr_transferfile tf
        ON DATE(tf.trf_create_dt) = ts.tanggal
),
namadbf_counts AS (
    SELECT
        tanggal,
        NamaFile,
        COUNT(NamaDBF) AS NamaDBF_Count
    FROM detailed_data
    GROUP BY tanggal, NamaFile
),
aggregated_data AS (
    SELECT
        dd.tanggal,
        dd.trf_create_by,
        MAX(dd.NamaFile) AS NamaFile,
        MAX(dd.keterangan) AS keterangan,
        COUNT(*) FILTER (WHERE dd.trf_create_by = 'JOB') AS job_count,
        COUNT(*) FILTER (WHERE dd.trf_create_by != 'JOB') AS bypass_count,
        COUNT(*) FILTER (WHERE dd.keterangan IS NOT NULL) AS problematic_count,
        MAX(ndc.NamaDBF_Count) AS NamaDBF_TotalCount
    FROM detailed_data dd
    LEFT JOIN namadbf_counts ndc
        ON dd.tanggal = ndc.tanggal AND dd.NamaFile = ndc.NamaFile
    GROUP BY dd.tanggal, dd.trf_create_by
),
final_status AS (
    SELECT
        ds.tanggal,
        MAX(
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM aggregated_data ad
                    WHERE ad.tanggal = ds.tanggal AND ad.problematic_count > 1
                ) THEN 'LAKUKAN PENGECEKAN'
                WHEN EXISTS (
                    SELECT 1 FROM aggregated_data ad
                    WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by != 'JOB'
                ) THEN
                    CASE
                        WHEN EXISTS (
                            SELECT 1 FROM aggregated_data ad2
                            WHERE ad2.tanggal = ds.tanggal AND ad2.trf_create_by = 'JOB'
                        ) THEN 'PROSES BYPASS DAN BYJOB BERHASIL'
                        ELSE 'BYPASS BERHASIL'
                    END
                WHEN EXISTS (
                    SELECT 1 FROM aggregated_data ad
                    WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by = 'JOB'
                ) THEN 'JOB OK'
                ELSE 'TIDAK JALAN'
            END
        ) AS status,
        STRING_AGG(DISTINCT trf_create_by, ', ') AS created_by,
        STRING_AGG(DISTINCT NamaFile, ', ') AS NamaFile,
        MAX(NamaDBF_TotalCount) AS NamaDBF_Count,
        STRING_AGG(DISTINCT keterangan, ', ') AS keterangan
    FROM date_series ds
    LEFT JOIN aggregated_data ad
        ON ad.tanggal = ds.tanggal
    GROUP BY ds.tanggal
)
SELECT *
FROM final_status
ORDER BY tanggal DESC
LIMIT 1
SQL;

$stmt = $conn->query($sql);
$data = $stmt->fetch();

if (!$data) {
    exit("⚠️ Tidak ada data monitoring.\n");
}

/**
 * ===============================
 * FORMAT PESAN WA
 * ===============================
 */
$pesan =
    "NOTIFIKASI OTOMATIS JOB DTA5 SPI BDL 1R\n\n" .
    "📅 Tanggal: {$data['tanggal']}\n" .
    "📝 Status: *{$data['status']}*\n" .
    "📂 File: " . ($data['namafile'] ?: '-') . "\n" .
    "📊 Jumlah DBF: " . ($data['namadbf_count'] ?: '0') . "\n" .
    "👤 Dibuat oleh: " . ($data['created_by'] ?: '-') . "\n";

if (!empty($data['keterangan'])) {
    $pesan .= "⚠️ Catatan: {$data['keterangan']}\n";
}

/**
 * ===============================
 * KIRIM WHATSAPP VIA FONNTE
 * ===============================
 */
$targets = [
    '6282180488184',
    '628972569035',
];

$url   = "https://api.fonnte.com/send";
$token = "V63djuqhUQnWYJKFmibu";

foreach ($targets as $target) {

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'target'  => $target,
            'message' => $pesan,
        ]),
        CURLOPT_HTTPHEADER => [
            "Authorization: $token",
            "Content-Type: application/x-www-form-urlencoded",
        ],
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "❌ Gagal kirim ke $target: $error\n";
    } else {
        echo "✅ Terkirim ke $target\n";
    }
}
