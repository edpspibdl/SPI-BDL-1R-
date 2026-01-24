<?php
require_once __DIR__ . '/../helper/connection.php';

/**
 * ===============================
 * QUERY BTB BELUM TERKIRIM SD6
 * ===============================
 */
$sql = <<<SQL
SELECT
    msth_nodoc,
    msth_kodesupplier,
    TO_CHAR(msth_tgldoc, 'DD-MM-YYYY') AS msth_tgldoc
FROM tbtr_mstran_h
WHERE msth_nodoc NOT IN (
    SELECT SUBSTR(ftp6_namadoc, 5, 10)
    FROM kirim_ftp_sd6
    WHERE ftp6_tgltrx >= TO_DATE('01012024','DDMMYYYY')
      AND ftp6_namadoc LIKE 'BTB_%'
)
AND msth_typetrn = 'B'
AND msth_recordid IS NULL
AND msth_create_by <> 'BKL'
AND msth_tgldoc >= TO_DATE('01012024','DDMMYYYY')
ORDER BY msth_tgldoc DESC
SQL;

try {
    $stmt = $conn->query($sql);
    $data_bpb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
     * ===============================
     * FORMAT PESAN WHATSAPP
     * ===============================
     */
    $pesan = "REPORT BTB GAGAL KIRIM KE FTP SD6\n\n";
    $pesan .= "📅 Monitoring BTB Gagal Terkirim ke SD6\n";
    $pesan .= "Tanggal Cek: " . date('d-m-Y H:i:s') . "\n";
    $pesan .= "-----------------------------------\n";

    if (!empty($data_bpb)) {
        $pesan .= "⚠️ *Daftar BTB yang BELUM Terkirim:*\n";
        $no = 1;
        foreach ($data_bpb as $row) {
            $pesan .=
                $no . ". BTB No: *{$row['msth_nodoc']}*\n" .
                "   Supplier: {$row['msth_kodesupplier']}\n" .
                "   Tgl Doc: {$row['msth_tgldoc']}\n";
            $no++;
        }
        $pesan .= "-----------------------------------\n";
        $pesan .= "Mohon segera ditindaklanjuti.\n";
    } else {
        $pesan .= "✅ Tidak ada BTB yang gagal terkirim.\n";
        $pesan .= "-----------------------------------\n";
    }

    /**
     * ===============================
     * KIRIM WHATSAPP VIA FONNTE
     * ===============================
     */
    $targets = [
        "6282180488184",
        "628972569035",
    ];

    $url   = "https://api.fonnte.com/send";
    $token = "V63djuqhUQnWYJKFmibu";

    $success = [];
    $failed  = [];

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
            $failed[] = "Gagal kirim ke $target: $error";
        } else {
            $success[] = "Berhasil kirim ke $target";
        }
    }

    /**
     * ===============================
     * RESULT UNTUK SWEETALERT
     * ===============================
     */
    $result = [
        'status'  => !empty($failed) ? 'warning' : 'success',
        'title'   => !empty($failed) ? 'Sebagian Gagal' : 'Berhasil',
        'message' => !empty($failed)
            ? implode("\\n", $failed)
            : implode("\\n", $success),
    ];

} catch (PDOException $e) {
    $result = [
        'status'  => 'error',
        'title'   => 'Database Error',
        'message' => addslashes($e->getMessage()),
    ];
}

/**
 * ===============================
 * JIKA VIA CLI (TASK SCHEDULER)
 * ===============================
 */
if (php_sapi_name() === 'cli') {
    echo "[INFO] Script dijalankan via Task Scheduler\n";
    echo strip_tags($result['message']) . "\n";
    exit;
}
?>

<!-- ==================== SWEETALERT (WEB ONLY) ==================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: '<?= $result['status'] ?>',
        title: '<?= $result['title'] ?>',
        text: '<?= $result['message'] ?>',
        confirmButtonText: 'OK'
    });
});
</script>
