<?php
require '../helper/connection.php'; // Pastikan file ini berisi koneksi PDO ke PostgreSQL

try {
    // Pastikan semua parameter tanggal ada
    if (!isset($_POST['tanggalAwal1'], $_POST['tanggalAkhir1'], $_POST['tanggalAwal2'], $_POST['tanggalAkhir2'], $_POST['tanggalAwal3'], $_POST['tanggalAkhir3'])) {
        throw new Exception("Parameter tanggal tidak lengkap.");
    }

    // Ambil data dari form
    $tanggalAwal1 = $_POST['tanggalAwal1'];
    $tanggalAkhir1 = $_POST['tanggalAkhir1'];
    $tanggalAwal2 = $_POST['tanggalAwal2'];
    $tanggalAkhir2 = $_POST['tanggalAkhir2'];
    $tanggalAwal3 = $_POST['tanggalAwal3'];
    $tanggalAkhir3 = $_POST['tanggalAkhir3'];

    // Query dengan PDO
    $sql = "SELECT * FROM tbtr_obi_h 
            WHERE (obi_tglpb::DATE BETWEEN :tanggalAwal1 AND :tanggalAkhir1)
               OR (obi_tglpb::DATE BETWEEN :tanggalAwal2 AND :tanggalAkhir2)
               OR (obi_tglpb::DATE BETWEEN :tanggalAwal3 AND :tanggalAkhir3)
            LIMIT 100";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':tanggalAwal1' => $tanggalAwal1,
        ':tanggalAkhir1' => $tanggalAkhir1,
        ':tanggalAwal2' => $tanggalAwal2,
        ':tanggalAkhir2' => $tanggalAkhir2,
        ':tanggalAwal3' => $tanggalAwal3,
        ':tanggalAkhir3' => $tanggalAkhir3
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tampilkan hasil dalam bentuk tabel HTML
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    
    // Buat header tabel dari nama kolom di database
    if (!empty($results)) {
        foreach (array_keys($results[0]) as $colName) {
            echo "<th>" . htmlspecialchars($colName) . "</th>";
        }
        echo "</tr>";

        // Isi tabel dengan data dari database
        foreach ($results as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<td colspan='100%'>Tidak ada data ditemukan.</td></tr>";
    }
    
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
