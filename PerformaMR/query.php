<?php
$query = " 
  WITH mr_base AS (
    SELECT DISTINCT cus_nosalesman AS mr
    FROM tbmaster_customer
    WHERE cus_kodeigr = '2G'
),
trans AS (
    SELECT
        c.cus_nosalesman AS mr,
        h.jh_cus_kodemember,
        DATE(h.jh_transactiondate) AS tgl
    FROM tbtr_jualheader h
    JOIN tbmaster_customer c 
        ON c.cus_kodemember = h.jh_cus_kodemember
    WHERE to_char(h.jh_transactiondate, 'YYYY-MM') = TO_CHAR(current_date, 'YYYY-MM')
      AND c.cus_recordid IS NULL
      AND c.cus_kodeigr = '2G'
      AND c.cus_namamember <> 'NEW'
      AND c.cus_flagmemberkhusus = 'Y'
),
summary AS (
    SELECT
        mr,
        jh_cus_kodemember,
        COUNT(DISTINCT tgl) AS jml_hari_belanja
    FROM trans
    GROUP BY mr, jh_cus_kodemember
),
belanja_mr AS (
    SELECT
        mr,
        SUM(CASE WHEN jml_hari_belanja = 1 THEN 1 ELSE 0 END) AS belanja_sekali,
        SUM(CASE WHEN jml_hari_belanja > 1 THEN 1 ELSE 0 END) AS belanja_repeat,
        COUNT(*) AS total_belanja_member
    FROM summary
    GROUP BY mr
),
get_member AS (
    SELECT cus_nosalesman AS mr, COUNT(*) AS get_member
    FROM tbmaster_customer
    WHERE DATE(cus_tglregistrasi) = current_date
      AND cus_kodeigr = '2G'
    GROUP BY cus_nosalesman
),
po AS (
    SELECT 
        c.cus_nosalesman AS mr,
        COUNT(DISTINCT obi.obi_nopb) AS total_po,
        SUM(obi.obi_ttlorder) AS total_nominal
    FROM tbtr_obi_h obi
    JOIN tbmaster_customer c ON c.cus_kodemember = obi.obi_kdmember
    WHERE obi.obi_tglpb = current_date
      AND (obi.obi_recid IS NULL OR obi.obi_recid NOT LIKE '%B%')
      AND c.cus_kodeigr = '2G'
    GROUP BY c.cus_nosalesman
)

SELECT
    m.mr,
    COALESCE(g.get_member, 0) AS get_member,
    COALESCE(p.total_po, 0) AS total_po,
    COALESCE(p.total_nominal, 0) AS total_nominal,
    COALESCE(b.total_belanja_member, 0) AS total_belanja,
    COALESCE(b.belanja_sekali, 0) AS belanja_sekali,
    COALESCE(b.belanja_repeat, 0) AS belanja_repeat
FROM mr_base m
LEFT JOIN get_member g ON g.mr = m.mr
LEFT JOIN po p ON p.mr = m.mr
LEFT JOIN belanja_mr b ON b.mr = m.mr
ORDER BY m.mr;

";

// Create connection to the database
require_once '../helper/connection.php';

// 3. Eksekusi query menggunakan PDO
    $stmt = $conn->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Contoh cara menampilkan data di tabel HTML
?>