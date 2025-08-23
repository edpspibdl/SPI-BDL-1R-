<?php
$query = "SELECT
                *
            FROM
                (
                    SELECT
                        *
                    FROM
                        (select 
prd_kodedivisi,
prd_kodedepartement,
prd_kodekategoribarang,
display_toko,
display_omi,
case when display_toko is null and display_omi is not null then 'GUDANG'
     when display_toko is not null and display_omi is null then 'TOKO'
     when display_toko is not null and display_omi is not null then 'TOKO+GUDANG'
end ket_alamat,
st_prdcd,
prd_deskripsipanjang,
prd_unit,
prd_frac,
prd_kodetag,
flag_main,
st_avgcost,
st_saldoakhir, 
case when prd_unit = 'KG' then (st_saldoakhir*st_avgcost)/prd_frac
     else st_saldoakhir*st_avgcost
end rph_lpp,
qty_plano,
COALESCE(omi_recid4,0) omi_recid4,
st_intransit,
(COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) qty_total_plano,
case when prd_unit = 'KG' then ((COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0))*st_avgcost)/prd_frac
     else (qty_plano+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0))*st_avgcost
end rph_total_plano,
(COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) - st_saldoakhir sel_qty,
((COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) - st_saldoakhir) * st_avgcost sel_rph,
case when (COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) > st_saldoakhir then 'PLANO>LPP'
     when (COALESCE(qty_plano,0)+COALESCE(omi_recid4,0)+COALESCE(qty_obi_pick,0)) = st_saldoakhir then 'PLANO=LPP'
     else 'PLANO<LPP'
end Kategori,
CASE WHEN ( display_toko IS NULL AND display_omi IS NULL ) THEN 'TDK ADA PLANO'
     ELSE 'ADA PLANO'
END keterangan,
COALESCE(qty_obi_pick,0) qty_obi_pick,
rph_obi_pick
from tbmaster_stock
join tbmaster_prodmast on prd_prdcd = st_prdcd
left join (SELECT
                                plu plu_obi,
                                SUM(qty_final) qty_obi_pick,
                                SUM(qty_final * st_avgcost) rph_obi_pick
                            FROM
                                (
                                    SELECT
                                        status,
                                        obi_tipebayar,
                                        obi_recid,
                                        obi_tglpb,
                                        obi_notrans,
                                        obi_nopb,
                                        obi_tglstruk,
                                        plu,
                                        qty_pick,
                                        qty_pack,
                                        qty_final,
                                        st_avgcost
                                    FROM
                                        (
                                            SELECT
                                                CASE
                                                    WHEN obi_recid = '1' THEN
                                                        'SIAP PICKING'
                                                    WHEN obi_recid = '2' THEN
                                                        'SIAP PACKING'
                                                    WHEN obi_recid = '3' THEN
                                                        'SIAP DRAFT STRUK'
                                                    WHEN obi_recid = '4' THEN
                                                        'KONFIRM PEMBAYARAN'
                                                    WHEN obi_recid = '5' THEN
                                                        'SIAP STRUK'
                                                    WHEN obi_recid = '6' THEN
                                                        'SELESAI STRUK'
                                                    WHEN obi_recid = '7' THEN
                                                        'SET ONGKIR'
                                                END status,
                                                obi_tipebayar,
                                                obi_recid,
                                                obi_tglpb obi_tglpb,
                                                obi_notrans,
                                                obi_nopb,
                                                substr(plu, 1, 6)
                                                || '0' plu,
                                                qty_pick,
                                                qty_pack,
                                                obi_tglstruk,
                                                CASE
                                                    WHEN qty_pick <> qty_pack
                                                         AND qty_pack IS NOT NULL THEN
                                                        qty_pack
                                                    ELSE
                                                        qty_pick
                                                END qty_final
                                            FROM
                                                tbtr_obi_h left
                                                JOIN (
                                                    SELECT
                                                        *
                                                    FROM
                                                        (
                                                            SELECT DISTINCT
                                                                tgl,
                                                                no_pb,
                                                                plu,
                                                                qty_pick,
                                                                qty_pack
                                                            FROM
                                                                (
                                                                    SELECT
                                                                        obi_tgltrans tgl,
                                                                        obi_notrans   no_pb,
                                                                        obi_prdcd     plu
                                                                    FROM
                                                                        tbtr_obi_d
                                                                        where COALESCE(OBI_QTYINTRANSIT,'0') = '0'
                                                                    UNION ALL
                                                                    SELECT
                                                                        pobi_tgltransaksi,
                                                                        pobi_notransaksi,
                                                                        pobi_prdcd
                                                                    FROM
                                                                        tbtr_packing_obi
                                                                ) obid1 left
                                                                JOIN (
                                                                    SELECT
                                                                        obi_tgltrans tgl_obid,
                                                                        obi_notrans,
                                                                        obi_prdcd,
                                                                        obi_qtyrealisasi qty_pick
                                                                    FROM
                                                                        tbtr_obi_d
                                                                ) obid2 ON tgl_obid = tgl
                                                                     AND obi_notrans = no_pb
                                                                     AND obi_prdcd = plu
                                                                LEFT JOIN (
                                                                    SELECT
                                                                        pobi_tgltransaksi pobi_tgltransaksi,
                                                                        pobi_notransaksi,
                                                                        pobi_prdcd,
                                                                        pobi_qty qty_pack
                                                                    FROM
                                                                        tbtr_packing_obi
                                                                ) obid3 ON pobi_tgltransaksi = tgl
                                                                     AND pobi_notransaksi = no_pb
                                                                     AND pobi_prdcd = plu
                                                        ) obid4
                                                ) obid5 ON tgl = obi_tglpb
                                                     AND no_pb = obi_notrans
                                        ) obid6 left
                                        JOIN (
                                            SELECT
                                                st_prdcd,
                                                st_avgcost
                                            FROM
                                                tbmaster_stock
                                            WHERE
                                                st_lokasi = '01'
                                        ) obid7 ON st_prdcd = plu
                                    WHERE
                                        obi_recid IN (
                                            '1',
                                            '2',
                                            '3',
                                            '7'
                                        )
                                ) obid8
                            GROUP BY
                                plu) introbi on plu_obi = st_prdcd
left join (select lks_prdcd, sum(lks_qty) qty_plano from tbmaster_lokasi group by lks_prdcd) plano on lks_prdcd = st_prdcd
left join (SELECT
    lks_prdcd     AS plu_displayomi,
    lks_koderak
    || '.'
    || lks_kodesubrak
    || '.'
    || lks_tiperak
    || '.'
    || lks_shelvingrak
    || '.'
    || lks_nourut AS display_omi
FROM
    tbmaster_lokasi
WHERE
    ( lks_tiperak = 'B'
      OR lks_tiperak LIKE 'I%' )
    AND lks_prdcd IS NOT NULL
    AND lks_noid IS NOT NULL
    AND lks_koderak NOT LIKE '%DNEW%') planodpd on plu_displayomi = st_prdcd
left join (SELECT
                                lks_prdcd AS plu_displaytoko,
                                lks_koderak
                                || '.'
                                || lks_kodesubrak
                                || '.'
                                || lks_tiperak
                                || '.'
                                || lks_shelvingrak
                                || '.'
                                || lks_nourut AS display_toko
                            FROM
                                tbmaster_lokasi
                            WHERE
                                ( lks_tiperak = 'B'
                                  OR lks_tiperak LIKE 'I%' )
                                AND lks_prdcd IS NOT NULL
                                AND lks_noid IS NULL
                                AND lks_koderak <> 'DNEW'
                                AND lks_koderak <> 'HDH'
                                AND lks_koderak <> 'DVOC'
                                AND lks_koderak NOT LIKE '%TAG%')planotoko on plu_displaytoko = st_prdcd
left join (SELECT
                                substr(pbo_pluigr, 0, 6)
                                || '0' plupb,
                                SUM(pbo_qtyrealisasi) omi_recid4
                            FROM
                                tbmaster_pbomi
                            WHERE
                                pbo_nokoli IS NOT NULL
                                and PBO_TGLPB BETWEEN CURRENT_DATE - INTERVAL '1 day' AND CURRENT_DATE
                                AND pbo_nokoli NOT IN (
                                    SELECT
                                        rpb_nokoli
                                    FROM
                                        tbtr_realpb
                                )
                            GROUP BY
                                substr(pbo_pluigr, 0, 6)
                                || '0') recid4 on plupb = st_prdcd
left join (SELECT 
    PRD_PRDCD AS PLU_flag,
    CASE 
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+IDM+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+IDM+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+IDM+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IDM+KLIK+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+IDM'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IGR+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'IDM+OMI'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IDM+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'OMI+KLIK'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IGR ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'IDM ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'N' 
        THEN 'KLIK ONLY'
        
        WHEN COALESCE(PRD_FLAGIGR, 'N') = 'N' 
             AND COALESCE(PRD_FLAGIDM, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOBI, 'N') = 'N' 
             AND COALESCE(PRD_FLAGOMI, 'N') = 'Y' 
        THEN 'OMI ONLY'
        
        ELSE 'BLANK'
    END AS FLAG_MAIN
FROM (
    SELECT 
        PRD_PRDCD, 
        PRD_FLAGIGR, 
        PRD_FLAGIDM, 
        PRD_FLAGOBI, 
        PRD_FLAGOMI
    FROM 
        TBMASTER_PRODMAST
    WHERE 
        PRD_PRDCD LIKE '%0'
) prodmast) flag on plu_flag = st_prdcd
where st_lokasi = '01'
and prd_unit <> 'KG'
AND coalesce(st_saldoakhir, 0) <> '0'
AND coalesce(prd_kodetag,' ') NOT IN ( 'H', 'A', 'N', 'O', 'X', 'U' )
AND prd_kodedepartement NOT IN ( '31', '32', '42' )

) alias1
                    ORDER BY
                        sel_rph asc
                ) alias2
            LIMIT 5 ";

include "../helper/connection.php";
try {
    $query = $conn->prepare($query);
    $query->execute();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Product Details</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered table-nonfluid compact" style="width:100%; font-size:12px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plu</th>
                        <th>Deskripsi</th>
                        <th>Unit</th>
                        <th>Frac</th>
                        <th>Acost</th>
                        <th>Sel. Qty</th>
                        <th>Sel. Rph</th>
                        <th>Flag</th>
                        <th>Alamat</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $no++; ?>
                        <tr>
                            <td class="text-right"> <?= $no ?> </td>
                            <td class="text-right"> <?= $row["st_prdcd"] ?> </td>
                            <td class="text-left"> <?= $row["prd_deskripsipanjang"] ?> </td>
                            <td class="text-left"> <?= $row["prd_unit"] ?> </td>
                            <td class="text-right"> <?= $row["prd_frac"] ?> </td>
                            <td class="text-right"> <?= number_format($row["st_avgcost"], 0, '.', ',') ?> </td>
                            <td class="text-right"> <?= number_format($row["sel_qty"], 0, '.', ',') ?> </td>
                            <td class="text-right"> <?= number_format($row["sel_rph"], 0, '.', ',') ?> </td>
                            <td class="text-left"> <?= $row["flag_main"] ?> </td>
                            <td class="text-left"> <?= $row["ket_alamat"] ?> </td>
                            <td class="text-center">
                                <span class="glyphicon glyphicon-zoom-in" style="cursor: pointer;" data-toggle="modal" data-target="#mdm<?= $no ?>">
                                </span>
                                <div id="mdm<?= $no ?>" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Detail</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6" style="padding-right: 0;">
                                                        <table style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>PLU</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["st_prdcd"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Satuan</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["prd_unit"] ?> / <?= $row["prd_frac"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Display Toko</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["display_toko"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tag IGR</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["prd_kodetag"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Lpp Qty</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["st_saldoakhir"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total LPP</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["st_saldoakhir"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="2">&nbsp;</th>
                                                                    <th class="text-right">Selisih Qty&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Lpp Rupiah</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= number_format($row["rph_lpp"], 0, '.', ',') ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6" style="padding-left: 0;">
                                                        <table style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Deskripsi</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["prd_deskripsipanjang"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Acost Pcs</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["st_avgcost"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Display Gudang</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["display_omi"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Flag</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["flag_main"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Plano Qty</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["qty_plano"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Idm Recid4</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["omi_recid4"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Qty Retur</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["omi_recid4"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Qty Obi Intr</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["qty_obi_pick"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Plano</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= $row["qty_total_plano"] ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>: <?= $row["sel_qty"] ?></th>
                                                                    <th colspan="2">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">
                                                                        <hr style="margin: 5px 0;">
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Plano Rupiah</th>
                                                                    <th>&nbsp;:&nbsp;</th>
                                                                    <th><?= number_format($row["rph_total_plano"], 0, '.', ',') ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">&nbsp;</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>: <?= number_format($row["sel_rph"], 0, '.', ',') ?></th>
                                                                    <th colspan="2">&nbsp;</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>