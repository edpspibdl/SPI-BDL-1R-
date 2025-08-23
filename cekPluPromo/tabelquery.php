<?php
require_once '../helper/connection.php';

if (empty($kodePromo)) {
    echo '<div class="alert alert-warning text-center">Silakan masukkan Kode Promosi untuk mencari data.</div>';
    exit;
}

$query = "SELECT 
    cbd_kodeigr, 
    cbd_recordid, 
    cbd_prdcd,
    PRD_DESKRIPSIPANJANG,
    cbd_kodepromosi,
    cbh_namapromosi,
    cbh_mekanisme,
    cbh_maxstrkperhari,
    cbh_minrphprodukpromo,
    cbh_cashback
FROM tbtr_cashback_dtl
JOIN TBMASTER_PRODMAST ON CBD_PRDCD = PRD_PRDCD
JOIN tbtr_cashback_hdr ON cbd_kodepromosi = cbh_kodepromosi
WHERE cbd_kodepromosi = :kodePromo
  AND PRD_RECORDID IS NULL
  AND cbd_recordid IS NULL";

$stmt = $conn->prepare($query);
$stmt->bindParam(':kodePromo', $kodePromo, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$noUrut = 0;
?>

<div class="container-fluid">
    <div class="table-responsive table-sm">
        <?php if (count($data) === 0): ?>
            <div class="text-center">Tidak ada data yang ditemukan untuk kode promosi tersebut.</div>
        <?php else: ?>
            <table id="GridView" class="table table-bordered table-striped table-hover webgrid-table-hidden">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>KD SPI</th>
                        <th>RECID</th>
                        <th>PLU</th>
                        <th>DESK</th>
                        <th>KD PROMO</th>
                        <th>NAMA PROMO</th>
                        <th>MEKANISME</th>
                        <th>MAX /H</th>
                        <th>MIN BELANJA</th>
                        <th>RPH CSBCK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= ++$noUrut; ?></td>
                            <td><?= htmlspecialchars($row['cbd_kodeigr']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['cbd_recordid']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['cbd_prdcd']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['prd_deskripsipanjang']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['cbd_kodepromosi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['cbh_namapromosi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['cbh_mekanisme']); ?></td>
                            <td><?= htmlspecialchars($row['cbh_maxstrkperhari']); ?></td>
                            <td><?= htmlspecialchars($row['cbh_minrphprodukpromo']); ?></td>
                            <td><?= htmlspecialchars($row['cbh_cashback']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>