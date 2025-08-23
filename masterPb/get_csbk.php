<?php
require_once '../helper/connection.php'; // Pastikan koneksi PDO sudah benar

// Cek apakah ada parameter POST 'pb'
if (isset($_POST['pb'])) {
    $pb = $_POST['pb'];

    try {
        $query = "
            SELECT
                TO_CHAR(DATE(tgl_trans), 'DDMMYYYY') || no_trans AS pk_d,
                kode_igr,
                no_pb,
                no_trans,
                tgl_trans,
                kode_promo,
                tipe_promo,
                cashback_order,
                cashback_real,
                gift_order,
                gift_real
            FROM promo_klikigr
            WHERE TO_CHAR(DATE(tgl_trans), 'DDMMYYYY') || no_trans = :pb
            ORDER BY kode_member DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':pb', $pb, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Selalu tampilkan header
        echo "<table class='table table-bordered table-hover'>";
        echo "<thead class='thead-dark'><tr>
                <th>No</th>
                <th>No PB</th>
                <th>Kode Promo</th>
                <th>Tipe Promo</th>
                <th>Cashback Order</th>
                <th>Cashback Real</th>
                <th>Selisih</th>
              </tr></thead>";
        echo "<tbody>";

        $total_cashback_order = 0;
        $total_cashback_real = 0;
        $total_selisih = 0; // Variabel untuk total selisih

        if ($rows) {
            $no = 1;
            foreach ($rows as $row) {
                // Hitung selisih antara cashback_real dan cashback_order
                $selisih = $row['cashback_real'] - $row['cashback_order'];

                // Jumlahkan total cashback_order, cashback_real, dan selisih
                $total_cashback_order += $row['cashback_order'];
                $total_cashback_real += $row['cashback_real'];
                $total_selisih += $selisih;

                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['no_pb']) . "</td>";
                echo "<td>" . htmlspecialchars($row['kode_promo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tipe_promo']) . "</td>";
                echo "<td align='right'>" . number_format($row['cashback_order'], 0) . "</td>";
                echo "<td align='right'>" . number_format($row['cashback_real'], 0) . "</td>";
                echo "<td align='right'>" . number_format($selisih, 0) . "</td>"; // Tampilkan selisih
                echo "</tr>";
            }

            // Tampilkan total di bawah tabel
            echo "<tr><td colspan='5' class='text-right'><strong>Grand Total</strong></td>";
            echo "<td align='right'><strong>" . number_format($total_cashback_real, 0) . "</strong></td>";
            echo "<td align='right'><strong>" . number_format($total_selisih, 0) . "</strong></td></tr>"; // Total selisih
        } else {
            // Kalau tidak ada data, tampilkan 1 baris keterangan
            echo "<tr>";
            echo "<td colspan='7' class='text-center text-danger'>PB ini tidak ada cashback.</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
