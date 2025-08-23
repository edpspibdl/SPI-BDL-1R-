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
                <th>#</th>
                <th>PLU</th>
                <th>DESK</th>
                <th>PRD_FRAC</th>
                <th>PRD_UNIT</th>
                <th>SPB_MINTA_CTN</th>
                <th>SPB_MINTA_PCS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $nomor = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $nomor . '</td>';
                echo '<td>' . htmlspecialchars($row["plu"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["desk"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["prd_frac"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["prd_unit"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["spb_minta_ctn"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["spb_minta_pcs"]) . '</td>';
                echo '</tr>';
                $nomor++;
            }
            ?>
        </tbody>
    </table>
</div>