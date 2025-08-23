<?php
require_once '../helper/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["plu"]) && !empty($_POST["plu"])) {
    $plu = str_pad($_POST["plu"], 7, '0', STR_PAD_LEFT);

    $query = "
    SELECT RAK, LOK, HSO_PRDCD, PRD_DESKRIPSIPANJANG, FLAG, HSO_TGLSO, HSO_QTYLAMA, HSO_QTYBARU, TGL_UPDATE, USER_ID,
           MODIF_USER,  MODIF_JAM 
    FROM (
        SELECT
            CONCAT(
                hso_koderak, '.', 
                hso_kodesubrak, '.', 
                hso_tiperak, '.', 
                hso_shelvingrak, '.', 
                hso_nourut
            ) AS rak,
            CASE 
                WHEN SUBSTRING(hso_koderak FROM 1 FOR 1) = 'D' OR SUBSTRING(hso_koderak FROM 1 FOR 1) = 'G' 
                THEN 'Gudang'
                ELSE 'Toko' 
            END AS lok,
            hso_prdcd,
            prd_deskripsipanjang,
            flag,
            hso_tglso,
            hso_qtylama,
            hso_qtybaru,
            TO_CHAR(hso_create_dt, 'DD-Mon-YYYY HH24:MI:SS') AS tgl_update,
            hso_create_by AS user_id,
            username
        FROM
            tbhistory_soplano
            LEFT JOIN tbmaster_user ON hso_create_by = userid
            LEFT JOIN tbmaster_prodmast ON hso_prdcd = prd_prdcd
            LEFT JOIN (
                SELECT
                    prd_prdcd AS pluflg,
                    CASE
                        WHEN (COALESCE(prd_flagigr, 'N') = 'Y' AND COALESCE(prd_flagidm, 'N') = 'Y') THEN 'IGR + IDM'
                        WHEN (COALESCE(prd_flagigr, 'N') = 'Y' AND COALESCE(prd_flagidm, 'N') = 'N') THEN 'IGR ONLY'
                        WHEN (COALESCE(prd_flagigr, 'N') = 'N' AND COALESCE(prd_flagidm, 'N') = 'Y') THEN 'IDM ONLY'
                        ELSE 'BLM FLAG'
                    END AS flag
                FROM tbmaster_prodmast
            ) AS flag_table ON pluflg = hso_prdcd
        WHERE
            hso_prdcd = :plu
            AND (hso_qtylama + hso_qtybaru) != 0
        ORDER BY
            hso_create_dt DESC
    ) AS subquery
    LEFT JOIN (
        SELECT
            CONCAT(
                LKS_KODERAK, '.', 
                LKS_KODESUBRAK, '.', 
                LKS_TIPERAK, '.', 
                LKS_SHELVINGRAK, '.', 
                LKS_NOURUT
            ) AS lokasi,
            LKS_PRDCD,
            LKS_MODIFY_BY AS modif_user,
            username AS modif_nama,
            TO_CHAR(LKS_MODIFY_DT, 'DD-Mon-YY HH24:MI:SS') AS modif_jam
        FROM tbmaster_lokasi
        JOIN tbmaster_user ON userid = LKS_MODIFY_BY
    ) AS loc_table ON lokasi = rak
    ";

    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':plu', $plu, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            echo "<p class='text-warning text-center'>Data tidak ditemukan.</p>";
            exit;
        }

        echo "<div class='table-responsive'>";
        echo "<table id='datatable' class='table table-bordered table-striped table-hover text-center' style='font-size: 12px;'>";
        echo "<thead class='thead-dark'>
                <tr>
                    <th>#</th>
                    <th>Rak</th>
                    <th>Lokasi</th>
                    <th>Plu</th>
                    <th>Deskripsi</th>
                    <th>Flag</th>
                    <th>Tanggal SO</th>
                    <th>Qty Lama</th>
                    <th>Qty Baru</th>
                    <th>Tanggal Update</th>
                    <th>User ID</th>
                    <th>Modif ID</th>
                    <th>Modif Jam</th>
                </tr>
              </thead><tbody>";

        $no = 1;
        foreach ($data as $row) {
            echo "<tr><td>$no</td>";
            foreach ($row as $col) {
                echo "<td class='text-nowrap'>" . htmlspecialchars($col) . "</td>";
            }
            echo "</tr>";
            $no++;
        }
        echo "</tbody></table></div>";
        echo "<script>
                $(document).ready(function() {
                    $('#datatable').DataTable({
                        'paging': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false
                    });
                });
              </script>";
    } catch (PDOException $e) {
        echo "<p class='text-danger text-center'>Query gagal: " . $e->getMessage() . "</p>";
    }
}
?>
