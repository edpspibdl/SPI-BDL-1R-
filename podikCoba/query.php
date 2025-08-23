<?php
require_once '../helper/connection.php';

function getProductAndSalesData(PDO $conn, string $kodePLU): array {
    $data = [];
    $trensale = [];

    if ($kodePLU !== '') {
        try {
            // Query informasi produk utama
            $stmt = $conn->prepare("SELECT DISTINCT ON (prd_prdcd)
                prd_prdcd, 
                prd_deskripsipanjang,
                prd_kategoritoko,
                prd_kodecabang, 
                prd_flaggudang,
                prd_create_dt,
                prd_kodedivisi || '   ' || COALESCE(div_namadivisi, '') || ' - ' || 
                COALESCE(prd_kodekategoribarang, '') || '  ' || COALESCE(kat_namakategori, '') || ' - ' ||
                COALESCE(prd_kodedepartement, '') || '  ' || COALESCE(dep_namadepartement, '') AS div_dept_kat
            FROM tbmaster_prodmast
            LEFT JOIN tbmaster_divisi ON prd_kodedivisi = div_kodedivisi
            LEFT JOIN tbmaster_departement ON prd_kodedepartement = dep_kodedepartement
            LEFT JOIN tbmaster_kategori ON prd_kodekategoribarang = kat_kodekategori
            WHERE prd_prdcd = :kodePLU");
            $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Query tren sales
            $stmt3 = $conn->prepare("SELECT a.*,
                ST_SALES,
                CASE
                    WHEN p.PRD_UNIT = 'KG'
                    AND p.PRD_FRAC  = 1000
                    THEN (ST_SALES*ST_AVGCOST)/ p.prd_frac
                    ELSE ST_SALES *ST_AVGCOST
                END HPP
                FROM TBTR_SALESBULANAN a
                LEFT JOIN TBMASTER_STOCK b ON a.SLS_PRDCD = b.ST_PRDCD
                LEFT JOIN tbmaster_prodmast p ON a.SLS_PRDCD = p.PRD_PRDCD
                WHERE ST_LOKASI ='01'
                AND SLS_PRDCD LIKE :kodePLU");

            $likeKodePLU = substr($kodePLU, 0, 6) . '%';
            $stmt3->bindParam(':kodePLU', $likeKodePLU, PDO::PARAM_STR);
            $stmt3->execute();
            $trensale = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    return ['data' => $data, 'trensale' => $trensale];
}
