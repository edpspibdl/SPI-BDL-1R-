<?php
// Pastikan path ini benar ke file koneksi PDO Anda
require_once '../helper/connection.php'; 

// Header untuk memberitahu browser bahwa respons ini adalah JSON
header('Content-Type: application/json');

// 1. Ambil parameter pencarian dari Select2 (q)
$search = isset($_GET['q']) ? $_GET['q'] : '';
$searchTerm = '%' . $search . '%';

// 2. Query menggunakan Prepared Statement (Sangat penting untuk keamanan PDO)
$sql = "SELECT sup_kodesupplier, sup_namasupplier 
        FROM supplier 
        WHERE sup_kodesupplier LIKE :search OR sup_namasupplier LIKE :search
        LIMIT 20";

try {
    // Asumsi: Variabel $conn adalah objek PDO yang sudah terinisialisasi
    // di dalam file '../helper/connection.php'
    global $conn; // Gunakan global jika $conn didefinisikan di luar scope function/class

    $stmt = $conn->prepare($sql);
    
    // Bind parameter pencarian
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    
    // Eksekusi statement
    $stmt->execute();

    // 3. Ambil semua hasil
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Keluarkan data dalam format JSON
    echo json_encode($results);

} catch (PDOException $e) {
    // Tangani error PDO
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Catatan: Data yang dikirimkan ke Select2 adalah array of objects:
/*
[
    {"sup_kodesupplier": "SUP001", "sup_namasupplier": "PT ABC"},
    {"sup_kodesupplier": "SUP002", "sup_namasupplier": "CV XYZ"}
]
*/
?>