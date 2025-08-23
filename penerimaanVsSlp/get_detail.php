<?php
require_once '../helper/connection.php'; // Pastikan koneksi PDO sudah benar

// Cek apakah ada parameter yang dibutuhkan
if (isset($_POST['slp_prdcd']) && isset($_POST['tanggalAwal']) && isset($_POST['tanggalAkhir'])) {
    $slp_prdcd = $_POST['slp_prdcd'];
    $tanggalAwal = $_POST['tanggalAwal'];
    $tanggalAkhir = $_POST['tanggalAkhir'];

    // Format tanggal sesuai kebutuhan query (YYYYMMDD)
    $tanggalAwalFormatted = !empty($tanggalAwal) ? date('Ymd', strtotime($tanggalAwal)) : null;
    $tanggalAkhirFormatted = !empty($tanggalAkhir) ? date('Ymd', strtotime($tanggalAkhir)) : null;

    // Query untuk mengambil detail berdasarkan kode produk (slp_prdcd) dan rentang tanggal
    try {
        $query = "SELECT
                            slp_prdcd,
                            SLP_CREATE_BY AS DIBUAT,
                            COALESCE(SLP_MODIFY_BY, '-') AS DIUBAH,
                            CASE
                                WHEN SLP_FLAG = 'P' THEN 'REALISASI'
                                WHEN SLP_FLAG = 'C' THEN 'BATAL'
                                WHEN SLP_FLAG IS NULL THEN 'BELUM'
                            END AS STATUS,
                            slp_koderak || '.' || slp_kodesubrak || '.' || slp_tiperak || '.' || slp_shelvingrak || '.' || SLP_NOURUT AS RAK,
                            CASE
                                WHEN slp_tiperak ='B' THEN 'BEAM'
                                WHEN slp_tiperak ='S' THEN 'STORAGE'
                                WHEN slp_tiperak LIKE 'I%' THEN 'INNER'
                            END AS LOKASI,
                            CASE
                                WHEN slp_koderak LIKE 'R%' OR slp_koderak LIKE 'O%' THEN 'TOKO'
                                WHEN slp_koderak LIKE 'G%' OR slp_koderak LIKE 'D%' THEN 'GUDANG'
                            END AS LOKASI2,
                            slp_qtypcs,
                            CASE
                                WHEN SLP_JENIS ='O' THEN 'OTOMATIS'
                                ELSE 'MANUAL'
                            END AS JENIS
                        FROM
                            tbtr_slp
                        WHERE
                            slp_prdcd = :slp_prdcd AND
                            TO_CHAR(DATE(slp_create_dt), 'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir AND
                            (SLP_FLAG IS NULL OR SLP_FLAG <> 'C')
                        ORDER BY
                            slp_koderak, slp_kodesubrak, slp_tiperak, slp_shelvingrak
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':slp_prdcd', $slp_prdcd, PDO::PARAM_STR);
        $stmt->bindParam(':tanggalAwal', $tanggalAwalFormatted, PDO::PARAM_STR); // Mengikat parameter tanggal
        $stmt->bindParam(':tanggalAkhir', $tanggalAkhirFormatted, PDO::PARAM_STR); // Mengikat parameter tanggal
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            // Tampilkan detail dalam bentuk tabel dengan Bootstrap styling
            echo "<table class='table table-bordered table-hover'>";
            echo "<thead class='thead-dark'><tr>
                    <th>No</th>
                    <th>PLU</th>
                    <th>Dibuat Oleh</th>
                    <th>Diubah Oleh</th>
                    <th>Status</th>
                    <th>Rak</th>
                    <th>Lokasi</th>
                    <th>Lokasi Detail</th>
                    <th>Qty PCS</th>
                    <th>Jenis</th>
                </tr></thead>";
            echo "<tbody>";

            $no = 1;
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['slp_prdcd']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dibuat']) . "</td>";
                echo "<td>" . htmlspecialchars($row['diubah']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rak']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lokasi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lokasi2']) . "</td>";
                echo "<td>" . number_format($row['slp_qtypcs'], 0, '.', ',') . "</td>";
                echo "<td>" . htmlspecialchars($row['jenis']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning' role='alert'>Detail lokasi tidak ditemukan untuk PLU ini dalam rentang tanggal yang dipilih.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Parameter SLP Product Code, Tanggal Awal, atau Tanggal Akhir tidak lengkap.</div>";
}
