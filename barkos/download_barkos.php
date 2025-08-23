<?php
// --- TINGKATKAN BATAS MEMORI & AKTIFKAN ERROR ---
ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR PENGATURAN ---

require_once '../helper/connection.php';
require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\DOWNLOAD BARKOS\\';
$input_value = 1;

try {
          // 1. Periksa dan buat folder
          if (!file_exists($tempSavePath)) {
                    if (!mkdir($tempSavePath, 0777, true)) {
                              die("Fatal Error: Gagal membuat folder: $tempSavePath. Periksa izin.");
                    }
          }
          if (!is_writable($tempSavePath)) {
                    die("Fatal Error: Folder tidak dapat ditulisi: $tempSavePath. Periksa izin.");
          }

          // 2. Eksekusi query
          $bln = date("m");
          $bln_1 = date("m", strtotime("-3 month"));
          $bln_2 = date("m", strtotime("-2 month"));
          $bln_3 = date("m", strtotime("-1 month"));
          $bln1 = sprintf("%02s", $bln_1);
          $bln2 = sprintf("%02s", $bln_2);
          $bln3 = sprintf("%02s", $bln_3);

          $query = "
        SELECT
        hgb.kodesup AS kode_supplier,
        sup.sup_namasupplier AS nama_supplier,
        rsl.rsl_prdcd AS plu,
        pm.prd_deskripsipanjang AS desk,
        pm.prd_kodetag as tag,
        rsl.avgsalesqty AS avg_sales,
        rsl.avgsalesrph AS avg_rph,
        rsl.sales_1,
        rsl.sales_2,
        rsl.sales_3,
        ROUND(COALESCE(CASE WHEN slv.po != 0 THEN (slv.bpb / slv.po) * 100 ELSE 0 END, 0), 0) || ' %' AS SL,
        COALESCE(st.st_saldoakhir, 0) AS saldo_akhir,
        COALESCE(ROUND(rsl.avgsalesqty / 30 * :input_value, 2), 0) AS SPD,  
        COALESCE(
            ROUND(
                CASE
                    WHEN rsl.avgsalesqty / 30 * :input_value = 0 THEN 0
                    ELSE COALESCE(st.st_saldoakhir, 0) / (rsl.avgsalesqty / 30 * :input_value)
                END, 2
            ), 0.00
        ) AS DSI,
        CASE WHEN POOUT IS NULL THEN 'TIDAK ADA PO' ELSE 'ADA PO' END KET_PO,
        mstd.BPB_TERAKHIR,
        CASE
            WHEN COALESCE(st.st_saldoakhir, 0) = 0 OR COALESCE(st.st_saldoakhir, 0) < COALESCE(ROUND(rsl.avgsalesqty / 30 * :input_value, 2), 0) THEN 'Barkos'
            ELSE 'Aman'
        END AS keterangan
    FROM
        tbmaster_prodmast pm
        LEFT JOIN (
            SELECT
                rsl_prdcd,
                ROUND(SUM(RSL_qty_$bln1)) AS sales_1,
                ROUND(SUM(RSL_qty_$bln2)) AS sales_2,
                ROUND(SUM(RSL_qty_$bln3)) AS sales_3,
                ROUND(
                    (COALESCE(SUM(RSL_qty_$bln1), 0) + COALESCE(SUM(RSL_qty_$bln2), 0) + COALESCE(SUM(RSL_qty_$bln3), 0)) / 3
                ) AS avgsalesqty,
                ROUND(
                    (COALESCE(SUM(RSL_rph_$bln1), 0) + COALESCE(SUM(RSL_rph_$bln2), 0) + COALESCE(SUM(RSL_rph_$bln3), 0)) / 3
                ) AS avgsalesrph
            FROM
                tbtr_rekapsalesbulanan
            GROUP BY
                rsl_prdcd
        ) rsl ON pm.prd_prdcd = rsl.rsl_prdcd
        LEFT JOIN (
            SELECT
                hgb_prdcd,
                hgb_kodesupplier AS kodesup
            FROM
                tbmaster_hargabeli
            WHERE
                hgb_tipe = '2'
        ) hgb ON pm.prd_prdcd = hgb.hgb_prdcd
        LEFT JOIN (
            SELECT
                st_prdcd,
                st_saldoakhir
            FROM
                tbmaster_stock
            WHERE
                st_lokasi = '01'
        ) st ON pm.prd_prdcd = st.st_prdcd
        LEFT JOIN tbmaster_supplier sup ON hgb.kodesup = sup.sup_kodesupplier
        LEFT JOIN (
            SELECT 
                MSTD_PRDCD,
                MAX(MSTD_TGLDOC) AS BPB_TERAKHIR
            FROM TBTR_MSTRAN_D
            WHERE MSTD_TYPETRN = 'B'
            GROUP BY MSTD_PRDCD
        ) mstd ON pm.prd_prdcd = mstd.MSTD_PRDCD
          LEFT JOIN (
        SELECT
            sl_prdcd_po AS slv_prdcd,
            SUM(sl_qty_po) AS po,
            SUM(sl_qty_bpb) AS bpb
        FROM (
            SELECT
                po.tpod_prdcd AS sl_prdcd_po,
                po.tpod_qtypo AS sl_qty_po,
                COALESCE(mst.mstd_qty, 0) AS sl_qty_bpb
            FROM tbtr_po_d po
            LEFT JOIN tbtr_mstran_d mst ON po.tpod_prdcd = mst.mstd_prdcd
                                      AND po.tpod_nopo = mst.mstd_nopo
            WHERE po.tpod_prdcd IS NOT NULL
        ) subquery
        GROUP BY sl_prdcd_po
    ) slv ON pm.prd_prdcd = slv.slv_prdcd
      LEFT JOIN (
        SELECT tpod_prdcd,
                SUM(tpod_qtypo)  AS POOUT,
                COUNT(tpod_nopo) AS JLHPOUT
        FROM tbtr_po_d
        WHERE tpod_nopo IN (
            SELECT tpoh_nopo
            FROM tbtr_po_h
            WHERE tpoh_recordid IS NULL
              AND (tpoh_tglpo + INTERVAL '1 day' * tpoh_jwpb) >= CURRENT_DATE
        )
        GROUP BY tpod_prdcd
    ) po ON pm.prd_prdcd = po.tpod_prdcd
    WHERE
        pm.prd_recordid IS NULL
        AND pm.prd_prdcd LIKE '%0'
        AND rsl.rsl_prdcd IS NOT NULL
        AND (pm.prd_kodetag IS NULL OR pm.prd_kodetag NOT IN ('H','A','N','O','X','T','G'))
        AND pm.prd_prdcd NOT IN (
            '0002310','0003030','0003040','0005090','0009170','0009190','0009380','0009480','0011280',
            '0030600','0030610','0030650','0030680','0030700','0032020','0032070','0033500','0033560',
            '0033570','0033580','0047180','0053250','0054950','0203710','0346810','0365020','0365030',
            '0365050','425130','0425150','450300','481890','481900','539960','543000','757750','814340',
            '00819360','0873130','0873140','0873150','874920','874930','1077740','1084080','1112220',
            '1116230','1116240','1116250','1116260','1116290','1116460','1117650','1117690','1130140',
            '1144270','1144730','1144920','1153710','1153720','1154480','1163720','1163730','1163740',
            '1163750','1166950','1178040','1178160','1178610','1178620','1178680','1178690','1209170',
            '1211940','1220090','1220100','1236280','1239240','1239250','1239260','1246360','1255020',
            '1255030','1257970','1257980','1259590','1259910','1269280','1271480','1271490','1271520',
            '1271530','1281800','1282000','1301700','1311920','1311940','1311990','1312010','1312050',
            '1312060','1312910','1324120','1332970','1347320','1347330','1350380','1356510','1363040',
            '1382230','1393250','1393460','1402850','1403680','1411160','1430360','1430370','1430380',
            '1430390','1442000','1476440','1479580','1479730','1481790','1482840','1498000','1498220',
            '1498270','1510880','1515080','1515990','1524470','1524620','1527470','1557670','1635490',
            '0505840','0646900','1386390','1411370','1418130','1511330','1556260','1557650','1703800',
            '0030180','0030200','1664840','1652500','1147320','1241640','1345920','1377990','1378030',
            '1500370','1579800','1515970','1515950','1674540','1372000','1674560','0875020','1152780',
            '1386150'
        )
    ";

          $stmt = $conn->prepare($query);
          $stmt->bindValue(':input_value', $input_value);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (empty($result)) {
                    die("Tidak ada data yang ditemukan dari database. Laporan tidak dibuat.");
          }

          // 3. Pisahkan data
          $all = $result;
          $barkos = array_filter($result, fn($r) => $r['keterangan'] == 'Barkos');
          $rekap = array_filter($result, fn($r) => $r['keterangan'] == 'Aman');

          // 4. Buat file Excel di server
          $writer = new XLSXWriter();

          function tulisSheet($writer, $sheetName, $data)
          {
                    if (empty($data)) return;
                    $header = array_combine(array_keys($data[0]), array_fill(0, count($data[0]), 'string'));
                    $writer->writeSheetHeader($sheetName, $header);
                    foreach ($data as $row) {
                              $writer->writeSheetRow($sheetName, array_values($row));
                    }
          }

          tulisSheet($writer, 'ALL', $all);
          tulisSheet($writer, 'BARKOS', $barkos);
          tulisSheet($writer, 'REKAP', $rekap);

          // Tentukan nama file dan path lengkap
          $filename = 'LAPORAN_BARKOS_' . date('Ymd_His') . '.xlsx';
          $fullpath = $tempSavePath . $filename;

          // Simpan file
          if ($writer->writeToFile($fullpath)) {
                    echo "Laporan berhasil dibuat di: " . $fullpath;
          } else {
                    die("Fatal Error: Gagal menyimpan file di: " . $fullpath);
          }
} catch (PDOException $e) {
          die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
