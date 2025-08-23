<?php
$query = "  SELECT
                CASE
                    WHEN hdr.obi_recid IS NULL THEN
                        'Siap Picking'
                    WHEN substring(hdr.obi_recid, 1, 1) = '1' THEN
                        'Siap Picking'
                    WHEN substring(hdr.obi_recid, 1, 1) = '2' THEN
                        'Siap Packing'
                    WHEN substring(hdr.obi_recid, 1, 1) = '3' THEN
                        'Siap Draft Struk'
                    WHEN substring(hdr.obi_recid, 1, 1) = '4' THEN
                        'Konfirmasi Pembayaran'
                    WHEN substring(hdr.obi_recid, 1, 1) = '5' THEN
                        'Siap Struk'
                    WHEN substring(hdr.obi_recid, 1, 1) = '6' THEN
                        'Selesai Struk'
                    WHEN substring(hdr.obi_recid, 1, 1) = '7' THEN
                        'Set Ongkir'
                    WHEN substring(hdr.obi_recid, 1, 1) = 'B' THEN
                        'Transaksi Batal'
                END AS status,
                obi_kdmember,
                cus_namamember,
                CASE
                    WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'KlikIGR' THEN
                        CASE
                            WHEN COALESCE(cus_jenismember, 'N') = 'T' THEN
                                'TMI'
                            WHEN COALESCE(cus_flagmemberkhusus, 'N') = 'Y' THEN
                                'Member Merah'
                            ELSE
                                'Member Umum'
                        END
                    WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'Corp' THEN
                        'Corporate'
                    WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'TMI' THEN
                        'TMI'
                    ELSE
                        'Member Merah'
                END AS tipe_member,
                obi_nopb,
                to_char(obi_createdt, 'DD/MM/YYYY HH24:MI:SS') AS obi_pbin,
                to_char(obi_tglpb, 'DD/MM/YYYY HH24:MI:SS') AS obi_tglpb,
                to_char(obi_sendpick, 'DD/MM/YYYY HH24:MI:SS') AS obi_sendpick,
                to_char(obi_selesaipick, 'DD/MM/YYYY HH24:MI:SS') AS obi_selesaipick,
                to_char(obi_tglstruk, 'DD/MM/YYYY HH24:MI:SS') AS obi_tglstruk
            FROM
                tbtr_obi_h hdr
            JOIN tbmaster_customer ON cus_kodemember = obi_kdmember
            WHERE
                to_char(obi_createdt, 'DD-MM-YYYY') = :tgl ";

include "../helper/connection.php";

$stmt = $conn->prepare($query);
$stmt->bindParam(':tgl', $tgl, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="table-responsive">
    <table id="GridView" class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%; font-size:12px">
        <thead>
            <tr>
                <th> # </th>
                <th> Status </th>
                <th> No Member </th>
                <th> Nama Member </th>
                <th> Tipe Member </th>
                <th> No PB </th>
                <th> Tgl PB </th>
                <th> Tgl PB In </th>
                <th> Mulai Send HH </th>
                <th> Selesai Pick </th>
                <th> Tgl Struck </th>
            </tr>
        </thead>
        <tbody>
     <?php
            $no = 0;
            foreach ($results as $row) {
                $no++; ?>
                <tr>
                    <td> <?= $no ?> </td>
                    <td> <?= $row["status"] ?> </td>
                    <td> <?= $row["obi_kdmember"] ?> </td>
                    <td> <?= $row["cus_namamember"] ?> </td>
                    <td> <?= $row["tipe_member"] ?> </td>
                    <td> <?= $row["obi_nopb"] ?> </td>
                    <td> <?= $row["obi_tglpb"] ?> </td>
                    <td> <?= $row["obi_pbin"] ?> </td>
                    <td> <?= $row["obi_sendpick"] ?> </td>
                    <td> <?= $row["obi_selesaipick"] ?> </td>
                    <td> <?= $row["obi_tglstruk"] ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>