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
    *
FROM
    (
        SELECT
            obi_nopb,
            obi_tglpb,
            dtl_tanggal        tglstruk,
            obi_kdmember,
            cus_namamember,
            dtl_struk          no_struk,
            dtl_tipemember,
            (obi_ttlorder + obi_ttlppn) ttl_rupiah,
            SUM(dtl_gross) AS rph_gross,
            SUM(dtl_margin) AS rph_margin,
            round(SUM(dtl_netto), 2) AS sls_nett,
            obi_jrkekspedisi   km,
            ongkir             f_ongkir,
            pot_ongkir         z_pot_ongkir,
            OBI_KDEKSPEDISI
        FROM
            (
                SELECT
                    obi_nopb,
                    obi_jrkekspedisi,
                    obi_kdmember,
                    cus_namamember,
                    obi_ttlppn,
                    obi_ttlorder, ( obi_ttlorder + obi_ttlppn ) ttl_rupiah,
                    ( obi_realorder + obi_realppn) obi_real,
                    TO_CHAR(obi_tglstruk, 'yyyy.mm.dd')
                    || '-'
                    || obi_kdstation
                    || '-'
                    || obi_cashierid
                    || '-'
                    || obi_nostruk
                    || '-'
                    || obi_tipe struk_obi,
                    TO_CHAR(obi_tglpb,'DD-MON-YY') obi_tglpb,
                    OBI_KDEKSPEDISI
                FROM
                    tbtr_obi_h left
                    JOIN tbmaster_customer ON cus_kodemember = obi_kdmember
                WHERE
                    to_char(obi_tglstruk,'YYYYMMDD') BETWEEN :tanggalMulai AND :tanggalSelesai
                    and obi_recid = '6'
            ) obih left
            JOIN (
                SELECT
                    dtl_tanggal,
                    dtl_tipemember,
                    dtl_prdcd,
                    dtl_prdcd_ctn,
                    dtl_cusno,
                    dtl_gross,
                    dtl_margin,
                    dtl_netto,
                    dtl_struk
                FROM
                    (SELECT   dtl_rtype, 
		       dtl_tanggal,
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
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_qty_pcs
		         ELSE dtl_qty_pcs * -1
		       END dtl_qty_pcs,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_qty
		         ELSE dtl_qty *- 1
		       END dtl_qty,
		       dtl_harga_jual,
		       dtl_diskon,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_gross
		         ELSE dtl_gross *- 1
		       END dtl_gross,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_netto
		         ELSE dtl_netto *- 1
		       END dtl_netto,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_hpp
		         ELSE dtl_hpp *- 1
		       END dtl_hpp,
		       CASE
		         WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
		         ELSE ( dtl_netto - dtl_hpp ) * -1
		       END dtl_margin,
		       dtl_k_div,
		       dtl_nama_div,
		       dtl_k_dept,
		       dtl_nama_dept,
		       dtl_k_katb,
		       dtl_nama_katb,
			   dtl_kodetokoomi,
		       dtl_cusno,
		       dtl_namamember,
		       dtl_memberkhusus,
		       dtl_outlet,
		       dtl_suboutlet,
               CUS_JENISMEMBER,
               dtl_idsegment,
               dtl_segment,
		       CASE
		         WHEN (dtl_memberkhusus = 'Y' and CUS_JENISMEMBER = 'T') THEN 'TMI'
                 WHEN dtl_memberkhusus = 'Y' THEN 'KHUSUS'
		         WHEN dtl_kasir = 'IDM' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'ID1' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'ID2' and CUS_JENISMEMBER = 'I' THEN 'IDM'
		         WHEN dtl_kasir = 'OMI'
		               OR dtl_kasir = 'BKL' THEN 'OMI'
		         ELSE 'REGULER'
		       END dtl_tipemember,
		       CASE
		         WHEN dtl_memberkhusus = 'Y' THEN 'GROUP_1_KHUSUS'
		         WHEN dtl_kasir = 'IDM' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'ID1' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'ID2' THEN 'GROUP_2_IDM'
		         WHEN dtl_kasir = 'OMI' OR dtl_kasir = 'BKL' THEN 'GROUP_3_OMI'
             	 WHEN dtl_memberkhusus is null AND dtl_outlet ='6' THEN 'GROUP_4_END_USER'
		         ELSE 'GROUP_5_OTHERS'
		       END dtl_group_member,
		       dtl_kodesupplier,
		       dtl_namasupplier,
		       dtl_belanja_pertama,
		       dtl_belanja_terakhir
		FROM   (SELECT sls.trjd_transactiontype        AS dtl_rtype,
		               sls.trjd_transactiondate AS dtl_tanggal,
		               To_char(sls.trjd_transactiondate, 'yyyy.mm.dd')
		               ||'-'||sls.trjd_cashierstation
		               ||'-'||sls.trjd_create_by
		               ||'-'||sls.trjd_transactionno
		               ||'-'||sls.trjd_transactiontype      AS dtl_struk,
		               sls.trjd_cashierstation         AS dtl_stat,
		               sls.trjd_create_by              AS dtl_kasir,
		               sls.trjd_transactionno          AS dtl_no_struk,
		               sls.trjd_seqno                  AS dtl_seqno,
		               Substr(sls.trjd_prdcd, 1, 6)
		               || '0'                          AS dtl_prdcd_ctn,
		               sls.trjd_prdcd                  AS dtl_prdcd,
		               prd.prd_deskripsipanjang        AS dtl_nama_barang,
		               prd.prd_unit                    AS dtl_unit,
		               prd.prd_frac                    AS dtl_frac,
		               coalesce(prd.prd_kodetag, ' ')       AS dtl_tag,
		               sls.trjd_flagtax1               AS dtl_bkp,
		               CASE
		                 WHEN PRD.prd_unit = 'KG'
		                      AND prd.prd_frac = 1000 THEN sls.trjd_quantity
		                 ELSE sls.trjd_quantity * prd.prd_frac
		               END                             dtl_qty_pcs,
		               sls.trjd_quantity               AS dtl_qty,
		               sls.trjd_unitprice              AS dtl_harga_jual,
		               sls.trjd_discount               AS dtl_diskon,
		               CASE
		                 WHEN sls.trjd_flagtax1 = 'Y' and  sls.trjd_flagtax2 = 'Y'
		                      AND sls.trjd_create_by IN( 'IDM','ID1','ID2', 'OMI', 'BKL' ) THEN
		                 sls.trjd_nominalamt * 11.1 / 10
		                 ELSE sls.trjd_nominalamt
		               END                             dtl_gross,
		               CASE
		                 WHEN sls.trjd_flagtax1 = 'Y' and  sls.trjd_flagtax2 = 'Y'
		                      AND sls.trjd_create_by NOT IN( 'IDM','ID1','ID2', 'OMI', 'BKL' ) THEN
		                 sls.trjd_nominalamt / 11.1 * 10
		                 ELSE sls.trjd_nominalamt
		               END                             dtl_netto,
		               CASE
		                 WHEN PRD.prd_unit = 'KG' THEN
		                 sls.trjd_quantity * sls.trjd_baseprice / 1000
		                 ELSE sls.trjd_quantity * sls.trjd_baseprice
		               END                             dtl_hpp,
		               Trim(sls.trjd_divisioncode)     AS dtl_k_div,
		               div.div_namadivisi              AS dtl_nama_div,
		               Substr(sls.trjd_division, 1, 2) AS dtl_k_dept,
		               dep.dep_namadepartement         AS dtl_nama_dept,
		               Substr(sls.trjd_division, 3, 2) AS dtl_k_katb,
		               kat.kat_namakategori            AS dtl_nama_katb,
                       tko.tko_kodeomi                 AS dtl_kodetokoomi,
		               sls.trjd_cus_kodemember         AS dtl_cusno,
		               cus.cus_namamember              AS dtl_namamember,
		               cus.cus_flagmemberkhusus        AS dtl_memberkhusus,
		               cus.cus_kodeoutlet              AS dtl_outlet,
		               cus.cus_kodesuboutlet           AS dtl_suboutlet,
                       crm_idsegment                   AS dtl_idsegment,
                       seg.seg_nama                    AS dtl_segment,
                       cus.CUS_JENISMEMBER             as CUS_JENISMEMBER,
		               sup.hgb_kodesupplier            AS dtl_kodesupplier,
		               sup.sup_namasupplier            AS dtl_namasupplier,
		               akt.jh_belanja_pertama          AS dtl_belanja_pertama,
		               akt.jh_belanja_terakhir         AS dtl_belanja_terakhir

		        FROM   tbtr_jualdetail sls
		        join tbmaster_prodmast prd on sls.trjd_prdcd = prd.prd_prdcd
		        join tbmaster_customer cus on sls.trjd_cus_kodemember = cus.cus_kodemember
                left join tbmaster_customercrm crm on sls.trjd_cus_kodemember = crm.crm_kodemember
                left join tbmaster_segmentasi seg on crm.crm_idsegment = seg.seg_id 
                left join tbmaster_tokoigr tko on sls.trjd_cus_kodemember = tko.tko_kodecustomer
		        left join tbmaster_divisi div on sls.trjd_divisioncode = div.div_kodedivisi
		        left join tbmaster_departement dep on Substr(sls.trjd_division, 1, 2) = dep.dep_kodedepartement
		        left join (SELECT kat_kodedepartement
		                       || kat_kodekategori AS kat_kodekategori,
		                       kat_namakategori
		                FROM   tbmaster_kategori) kat on sls.trjd_division = kat.kat_kodekategori
		        left join (SELECT m.hgb_prdcd,
		                       m.hgb_kodesupplier,
		                       s.sup_namasupplier
		                FROM   tbmaster_hargabeli m
                        left join tbmaster_supplier s on m.hgb_kodesupplier = s.sup_kodesupplier
		                WHERE  m.hgb_tipe = '2'
		                       AND m.hgb_recordid IS NULL) sup on Substr(sls.trjd_prdcd, 1, 6) || 0 = sup.hgb_prdcd
                left join (SELECT jh_cus_kodemember,
						       to_char(Min(jh_transactiondate),'DD-MON-YY') AS jh_belanja_pertama,
						       to_char(Max(jh_transactiondate),'DD-MON-YY') AS jh_belanja_terakhir
						FROM   tbtr_jualheader
						WHERE  jh_cus_kodemember IS NOT NULL
						GROUP  BY jh_cus_kodemember) akt on sls.trjd_cus_kodemember = akt.jh_cus_kodemember
		        WHERE  sls.trjd_recordid IS NULL
		               AND sls.trjd_quantity <> 0) alias1) view_detail
            ) view_detail_struk_revisi ON dtl_struk = struk_obi
            LEFT JOIN (
                SELECT
                    kode_member,
                    no_pb,
                    no_trans,
                    tgl_trans,
                    ongkir,
                    pot_ongkir
                FROM
                    payment_klikigr
                WHERE
                    pot_ongkir <> 0
            )                                payment_klikigr ON obi_kdmember = payment_klikigr.kode_member
                                 AND obi_nopb = payment_klikigr.no_pb
        GROUP BY
            obi_nopb,
            obi_kdmember,
            cus_namamember,
            ttl_rupiah,
            obi_tglpb,
            dtl_tanggal,
            dtl_cusno,
            dtl_struk,
            dtl_tipemember,
            obi_jrkekspedisi,
            ongkir,
            pot_ongkir,
            ( obi_ttlorder + obi_ttlppn ),
            OBI_KDEKSPEDISI
        ORDER BY
            dtl_tanggal ASC
    )uyee
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
        <h3 class="text-center">REPORT SALES VS ONGKIR ALL</h3>
        <a href="../salesPromo/index.php" class="btn btn-primary">BACK</a>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Card Baru untuk Judul Periode Laporan -->
            <div class="card mb-3">
                <div class="card-body text-center" style="background-color: rgb(52, 160, 248); color: white; border-radius: 10px;">
                    <h4 class="card-title" style="font-weight: bold; font-size: 1.3rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
                        Periode Laporan:
                        <span style="font-size: 1.4rem; color: #ffd700;"><?= date('d-m-Y', strtotime($tanggalMulaiFormatted)) ?> s/d <?= date('d-m-Y', strtotime($tanggalSelesaiFormatted)) ?></span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="table-1">
                            <thead>
                                <tr>
                                    <th>NO PB</th>
                                    <th>TGL PB</th>
                                    <th>TGL STRUK</th>
                                    <th>KD MEMBER</th>
                                    <th>NAMA MEMBER</th>
                                    <th>NO STRUK</th>
                                    <th>TIPE MEMBER</th>
                                    <th>RUPIAH</th>
                                    <th>GROSS</th>
                                    <th>MARGIN</th>
                                    <th>NETT</th>
                                    <th>KM</th>
                                    <th>ONGKIR</th>
                                    <th>POT ONGKIR</th>
                                    <th>EKSPEDISI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($result as $row): ?>
                                    <tr>
                                        <td align="center"><?= $row['obi_nopb'] ?></td>
                                        <td align="center"><?= $row['obi_tglpb'] ?></td>
                                        <td align="center"><?= $row['tglstruk'] ?></td>
                                        <td align="center"><?= $row['obi_kdmember'] ?></td>
                                        <td align="left"><?= $row['cus_namamember'] ?></td>
                                        <td align="center"><?= $row['no_struk'] ?></td>
                                        <td align="center"><?= $row['dtl_tipemember'] ?></td>
                                        <td align="right"><?= number_format($row['ttl_rupiah'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['rph_gross'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['rph_margin'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['sls_nett'], 0, '.', ',') ?></td>
                                        <td align="right"><?= number_format($row['km'], 2, '.', ',') ?></td>
                                        <td align="center"><?= $row['f_ongkir'] ?></td>
                                        <td align="center"><?= $row['z_pot_ongkir'] ?></td>
                                        <td align="center"><?= $row['obi_kdekspedisi'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
            responsive: false,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: true,
            columnDefs: [{
                targets: [4], // Kolom dengan index 4 yang tidak dapat diurutkan
                orderable: false
            }],
            buttons: [{
                    extend: 'copy',
                    text: 'Copy',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        // Mengabaikan header saat menyalin data
                        stripHtml: true,
                        columns: ':visible', // Hanya kolom yang terlihat yang disalin
                        orthogonal: 'export' // Pastikan hanya data tanpa format HTML
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'SALES_VS_ONGKIR_ALL' + new Date().toISOString().split('T')[0],
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