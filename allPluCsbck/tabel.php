<?php
require 'query.php'; // ambil variabel $stmt dari query.php
?>

<!-- Styling tabel -->
<style>
    #table-1 {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }

    #table-1 th,
    #table-1 td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
        white-space: nowrap;
        /* agar tidak wrap */
    }

    #table-1 th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
    }

    .table-responsive {
        overflow-x: auto;
    }

    /* Optional: jika ingin kolom teks panjang tetap bisa wrap, hapus white-space untuk kolom itu */
    #table-1 td.desk-column {
        white-space: normal !important;
        word-break: break-word;
    }
</style>

<div class="table-responsive">
    <table class="table table-hover table-striped" id="table-1">
        <thead>
            <tr>
                <th style="text-align:left">No</th>
                <th style="text-align:left">Div</th>
                <th style="text-align:left">Dept</th>
                <th style="text-align:left">Kat B</th>
                <th style="text-align:left">PLU</th>
                <th style="text-align:left">DESC PANJANG</th>
                <th style="text-align:left">UNIT</th>
                <th style="text-align:left">FRAC</th>
                <th style="text-align:left">LCOST</th>
                <th style="text-align:left">ACOST</th>
                <th style="text-align:left">CB MM</th>
                <th style="text-align:left">HRG MM</th>
                <th style="text-align:left">HRG NET MM</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $nomor = 1;
            if ($stmt && $stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $nomor++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['div']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['dept']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['kat']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['plu']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['desk']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['prd_unit']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['prd_frac']) . '</td>';
                    echo '<td>' . number_format((float)$row['lcost'], 2) . '</td>';
                    echo '<td>' . number_format((float)$row['acost'], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($row['cbmm']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['hrg_normal']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['hrg_netmm']) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr class="no-data-row">';
                echo '<td colspan="13" class="text-center">Tidak ada data yang tersedia.</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>