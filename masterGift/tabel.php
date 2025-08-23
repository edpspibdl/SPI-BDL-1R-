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
                <th>No</th>
                <th>ALAMAT</th>
                <th>KD PROMOSI</th>
                <th>KET PROMOSI</th>
                <th>NAMA PROMOSI</th>
                <th>TGL AWAL</th>
                <th>TGL AKHIR</th>
                <th>LPP</th>
                <th>PLANO</th>
                <th>PENGELUARAN</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $nomor = 1;
            if ($stmt && $stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $nomor++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['alamat']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gfh_kodepromosi']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gfh_kethadiah']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gfh_namapromosi']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gfh_tglawal']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['tgl_berlaku_akhir']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['lpp']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['plano']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['pengeluaran']) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr class="no-data-row">';
                echo '<td colspan="10" class="text-center">Tidak ada data yang tersedia.</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>