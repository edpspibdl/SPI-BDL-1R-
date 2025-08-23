<?php
require_once '../helper/connection.php'; // Pastikan koneksi PDO sudah benar

// Cek apakah ada parameter POST 'pb'
if (isset($_POST['pb'])) {
    $pb = $_POST['pb'];

    // Query untuk mengambil detail berdasarkan kode pb
    try {
        $query = "
        SELECT
            PK_D,
            OBI_PRDCD,
            PRD_DESKRIPSIPANJANG,
            OBI_HARGASATUAN,
            OBI_QTYORDER,
            OBI_QTYREALISASI,
            OBI_QTYORDER * OBI_HARGASATUAN AS RPH_ORDER_DET,
            OBI_QTYREALISASI * OBI_HARGASATUAN AS RPH_REAL_DET
        FROM
            (
                SELECT
                    TO_CHAR(DATE(obi_tgltrans), 'DDMMYYYY') || OBI_NOTRANS AS PK_D,
                    OBI_PRDCD,
                    PRD_DESKRIPSIPANJANG,
                    OBI_QTYORDER,
                    COALESCE(OBI_QTYREALISASI, 0) AS OBI_QTYREALISASI,
                    OBI_HARGASATUAN + OBI_PPN AS OBI_HARGASATUAN
                FROM
                    tbtr_obi_d
                LEFT JOIN
                    tbmaster_prodmast ON prd_prdcd = obi_prdcd
            ) AS subquery
        WHERE
            PK_D = :pb
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':pb', $pb, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            // Tampilkan detail dalam bentuk tabel dengan Bootstrap styling
            echo "<table class='table table-bordered table-hover'>";
            echo "<thead class='thead-dark'><tr>
                    <th>No</th>
                    <th>PLU</th>
                    <th>Deskripsi Panjang</th>
                    <th>Harga Satuan</th>
                    <th>Qty Order</th>
                    <th>Qty Realisasi</th>
                    <th>RPH Order</th>
                    <th>RPH Realisasi</th>
                  </tr></thead>";
            echo "<tbody>";
        
            $no = 1; // Inisialisasi nomor urut
            $rph_total_order = 0;
            $rph_total_real = 0;
        
            // Iterasi untuk menampilkan setiap baris data
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>"; // Tambahkan nomor urut
                echo "<td>" . htmlspecialchars($row['obi_prdcd']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prd_deskripsipanjang']) . "</td>";
                echo "<td>" . number_format($row['obi_hargasatuan'], 0) . "</td>";
                echo "<td>" . number_format($row['obi_qtyorder']) . "</td>";
                echo "<td>" . number_format($row['obi_qtyrealisasi'], 0) . "</td>";
                echo "<td>" . number_format($row['rph_order_det'], 0) . "</td>";
                echo "<td>" . number_format($row['rph_real_det'], 0) . "</td>";
                echo "</tr>";
        
                // Menjumlahkan total
                $rph_total_order += $row['rph_order_det'];
                $rph_total_real += $row['rph_real_det'];
            }
        
            echo "</tbody>";
        
            // Tambahkan footer total
            echo "<tfoot>
                    <tr class='table-info'>
                        <td colspan='6' align='center'>Total</td>
                        <td align='center'>" . number_format($rph_total_order, 0, '.', ',') . "</td>
                        <td align='center'>" . number_format($rph_total_real, 0, '.', ',') . "</td>
                    </tr>
                  </tfoot>";
        
            echo "</table>";

        } else {
            echo "<div class='alert alert-warning' role='alert'>Detail tidak ditemukan.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
