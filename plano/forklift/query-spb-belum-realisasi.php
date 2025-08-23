<?php
    include '../_/connection.php';
    include '../views/view_spb.php';

    // Debug: Pastikan $viewSpb terdefinisi
    if (!isset($viewSpb)) {
        die("Error: \$viewSpb is not defined.");
    }

    // Query
    $query = "SELECT Count(spb_prdcd) AS spb_belum_realisasi
              FROM {$viewSpb} 
              WHERE spb_prdcd IS NOT NULL
                    AND spb_lokasiasal LIKE '%S%'
                    AND spb_lokasiasal NOT LIKE '%C%'
                    AND spb_status = '3'";



    // Jalankan query
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Buat variabel untuk menampung jumlah spb
    $spbBelumRealisasi = 0;

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spbBelumRealisasi = $row['spb_belum_realisasi'];
    }

    // Debug: Tampilkan hasil
    echo "SPB Belum Realisasi: $spbBelumRealisasi";
?>
