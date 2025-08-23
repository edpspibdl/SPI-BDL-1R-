<?php
$bv_hdh = $_POST['id'];

include '../helper/connection.php';

$query = "SELECT
        *
    FROM
        (
            SELECT
                date_trunc('day', tgl_trans)::date || kd_station || create_by || trans_no AS pk_gift,
                jenis_hadiah,
                kd_promosi,
                CASE
                    WHEN prd_deskripsipanjang IS NULL THEN
                        gfh_kethadiah
                    ELSE
                        gfh_kethadiah || '-' || prd_deskripsipanjang
                END AS hadiah,
                gfh_kethadiah,
                prd_deskripsipanjang,
                ket_hadiah,
                jmlh_hadiah,
                prd_frac
            FROM
                m_gift_h
                JOIN (
                    SELECT
                        *
                    FROM
                        tbtr_gift_hdr
                        LEFT JOIN tbmaster_prodmast ON prd_prdcd = gfh_kethadiah
                ) AS gift_hdr ON gfh_kodepromosi = kd_promosi
            WHERE
                jenis_hadiah IN ('PD', 'BM')
        ) AS gift_data
    WHERE
        pk_gift = :bv_hdh
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':bv_hdh', $bv_hdh);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo "<h5>Tidak ada data hadiah untuk ID Transaksi: <strong>" . htmlspecialchars($bv_hdh) . "</strong></h5>";
    exit;
}
?>

<!-- Modal Body Content -->
<h5>ID Transaksi: <strong><?= htmlspecialchars($bv_hdh) ?></strong></h5>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover cell-border compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Hadiah</th>
                <th>Frac</th>
                <th>Qty Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($no++) . "</td>
                        <td>" . htmlspecialchars($row["hadiah"]) . "</td>
                        <td>" . htmlspecialchars($row["prd_frac"]) . "</td>
                        <td>" . htmlspecialchars($row["jmlh_hadiah"]) . "</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>