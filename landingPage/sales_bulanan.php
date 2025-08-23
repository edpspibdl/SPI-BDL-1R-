<?php
// sales_bulanan.php
require_once '../helper/connection.php'; // Pastikan path ini benar!
header('Content-Type: application/json');

// --- PENTING: Untuk DEVELOPMENT, aktifkan ini untuk melihat error PHP ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------------------------------------------------

$response = [
    'success' => false,
    'sales' => [], // Array untuk menyimpan data sales per bulan
    'message' => ''
];

try {
    if (!isset($conn) || !$conn instanceof PDO) {
        throw new Exception("Koneksi PDO tidak ditemukan atau tidak valid. Periksa ../helper/connection.php");
    }

    $sql = "SELECT
        TO_CHAR(DATE_TRUNC('month', dtl_tanggal), 'Mon YYYY') AS month_label,
        DATE_TRUNC('month', dtl_tanggal) AS month_start, -- Ubah alias menjadi month_start
        -- Kolom yang diformat Rupiah untuk tampilan
        'Rp' || REPLACE(REPLACE(TO_CHAR(COALESCE(SUM(dtl_netto), 0), 'FM999,999,999,999.00'), ',', '#'), '.', ',') AS total_sales_formatted,
        'Rp' || REPLACE(REPLACE(TO_CHAR(COALESCE(SUM(dtl_gross), 0), 'FM999,999,999,999.00'), ',', '#'), '.', ',') AS total_gross_formatted,
        'Rp' || REPLACE(REPLACE(TO_CHAR(COALESCE(SUM(dtl_margin), 0), 'FM999,999,999,999.00'), ',', '#'), '.', ',') AS total_margin_formatted,
        -- Kolom numerik asli untuk perhitungan (penting!)
        COALESCE(SUM(dtl_netto), 0) AS total_sales_numeric,
        COALESCE(SUM(dtl_gross), 0) AS total_gross_numeric,
        COALESCE(SUM(dtl_margin), 0) AS total_margin_numeric
    FROM (
        -- Subquery dtl_inner dan dtl_outer Anda yang sangat panjang
        SELECT
            dtl_rtype,
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
            END AS dtl_qty_pcs,
            CASE
                WHEN dtl_rtype = 'S' THEN dtl_qty
                ELSE dtl_qty * -1
            END AS dtl_qty,
            dtl_harga_jual,
            dtl_diskon,
            CASE
                WHEN dtl_rtype = 'S' THEN dtl_gross_original
                ELSE dtl_gross_original * -1
            END AS dtl_gross,
            CASE
                WHEN dtl_rtype = 'S' THEN dtl_netto_original
                ELSE dtl_netto_original * -1
            END AS dtl_netto,
            CASE
                WHEN dtl_rtype = 'S' THEN dtl_hpp_original
                ELSE dtl_hpp_original * -1
            END AS dtl_hpp,
            CASE
                WHEN dtl_rtype = 'S' THEN dtl_netto_original - dtl_hpp_original
                ELSE (dtl_netto_original - dtl_hpp_original) * -1
            END AS dtl_margin,
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
            CASE
                WHEN (dtl_memberkhusus = 'Y' AND CUS_JENISMEMBER = 'T') THEN 'TMI'
                WHEN dtl_memberkhusus = 'Y' THEN 'KHUSUS'
                WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') AND CUS_JENISMEMBER = 'I' THEN 'IDM'
                WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'OMI'
                ELSE 'REGULER'
            END AS dtl_tipemember,
            CASE
                WHEN dtl_memberkhusus = 'Y' THEN 'GROUP_1_KHUSUS'
                WHEN dtl_kasir IN ('IDM', 'ID1', 'ID2') THEN 'GROUP_2_IDM'
                WHEN dtl_memberkhusus IS NULL AND dtl_outlet = '6' THEN 'GROUP_4_END_USER'
                WHEN dtl_kasir IN ('OMI', 'BKL') THEN 'GROUP_3_OMI'
                ELSE 'GROUP_5_OTHERS'
            END AS dtl_group_member,
            dtl_kodesupplier,
            dtl_namasupplier,
            dtl_belanja_pertama,
            dtl_belanja_terakhir
        FROM (
            SELECT
                sls.trjd_transactiontype AS dtl_rtype,
                DATE_TRUNC('day', sls.trjd_transactiondate) AS dtl_tanggal,
                TO_CHAR(DATE_TRUNC('day', sls.trjd_transactiondate), 'yyyy.mm.dd')
                    || '-' || sls.trjd_cashierstation
                    || '-' || sls.trjd_create_by
                    || '-' || sls.trjd_transactionno
                    || '-' || sls.trjd_transactiontype AS dtl_struk,
                sls.trjd_cashierstation AS dtl_stat,
                sls.trjd_create_by AS dtl_kasir,
                sls.trjd_transactionno AS dtl_no_struk,
                sls.trjd_seqno AS dtl_seqno,
                Substr(sls.trjd_prdcd, 1, 6) || '0' AS dtl_prdcd_ctn,
                sls.trjd_prdcd AS dtl_prdcd,
                prd.prd_deskripsipanjang AS dtl_nama_barang,
                prd.prd_unit AS dtl_unit,
                prd.prd_frac AS dtl_frac,
                COALESCE(prd.prd_kodetag, ' ') AS dtl_tag,
                sls.trjd_flagtax1 AS dtl_bkp,
                CASE
                    WHEN prd.prd_unit = 'KG' AND prd.prd_frac = 1000 THEN sls.trjd_quantity
                    ELSE sls.trjd_quantity * prd.prd_frac
                END AS dtl_qty_pcs,
                sls.trjd_quantity AS dtl_qty,
                sls.trjd_unitprice AS dtl_harga_jual,
                sls.trjd_discount AS dtl_diskon,
                CASE
                    WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                        AND sls.trjd_create_by IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                        sls.trjd_nominalamt * 11.1 / 10
                    ELSE sls.trjd_nominalamt
                END AS dtl_gross_original,
                CASE
                    WHEN sls.trjd_flagtax1 = 'Y' AND sls.trjd_flagtax2 = 'Y'
                        AND sls.trjd_create_by NOT IN ('IDM', 'ID1', 'ID2', 'OMI', 'BKL') THEN
                        sls.trjd_nominalamt / 11.1 * 10
                    ELSE sls.trjd_nominalamt
                END AS dtl_netto_original,
                CASE
                    WHEN prd.prd_unit = 'KG' THEN
                        sls.trjd_quantity * sls.trjd_baseprice / 1000
                    ELSE
                        sls.trjd_quantity * sls.trjd_baseprice
                END AS dtl_hpp_original,
                Trim(sls.trjd_divisioncode) AS dtl_k_div,
                div.div_namadivisi AS dtl_nama_div,
                Substr(sls.trjd_division, 1, 2) AS dtl_k_dept,
                dep.dep_namadepartement AS dtl_nama_dept,
                Substr(sls.trjd_division, 3, 2) AS dtl_k_katb,
                kat.kat_namakategori AS dtl_nama_katb,
                tko.tko_kodeomi AS dtl_kodetokoomi,
                sls.trjd_cus_kodemember AS dtl_cusno,
                cus.cus_namamember AS dtl_namamember,
                cus.cus_flagmemberkhusus AS dtl_memberkhusus,
                cus.cus_kodeoutlet AS dtl_outlet,
                cus.cus_kodesuboutlet AS dtl_suboutlet,
                cus.CUS_JENISMEMBER AS CUS_JENISMEMBER,
                sup.hgb_kodesupplier AS dtl_kodesupplier,
                sup.sup_namasupplier AS dtl_namasupplier,
                akt.jh_belanja_pertama AS dtl_belanja_pertama,
                akt.jh_belanja_terakhir AS dtl_belanja_terakhir
            FROM tbtr_jualdetail sls
            LEFT JOIN tbmaster_prodmast prd ON sls.trjd_prdcd = prd.prd_prdcd
            LEFT JOIN tbmaster_customer cus ON sls.trjd_cus_kodemember = cus.cus_kodemember
            LEFT JOIN tbmaster_tokoigr tko ON sls.trjd_cus_kodemember = tko.tko_kodecustomer
            LEFT JOIN tbmaster_divisi div ON sls.trjd_divisioncode = div.div_kodedivisi
            LEFT JOIN tbmaster_departement dep ON Substr(sls.trjd_division, 1, 2) = dep.dep_kodedepartement
            LEFT JOIN (
                SELECT kat_kodedepartement || kat_kodekategori AS kat_kodekategori, kat_namakategori
                FROM tbmaster_kategori
            ) kat ON sls.trjd_division = kat.kat_kodekategori
            LEFT JOIN (
                SELECT m.hgb_prdcd, m.hgb_kodesupplier, s.sup_namasupplier
                FROM tbmaster_hargabeli m
                LEFT JOIN tbmaster_supplier s ON m.hgb_kodesupplier = s.sup_kodesupplier
                WHERE m.hgb_tipe = '2' AND m.hgb_recordid IS NULL
            ) sup ON Substr(sls.trjd_prdcd, 1, 6) || '0' = sup.hgb_prdcd
            LEFT JOIN (
                SELECT jh_cus_kodemember, DATE_TRUNC('day', Min(jh_transactiondate)) AS jh_belanja_pertama, DATE_TRUNC('day', Max(jh_transactiondate)) AS jh_belanja_terakhir
                FROM tbtr_jualheader
                WHERE jh_cus_kodemember IS NOT NULL
                GROUP BY jh_cus_kodemember
            ) akt ON sls.trjd_cus_kodemember = akt.jh_cus_kodemember
            WHERE sls.trjd_recordid IS NULL AND sls.trjd_quantity <> 0
        ) AS dtl_inner
    ) AS dtl_outer
    WHERE DATE_TRUNC('month', dtl_tanggal) >= DATE_TRUNC('month', current_date - INTERVAL '11 months')
    GROUP BY month_start, month_label
    ORDER BY month_start ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inisialisasi array untuk 12 bulan terakhir
    $monthly_data_filled = [];
    $current_date_php = new DateTime(); // Gunakan nama variabel berbeda untuk menghindari konflik
    for ($i = 11; $i >= 0; $i--) {
        $date = clone $current_date_php;
        $date->modify("-$i month");
        $month_key = $date->format('M Y');
        $monthly_data_filled[$month_key] = [
            'total_sales_formatted' => 'Rp0,00', // Default string Rupiah
            'total_gross_formatted' => 'Rp0,00', // Default string Rupiah
            'total_margin_formatted' => 'Rp0,00', // Default string Rupiah
            'total_sales_numeric' => 0,
            'total_gross_numeric' => 0,
            'total_margin_numeric' => 0
        ];
    }

    // Isi data dari database ke array yang sudah diinisialisasi
    foreach ($results as $row) {
        $month_label = $row['month_label'];
        if (isset($monthly_data_filled[$month_label])) {
            $monthly_data_filled[$month_label]['total_sales_formatted'] = $row['total_sales_formatted'];
            $monthly_data_filled[$month_label]['total_gross_formatted'] = $row['total_gross_formatted'];
            $monthly_data_filled[$month_label]['total_margin_formatted'] = $row['total_margin_formatted'];
            $monthly_data_filled[$month_label]['total_sales_numeric'] = (float) $row['total_sales_numeric'];
            $monthly_data_filled[$month_label]['total_gross_numeric'] = (float) $row['total_gross_numeric'];
            $monthly_data_filled[$month_label]['total_margin_numeric'] = (float) $row['total_margin_numeric'];
        }
    }

    // Konversi ke format array indeks numerik yang diharapkan JavaScript
    $response['success'] = true;
    foreach ($monthly_data_filled as $month => $data) {
        $response['sales'][] = [
            'month' => $month,
            'total_sales_formatted' => $data['total_sales_formatted'],
            'total_gross_formatted' => $data['total_gross_formatted'],
            'total_margin_formatted' => $data['total_margin_formatted'],
            'total_sales_numeric' => $data['total_sales_numeric'],
            'total_gross_numeric' => $data['total_gross_numeric'],
            'total_margin_numeric' => $data['total_margin_numeric']
        ];
    }
    $response['message'] = "Data sales bulanan berhasil diambil (Net, Gross & Margin).";
} catch (PDOException $e) {
    http_response_code(500);
    $response['message'] = "Database error: " . $e->getMessage();
    error_log("PDO Error in sales_bulanan.php: " . $e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = "Server error: " . $e->getMessage();
    error_log("General Error in sales_bulanan.php: " . $e->getMessage());
}

echo json_encode($response);
