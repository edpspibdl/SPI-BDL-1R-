<?php
$query_p = "   SELECT
    kd_promosi,
    hadiah,
    prd_frac,
    SUM(jmlh_hadiah) AS jmlh_hadiah
FROM (
    SELECT *
    FROM (
        SELECT
            obi_nopb,
            obi_tglpb,
            obi_notrans,
            obi_kdmember,
            cus_namamember,
            TO_CHAR(DATE_TRUNC('day', obi_tglstruk), 'YYYYMMDD') ||
            obi_kdstation ||
            obi_cashierid ||
            obi_nostruk AS pk_obi
        FROM
            tbtr_obi_h
        LEFT JOIN
            tbmaster_customer ON cus_kodemember = obi_kdmember
        WHERE
            obi_recid = '6'
            AND obi_tipe = 'S'
            AND TO_CHAR(DATE_TRUNC('day', obi_tglpb), 'YYYYMMDD') BETWEEN :bv_tanggal1 AND :bv_tanggal2
        ORDER BY
            TO_CHAR(DATE_TRUNC('day', obi_tglpb), 'YYYYMMDD') || obi_notrans
    ) AS obi_data
    LEFT JOIN (
        SELECT *
        FROM (
            SELECT
                TO_CHAR(DATE_TRUNC('day', tgl_trans), 'YYYYMMDD') ||
                kd_station ||
                create_by ||
                trans_no AS pk_gift,
                JENIS_HADIAH,
                kd_promosi,
                CASE
                    WHEN prd_deskripsipanjang IS NULL THEN gfh_kethadiah
                    ELSE gfh_kethadiah || '-' || prd_deskripsipanjang
                END AS hadiah,
                gfh_kethadiah,
                prd_deskripsipanjang,
                ket_hadiah,
                jmlh_hadiah,
                prd_frac
            FROM
                m_gift_h
            JOIN (
                SELECT *
                FROM tbtr_gift_hdr
                LEFT JOIN tbmaster_prodmast ON prd_prdcd = gfh_kethadiah
            ) AS hadiah_detail ON gfh_kodepromosi = kd_promosi
            WHERE jenis_hadiah IN ('PD', 'BM')
        ) AS gift_data
    ) AS gift_join ON obi_data.pk_obi = gift_join.pk_gift
    WHERE hadiah IS NOT NULL
) AS final_data
GROUP BY hadiah, prd_frac,kd_promosi
ORDER BY jmlh_hadiah DESC ";
include '../helper/connection.php';
$stmt = $conn->prepare($query_p);
$stmt->bindParam(':bv_tanggal1', $tgl_mulai);
$stmt->bindParam(':bv_tanggal2', $tgl_selesai);
// Execute the query
$stmt->execute();


?>
<h3 class="text-center" style="margin-top: 10px;">Daftar Hadiah</h3>
<div class="table-responsive">
    <table id="table-1" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th> No </th>
                <th> kd promo </th>
                <th> Hadiah </th>
                <th> Fraction </th>
                <th> Jumlah </th>
            </tr>
        <tbody>
            <?php
            $no_ = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $no_++; ?>
                <tr>
                    <td><?= $no_ ?></td>
                    <td class="text-nowrap"> <?= $row["kd_promosi"] ?> </td>
                    <td class="text-nowrap"> <?= $row["hadiah"] ?> </td>
                    <td class="text-nowrap"> <?= $row["prd_frac"] ?> </td>
                    <td class="text-nowrap"> <?= $row["jmlh_hadiah"] ?> </td>
                </tr
                    <?php } ?>
                    </tbody>
    </table>
</div>
<?php require_once '../layout/_bottom.php'; ?>