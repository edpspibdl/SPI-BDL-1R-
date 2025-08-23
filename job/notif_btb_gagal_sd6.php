<?php
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Query untuk mendapatkan dokumen BPB yang belum terkirim ke SD6
// Menggunakan nama kolom asli dari tabel PostgreSQL
$sql = <<<select
SELECT
msth_nodoc,        -- Menggunakan nama kolom asli
msth_kodesupplier, -- Menggunakan nama kolom asli
TO_CHAR(msth_tgldoc, 'DD-MM-YYYY') AS msth_tgldoc -- Tetap menggunakan alias untuk format tanggal
from tbtr_mstran_h
where msth_nodoc not in
(select substr(ftp6_namadoc,5,10)
from kirim_ftp_sd6 where ftp6_tgltrx >= to_date('01012024','ddmmyyyy')
and ftp6_namadoc like 'BTB_%')
and msth_typetrn='B'
and msth_recordid is null
and msth_create_by <> 'BKL'
and msth_tgldoc >= to_date('01012024','ddmmyyyy')
order by msth_tgldoc desc
;
select; // PENTING: Penutup heredoc ini harus di awal baris, tanpa spasi atau tab di depannya

try {
          // --- BLOK INI AKAN MENGAMBIL DATA DARI DATABASE ASLI ANDA ---
          // Pastikan koneksi database di 'connection.php' sudah benar.
          $stmt = $conn->query($sql);
          $data_bpb = $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengambil semua baris data

          // Membangun pesan WhatsApp utama
          $pesan = "REPORT BTB GAGAL KIRIM KE FTP SD6" . "\n"
                    . "\n"
                    . "📅 *Monitoring BTB Gagal Terkirim ke SD6)*\n"
                    . "Tanggal Cek: " . date('d-m-Y H:i:s') . "\n" // Menggunakan waktu server saat ini
                    . "-----------------------------------\n";

          if (!empty($data_bpb)) {
                    // Jika ada data BPB yang belum terkirim
                    $pesan .= "⚠️ *Daftar BPB yang BELUM Terkirim ke SD6:*\n";
                    $counter = 1;
                    foreach ($data_bpb as $row) {
                              // Menggunakan nama kolom asli dari database untuk akses array
                              $pesan .= "$counter. BPB No: *" . $row['msth_nodoc'] . "*\n"
                                        . "   Supplier: " . $row['msth_kodesupplier'] . "\n"
                                        . "   Tgl Doc: " . $row['msth_tgldoc'] . "\n"; // Ini tetap menggunakan alias karena TO_CHAR
                              $counter++;
                    }
                    $pesan .= "-----------------------------------\n"
                              . "Mohon segera ditindaklanjuti!\n";
          } else {
                    // Jika tidak ada data BPB yang belum terkirim (semua berhasil)
                    $pesan .= "Tidak ada BTB yang gagal terkirim.\n";
                    $pesan .= "-----------------------------------\n";
          }

          // Daftar nomor WA tujuan
          $targets = [
                    "6282180488184", // Contoh nomor 1
                    "628972569035", // Contoh nomor 2
                    // Tambahkan nomor lain jika perlu
          ];

          $url = "https://api.fonnte.com/send";
          $token = "KKVJZ5ZraZxuJRxW5Hsg"; // Ganti dengan token Fonnte Anda

          foreach ($targets as $target) {
                    $curl = curl_init();

                    // Parameter untuk Fonnte API
                    $postData = [
                              'target' => $target,
                              'message' => $pesan, // Pesan sudah mengandung semua detail BTB
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
} catch (PDOException $e) {
          // Tangani error jika terjadi masalah saat koneksi atau menjalankan query
          die("Error mengambil data dari database: " . $e->getMessage());
}
