<?php
require_once '../helper/connection.php';

$sql = <<<select
SELECT
msth_nodoc,
msth_kodesupplier,
TO_CHAR(msth_tgldoc, 'DD-MM-YYYY') AS msth_tgldoc
FROM tbtr_mstran_h
WHERE msth_nodoc NOT IN (
    SELECT substr(ftp6_namadoc,5,10)
    FROM kirim_ftp_sd6
    WHERE ftp6_tgltrx >= to_date('01012024','ddmmyyyy')
    AND ftp6_namadoc LIKE 'BTB_%'
)
AND msth_typetrn='B'
AND msth_recordid IS NULL
AND msth_create_by <> 'BKL'
AND msth_tgldoc >= to_date('01012024','ddmmyyyy')
ORDER BY msth_tgldoc DESC;
select;

try {
    $stmt = $conn->query($sql);
    $data_bpb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pesan = "REPORT BTB GAGAL KIRIM KE FTP SD6" . "\n\n"
            . "📅 *Monitoring BTB Gagal Terkirim ke SD6)*\n"
            . "Tanggal Cek: " . date('d-m-Y H:i:s') . "\n"
            . "-----------------------------------\n";

    if (!empty($data_bpb)) {
        $pesan .= "⚠️ *Daftar BPB yang BELUM Terkirim ke SD6:*\n";
        $counter = 1;
        foreach ($data_bpb as $row) {
            $pesan .= "$counter. BPB No: *" . $row['msth_nodoc'] . "*\n"
                    . "   Supplier: " . $row['msth_kodesupplier'] . "\n"
                    . "   Tgl Doc: " . $row['msth_tgldoc'] . "\n";
            $counter++;
        }
        $pesan .= "-----------------------------------\n"
                . "Mohon segera ditindaklanjuti!\n";
    } else {
        $pesan .= "Tidak ada BTB yang gagal terkirim.\n"
                . "-----------------------------------\n";
    }

    $targets = [
        "6282180488184",
        "628972569035",
    ];

    $url = "https://api.fonnte.com/send";
    $token = "V63djuqhUQnWYJKFmibu";

    $success = [];
    $failed = [];

    foreach ($targets as $target) {
        $curl = curl_init();
        $postData = [
            'target' => $target,
            'message' => $pesan,
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
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                "Authorization: $token",
                "Content-Type: application/x-www-form-urlencoded",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $failed[] = "Gagal kirim ke $target: $err";
        } else {
            $success[] = "Berhasil kirim ke $target.";
        }
    }

    // simpan hasil dalam variabel untuk JavaScript
    $result = [
        'status' => !empty($failed) ? 'warning' : 'success',
        'title' => !empty($failed) ? 'Sebagian Gagal' : 'Berhasil!',
        'message' => !empty($failed)
            ? implode("\\n", $failed)
            : implode("\\n", $success)
    ];

} catch (PDOException $e) {
    $result = [
        'status' => 'error',
        'title' => 'Database Error',
        'message' => addslashes($e->getMessage())
    ];
}
?>

<!-- ==================== SWEETALERT ==================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Jalankan SweetAlert tanpa reload halaman
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: '<?= $result['status'] ?>',
        title: '<?= $result['title'] ?>',
        text: '<?= $result['message'] ?>',
        confirmButtonText: 'OK'
    });
});
</script>
