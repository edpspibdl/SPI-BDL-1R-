<?php
// Koneksi database PostgreSQL using PDO
include '../helper/connection.php';

// Variabel utama
$kdmember = $_GET['mbr'];
$tgltran = $_GET['tgltran'];
$cashierid = $_GET['cashierid'];
$station = $_GET['station'];
$notran = $_GET['notran'];
$totaltran = 0;

// SQL query with named placeholders
$sql_jualheader = "
    SELECT 
        trjd_transactiondate,
        trjd_create_by,
        trjd_cashierstation,
        trjd_transactionno,
        trjd_prdcd,
        trjd_prd_deskripsipendek,
        trjd_quantity,
        trjd_unitprice,
        trjd_nominalamt,
        trjd_cus_kodemember,
        trjd_transactiontype
    FROM 
        tbtr_jualdetail
    WHERE 
        trjd_cus_kodemember = :kodemember
        AND DATE(trjd_transactiondate) = :tgltran
        AND trjd_create_by = :cashierid
        AND trjd_cashierstation = :station
        AND trjd_transactionno = :notran
    ORDER BY 
        trjd_seqno
";

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql_jualheader);
    // Execute the statement with parameters
    $stmt->execute([
        ':kodemember' => $kdmember,
        ':tgltran' => $tgltran,
        ':cashierid' => $cashierid,
        ':station' => $station,
        ':notran' => $notran
    ]);

    // Prepare HTML to display
    echo "KODE MEMBER : <b>$kdmember </b><br/>";
    echo "NOMOR TRANSAKSI : <b>$cashierid.$station.$notran </b><br/>";
    echo "TGL TRANSAKSI : <b>$tgltran </b><br/><br/>";

    echo "<table id='transactionTable' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>PLU</th> 
                <th>DESKRIPSI</th> 
                <th>QTY</th> 
                <th>UNITPRICE</th> 
                <th>NOMINAL RPH</th> 
                <th>TYPE</th> 
            </tr>
        </thead>
        <tbody>";

    // Fetch and display results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['trjd_prdcd']) . "</td>";
        echo "<td>" . htmlspecialchars($row['trjd_prd_deskripsipendek']) . "</td>";
        echo "<td style='text-align:right'>" . htmlspecialchars($row['trjd_quantity']) . "</td>";
        echo "<td style='text-align:right'>" . number_format($row['trjd_unitprice'], 0, ".", ",") . "</td>";
        echo "<td style='text-align:right'>" . number_format($row['trjd_nominalamt'], 0, ".", ",") . "</td>";
        $totaltran += $row['trjd_nominalamt'];
        echo "<td>" . htmlspecialchars($row['trjd_transactiontype']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
    echo "<div style='text-align:right'><b>TOTAL NILAI TRANSAKSI: </b> <b>" . number_format($totaltran, 2, ".", ",") . "</b></div>";

} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage();
}
?>

<script>
    $(document).ready(function() {
        $('#transactionTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false
        });
    });
</script>
