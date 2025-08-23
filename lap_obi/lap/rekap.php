<?php
include "../helper/connection.php";

// Initialize variables
$p_b = 0;
$i_b = 0;
$q_b = 0;
$r_b = 0;

$p_S = 0;
$i_S = 0;
$q_S = 0;
$r_S = 0;

$p_R = 0;
$i_R = 0;
$q_R = 0;
$r_R = 0;

$b = 0;

// --- BATAL ---
$batal = "SELECT
            hdr.obi_tgltrans, 
            COUNT(DISTINCT OBI_NOPB) AS PB_B,
            COUNT(DISTINCT substring(OBI_PRDCD, 1, 6) || '0') AS ITEM_B,
            SUM(OBI_QTYORDER) AS QTY_B, 
            SUM(OBI_HARGASATUAN * OBI_QTYORDER) AS RPH_B
        FROM
            tbtr_obi_h hdr
            JOIN tbtr_obi_d dtl ON hdr.obi_notrans = dtl.obi_notrans
                               AND hdr.obi_tgltrans = dtl.obi_tgltrans
        WHERE
            hdr.obi_tgltrans = TO_DATE(:bv_tgl, 'DD-MM-YYYY')
            AND substring(hdr.obi_recid, 1, 1) = 'B'
        GROUP BY 
            hdr.obi_tgltrans";

$stmt = $conn->prepare($batal);
$stmt->bindParam(':bv_tgl', $tgl, PDO::PARAM_STR);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $p_b = $row['pb_b'];
    $i_b = $row['item_b'];
    $q_b = $row['qty_b'];
    $r_b = $row['rph_b'];
}

// --- SEND ---
$send = "SELECT
            hdr.obi_tgltrans,
            COUNT(DISTINCT OBI_NOPB) AS PB_S,
            COUNT(DISTINCT substring(OBI_PRDCD, 1, 6) || '0') AS ITEM_S,
            SUM(OBI_QTYORDER) AS QTY_S, 
            SUM(OBI_HARGASATUAN * OBI_QTYORDER) AS RPH_S
        FROM
            tbtr_obi_h hdr
            JOIN tbtr_obi_d dtl ON hdr.obi_notrans = dtl.obi_notrans
                               AND hdr.obi_tgltrans = dtl.obi_tgltrans
        WHERE
            hdr.obi_tgltrans = TO_DATE(:bv_tgl, 'DD-MM-YYYY')
            AND substring(hdr.obi_recid, 1, 1) <> 'B'
        GROUP BY 
            hdr.obi_tgltrans";

$stmt = $conn->prepare($send);
$stmt->bindParam(':bv_tgl', $tgl, PDO::PARAM_STR);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $p_S = $row['pb_s'];
    $i_S = $row['item_s'];
    $q_S = $row['qty_s'];
    $r_S = $row['rph_s'];
}

// --- REALISASI ---
$real = "SELECT
            hdr.obi_tgltrans,
            COUNT(DISTINCT OBI_NOPB) AS PB_R,
            COUNT(DISTINCT substring(OBI_PRDCD, 1, 6) || '0') AS ITEM_R,
            SUM(OBI_QTYREALISASI) AS QTY_R,
            SUM(OBI_HARGASATUAN * OBI_QTYREALISASI) AS RPH_R
        FROM
            tbtr_obi_h hdr
            JOIN tbtr_obi_d dtl ON hdr.obi_notrans = dtl.obi_notrans
                               AND hdr.obi_tgltrans = dtl.obi_tgltrans
        WHERE
            hdr.obi_tgltrans = TO_DATE(:bv_tgl, 'DD-MM-YYYY')
            AND COALESCE(substring(hdr.obi_recid, 1, 1), '1') >= '6'
            AND substring(hdr.obi_recid, 1, 1) <> 'B'
        GROUP BY 
            hdr.obi_tgltrans";

$stmt = $conn->prepare($real);
$stmt->bindParam(':bv_tgl', $tgl, PDO::PARAM_STR);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $p_R = $row['pb_r'];
    $i_R = $row['item_r'];
    $q_R = $row['qty_r'];
    $r_R = $row['rph_r'];
}

// --- BELUM ---
$blm = "SELECT
            COUNT(*) AS b
        FROM
            tbtr_obi_h hdr
        WHERE
            hdr.obi_tgltrans = TO_DATE(:bv_tgl, 'DD-MM-YYYY')
            AND COALESCE(substring(hdr.obi_recid, 1, 1), '1') < '6'
            OR hdr.obi_recid IS NULL
            AND substring(hdr.obi_recid, 1, 1) <> 'B'";

$stmt = $conn->prepare($blm);
$stmt->bindParam(':bv_tgl', $tgl, PDO::PARAM_STR);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $b = $row['b'];
}
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact" style="width:100%; font-size:12px">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center"> TANGGAL </th>
                            <th colspan="4" class="text-center"> PB BATAL </th>
                            <th colspan="4" class="text-center"> PB UPLOAD </th>
                            <th colspan="4" class="text-center"> PB REALISASI </th>
                        </tr>
                        <tr>
                            <th class="text-center"> PB </th>
                            <th class="text-center"> ITEM </th>
                            <th class="text-center"> QTY </th>
                            <th class="text-center"> RPH </th>
                            <th class="text-center"> PB </th>
                            <th class="text-center"> ITEM </th>
                            <th class="text-center"> QTY </th>
                            <th class="text-center"> RPH </th>
                            <th class="text-center"> PB </th>
                            <th class="text-center"> ITEM </th>
                            <th class="text-center"> QTY </th>
                            <th class="text-center"> RPH </th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td> <?= $tgl ?> </td>

                            <td> <?= $p_b ?> </td>
                            <td> <?= $i_b ?> </td>
                            <td> <?= $q_b ?> </td>
                            <td> <?= $r_b ?> </td>

                            <td> <?= $p_S ?> </td>
                            <td> <?= $i_S ?> </td>
                            <td> <?= $q_S ?> </td>
                            <td> <?= $r_S ?> </td>

                            <td> <?= $p_R ?> </td>
                            <td> <?= $i_R ?> </td>
                            <td> <?= $q_R ?> </td>
                            <td> <?= $r_R ?> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <?= $b > 0 ? '<div class="alert alert-warning alert-dismissible show" role="alert">
                                <strong>' . $b . ' PB Belum Struk!</strong> 
                                    Masih ada PB OBI tanggal ' . $tgl . ' yang belum DSPB.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>' : ''; ?>
        </div>
        <div class="col-md-6 text-right">
            <a href="../dashboard/" class="btn btn-info btn-lg">
                <small class="glyphicon glyphicon-home"></small> Dashboard OBI
            </a>
        </div>
    </div>



</div>