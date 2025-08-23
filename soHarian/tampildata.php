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

    $totalqtyplano = 0;
    $totalqtylpp = 0;
    $totalrphplano = 0;
    $totalrphlpp = 0;
    $acost = 0;
    $totalpicking = 0;

    if (isset($_POST['divisi'])) {
        $divisi = strtoupper($_POST['divisi']);
    } else {
        $divisi = "";
    }

    $tgl_awal = date('d/F/y H:i:s');

    if (isset($_POST['plu'])) {
        $plu = trim($_POST['plu']);
    } elseif (isset($_GET['plu'])) {
        $plu = trim($_GET['plu']);
    } else {
        echo "<h2>Jangan lupa isi PLU-nya... :)</h2>";
        $plu = 0;
    }

    // Explode masukan ke bentuk banyak plu
    $pluex = explode(",", $plu);

    foreach ($pluex as $plu0) {
        $plu0 = "'" . sprintf("%07s", $plu0) . "'";
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
        plu AS plu_obi,
        SUM(qty_final) AS qty_obi_pick,
        SUM(qty_final * st_avgcost) AS rph_obi_pick
    FROM (
        SELECT
            CASE
                WHEN obi_recid = '1' THEN 'SIAP PICKING'
                WHEN obi_recid = '2' THEN 'SIAP PACKING'
                WHEN obi_recid = '3' THEN 'SIAP DRAFT STRUK'
                WHEN obi_recid = '4' THEN 'KONFIRM PEMBAYARAN'
                WHEN obi_recid = '5' THEN 'SIAP STRUK'
                WHEN obi_recid = '6' THEN 'SELESAI STRUK'
                WHEN obi_recid = '7' THEN 'SET ONGKIR'
            END AS status,
            obi_tipebayar,
            obi_recid,
            DATE_TRUNC('day', obi_tglpb) AS obi_tglpb,
            obi_notrans,
            obi_nopb,
            SUBSTRING(plu, 1, 6) || '0' AS plu,
            qty_pick,
            qty_pack,
            obi_tglstruk,
            CASE
                WHEN qty_pick <> qty_pack AND qty_pack IS NOT NULL THEN qty_pack
                ELSE qty_pick
            END AS qty_final,
            st_avgcost
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
    LEFT JOIN (
        SELECT
            st_prdcd,
            st_avgcost
        FROM tbmaster_stock
        WHERE st_lokasi = '01'
    ) s ON s.st_prdcd = SUBSTRING(plu, 1, 6) || '0'
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
    AND PRD_PRDCD IN ($plu0) 
ORDER BY 
    DESKRIPSI, AREA, LKS_KODERAK, LKS_KODESUBRAK, LKS_TIPERAK, LKS_SHELVINGRAK, LKS_NOURUT ASC
";

     // Cetak data so toko
        $stmt = $conn->prepare($sql_sotoko);
        if (!$stmt->execute()) {
            echo "Query failed: " . implode(":", $stmt->errorInfo());
        } else {
	   
        echo "<div class='inline'> <h3>SO HARIAN $tgl_awal | PLU $plu0</h3> <h3 style='color:darkgray; padding-left: 6vw;'></h3> </div>";
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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['plu'] . "</td>"; 
            echo "<td>" . $row['deskripsi'] . "</td>"; 
            echo "<td>" . $row['unit'] . "/" . $row['frac'] . "</td>"; 
            echo "<td>" . $row['tag'] . "</td>"; 
            echo "<td>" . $row['area'] . "</td>"; 
            echo "<td>" . $row['rak'] . "." . $row['subrak'] . "." . $row['tipe'] . "." . $row['shelv'] . "." . $row['nourut'] . "</td>"; 
            echo "<td class='ratakanan'>" . number_format($row['stok_plano'], 0, ",", ",") . "</td>";    
            $totalqtyplano += $row['stok_plano'];
            $totalqtylpp = $row['stok_lpp'];
            $acost = $row['acost'];
            $totalpicking = $row['qty_obi_pick'];
            echo "<td></td>"; 
            echo "<td></td>"; 
            echo "</tr>";
        }
        $totalrphlpp = $acost * $totalqtylpp;
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
        $totalqtyplano = 0;
        $totalqtylpp = 0;
		
		}
    }

    ?>

</body>

</html>