<?php
// Ambil input filter dari URL (GET), jika tidak ada gunakan default
$tgl_filter = isset($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
$kec_filter = isset($_GET['kecamatan']) ? $_GET['kecamatan'] : 'ALL';

// Pengaturan Target Statis (Ubah angka ini sesuai target kantor Anda)
$target_statis = 50000000; 

try {
    // 1. Ambil List Kecamatan untuk Dropdown Filter
    $sql_kec = "SELECT DISTINCT crm_alamatusaha4 
                FROM TBMASTER_CUSTOMERCRM 
                WHERE crm_kodeigr = '1R' AND crm_alamatusaha4 IS NOT NULL 
                ORDER BY crm_alamatusaha4 ASC";
    $list_kecamatan = $conn->query($sql_kec)->fetchAll(PDO::FETCH_COLUMN);

    // 2. Siapkan parameter query
    $params = [':tgl' => $tgl_filter];
    $where_kec = "";
    if ($kec_filter !== 'ALL') {
        $where_kec = " AND crm.crm_alamatusaha4 = :kec";
        $params[':kec'] = $kec_filter;
    }

    // 3. Query Utama untuk Tabel dan Chart
    $sql = "
SELECT 
    UPPER(nama_salesman) as nama_salesman,
    COUNT(obi_nopb) as total_pb,
    SUM(pb_batal_flag) as jml_pb_batal, -- Menghitung jumlah PB yang batal
    SUM(total_order) as nominal_order,
    SUM(total_real) as nominal_real,
    SUM(total_loss_sales) as total_loss_sales,
    CASE 
        WHEN SUM(total_order) > 0 
        THEN ROUND((SUM(total_real) / SUM(total_order)) * 100, 2) 
        ELSE 0 
    END AS achieve_pct
FROM (
    SELECT 
        UPPER(COALESCE(c.cus_nosalesman, 'TANPA SALES')) AS nama_salesman,
        h.obi_nopb,
        -- LOGIKA BARU ANDA DISINI
        CASE 
            WHEN substring(h.obi_recid, 1, 1) = 'B' THEN 1 
            ELSE 0 
        END AS pb_batal_flag,
        (h.obi_ttlorder + h.obi_ttlppn - COALESCE(pki.cashback_order, 0)) AS total_order,
        (h.obi_realorder + h.obi_realppn - COALESCE(pki.cashback_real, 0)) AS total_real,
        ((h.obi_ttlorder + h.obi_ttlppn - COALESCE(pki.cashback_order, 0)) - 
         (h.obi_realorder + h.obi_realppn - COALESCE(pki.cashback_real, 0))) AS total_loss_sales
    FROM tbtr_obi_h h
    LEFT JOIN tbmaster_customer c ON h.obi_kdmember = c.cus_kodemember AND c.cus_kodeigr = '1R'
    LEFT JOIN TBMASTER_CUSTOMERCRM crm ON h.obi_kdmember = crm.crm_kodemember AND crm.crm_kodeigr = '1R'
    LEFT JOIN (
        SELECT no_pb, SUM(cashback_order) AS cashback_order, SUM(cashback_real) AS cashback_real
        FROM promo_klikigr GROUP BY no_pb
    ) pki ON pki.no_pb = h.obi_nopb
    WHERE h.obi_tgltrans::DATE = :tgl $where_kec
) subquery
GROUP BY UPPER(nama_salesman)
ORDER BY nominal_real DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Kalkulasi Data untuk Widget & Chart
    $total_pb_batal = 0; // Inisialisasi
    $total_realisasi = 0;
    $total_order = 0;
    $total_loss = 0;
    $total_pb_masuk = 0;
    $chart_labels = [];
    $chart_data = [];

    foreach ($result as $row) {
        $total_realisasi += $row['nominal_real'];
        $total_order += $row['nominal_order'];
        $total_loss += $row['total_loss_sales'];
        $total_pb_masuk += $row['total_pb'];
        $total_pb_batal += $row['jml_pb_batal']; // Tambahkan ini
        
        $chart_labels[] = $row['nama_salesman'];
        $chart_data[] = $row['nominal_order'];
    }

} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}