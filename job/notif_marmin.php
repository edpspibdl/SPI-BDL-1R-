<?php
require_once '../helper/connection.php'; // Pastikan koneksi PDO di sini valid

// Query untuk mendapatkan produk dengan margin negatif
$sql = <<<SQL
SELECT PRD_KODEDIVISI DIV,
       PRD_PRDCD PLU,
       PRD_DESKRIPSIPANJANG DESKRIPSI,
       PRD_FRAC FRAC,
       PRD_UNIT UNIT,
       PRD_KODETAG TAG,
       ST_SALDOAKHIR LPP,
       PRD_HRGJUAL HRG,
       PRMD_HRGJUAL HRG_P,
       LCOST LCOST_PCS,
       ACOST ACOST_PCS,
       ACOST_INCLUDE A_COST_INC,
       MARGIN_A MARGIN,
       MARGIN_L MARGIN_LCOST,
       MARGIN_A_MD,
       MARGIN_L_MD
FROM (
    -- Subquery hitung margin
    SELECT PRD_KODEDIVISI,
           PRD_PRDCD,
           PRD_DESKRIPSIPANJANG,
           PRD_FRAC,
           PRD_UNIT,
           PRD_KODETAG,
           ST_SALDOAKHIR,
           PRD_HRGJUAL,
           PRMD_HRGJUAL,
           LCOST,
           ACOST,
           ACOST_INCLUDE,
           MARGIN_A,
           MARGIN_L,
           CASE
               WHEN PRD_UNIT='KG'
               THEN (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
               WHEN COALESCE(prd_flagbkp1,'T') ='Y' AND COALESCE(prd_flagbkp2,'T') ='Y'
               THEN (((PRMD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
               ELSE (((PRMD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
           END AS MARGIN_A_MD,
           CASE
               WHEN PRD_UNIT='KG'
               THEN (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRMD_HRGJUAL)*100)
               WHEN COALESCE(prd_flagbkp1,'T') ='Y' AND COALESCE(prd_flagbkp2,'T') ='Y'
               THEN (((PRMD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRMD_HRGJUAL/1.11)*100)
               ELSE (((PRMD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRMD_HRGJUAL)*100)
           END AS MARGIN_L_MD
    FROM (
        -- Subquery join produk & stock
        SELECT prd.PRD_KODEDIVISI,
               prd.PRD_PRDCD,
               prd.PRD_DESKRIPSIPANJANG,
               prd.PRD_FRAC,
               prd.PRD_UNIT,
               prd.PRD_KODETAG,
               stk.ST_SALDOAKHIR,
               prd.PRD_HRGJUAL,
               stk.ST_LASTCOST,
               stk.ST_AVGCOST,
               prd.prd_flagbkp1,
               prd.prd_flagbkp2,
               CASE WHEN PRD_UNIT='KG' THEN (ST_LASTCOST*PRD_FRAC)/1000 ELSE ST_LASTCOST*PRD_FRAC END AS LCOST,
               CASE WHEN PRD_UNIT='KG' THEN (ST_AVGCOST*PRD_FRAC)/1000 ELSE ST_AVGCOST*PRD_FRAC END AS ACOST,
               CASE WHEN PRD_UNIT='KG' THEN ((ST_AVGCOST*PRD_FRAC)/1000)*1.11 ELSE (ST_AVGCOST*PRD_FRAC)*1.11 END AS ACOST_INCLUDE,
               -- Hitung margin
               CASE
                   WHEN PRD_UNIT='KG'
                   THEN (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
                   WHEN COALESCE(prd_flagbkp1,'T') ='Y' AND COALESCE(prd_flagbkp2,'T') ='Y'
                   THEN (((PRD_HRGJUAL/1.11)-(ST_AVGCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
                   ELSE (((PRD_HRGJUAL-(ST_AVGCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
               END AS MARGIN_A,
               CASE
                   WHEN PRD_UNIT='KG'
                   THEN (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC/1000))/PRD_HRGJUAL)*100)
                   WHEN COALESCE(prd_flagbkp1,'T') ='Y' AND COALESCE(prd_flagbkp2,'T') ='Y'
                   THEN (((PRD_HRGJUAL/1.11)-(ST_LASTCOST*PRD_FRAC))/(PRD_HRGJUAL/1.11)*100)
                   ELSE (((PRD_HRGJUAL-(ST_LASTCOST*PRD_FRAC))/PRD_HRGJUAL)*100)
               END AS MARGIN_L
        FROM (
            SELECT PRD_PRDCD, PRD_KODEDIVISI, PRD_DESKRIPSIPANJANG, PRD_FRAC, PRD_UNIT, PRD_KODETAG,
                   PRD_HRGJUAL, prd_flagbkp1, prd_flagbkp2
            FROM tbmaster_prodmast
        ) prd
        LEFT JOIN (
            SELECT ST_PRDCD, ST_SALDOAKHIR, ST_LASTCOST, ST_AVGCOST
            FROM tbmaster_stock
            WHERE st_lokasi='01'
        ) stk ON prd.PRD_PRDCD = stk.ST_PRDCD
        WHERE COALESCE(prd.PRD_KODETAG,'0') NOT IN ('N','X','Z') AND ST_SALDOAKHIR <> 0
    ) HRG_N
    LEFT JOIN (
        SELECT PRMD_PRDCD AS PLUMD, PRMD_HRGJUAL
        FROM TBTR_PROMOMD
        WHERE CURRENT_DATE BETWEEN DATE(PRMD_TGLAWAL) AND DATE(PRMD_TGLAKHIR)
    ) PRMD ON HRG_N.PRD_PRDCD = PRMD.PLUMD
) MARGINM
WHERE (MARGIN_A<0 OR MARGIN_A_MD<0);
SQL;

try {
    $stmt = $conn->query($sql);
    $data_margin = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($data_margin)) {
        // --- Bangun pesan WA ---
        $pesan = "⚠️ *REPORT PRODUK MARGIN NEGATIF*\n\n";
        $pesan .= "Tanggal Cek: " . date('d-m-Y H:i:s') . "\n";
        $pesan .= "-----------------------------------\n";

        $counter = 1;
        foreach ($data_margin as $row) {
            $pesan .= "$counter. PLU: *" . $row['PLU'] . "*\n"
                    . "   Deskripsi: " . $row['DESKRIPSI'] . "\n"
                    . "   Divisi: " . $row['DIV'] . "\n"
                    . "   Unit: " . $row['UNIT'] . "\n"
                    . "   LPP: " . $row['LPP'] . "\n"
                    . "   Harga: " . $row['HRG'] . "\n"
                    . "   Harga Promo: " . $row['HRG_P'] . "\n"
                    . "   Margin: " . number_format($row['MARGIN_A'],2) . "%\n"
                    . "   Margin MD: " . number_format($row['MARGIN_A_MD'],2) . "%\n\n";
            $counter++;
        }

        $pesan .= "-----------------------------------\nMohon segera ditindaklanjuti!\n";

        $targets = [
            "6282180488184",
            "628972569035"
        ];

        $url = "https://api.fonnte.com/send";
        $token = "KKVJZ5ZraZxuJRxW5Hsg";

        foreach ($targets as $target) {
            $curl = curl_init();
            $postData = [
                'target' => $target,
                'message' => $pesan,
            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($postData),
                CURLOPT_HTTPHEADER => [
                    "Authorization: $token",
                    "Content-Type: application/x-www-form-urlencoded",
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "❌ Gagal mengirim notifikasi ke $target: " . $err . "<br>";
            } else {
                echo "✅ Notifikasi dikirim ke: $target. Respon API: " . $response . "<br>";
            }
        }

    } else {
        echo "Tidak ada produk dengan margin negatif. Tidak ada notifikasi yang dikirim.<br>";
    }

} catch (PDOException $e) {
    die("Error mengambil data dari database: " . $e->getMessage());
}
?>
