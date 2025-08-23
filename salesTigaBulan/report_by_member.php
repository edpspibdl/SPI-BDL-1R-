<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Fungsi untuk format tanggal Indonesia
function tanggalIND($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tanggal = explode('-', $tanggal);
    return $tanggal[2] . ' ' . $bulan[(int)$tanggal[1]] . ' ' . $tanggal[0];
}

// Ambil data dari form (jika ada)
$tanggalAwal1 = $_POST['tanggalAwal1'] ?? '';
$tanggalAkhir1 = $_POST['tanggalAkhir1'] ?? '';
$tanggalAwal2 = $_POST['tanggalAwal2'] ?? '';
$tanggalAkhir2 = $_POST['tanggalAkhir2'] ?? '';
$tanggalAwal3 = $_POST['tanggalAwal3'] ?? '';
$tanggalAkhir3 = $_POST['tanggalAkhir3'] ?? '';

// Periksa apakah input sudah ada, jika tidak ada, set ke kosong.
$tglMulaiSebelumPromosi = $tanggalAwal1 ? date('Ymd', strtotime($tanggalAwal1)) : '';
$tglSelesaiSebelumPromosi = $tanggalAkhir1 ? date('Ymd', strtotime($tanggalAkhir1)) : '';

$tglMulaiPromosi = $tanggalAwal2 ? date('Ymd', strtotime($tanggalAwal2)) : '';
$tglSelesaiPromosi = $tanggalAkhir2 ? date('Ymd', strtotime($tanggalAkhir2)) : '';

$tglMulaiSetelahPromosi = $tanggalAwal3 ? date('Ymd', strtotime($tanggalAwal3)) : '';
$tglSelesaiSetelahPromosi = $tanggalAkhir3 ? date('Ymd', strtotime($tanggalAkhir3)) : '';

// Query dengan parameter binding
$query = "SELECT
    dtl_outlet,
    dtl_cusno,
    dtl_namamember,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_tanggal
        END
    )) AS dtl_kunjungan,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_cusno
        END
    )) AS dtl_member,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_struk
        END
    )) AS dtl_struk,
    COUNT(DISTINCT(CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item,
    SUM(CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_gross
        END
    ) AS dtl_gross,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_netto
        END
    )) as dtl_netto,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal1 AND :tanggalAkhir1 THEN
                dtl_margin
        END
    )) AS dtl_margin,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN dtl_tanggal
        END
    )) AS dtl_kunjungan_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_cusno
        END
    )) AS dtl_member_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_struk
        END
    )) AS dtl_struk_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item_2,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs_2,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_gross
        END
    ) AS dtl_gross_2,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_netto
        END
    )) AS dtl_netto_2,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal2 AND :tanggalAkhir2 THEN
                dtl_margin END)) AS dtl_margin_2,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_tanggal
        END
    )) AS dtl_kunjungan_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_cusno
        END
    )) AS dtl_member_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_struk
        END
    )) AS dtl_struk_3,
    COUNT(DISTINCT(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_prdcd_ctn
        END
    )) AS dtl_item_3,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_qty_pcs
        END
    ) AS dtl_qty_in_pcs_3,
    SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_gross
        END
    ) AS dtl_gross_3,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_netto
        END
    )) AS dtl_netto_3,
    trunc(SUM(
        CASE
            WHEN TO_CHAR(dtl_tanggal, 'YYYYMMDD') BETWEEN :tanggalAwal3 AND :tanggalAkhir3 THEN
                dtl_margin
        END
    )) AS dtl_margin_3 FROM (( SELECT
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
            dtl_gross * -1 end AS dtl_gross,
    CASE
        WHEN dtl_rtype = 'R' THEN
            ( dtl_netto * - 1 )
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
    dtl_k_div,
    dtl_nama_div,
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
        WHEN trjd_divisioncode = '5'
             AND substr(trjd_division, 1, 2) = '39' THEN
            case
        WHEN 'Y' = 'Y' THEN
            trjd_nominalamt
    END else case when coalesce(tko_kodesbu, 'z') in ('O', 'I') then case when tko_tipeomi in ('HE', 'HG') 
    then trjd_nominalamt - ( case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') 
    and coalesce(prd_kodetag, 'zz') <> 'Q' then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) else 0 end ) 
    else trjd_nominalamt end else trjd_nominalamt - ( case when substr(trjd_create_by, 1, 2) = 'EX' then 0 else 
    case when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') 
    and coalesce(prd_kodetag, 'zz') <> 'Q' then (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100)))) 
    else 0 end end ) end end as dtl_netto, case when trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' 
    then case when 'Y' = 'Y' then trjd_nominalamt - ( case when prd_markupstandard is null then (5 * trjd_nominalamt) / 100 
    else (prd_markupstandard * trjd_nominalamt) / 100 end ) end else (trjd_quantity / case when prd_unit = 'KG' then 1000 
    else 1 end) * trjd_baseprice end as dtl_hpp from tbtr_jualdetail left join tbmaster_prodmast on trjd_prdcd = prd_prdcd 
    left join tbmaster_tokoigr on trjd_cus_kodemember = tko_kodecustomer 
    left join tbmaster_customer on trjd_cus_kodemember = cus_kodemember 
    left join tbmaster_customercrm on trjd_cus_kodemember = crm_kodemember 
    left join tbmaster_divisi on trjd_division = div_kodedivisi 
    left join tbmaster_departement on substr(trjd_division, 1, 2) = dep_kodedepartement 
    left join tbmaster_kategori on trjd_division = kat_kodedepartement || kat_kodekategori)sls 
    left join (select m.hgb_prdcd hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier 
    from tbmaster_hargabeli m 
    left join tbmaster_supplier s on m.hgb_kodesupplier = s.sup_kodesupplier 
    where m.hgb_tipe = '2' and m.hgb_recordid is null)gb on dtl_prdcd_ctn=hgb_prdcd) ) detailstruk 
    WHERE ( TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal1 and :tanggalAkhir1 
    OR TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal2 and :tanggalAkhir2 
    OR TO_CHAR(dtl_tanggal,'YYYYMMDD') BETWEEN :tanggalAwal3 and :tanggalAkhir3 ) 
    GROUP BY dtl_outlet, dtl_cusno, dtl_namamember";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':tanggalAwal1' => $tglMulaiSebelumPromosi,
        ':tanggalAkhir1' => $tglSelesaiSebelumPromosi,
        ':tanggalAwal2' => $tglMulaiPromosi,
        ':tanggalAkhir2' => $tglSelesaiPromosi,
        ':tanggalAwal3' => $tglMulaiSetelahPromosi,
        ':tanggalAkhir3' => $tglSelesaiSetelahPromosi
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}
?>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h3 class="text-center">Sales 3 Bulan By Member</h3>
        <a href="../salesTigaBulan/index.php" class="btn btn-danger">BACK</a>
    </div>    
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="GridView" class="table table-bordered">
                    <thead>
                        <tr class="active">
                            <th rowspan="2" class="text-center">#</th>
                            <th colspan="3" class="text-center">Member</th>
                            
                            <th colspan="7" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiSebelumPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiSebelumPromosi))) ?>
                            </th>
                            <th colspan="7" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiPromosi))) ?>
                            </th>
                            <th colspan="7" class="text-center">
                                <?= tanggalIND(date('Y-m-d', strtotime($tglMulaiSetelahPromosi))) ?> - 
                                <?= tanggalIND(date('Y-m-d', strtotime($tglSelesaiSetelahPromosi))) ?>
                            </th>
                            
                        </tr>
                        <tr class="active">				  
				  
				  <th class="text-center">Outlet</th>
				  <th class="text-center">Kode</th>
				  <th class="text-center">Nama</th>
		

				  <!-- sebelum promosi-->
				  <th class="text-center">Kunjungan</th>
				  
				  <th class="text-center">Struk</th>
				  <th class="text-center">Qty</th>

				  <th class="text-center">Gross</th>
				  <th class="text-center">Netto</th>
				  <th class="text-center">Margin</th>
				  <th class="text-center">%</th>

				  <!-- promosi-->
				  
				  <th class="text-center">Kunjungan</th>
				  
				  <th class="text-center">Struk</th>
				  <th class="text-center">Qty</th>

				  <th class="text-center">Gross</th>
				  <th class="text-center">Netto</th>
				  <th class="text-center">Margin</th>
				  <th class="text-center">%</th>

				  <!-- setelah promosi-->
				  
				  <th class="text-center">Kunjungan</th>
				  
				  <th class="text-center">Struk</th>
				  <th class="text-center">Qty</th>

				  <th class="text-center">Gross</th>
				  <th class="text-center">Netto</th>
				  <th class="text-center">Margin</th>
				  <th class="text-center">%</th>

				  </tr>
                    </thead>
                    <tbody>
                        <?php
                        $noUrut = 0;
                        foreach ($results as $row) {    
                            $noUrut++;
                            print '<tr>';
					   echo '<td align="right">'  . $noUrut . '</td>';
					   echo '<td align="center">' . $row['dtl_outlet'] . '</td>';
					   echo '<td align="center">' . $row['dtl_cusno'] . '</td>';
					   echo '<td align="left" class="text-nowrap">' . $row['dtl_namamember'] . '</td>';
					

					//sebelum promosi
					   echo '<td align="right">'  . number_format($row['dtl_kunjungan'], 0, '.', ',') . '</td>';
					   //echo '<td align="right">'  . number_format($row['dtl_member'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_struk'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_qty_in_pcs'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_gross'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_netto'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_margin'], 0, '.', ',') . '</td>';	
					   if ($row['dtl_netto'] == 0) {
					   		echo '<td align="right">'  . number_format(0 , 2, '.', ',') . '</td>';		
					   } else {
					   		echo '<td align="right">'  . number_format($row['dtl_margin'] / $row['dtl_netto']  * 100, 2, '.', ',') . '</td>';	
					   }

					   // promosi
					   echo '<td align="right">'  . number_format($row['dtl_kunjungan_2'], 0, '.', ',') . '</td>';
					   //echo '<td align="right">'  . number_format($row['dtl_member_2'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_struk_2'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_qty_in_pcs_2'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_gross_2'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_netto_2'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_margin_2'], 0, '.', ',') . '</td>';	
					   if ($row['dtl_netto_2'] == 0) {
					   		echo '<td align="right">'  . number_format(0 , 2, '.', ',') . '</td>';		
					   } else {
					   		echo '<td align="right">'  . number_format($row['dtl_margin_2'] / $row['dtl_netto_2']  * 100, 2, '.', ',') . '</td>';	
					   }

					   //setelah promosi
					   echo '<td align="right">'  . number_format($row['dtl_kunjungan_3'], 0, '.', ',') . '</td>';
					   //echo '<td align="right">'  . number_format($row['dtl_member_3'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_struk_3'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_qty_in_pcs_3'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_gross_3'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_netto_3'], 0, '.', ',') . '</td>';
					   echo '<td align="right">'  . number_format($row['dtl_margin_3'], 0, '.', ',') . '</td>';	
					   if ($row['dtl_netto_3'] == 0) {
					   		echo '<td align="right">'  . number_format(0 , 2, '.', ',') . '</td>';		
					   } else {
					   		echo '<td align="right">'  . number_format($row['dtl_margin_3'] / $row['dtl_netto_3']  * 100, 2, '.', ',') . '</td>';	
					   }
					   echo '<td align="center">&nbsp;</td>';
					   

					} //end while
					   
					  print '</tr>';
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

	

<?php require_once '../layout/_bottom.php'; ?>

<script>
    $(document).ready(function() {
        $('#GridView').DataTable({
            responsive: false,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: false,
            buttons: [
                { extend: 'copy', text: 'Copy' },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'PARETO_3_BULAN_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function () {
                this.api().columns.adjust().draw();
            }
        });
    });
</script>



