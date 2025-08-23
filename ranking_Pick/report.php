<?php

require_once '../layout/_top.php';
require_once '../helper/connection.php';

$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : '';

if (!empty($tanggalAwal)) {
    $tanggalAwalFormatted = date('Y-m-d', strtotime($tanggalAwal));
} else {
    $tanggalAwalFormatted = '';
}

$query = "SELECT 
    d.obi_picker, 
    CASE 
        WHEN d.obi_picker = 'KL1' THEN 'ILHAM'
        WHEN d.obi_picker = 'KL2' THEN 'PUTRA'
        WHEN d.obi_picker = 'KL3' THEN 'FARHAN'
        WHEN d.obi_picker = 'KL4' THEN 'RISKI'
        ELSE 'NO USER'
    END AS obi_picker_name, 
    COUNT(DISTINCT h.obi_nopb) AS total_nopb,
    COUNT(d.obi_prdcd) AS plu_pick
FROM TBTR_OBI_H h
LEFT JOIN TBTR_OBI_D d 
    ON h.obi_notrans = d.obi_notrans 
    AND h.obi_tgltrans = d.obi_tgltrans
WHERE DATE(d.obi_close_dt) = :tanggalAwal
AND h.obi_recid NOT IN ('1', '2')
GROUP BY d.obi_picker, obi_picker_name
ORDER BY total_nopb DESC";

try {
    $stmt = $conn->prepare($query);
    if (!empty($tanggalAwalFormatted)) {
        $stmt->bindValue(':tanggalAwal', $tanggalAwalFormatted);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Query failed: " . $e->getMessage() . "</div>";
    exit;
}

?>


<body>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">KLASEMEN SEMENTARA LIGA PICKING SPI METRO</h3>
        <a href="../pluNoSales/index.php" class="btn btn-back">BACK</a>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
    <table id="GridView" class="table table-striped table-bordered table-hover text-center">
        <thead class="thead-dark">
            <tr>
                <th class="align-middle">NO</th>
                <th class="align-middle">ID PICK</th>
                <th class="align-middle">NAMA PICKER</th>
                <th class="align-middle">TOTAL PB</th>
                <th class="align-middle">PLU PICK</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            if (!empty($result)) {
                foreach ($result as $row): 
            ?>
                <tr>
                    <td class="align-middle"><?php echo $no++; ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($row['obi_picker'] ?? ''); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($row['obi_picker_name'] ?? ''); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($row['total_nopb'] ?? 0); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($row['plu_pick'] ?? 0); ?></td>
                </tr>
            <?php 
                endforeach;
            } else {
            ?>
                <tr>
                    <td colspan="5" class="text-center align-middle">Data tidak tersedia</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>


<script>
    $(document).ready(function() {
        $('#GridView').DataTable({
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: false,
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'PICKING_USER_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function () {
                this.api().columns.adjust().draw();
            }
        });
    });
</script>

</body>
