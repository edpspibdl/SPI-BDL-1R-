<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Ensure this is the correct path

// Get the start and end date from the form input
$tanggalMulai = isset($_GET['tanggalMulai']) ? $_GET['tanggalMulai'] : '';
$tanggalSelesai = isset($_GET['tanggalSelesai']) ? $_GET['tanggalSelesai'] : '';

// Convert the dates to the format that SQL expects (YYYYMMDD)
$tanggalMulaiFormatted = date('Ymd', strtotime($tanggalMulai));
$tanggalSelesaiFormatted = date('Ymd', strtotime($tanggalSelesai));

// Prepare the SQL query with placeholders for the date range
$query = "SELECT
CAST(dtl_prdcd_ctn AS NUMERIC) AS dtl_prdcd_ctn,
dtl_nama_barang,
dtl_k_div,
dtl_k_dept,
dtl_k_katb,
pkm.pkm_pkmt,
COUNT(DISTINCT dtl_tanggal) AS kunjungan,
COUNT(DISTINCT dtl_cusno) AS jml_member,
COUNT(DISTINCT dtl_struk) AS struk,
SUM(dtl_qty_pcs) AS qty_in_pcs,
SUM(dtl_gross) AS dtl_gross,
SUM(dtl_netto) AS dtl_netto,
SUM(dtl_margin) AS dtl_margin,
round(SUM(dtl_margin) / SUM(dtl_netto) * 100, 2) AS dtl_margin_persen
FROM (( SELECT
dtl_rtype,
dtl_tanggal,
dtl_jam,
dtl_struk,
dtl_stat,
dtl_kasir,
dtl_no_struk,
dtl_seqno,
dtl_prdcd_ctn,
dtl_prdcd,
dtl_nama_barang,
dtl_unit,
dtl_frac,
dtl_tag,
dtl_bkp,
dtl_qty_pcs,
dtl_qty,
dtl_harga_jual,
dtl_diskon,
CASE
    WHEN dtl_rtype = 'S' THEN
        dtl_gross
    ELSE
        dtl_gross * - 1
END AS dtl_gross,
CASE
    WHEN dtl_rtype = 'R' THEN
        ( dtl_netto * - 1)
    ELSE
        dtl_netto
END AS dtl_netto,
CASE
    WHEN dtl_rtype = 'R' THEN
        ( dtl_hpp * - 1 )
    ELSE
        dtl_hpp
END AS dtl_hpp,
CASE
    WHEN dtl_rtype = 'S' THEN
        dtl_netto - dtl_hpp
    ELSE
        ( dtl_netto - dtl_hpp ) * - 1
END AS dtl_margin,
dtl_k_div, dtl_nama_div,
dtl_k_dept,
dtl_nama_dept,
dtl_k_katb,
dtl_nama_katb,
dtl_cusno,
dtl_namamember,
dtl_memberkhusus,
dtl_outlet,
dtl_suboutlet,
dtl_kategori,
dtl_sub_kategori,
dtl_tipemember,
dtl_group_member,
hgb_kodesupplier   AS dtl_kodesupplier,
sup_namasupplier   AS dtl_namasupplier
FROM ( SELECT
date_trunc('day', trjd_transactiondate) AS dtl_tanggal,
TO_CHAR(trjd_transactiondate, 'hh24:mi:ss') AS dtl_jam,
TO_CHAR(trjd_transactiondate, 'yyyymmdd')
|| trjd_create_by
|| trjd_transactionno
|| trjd_transactiontype AS dtl_struk,
trjd_cashierstation    AS dtl_stat,
trjd_create_by         AS dtl_kasir,
trjd_transactionno     AS dtl_no_struk,
substr(trjd_prdcd, 1, 6)
|| '0' AS dtl_prdcd_ctn,
trjd_prdcd             AS dtl_prdcd,
prd_deskripsipanjang   AS dtl_nama_barang,
prd_unit               AS dtl_unit,
prd_frac               AS dtl_frac,
coalesce(prd_kodetag, ' ') AS dtl_tag,
trjd_flagtax1          AS dtl_bkp,
trjd_transactiontype   AS dtl_rtype,
TRIM(trjd_divisioncode) AS dtl_k_div,
div_namadivisi         AS dtl_nama_div,
substr(trjd_division, 1, 2) AS dtl_k_dept,
dep_namadepartement    AS dtl_nama_dept,
substr(trjd_division, 3, 2) AS dtl_k_katb,
kat_namakategori       AS dtl_nama_katb,
trjd_cus_kodemember    AS dtl_cusno,
cus_namamember         AS dtl_namamember,
cus_flagmemberkhusus   AS dtl_memberkhusus,
cus_kodeoutlet         AS dtl_outlet,
upper(cus_kodesuboutlet) AS dtl_suboutlet,
crm_kategori           AS dtl_kategori,
crm_subkategori        AS dtl_sub_kategori,
trjd_quantity          AS dtl_qty,
trjd_unitprice         AS dtl_harga_jual,
trjd_discount          AS dtl_diskon,
trjd_seqno             AS dtl_seqno,
CASE
    WHEN cus_jenismember = 'T'      THEN
        'TMI'
    WHEN cus_flagmemberkhusus = 'Y' THEN
        'KHUSUS'
    WHEN trjd_create_by IN (
        'IDM',
        'ID1',
        'ID2'
    ) THEN
        'IDM'
    WHEN trjd_create_by IN (
        'OMI',
        'BKL'
    ) THEN
        'OMI'
    ELSE
        'REGULER'
END AS dtl_tipemember,
CASE
    WHEN cus_flagmemberkhusus = 'Y' THEN
        'GROUP_1_KHUSUS'
    WHEN trjd_create_by = 'IDM'     THEN
        'GROUP_2_IDM'
    WHEN trjd_create_by IN (
        'OMI',
        'BKL'
    ) THEN
        'GROUP_3_OMI'
    WHEN cus_flagmemberkhusus IS NULL
         AND cus_kodeoutlet = '6' THEN
        'GROUP_4_END_USER'
    ELSE
        'GROUP_5_OTHERS'
END AS dtl_group_member,
CASE
    WHEN prd_unit = 'KG'
         AND prd_frac = 1000 THEN
        trjd_quantity
    ELSE
        trjd_quantity * prd_frac
END AS dtl_qty_pcs,
CASE
    WHEN trjd_flagtax1 = 'Y'
         AND trjd_create_by IN (
        'IDM',
        'OMI',
        'BKL'
    ) THEN
        trjd_nominalamt * 11.1 / 10
    ELSE
        trjd_nominalamt
END AS dtl_gross,
CASE
    WHEN trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' THEN
        case
    WHEN 'Y' = 'Y' THEN
        trjd_nominalamt
END else case when coalesce(tko_kodesbu, 'z') in ('O', 'I') then case when tko_tipeomi in ('HE', 'HG') 
then trjd_nominalamt - ( case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') 
and coalesce(prd_kodetag, 'zz') <> 'Q' then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) else 0 end ) 
else trjd_nominalamt end else trjd_nominalamt - ( case when substr(trjd_create_by, 1, 2) = 'EX' then 0 
else case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') and coalesce(prd_kodetag, 'zz') <> 'Q' 
then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) else 0 end end ) end end as dtl_netto, 
case when trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' then case when 'Y' = 'Y' 
then trjd_nominalamt - ( case when prd_markupstandard is null then (5 * trjd_nominalamt) / 100 
else (prd_markupstandard * trjd_nominalamt) / 100 end ) 
end else (trjd_quantity / case when prd_unit = 'KG' then 1000 else 1 end) * trjd_baseprice 
end as dtl_hpp from tbtr_jualdetail left join tbmaster_prodmast on trjd_prdcd = prd_prdcd 
left join tbmaster_tokoigr on trjd_cus_kodemember = tko_kodecustomer 
left join tbmaster_customer on trjd_cus_kodemember = cus_kodemember 
left join tbmaster_customercrm on trjd_cus_kodemember = crm_kodemember 
left join tbmaster_divisi on trjd_division = div_kodedivisi 
left join tbmaster_departement on substr(trjd_division, 1, 2) = dep_kodedepartement 
left join tbmaster_kategori on trjd_division = kat_kodedepartement || kat_kodekategori)sls 
left join (select m.hgb_prdcd hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier from tbmaster_hargabeli m 
left join tbmaster_supplier s on m.hgb_kodesupplier = s.sup_kodesupplier 
where m.hgb_tipe = '2' and m.hgb_recordid is null)gb on dtl_prdcd_ctn=hgb_prdcd) ) detailstruk 
LEFT JOIN tbmaster_prodmast prd ON dtl_prdcd_ctn = prd.prd_prdcd 
LEFT JOIN 
( SELECT st_prdcd, st_saldoakhir FROM tbmaster_stock WHERE st_lokasi = '01' ) stk ON dtl_prdcd_ctn = stk.st_prdcd 
LEFT JOIN ( SELECT pkm_prdcd, pkm_pkmt, pkm_minorder, pkm_leadtime, pkm_mindisplay 
FROM tbmaster_kkpkm ) pkm ON dtl_prdcd_ctn = pkm.pkm_prdcd 
WHERE to_char(dtl_tanggal, 'yyyymmdd') BETWEEN :tanggalMulai AND :tanggalSelesai
AND DTL_PRDCD_CTN NOT IN (SELECT NON_PRDCD FROM TBMASTER_PLUNONPROMO )
GROUP BY dtl_prdcd_ctn, dtl_nama_barang, dtl_k_div, dtl_k_dept, dtl_k_katb, pkm_pkmt 
HAVING COALESCE(SUM(dtl_netto), 0) <> 0 ORDER BY dtl_k_div, dtl_k_dept, dtl_k_katb
";

// Execute the query and fetch the results using the PDO connection
try {
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':tanggalMulai', $tanggalMulaiFormatted);
    $stmt->bindValue(':tanggalSelesai', $tanggalSelesaiFormatted);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}

?>

<style>
    .report-container {
        margin-top: 30px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th {
        background-color: #007bff;
        color: white;
    }

    .table td {
        text-align: left;
    }

    .table td:first-child,
    .table th:first-child {
        text-align: left;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* Set table layout to auto for flexible column width */
    .table {
        table-layout: auto;
        /* This allows columns to adjust based on content */
    }
</style>
<style>
    .report-container {
        margin-top: 30px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th {
        background-color: #007bff;
        color: white;
    }

    .table td {
        text-align: left;
        white-space: nowrap;
        /* Mencegah teks dibungkus dalam sel */
    }

    .table td:first-child,
    .table th:first-child {
        text-align: left;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* Apply table-layout: auto for automatic column width adjustment */
    .table {
        width: 100%;
        table-layout: auto;
        /* Memungkinkan kolom menyesuaikan lebar berdasarkan konten */
    }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">REPORT SALES DI LUAR ITEM LARANGAN BY PRODUK</h3>
        <a href="../salesPromo/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- Card Baru untuk Judul dan Tanggal Periode -->
            <div class="card mb-3 text-center" style="background: linear-gradient(135deg, #007bff, #00c6ff); color: white;">
                <div class="card-body">
                    <h5 class="card-title mb-1" style="font-weight: bold;">Periode Laporan</h5>
                    <p class="card-text mb-0" style="font-size: 1.1rem;">
                        <?= date('d-m-Y', strtotime($tanggalMulaiFormatted)) ?> s/d <?= date('d-m-Y', strtotime($tanggalSelesaiFormatted)) ?>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>D</th>
                                    <th>Dp</th>
                                    <th>Kb</th>
                                    <th>PLU</th>
                                    <th>Nama Barang</th>
                                    <th>Kunjungan</th>
                                    <th>Member</th>
                                    <th>Struk</th>
                                    <th class="text-nowrap">Qty in pcs</th>
                                    <th>Gross</th>
                                    <th>Netto</th>
                                    <th>Margin</th>
                                    <th>Persen</th>
                                    <th>PKMT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $noUrut = 0;
                                $totalKunjungan = 0;
                                $totalMember = 0;
                                $totalStruk = 0;
                                $totalQtyInPcs = 0;
                                $totalGross = 0;
                                $totalNetto = 0;
                                $totalMargin = 0;

                                foreach ($result as $row):
                                    $noUrut++;
                                    echo '<tr>';
                                    echo '<td align="right">' . $noUrut . '</td>';
                                    echo '<td align="center">' . $row['dtl_k_div'] . '</td>';
                                    echo '<td align="center">' . $row['dtl_k_dept'] . '</td>';
                                    echo '<td align="center">' . $row['dtl_k_katb'] . '</td>';
                                    echo '<td align="center">' . $row['dtl_prdcd_ctn'] . '</td>';
                                    echo '<td align="left">' . $row['dtl_nama_barang'] . '</td>';
                                    echo '<td align="right">' . number_format($row['kunjungan'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['jml_member'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['struk'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['qty_in_pcs'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['dtl_gross'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['dtl_netto'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['dtl_margin'], 0, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['dtl_margin_persen'], 2, '.', ',') . '</td>';
                                    echo '<td align="right">' . number_format($row['pkm_pkmt'], 2, '.', ',') . '</td>';
                                    echo '</tr>';

                                    // Hitung total
                                    $totalKunjungan += $row['kunjungan'];
                                    $totalMember += $row['jml_member'];
                                    $totalStruk += $row['struk'];
                                    $totalQtyInPcs += $row['qty_in_pcs'];
                                    $totalGross += $row['dtl_gross'];
                                    $totalNetto += $row['dtl_netto'];
                                    $totalMargin += $row['dtl_margin'];
                                endforeach;

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#table-1').DataTable({
            responsive: true,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: true,
            columnDefs: [{
                targets: [4],
                orderable: false
            }],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'REPORT_SALES_PROMO_BY_PRODUK' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function() {
                this.api().columns.adjust().draw();
            }
        });

        // Tambahkan tombol ke bagian atas kiri
        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>