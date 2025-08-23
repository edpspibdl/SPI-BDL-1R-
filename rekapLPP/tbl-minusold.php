<?php
$query = " WITH temp_lpp AS (
                SELECT
                    prd_kodedivisi                                                                             div,
                    prd_kodedepartement                                                                        dept,
                    prd_kodekategoribarang                                                                     katb,
                    prd_prdcd                                                                                  plu,
                    prd_deskripsipendek                                                                        deskripsi,
                    prd_unit                                                                                   unit,
                    prd_frac                                                                                   frac,
                    st_avgcost                                                                                 acost_pcs,
                    prd_kodetag                                                                                tag,
                    flag,
                    SUM(lks_qty)                                                                               stok_plano,
                    st_saldoakhir                                                                              stok_lpp,
                    SUM(lks_qty * st_avgcost)                                                                  rph_plano,
                    ( st_saldoakhir * st_avgcost )                                                             rph_lpp,
                    SUM(nvl(lks_qty, 0) - nvl(st_saldoakhir, 0))                                               sel_qty,
                    SUM(nvl(lks_qty, 0) * nvl(st_avgcost, 0)) - ( nvl(st_saldoakhir, 0) * nvl(st_avgcost, 0) ) sel_rph
                FROM
                    tbmaster_prodmast,
                    tbmaster_stock,
                    tbmaster_lokasi,
                    cek_flag
                WHERE
                        prd_prdcd = st_prdcd
                    AND prd_prdcd = lks_prdcd
                    AND prd_prdcd = plu_flag
                    AND st_lokasi = '01'
                    AND prd_unit <> 'KG'
                GROUP BY
                    prd_kodedivisi,
                    prd_kodedepartement,
                    prd_kodekategoribarang,
                    prd_prdcd,
                    prd_deskripsipendek,
                    prd_unit,
                    prd_frac,
                    flag,
                    prd_kodetag,
                    st_avgcost,
                    st_saldoakhir,
                    (
                        st_saldoakhir * st_avgcost
                    )
                ORDER BY
                    SUM(nvl(lks_qty, 0) * nvl(st_avgcost, 0)) - ( nvl(st_saldoakhir, 0) * nvl(st_avgcost, 0) ) ASC
            )
            SELECT
                a.*,
                CASE
                    WHEN plu_tko IS NOT NULL
                        AND plu_gdg IS NULL THEN
                        'TOKO'
                    WHEN plu_tko IS NULL
                        AND plu_gdg IS NOT NULL THEN
                        'GUDANG'
                    ELSE
                        'TOKO+GUDANG'
                END AS lokasi
            FROM
                temp_lpp a
                LEFT JOIN (
                    SELECT
                        lks_prdcd     AS plu_gdg,
                        lks_koderak
                        || '.'
                        || lks_kodesubrak
                        || '.'
                        || lks_tiperak
                        || '.'
                        || lks_shelvingrak
                        || '.'
                        || lks_nourut AS disp_gdg
                    FROM
                        tbmaster_lokasi,
                        tbmaster_grouprak
                    WHERE
                            lks_koderak = grr_koderak
                        AND lks_kodesubrak = grr_subrak
                        AND REGEXP_LIKE ( lks_koderak,
                                        '^D|^G' )
                        AND lks_jenisrak NOT LIKE 'S%'
                ) ON plu = plu_gdg
                LEFT JOIN (
                    SELECT
                        lks_prdcd     AS plu_tko,
                        lks_koderak
                        || '.'
                        || lks_kodesubrak
                        || '.'
                        || lks_tiperak
                        || '.'
                        || lks_shelvingrak
                        || '.'
                        || lks_nourut AS disp_tko
                    FROM
                        tbmaster_lokasi
                    WHERE
                        NOT REGEXP_LIKE ( lks_koderak,
                                        '^D|^G|^H' )
                            --AND lks_jenisrak NOT LIKE 'S%'
                            AND lks_tiperak = 'B'
                ) ON plu = plu_tko
            WHERE
                ROWNUM <= 5 ";

include "../include/koneksi.php";
$stid = oci_parse ($conn, $query);
oci_execute($stid);
?>

<div class="table-responsive">
    <table class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact"
        style="width:100%; font-size:12px; margin-bottom: 0;">
        <thead>
            <tr>
                <th> No </th>
                <th> Div </th>
                <th> Dept </th>
                <th> Katb </th>
                <th> Plu </th>
                <th> Deskripsi </th>
                <th> Unit </th>
                <th> Frac </th>
                <th> Acost </th>
                <th> Sel. Qty </th>
                <th> Sel. Rph </th>
                <th> Flag </th>
                <th> Alamat </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {  
            $no++;  ?>
            <tr>
                <td class="text-right"> <?= $no ?> </td>
                <td class="text-left"> <?= $row["DIV"] ?> </td>
                <td class="text-left"> <?= $row["DEPT"] ?> </td>
                <td class="text-left"> <?= $row["KATB"] ?> </td>
                <td class="text-right"> <?= $row["PLU"] ?> </td>
                <td class="text-left"> <?= $row["DESKRIPSI"] ?> </td>
                <td class="text-left"> <?= $row["UNIT"] ?> </td>
                <td class="text-right"> <?= $row["FRAC"] ?> </td>
                <td class="text-right"> <?= number_format($row["ACOST_PCS"], 0, '.', ',') ?> </td>
                <td class="text-right"> <?= number_format($row["SEL_QTY"], 0, '.', ',') ?> </td>
                <td class="text-right"> <?= number_format($row["SEL_RPH"], 0, '.', ',') ?> </td>
                <td class="text-left"> <?= $row["FLAG"] ?> </td>
                <td class="text-left"> <?= $row["LOKASI"] ?> </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>