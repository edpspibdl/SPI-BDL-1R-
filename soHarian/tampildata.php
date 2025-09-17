<!DOCTYPE html>
<html>

<head>
    <title>FORMULIR SO PER-PLU</title>
    <style>
    body {
        font-family:'Courier New',sans-serif;
    }
    h3 {
        margin:0px;
    }
    table {
        border: 0px solid black;
        border-collapse: collapse;
        margin:0 0 10px;
        width:auto;
        font-size:14px;
    }

    th{
        background:#66CCCC;
        padding:5px;
        font-weight:400;
        border: 1px solid black;
    }
    td{
        padding:2px 5px;
        border: 1px solid black;
        border-collapse: collapse;
        margin:0 0 10px;
        width:auto;
        font-size:14px;
    }
    .ratakanan {
        text-align:right;
    }

    .inline h3 {
        display: inline-block;
    }
</style>
</head>

<body>
    <?php
    $error = 0;

    // Koneksi database PostgreSQL
    require_once '../helper/connection.php'; 

    // GET VARIABEL
    date_default_timezone_set('Asia/Jakarta');

    if (isset($_POST['plu'])) {
        $plu = trim($_POST['plu']);
    } elseif (isset($_GET['plu'])) {
        $plu = trim($_GET['plu']);
    } else {
        echo "<h2>Jangan lupa isi PLU-nya... :)</h2>";
        $plu = 0;
    }

    if ($plu == 0) {
        exit(); // Hentikan eksekusi jika tidak ada PLU
    }

    // Explode masukan ke bentuk banyak plu dan format untuk query
    $pluex = explode(",", $plu);
    $plu_list = array_map(function($p) {
        return "'" . sprintf("%07s", trim($p)) . "'";
    }, $pluex);
    $plu_in_clause = implode(",", $plu_list);

    // SQL query diubah untuk mengambil semua PLU dalam satu kali jalan
    $sql_sotoko = "
    SELECT 
        PRD_PRDCD AS PLU, 
        PRD_DESKRIPSIPENDEK AS DESKRIPSI, 
        PRD_FRAC AS FRAC, 
        PRD_UNIT AS UNIT, 
        PRD_KODETAG AS TAG,
        CASE 
            WHEN SUBSTRING(LKS_KODERAK, 1, 1) NOT IN ('D', 'G') THEN 'TOKO'
            WHEN SUBSTRING(LKS_KODERAK, 1, 1) IN ('D') THEN 'GUDANG'
        END AS AREA,
        LKS_KODERAK AS RAK, 
        LKS_KODESUBRAK AS SUBRAK, 
        LKS_TIPERAK AS TIPE, 
        LKS_SHELVINGRAK AS SHELV, 
        LKS_NOURUT AS NOURUT,
        LKS_QTY AS STOK_PLANO, 
        ST_SALDOAKHIR AS STOK_LPP, 
        ST_AVGCOST AS ACOST, 
        RECID4, 
        QTY_OBI_PICK
    FROM 
        TBMASTER_PRODMAST 
    LEFT JOIN 
        TBMASTER_LOKASI ON PRD_PRDCD = LKS_PRDCD
    LEFT JOIN 
        TBMASTER_STOCK ON ST_PRDCD = PRD_PRDCD
    LEFT JOIN (
        SELECT
            plu_obi,
            SUM(qty_obi_pick) AS QTY_OBI_PICK
        FROM (
            SELECT
                SUBSTRING(plu, 1, 6) || '0' AS plu_obi,
                CASE
                    WHEN qty_pick <> qty_pack AND qty_pack IS NOT NULL THEN qty_pack
                    ELSE qty_pick
                END AS qty_obi_pick
            FROM
                tbtr_obi_h
            LEFT JOIN (
                SELECT DISTINCT
                    DATE_TRUNC('day', tgl) AS tgl,
                    no_pb,
                    plu,
                    qty_pick,
                    qty_pack
                FROM (
                    SELECT
                        DATE_TRUNC('day', obi_tgltrans) AS tgl,
                        obi_notrans AS no_pb,
                        obi_prdcd AS plu
                    FROM tbtr_obi_d
                    UNION ALL
                    SELECT
                        DATE_TRUNC('day', pobi_tgltransaksi),
                        pobi_notransaksi,
                        pobi_prdcd
                    FROM tbtr_packing_obi
                ) t1
                LEFT JOIN (
                    SELECT
                        DATE_TRUNC('day', obi_tgltrans) AS tgl_obid,
                        obi_notrans,
                        obi_prdcd,
                        obi_qtyrealisasi AS qty_pick
                    FROM tbtr_obi_d
                ) t2 ON t1.tgl = t2.tgl_obid
                AND t1.no_pb = t2.obi_notrans
                AND t1.plu = t2.obi_prdcd
                LEFT JOIN (
                    SELECT
                        DATE_TRUNC('day', pobi_tgltransaksi) AS pobi_tgltransaksi,
                        pobi_notransaksi,
                        pobi_prdcd,
                        pobi_qty AS qty_pack
                    FROM tbtr_packing_obi
                ) t3 ON t1.tgl = t3.pobi_tgltransaksi
                AND t1.no_pb = t3.pobi_notransaksi
                AND t1.plu = t3.pobi_prdcd
            ) t ON DATE_TRUNC('day', t.tgl) = DATE_TRUNC('day', obi_tglpb)
            AND t.no_pb = obi_notrans
            WHERE obi_recid IN ('1', '2', '3', '7')
        ) AS subquery
        GROUP BY plu_obi
    ) AS obi ON plu_obi = PRD_PRDCD
    LEFT JOIN (
        SELECT 
            SUBSTRING(PBO_PLUIGR, 1, 6) || '0' AS pboplu, 
            SUM(pbo_qtyrealisasi) AS RECID4 
        FROM tbmaster_pbomi 
        WHERE pbo_nokoli IS NOT NULL 
        AND pbo_nokoli NOT IN (SELECT RPB_NOKOLI FROM TBTR_REALPB) 
        GROUP BY SUBSTRING(PBO_PLUIGR, 1, 6) || '0'
    ) pb ON pboplu = PRD_PRDCD
    WHERE 
        ST_LOKASI = '01' 
        AND PRD_PRDCD IN ($plu_in_clause) 
    ORDER BY 
    LKS_KODERAK, LKS_KODESUBRAK, LKS_TIPERAK, LKS_SHELVINGRAK, LKS_NOURUT ASC
    ";

    // Data dari query besar disimpan ke dalam array
    $stmt = $conn->prepare($sql_sotoko);
    if (!$stmt->execute()) {
        echo "Query failed: " . implode(":", $stmt->errorInfo());
    } else {
        $data_per_plu = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $plu_key = $row['plu'];
            if (!isset($data_per_plu[$plu_key])) {
                $data_per_plu[$plu_key] = [
                    'details' => [],
                    'stok_plano_total' => 0,
                    'stok_lpp' => $row['stok_lpp'],
                    'acost' => $row['acost'],
                    'qty_obi_pick' => $row['qty_obi_pick']
                ];
            }
            $data_per_plu[$plu_key]['details'][] = $row;
            $data_per_plu[$plu_key]['stok_plano_total'] += $row['stok_plano'];
        }

        // Loop untuk menampilkan data
        foreach ($data_per_plu as $plu_key => $plu_data) {
            $tgl_awal = date('d/F/y H:i:s');
            echo "<div class='inline'> <h3>SO HARIAN $tgl_awal | PLU $plu_key</h3> <h3 style='color:darkgray; padding-left: 6vw;'></h3> </div>";
            echo "<table>
                <tr>
                    <th style='width:60px'>PLU</th>
                    <th style='width:200px'>DESKRIPSI</th>
                    <th>FRAC</th>
                    <th>TAG</th>
                    <th>AREA</th>
                    <th>LOKASI</th>
                    <th>QTY PLANO</th>
                    <th>QTY FISIK</th>
                    <th>SELISIH</th>
                </tr>";

            foreach ($plu_data['details'] as $row) {
                echo "<tr>";
                echo "<td>" . $row['plu'] . "</td>";
                echo "<td>" . $row['deskripsi'] . "</td>";
                echo "<td>" . $row['unit'] . "/" . $row['frac'] . "</td>";
                echo "<td>" . $row['tag'] . "</td>";
                echo "<td>" . $row['area'] . "</td>";
                echo "<td>" . $row['rak'] . "." . $row['subrak'] . "." . $row['tipe'] . "." . $row['shelv'] . "." . $row['nourut'] . "</td>";
                echo "<td class='ratakanan'>" . number_format($row['stok_plano'], 0, ",", ",") . "</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
            }

            // Tampilkan total
            $totalqtyplano = $plu_data['stok_plano_total'];
            $totalqtylpp = $plu_data['stok_lpp'];
            $acost = $plu_data['acost'];
            $totalpicking = $plu_data['qty_obi_pick'];

            echo "<tr>";
            echo "<td style='border:0px'><b> ACOST :</b></td>";
            echo "<td style='border:0px'><b>" . number_format($acost, 0, ".", ",") . "</b></td>";
            echo "<td colspan='4' class='ratakanan' style='border:0px'><b>TOTAL QTY PLANO (a)</b></td>";
            echo "<td class='ratakanan'><b>" . number_format($totalqtyplano, 0, ",", ",") . "</b></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td colspan='6' class='ratakanan' style='border:0px'><b>QTY LPP (b)</b></td>";
            echo "<td class='ratakanan'><b>" . number_format($totalqtylpp, 0, ",", ",") . "</b></td>";
            echo "<td class='ratakanan'><b>" . number_format($totalqtylpp, 0, ",", ",") . "</b></td>";
            echo "<td>///////</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td colspan='6' class='ratakanan' style='border:0px'><b>Picking (c)</b></td>";
            echo "<td class='ratakanan'><b>" . number_format($totalpicking, 0, ",", ",") . "</b></td>";
            echo "<td></td>";
            echo "<td>///////</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td colspan='6' class='ratakanan' style='border:0px'><b>SELISIH (a+c)-b</b></td>";
            echo "<td class='ratakanan'><b>" . number_format(($totalqtyplano + $totalpicking) - $totalqtylpp, 0, ",", ",") . "</b></td>";
            echo "<td></td>";
            echo "<td>///////</td>";
            echo "</tr>";

            echo "</table><br/>";
        }
    }
    ?>
</body>

</html>