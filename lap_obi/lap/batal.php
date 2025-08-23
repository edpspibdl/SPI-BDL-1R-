<?php
$query = " SELECT
    hdr.obi_tglpb AS obi_tglpb,
    COALESCE(loc_toko.RAK_TOKO, '-') AS RAK_TOKO,
    COALESCE(loc_dpd.RAK_DPD, '-') AS RAK_DPD,
    obi_prdcd,
    prd_deskripsipendek,
    CASE WHEN prd_unit = 'KG' THEN 1 ELSE prd_frac END AS prd_frac,
    COALESCE(STH_SALDOAKHIR, 0) AS LPP_HARIAN,
    COALESCE(ST_SALDOAKHIR, 0) AS LPP_SAATINI,
    COALESCE(PLANO_DPD, 0) + COALESCE(PLANO_TOKO, 0) AS plano,
    obi_qtyorder,
    COALESCE(pobi_qty, obi_qtyrealisasi) AS obi_qtyrealisasi,
    obi_qtyorder - COALESCE(pobi_qty, obi_qtyrealisasi) AS qty_selisih,
    obi_hargasatuan,
    obi_ppn,
    COALESCE(obi_hargaweb, ROUND((obi_hargasatuan + obi_ppn) * (CASE WHEN prd_unit = 'KG' THEN 1 ELSE prd_frac END), 0)) AS obi_hargaweb,
    obi_ekspedisi,
    hdr.obi_nopb AS obi_nopb,
    obi_kdmember AS MEMBER,
    hdr.obi_notrans AS obi_notrans,
    obi_diskon,
    obi_nostruk,
    obi_tglstruk,
    obi_kdstation,
    obi_kdmember,
    COALESCE(hdr.obi_nopo, '-') AS nopo,
    hdr.obi_realcashback AS realcashback,
    dtl.obi_kd_promosi AS kdpromo,
    dtl.obi_cashback AS nominal_csb,
    (COALESCE(prd_ppn, 0) / 100) AS prd_ppn,
    COALESCE(prd_flagbkp1, '-') || COALESCE(prd_flagbkp2, '-') AS plubkp,
    COALESCE(pobi_nocontainer, '-') AS nocon,
    dtl.obi_scan_dt AS obi_scan_dt,
    COALESCE(admin_fee, 0) AS admin_fee,
    COALESCE(pot_ongkir, 0) AS pot_ongkir,
    COALESCE(obi_tipebayar, 'x') AS tipe_bayar,
    COALESCE(amm_namapenerima, '-') AS amm_namapenerima,
    obi_cashierid,
    COALESCE(payment_klikigr.total, 0) AS ttl_bayar,
    COALESCE(hdr.obi_pointbasic, 0) AS point_basic,
    COALESCE(hdr.obi_pointbonus, 0) AS point_bonus,
    ROUND(sat_gram / NULLIF(sat_pcs, 0)) AS konversi
FROM
    tbtr_obi_h hdr
JOIN
    tbtr_obi_d dtl ON hdr.obi_notrans = dtl.obi_notrans AND hdr.obi_tgltrans = dtl.obi_tgltrans
LEFT JOIN
    (SELECT
         lks_prdcd AS PRDCD_TKO,
         lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut AS RAK_TOKO,
         SUM(COALESCE(LKS_Qty, 0)) AS PLANO_TOKO
     FROM
         tbMaster_lokasi
     WHERE
         NOT REGEXP_LIKE(LKS_KodeRak, '^D|^G')
         AND LKS_JENISRAK NOT LIKE 'S%'
         AND LKS_JENISRAK <> 'H'
         AND REGEXP_LIKE(LKS_TIPERAK, '^B|^I')
     GROUP BY
         lks_prdcd,
         lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut) loc_toko ON substr(obi_prdcd, 1, 6) || '0' = loc_toko.PRDCD_TKO
LEFT JOIN
    (SELECT
         lks_prdcd AS PRDCD_DPD,
         lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut AS RAK_DPD,
         SUM(COALESCE(LKS_Qty, 0)) AS PLANO_DPD
     FROM
         tbMaster_lokasi l
     JOIN
         tbMaster_GroupRak g ON l.LKS_KodeRak = g.GRR_KodeRak AND l.LKS_KodeSubrak = g.GRR_Subrak
     WHERE
         REGEXP_LIKE(LKS_KodeRak, '^D|^G')
         AND LKS_JENISRAK NOT LIKE 'S%'
     GROUP BY
         lks_prdcd,
         lks_koderak || '.' || lks_kodesubrak || '.' || lks_tiperak || '.' || lks_shelvingrak || '.' || lks_nourut) loc_dpd ON substr(obi_prdcd, 1, 6) || '0' = loc_dpd.PRDCD_DPD
LEFT JOIN
    tbmaster_stock ON substr(dtl.obi_prdcd, 1, 6) || '0' = tbmaster_stock.ST_PRDCD AND tbmaster_stock.ST_LOKASI = '01'
LEFT JOIN
    tbtr_stockharian ON substr(dtl.obi_prdcd, 1, 6) || '0' = tbtr_stockharian.STH_PRDCD AND tbtr_stockharian.STH_LOKASI = '01' AND DATE_TRUNC('day', dtl.OBI_TGLTRANS) = DATE_TRUNC('day', tbtr_stockharian.STH_PERIODE)
JOIN
    tbtr_alamat_mm ON hdr.obi_notrans = tbtr_alamat_mm.amm_notrans AND hdr.obi_nopb = tbtr_alamat_mm.amm_nopb AND hdr.obi_kdmember = tbtr_alamat_mm.amm_kodemember
JOIN
    tbmaster_prodmast ON hdr.obi_kodeigr = tbmaster_prodmast.prd_kodeigr AND dtl.obi_prdcd = tbmaster_prodmast.prd_prdcd
LEFT JOIN
    tbtr_packing_obi ON dtl.obi_notrans = tbtr_packing_obi.pobi_notransaksi AND dtl.obi_tgltrans = tbtr_packing_obi.pobi_tgltransaksi AND dtl.obi_prdcd = tbtr_packing_obi.pobi_prdcd
LEFT JOIN
    payment_klikigr ON hdr.obi_notrans = payment_klikigr.no_trans AND hdr.obi_tgltrans = payment_klikigr.tgl_trans AND hdr.obi_kdmember = payment_klikigr.kode_member AND hdr.obi_nopb = payment_klikigr.no_pb
LEFT JOIN
    spibdl1r.konversi_item_klikigr knv ON dtl.obi_prdcd = knv.pluigr
WHERE
    hdr.obi_tglpb = TO_DATE(:tgl, 'DD-MM-YYYY')
    AND substr(hdr.OBI_RECID, 1, 1) = 'B'
    AND COALESCE(obi_qtyrealisasi, 0) = 0
    AND dtl.obi_recid IS NULL
ORDER BY
    COALESCE(pobi_nocontainer, '-') ASC, dtl.obi_scan_dt DESC, dtl.obi_prdcd ASC  ";

include "../helper/connection.php";
$stmt = $conn->prepare($query);
$stmt->bindParam(':tgl', $tgl);
$stmt->execute();

// Fetch all rows into an associative array
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-responsive">
    <table id="GridView"
        class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden"
        style="width:100%; font-size:12px">
        <thead>
            <tr>
              
                <th> TGLPB </th>
                <th> RAK TOKO </th>
                <th> RAK DPD </th>
                <th> PRDCD </th>
                <th> PRD DESKRIPSIPENDEK </th>
                <th> PRD FRAC </th>
                <th> LPP HARIAN </th>
                <th> LPP SAATINI </th>
                <th> PLANO </th>
                <th> QTY ORDER </th>
                <th> QTY REALISASI </th>
                <th> QTY SELISIH </th>
                <th> HARGASATUAN </th>
                <th> PPN </th>
                <th> HARGAWEB </th>
                <th> EKSPEDISI </th>
                <th> NOPB </th>
                <th> MEMBER </th>
                <th> NOTRANS </th>
                <th> DISKON </th>
                <th> NOSTRUK </th>
                <th> TGLSTRUK </th>
                <th> KDSTATION </th>
                <th> KDMEMBER </th>
                <th> NOPO </th>
                <th> REALCASHBACK </th>
                <th> KDPROMO </th>
                <th> NOMINAL CSB </th>
                <th> PRD PPN </th>
                <th> PLUBKP </th>
                <th> NOCON </th>
                <th> SCAN DT </th>
                <th> ADMIN FEE </th>
                <th> POT ONGKIR </th>
                <th> TIPE BAYAR </th>
                <th> AMM NAMAPENERIMA </th>
                <th> CASHIERID </th>
                <th> TTL BAYAR </th>
                <th> POINT BASIC </th>
                <th> POINT BONUS </th>
                <th> KONVERSI </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) : ?>
            <tr>
            <td> <?= $row["obi_tglpb"] ?> </td>
<td> <?= $row["rak_toko"] ?> </td>
<td> <?= $row["rak_dpd"] ?> </td>
<td> <?= $row["obi_prdcd"] ?> </td>
<td> <?= $row["prd_deskripsipendek"] ?> </td>
<td> <?= $row["prd_frac"] ?> </td>
<td> <?= $row["lpp_harian"] ?> </td>
<td> <?= $row["lpp_saatini"] ?> </td>
<td> <?= $row["plano"] ?> </td>
<td> <?= $row["obi_qtyorder"] ?> </td>
<td> <?= $row["obi_qtyrealisasi"] ?> </td>
<td> <?= $row["qty_selisih"] ?> </td>
<td> <?= $row["obi_hargasatuan"] ?> </td>
<td> <?= $row["obi_ppn"] ?> </td>
<td> <?= $row["obi_hargaweb"] ?> </td>
<td> <?= $row["obi_ekspedisi"] ?> </td>
<td> <?= $row["obi_nopb"] ?> </td>
<td> <?= $row["member"] ?> </td>
<td> <?= $row["obi_notrans"] ?> </td>
<td> <?= $row["obi_diskon"] ?> </td>
<td> <?= $row["obi_nostruk"] ?> </td>
<td> <?= $row["obi_tglstruk"] ?> </td>
<td> <?= $row["obi_kdstation"] ?> </td>
<td> <?= $row["obi_kdmember"] ?> </td>
<td> <?= $row["nopo"] ?> </td>
<td> <?= $row["realcashback"] ?> </td>
<td> <?= $row["kdpromo"] ?> </td>
<td> <?= $row["nominal_csb"] ?> </td>
<td> <?= $row["prd_ppn"] ?> </td>
<td> <?= $row["plubkp"] ?> </td>
<td> <?= $row["nocon"] ?> </td>
<td> <?= $row["obi_scan_dt"] ?> </td>
<td> <?= $row["admin_fee"] ?> </td>
<td> <?= $row["pot_ongkir"] ?> </td>
<td> <?= $row["tipe_bayar"] ?> </td>
<td> <?= $row["amm_namapenerima"] ?> </td>
<td> <?= $row["obi_cashierid"] ?> </td>
<td> <?= $row["ttl_bayar"] ?> </td>
<td> <?= $row["point_basic"] ?> </td>
<td> <?= $row["point_bonus"] ?> </td>
<td> <?= $row["konversi"] ?> </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>