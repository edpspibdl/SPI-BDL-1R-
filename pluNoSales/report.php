<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Ensure this is the correct path

// Get the start and end date from the form input
$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : '';
$tanggalAkhir = isset($_GET['tanggalAkhir']) ? $_GET['tanggalAkhir'] : '';

// Check if dates are not empty before proceeding with formatting
if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
    // Convert the dates to the format that SQL expects (YYYYMMDD)
    $tanggalAwalFormatted = date('Ymd', strtotime($tanggalAwal));
    $tanggalAkhirFormatted = date('Ymd', strtotime($tanggalAkhir));
} else {
    // Default to empty strings if the dates are not provided
    $tanggalAwalFormatted = '';
    $tanggalAkhirFormatted = '';
}

// Prepare the SQL query with placeholders for the date range
$query = "
    SELECT DIV, DEP, KAT, PLU, DESKRIPSI, UNIT, FRAC, LPP, LAST_BPB, KODETAG, FLAG
FROM (
    SELECT DIV, DEP, KAT, PLU, DESKRIPSI, UNIT, FRAC, LPP, LAST_BPB, KODETAG,
           CASE 
               WHEN IGR = 'Y' AND IDM = 'Y' THEN 'IGR + IDM'
               WHEN IGR = 'N' AND IDM = 'Y' THEN 'IDM ONLY'
               WHEN IGR = 'Y' AND IDM = 'N' THEN 'IGR ONLY'
           END AS FLAG
    FROM (
        SELECT prd_kodedivisi AS DIV,
               prd_kodedepartement AS DEP,
               PRD_KODEKATEGORIBARANG AS KAT,
               st_prdcd AS PLU,
               prd_deskripsipanjang AS DESKRIPSI,
               prd_unit AS UNIT,
               prd_frac AS FRAC,
               LAST_BPB,
               PRD_KODETAG AS KODETAG,
               st_saldoakhir AS LPP,
               COALESCE(prd_flagigr, 'N') AS IGR,
               COALESCE(prd_flagidm, 'N') AS IDM
        FROM tbmaster_stock
        LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
        LEFT JOIN (
            SELECT mstd_prdcd AS PLU_MSTRAN, 
                   MAX(mstd_tgldoc) AS LAST_BPB
            FROM tbtr_mstran_d
            WHERE mstd_typetrn = 'B'
            GROUP BY mstd_prdcd
        ) AS subquery_mstran ON PLU_MSTRAN = st_prdcd
        WHERE st_lokasi = '01'
          AND (prd_hrgjual IS NOT NULL OR prd_hrgjual <> '0')
          AND st_saldoakhir <> '0'
          AND st_prdcd IN (
              SELECT mstd_prdcd
              FROM tbtr_mstran_d
              WHERE date_trunc('day', MSTD_TGLDOC) <= :tanggalAkhir
                AND mstd_typetrn IN ('B', 'L', 'I')
          )
          AND substr(st_prdcd, 1, 6) NOT IN (
              SELECT substr(trjd_prdcd, 1, 6)
              FROM tbtr_jualdetail
              WHERE TRJD_TRANSACTIONTYPE = 'S'
                AND date_trunc('day', TRJD_TRANSACTIONDATE) BETWEEN :tanggalAwal AND :tanggalAkhir
          )
        ORDER BY PLU ASC
    ) AS subquery_main
) AS subquery_flag
";

// Execute the query and fetch the results using the PDO connection
try {
    $stmt = $conn->prepare($query);
    // Only bind if the dates are formatted correctly
    if (!empty($tanggalAwalFormatted) && !empty($tanggalAkhirFormatted)) {
        $stmt->bindValue(':tanggalAwal', $tanggalAwalFormatted);
        $stmt->bindValue(':tanggalAkhir', $tanggalAkhirFormatted);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}

?>

<style>
    .report-container {
        margin-top: 30px;
    }
    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
    }
    .table thead th {
        background-color: #007bff;
        color: white;
        text-align: center;
    }
    .table td {
        text-align: left    ;
    }
    .table td:first-child, .table th:first-child {
        text-align: left;
    }
    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .table {
        width: 100%;
        table-layout: auto;
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">PLU NO SALES REPORT</h3>
        <a href="../pluNoSales/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pluNoSalesTable" class="table table-sm table-striped table-bordered table-hover text-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th>NO</th>
                                    <th>DIV</th>
                                    <th>DEP</th>
                                    <th>KAT</th>
                                    <th>PLU</th>
                                    <th>DESKRIPSI</th>
                                    <th>UNIT</th>
                                    <th>FRAC</th>
                                    <th>LPP</th>
                                    <th>LAST_BPB</th>
                                    <th>KODETAG</th>
                                    <th>FLAG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($result as $row): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['div']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dep']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['plu']); ?></td>
                                        <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                                        <td><?php echo htmlspecialchars($row['frac']); ?></td>
                                        <td><?php echo htmlspecialchars($row['lpp']); ?></td>
                                        <td><?php echo htmlspecialchars($row['last_bpb']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kodetag']); ?></td>
                                        <td><?php echo htmlspecialchars($row['flag']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    $(document).ready(function () {
        const table = $('#pluNoSalesTable').DataTable({
            responsive: true,
            autoWidth: true,
            lengthMenu: [10, 25, 50, 100],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: 'Copy'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    filename: 'PLU_NO_SALES_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ]
        });

        table.columns.adjust().draw();
    });
</script>


