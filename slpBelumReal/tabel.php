<?php
require 'query.php'; // ambil variabel $stmt dari query.php
?>

<!-- Styling tabel -->
<style>
    #table-1 {
        width: 100%;
        table-layout: auto;
        /* Penting agar lebar kolom mengikuti konten */
        border-collapse: collapse;
    }

    #table-1 th,
    #table-1 td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    #table-1 th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
    }

    #table-1 td.desk-column {
        white-space: nowrap !important;
        word-break: break-word;
        min-width: 200px;
        /* Bisa ubah jadi nilai minimum */
    }


    .table-responsive {
        overflow-x: auto;
    }
</style>


<div class="table-responsive">
    <table class="table table-hover table-striped" id="table-1">
        <thead>
            <tr>
                <th> # </th>
                <th> PLU </th>
                <th> SLP ID </th>
                <th> DESK </th>
                <th> UNIT </th>
                <th> FRAC </th>
                <th> QTY CTN </th>
                <th> QTY PCS </th>
                <th> EXP </th>
                <th> SLP TUJUAN </th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assuming $stmt is a valid PDOStatement object that has been executed
            $nomor = 1;
            // Check if there are any rows to fetch
            if ($stmt && $stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $nomor . '</td>';
                    // Use htmlspecialchars for all output to prevent XSS attacks
                    echo '<td>' . htmlspecialchars($row["slp_prdcd"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_id"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_deskripsi"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_unit"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_frac"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_qtycrt"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_qtypcs"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_expdate"] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($row["slp_tujuan"] ?? '') . '</td>';
                    echo '</tr>';
                    $nomor++;
                }
            } else {
                // Display a "no data" row if the query returns no results
                echo '<tr class="no-data-row">';
                // Colspan should match the number of columns in the <thead>
                echo '<td colspan="10" class="text-center">Tidak ada data yang tersedia.</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>