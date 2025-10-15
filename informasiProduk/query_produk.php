<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) && $_GET['kodePLU'] !== "" ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
$data = [];

if ($kodePLU !== '') {
    $stmt = $conn->prepare("SELECT DISTINCT ON (prd_prdcd)
        prd_prdcd,
        prd_kodetag,
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
}
?>
