<?php
include "../helper/connection.php";

$batal = "SELECT 
            CASE 
                WHEN obi_recid IS NULL THEN 'Siap Picking'
                WHEN substring(obi_recid, 1, 1) = '1' THEN 'Siap Picking'
                WHEN substring(obi_recid, 1, 1) = '2' THEN 'Siap Packing'
                WHEN substring(obi_recid, 1, 1) = '3' THEN 'Siap Draft Struk'
                WHEN substring(obi_recid, 1, 1) = '4' THEN 'Konfirmasi Pembayaran'
                WHEN substring(obi_recid, 1, 1) = '5' THEN 'Siap Struk'
                WHEN substring(obi_recid, 1, 1) = '6' THEN 'Selesai Struk'
                WHEN substring(obi_recid, 1, 1) = '7' THEN 'Set Ongkir'
                WHEN substring(obi_recid, 1, 1) = 'B' THEN 'Transaksi Batal'
            END AS status,
            obi_kdmember AS kode_member,
            CASE 
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'KlikIGR' THEN
                    CASE 
                        WHEN COALESCE(cus_jenismember, 'N') = 'T' THEN 'TMI'
                        WHEN COALESCE(cus_flagmemberkhusus, 'N') = 'Y' THEN 'Member Merah'
                        ELSE 'Member Umum'
                    END
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'Corp' THEN 'Corporate'
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'TMI' THEN 'TMI'
                ELSE 'Member Merah'
            END AS tipe_member,
            COALESCE(obi_alasanbtl, ' ') AS alasan_batal,
            obi_itemorder AS item_order,
            obi_realitem AS item_real,
            ROUND(obi_ttlorder + obi_ttlppn, 0) AS total_order,
            ROUND(obi_realorder + obi_realppn, 0) AS total_real
        FROM tbtr_obi_h
        LEFT JOIN tbmaster_customer ON obi_kdmember = cus_kodemember
        WHERE obi_tgltrans = TO_DATE(:bv_tgl, 'DD-MM-YYYY')
        ORDER BY 1 DESC";

$stmt = $conn->prepare($batal);
$stmt->bindParam(':bv_tgl', $tgl); // Assuming $tgl is defined somewhere in your code
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="table-responsive">
        <table id="GridView"
            class="table table-striped table-hover table-bordered"
            style="width:100%; font-size:13px; border-collapse:collapse; text-align:center;">
            <thead style="background-color:#f1f1f1;">
                <tr>
                    <th>TANGGAL</th>
                    <th>STATUS</th>
                    <th>KODE MEMBER</th>
                    <th>TIPE MEMBER</th>
                    <th>ITEM ORDER</th>
                    <th>ITEM REAL</th>
                    <th>TOTAL ORDER</th>
                    <th>TOTAL REAL</th>
                    <th>ALASAN BATAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                foreach ($results as $row) {
                    $no++; ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($tgl)) ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['kode_member'] ?></td>
                        <td><?= $row['tipe_member'] ?></td>
                        <td><?= number_format($row['item_order'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['item_real'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['total_order'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['total_real'], 0, ',', '.') ?></td>
                        <td><?= $row['alasan_batal'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
