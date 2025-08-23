<?php
require_once '_config.php';

class OBI extends DBConfig
{
    public function headerTgl($id)
    {
        $query = "SELECT
                        to_char(obi_tgltrans, 'DD-MON-YYYY') AS obi_tgltrans,
                        SUM(coalesce(pb_masuk, 0)) AS pb_masuk,
                        SUM(coalesce(siap_send, 0)) AS siap_send,
                        SUM(coalesce(siap_pick, 0)) AS siap_pick,
                        SUM(coalesce(siap_pack, 0)) AS siap_pack,
                        SUM(coalesce(siap_struk, 0)) AS siap_struk,
                        SUM(coalesce(ssai_struk, 0)) AS ssai_struk,
                        SUM(coalesce(pb_batal, 0)) AS pb_batal
                    FROM
                        (SELECT
                                obi_tgltrans,
                                CASE WHEN hdr.obi_tgltrans IS NOT NULL THEN 1 END AS pb_masuk,
                                CASE WHEN hdr.obi_recid IS NULL OR substring(hdr.obi_recid, 1, 1) = '1' AND obi_sendpick IS NULL THEN 1 END AS siap_send,
                                CASE WHEN substring(hdr.obi_recid, 1, 1) = '1' AND obi_sendpick IS NOT NULL THEN 1 END AS siap_pick,
                                CASE WHEN substring(hdr.obi_recid, 1, 1) = '2' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 1 END AS siap_pack,
                                CASE WHEN substring(hdr.obi_recid, 1, 1) = '5' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 1 END AS siap_struk,
                                CASE WHEN substring(hdr.obi_recid, 1, 1) = '6' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL AND obi_tglstruk IS NOT NULL THEN 1 END AS ssai_struk,
                                CASE WHEN substring(hdr.obi_recid, 1, 1) = 'B' THEN 1 END AS pb_batal
                            FROM
                                tbtr_obi_h hdr 
                            WHERE
                                date_trunc('day', obi_tgltrans) >= date_trunc('day', current_date) - INTERVAL ':id day'
                        ) subquery
                    GROUP BY
                        obi_tgltrans
                    ORDER BY
                        1";
        
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetchAll();
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
        return $result;
    }
}

// header('Content-Type: application/json; charset=utf-8');
// $obi = new OBI;
// $hdr = $obi->headerTgl(4);
// echo json_encode($hdr);
?>


<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover cell-border compact">
        <thead>
            <tr>
                <th> TGL </th>
                <th> PB </th>
                <th> SIAP SEND </th>
                <th> SIAP PICK </th>
            </tr>
        <tbody>
            <?php
            $obi = new OBI;
            $hdr = $obi->headerTgl(4);
            foreach ($hdr as $row) { ?>
                <tr>
                    <td><?= $row['OBI_TGLTRANS']; ?></td>
                    <td><?= $row['PB_MASUK']; ?></td>
                    <td><?= $row['SIAP_SEND']; ?></td>
                    <td><?= $row['SIAP_PICK']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>