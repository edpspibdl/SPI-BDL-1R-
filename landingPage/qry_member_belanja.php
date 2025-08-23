<?php
// qry_member_belanja.php
require_once '../helper/connection.php';
header('Content-Type: application/json');

// --- DEBUGGING: Tampilkan semua error PHP (HANYA UNTUK DEVELOPMENT!) ---
// Hapus baris-baris ini saat deployment ke lingkungan produksi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------------------------------------------------

$response = [
          'success' => false,
          'total_member_khusus' => 0,
          'message' => ''
];

try {
          if (!isset($conn) || !$conn instanceof PDO || $conn === null) {
                    throw new Exception("Koneksi database tidak tersedia. Periksa connection.php.");
          }

          $sql = "
        SELECT
            COUNT(DISTINCT JH_CUS_KODEMEMBER) AS MEMBER_COUNT
        FROM
            tbtr_jualheader
        WHERE
            JH_CUS_KODEMEMBER IN (
                SELECT cus_kodemember
                FROM tbmaster_customer
                WHERE cus_flagmemberkhusus = 'Y'
            )
            AND TO_CHAR(JH_CREATE_DT, 'YYYY-MM') = TO_CHAR(CURRENT_DATE, 'YYYY-MM')
    ";

          $stmt = $conn->prepare($sql);
          $stmt->execute();

          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          // --- KOREKSI PENTING DI SINI ---
          // Mengakses 'member_count' dengan huruf kecil, sesuai output var_dump
          // Atau bisa juga menggunakan COALESCE pada SQL agar hasilnya selalu ada
          if ($result && isset($result['member_count'])) { // Pastikan $result ada dan key 'member_count' ada
                    $response['success'] = true;
                    $response['total_member_khusus'] = (int) $result['member_count']; // Mengakses dengan huruf kecil

                    if ($response['total_member_khusus'] > 0) {
                              $response['message'] = "Data jumlah member berhasil diambil.";
                    } else {
                              $response['message'] = "Tidak ada data member belanja ditemukan untuk bulan ini.";
                    }
          } else {
                    // Fallback jika $result kosong atau 'member_count' tidak ada (meskipun seharusnya ada dari COUNT)
                    $response['success'] = true;
                    $response['total_member_khusus'] = 0;
                    $response['message'] = "Tidak ada data member belanja ditemukan untuk bulan ini (fallback).";
          }
} catch (PDOException $e) {
          $response['message'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
          $response['message'] = "Server error: " . $e->getMessage();
}

echo json_encode($response);
// Pastikan tidak ada karakter atau spasi setelah tag penutup PHP ini