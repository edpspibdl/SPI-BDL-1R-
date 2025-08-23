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

    #table-1 td.respons-column {
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
            <tr class="success">
                <th>NO</th>
                <th>LOKASI</th>
                <th>LKS_PRDCD</th>
                <th>PRD_DESKRIPSIPANJANG</th>
                <th>PRO_CB_GF</th>
                <th>PRO_KODE_PROMOSI</th>
                <th>PRO_NAMA_PROMOSI</th>
                <th>PRO_MEMBER</th>
                <th>PRO_MULAI</th>
                <th>PRO_SELESAI</th>
                <th>PRO_CASHBACK</th>
                <th>PRO_HADIAH</th>
                <th>PRO_BELI_RPH</th>
                <th>PRO_BELI_QTY</th>
                <th>PRO_HADIAH_QTY</th>
                <th>PRO_ALOKASI</th>
                <th>PRO_HDH_KELUAR</th>
                <th>PRO_HDH_SISA</th>
        </thead>
        <tbody>
            <?php
            $nomor = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>'
                    . '<td>' . $nomor . '</td>'
                    . '<td>' . $row['lokasi'] . '</td>'
                    . '<td>' . $row['lks_prdcd'] . '</td>'
                    . '<td class="respons-column">' . $row['prd_deskripsipanjang'] . '</td>'
                    . '<td>' . $row['pro_cb_gf'] . '</td>'
                    . '<td>' . $row['pro_kode_promosi'] . '</td>'
                    . '<td class="respons-column">' . $row['pro_nama_promosi'] . '</td>'
                    . '<td>' . $row['pro_member'] . '</td>'
                    . '<td class="respons-column">' . $row['pro_mulai'] . '</td>'
                    . '<td class="respons-column">' . $row['pro_selesai'] . '</td>'
                    . '<td nowrap>' . $row['pro_cashback'] . '</td>'
                    . '<td class="respons-column">' . $row['pro_hadiah'] . '</td>'
                    . '<td>' . $row['pro_beli_rph'] . '</td>'
                    . '<td>' . $row['pro_beli_qty'] . '</td>'
                    . '<td>' . $row['pro_hadiah_qty'] . '</td>'
                    . '<td>' . $row['pro_alokasi'] . '</td>'
                    . '<td>' . $row['pro_hdh_keluar'] . '</td>'
                    . '<td>' . $row['pro_hdh_sisa'] . '</td>'
                    . '</tr>';
                $nomor++;
            }
            ?>
        </tbody>

    </table>
</div>