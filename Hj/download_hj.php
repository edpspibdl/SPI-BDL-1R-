<?php

require_once '../helper/PHP_XLSXWriter/xlsxwriter.class.php'; // Sesuaikan path dengan lokasi file Anda
require_once '../helper/connection.php';

// Tentukan path untuk menyimpan file sementara
$tempSavePath = 'D:\\LAPORAN PAGI\\UPLOAD HJ\\';

// Memeriksa apakah folder ada, jika tidak, buat folder tersebut
if (!file_exists($tempSavePath)) {
       // Parameter ketiga 'true' berarti akan membuat direktori secara rekursif jika parent directory tidak ada
       // Parameter kedua '0777' adalah izin folder (baca, tulis, eksekusi untuk semua).
       // Anda mungkin ingin menggunakan izin yang lebih ketat seperti 0755 di lingkungan produksi.
       if (!mkdir($tempSavePath, 0777, true)) {
              die("Gagal membuat folder: $tempSavePath. Pastikan PHP memiliki izin untuk membuat folder.");
       }
       error_log("Folder berhasil dibuat: $tempSavePath");
}

// Memeriksa apakah folder dapat ditulis (setelah dipastikan ada atau dibuat)
if (!is_writable($tempSavePath)) {
       die("Folder tidak dapat ditulisi: $tempSavePath. Periksa izin folder.");
}

// Query data dari database
$query = "SELECT 
PLU, 
PRD_DESKRIPSIPANJANG DESK, 
PRD_UNIT, 
PRD_FRAC,
ROUND (PRD_LASTCOST) LCOST,
HRG_NETMM,
PRD_KODEDIVISI DIV,
PRD_KODEDEPARTEMENT DEPT,
PRD_KODEKATEGORIBARANG KAT
FROM
(SELECT PLU,
       HRGMM,
       CBMM,
       HRG_NETMM,
       HRGBIRU,
       CBBIRU,
       HRG_NETBIRU,
       HRGPLA,
       CBPLA,
       HRG_NETPLA
FROM (       
SELECT PRD_PRDCD PLU, 
PRD_LASTCOST LCOST
FROM TBMASTER_PRODMAST) SUB35
LEFT JOIN (
select PLUMM,
       HRGMM,HRGP,
       CBMM,
       HRG_NETMM
from (
select pluN plumm, HRGN,
       HRGP,
       (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END ) HRGMM,
       qty qtymm,
       cb cbmm,
       (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE (HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netmm 
from ( 
select pluN,  HRGN,
       HRGP,
       hrg,qty, 
       sum((jmlcbh*cbh)+(jmlcbd*cbd)) CB 
from ( 
select distinct pluN, 
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC, 
       cbd, 
       cbh,
        HRGN,
       HRGP,
       hrg,
       qty,
       sum(case when pluN like '%0' 
            then (case when COALESCE(MINRPHC,0)<>'0' 
                          then  ( case when (hrg) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            else (case when COALESCE(MINRPHC,0)<>'0' 
                          then  ( case when (hrg*qty) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg*qty)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            end )jmlcbh, 
       sum(case when COALESCE(MINJUALC,0)<>'0' 
              then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                   ( case when qty > MAXJUALC
                          then FLOOR(MAXJUALC/MINJUALC) 
                          else FLOOR(qty/MINJUALC) 
                          end )END )
             else 0 
             end ) jmlcbd 
from (
SELECT PLUN,
       DESK,
	   FRACN,
	   UNIT,
       (CASE WHEN UNIT LIKE '%RCG%'
             THEN ( 1 * MINJUALN )
             ELSE ( FRACN*MINJUALN) 
             END ) QTY,
       HRGN,
       HRGP,
       (CASE WHEN COALESCE(HRGP,0)='0' THEN HRGN ELSE HRGP END ) HRG,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD
FROM (
SELECT PLUN,
       DESK,
       FRACN,
	   UNIT,
       MINJUALN,
       HRGN,
       HRGP
FROM ( 
SELECT PLUP,HRGP,FLAG FROM 
(SELECT DISTINCT PRMD_PRDCD PLUP,
        PRMD_HRGJUAL HRGP,
        (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
              WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
              ELSE 'MERAH' END ) FLAG
FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI SUB36 ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
-- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(CURRENT_DATE +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(CURRENT_DATE -1) 
WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date) ) SUB33 WHERE FLAG='MERAH' ) SUB32
RIGHT JOIN ( 
SELECT PRD_PRDCD PLUN,
       PRD_DESKRIPSIPANJANG DESK,
       PRD_LASTCOST LCOST,
       PRD_FRAC FRACN,
	   PRD_UNIT UNIT,
       PRD_MINJUAL MINJUALN,
       PRD_HRGJUAL HRGN
FROM TBMASTER_PRODMAST ) SUB31 ON PLUN=PLUP ) SUB30
LEFT JOIN (
SELECT PLUC,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD 
FROM (
select cbd_kodepromosi kode, 
       cbd_prdcd pluC, 
      (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
            when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
            else cbh_mintotbelanja 
            end ) minrphC, 
       cbd_minstruk minjuALC, 
       (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
       (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
       cbh_cashback cbh, 
       cbd_cashback cbd 
from tbtr_cashback_hdr left join tbtr_cashback_dtl SUB37 on cbh_kodepromosi=cbd_kodepromosi 
                       left join tbtr_cashback_alokasi SUB38 on cbh_kodepromosi=cba_kodepromosi 
where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', CURRENT_DATE) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', CURRENT_DATE)   AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
  and ( COALESCE(cba_retailer,'0')='1' or COALESCE(cba_silver,'0')='1' or COALESCE(cba_gold1,'0')='1' or COALESCE(cba_gold2,'0')='1' or COALESCE(cba_gold2,'0')='1' ) 
  and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%' 
  and COALESCE(cbd_recordid,'2') <>'1' 
  and  COALESCE(cbd_redeempoint,0)='0' ) SUB29 ) SUB28 ON SUBSTR(PLUN,1,6)||0=PLUC ) SUB34
group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) SUB27 group by pluN, HRGN, HRGP, hrg, qty) SUB26 ) SUB25
ORDER BY PLUMM ) SUB24 ON PLU=PLUMM
LEFT JOIN (
select PLUBIRU,
       HRGBIRU,
       CBBIRU,
       HRG_NETBIRU
from (
select pluN pluBIRU, HRGN,
       HRGP,
       (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG  ELSE ( HRG * QTY) END ) HRGBIRU,
       qty qtyBIRU,
       cb cbBIRU,
       (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG  ELSE ( HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netBIRU 
from ( 
select pluN,  HRGN,
       HRGP,
       hrg,qty, 
       sum(COALESCE((jmlcbh*cbh),0)+COALESCE((jmlcbd*cbd),0)) CB 
from ( 
select distinct pluN, 
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC, 
       cbd, 
       cbh,
       HRGN,
       HRGP,
       hrg,
       qty,
       sum(case when pluN like '%0' 
            then (case when COALESCE(MINRPHC,0)<>'0' 
                          then  ( case when (hrg) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            else (case when COALESCE(MINRPHC,0)<>'0' 
                          then  ( case when (hrg*qty) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg*qty)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            end )jmlcbh, 
       sum(case when COALESCE(MINJUALC,0)<>'0' 
             then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                   ( case when qty > MAXJUALC
                          then FLOOR(MAXJUALC/MINJUALC) 
                          else FLOOR(qty/MINJUALC) 
                          end )END )
             else 0 
             end ) jmlcbd 
from (
SELECT PLUN,
       DESK,
	   FRACN,
	   UNIT,
       (CASE WHEN UNIT LIKE '%RCG%'
             THEN ( 1 * MINJUALN )
             ELSE ( FRACN*MINJUALN) 
             END ) QTY,
       HRGN,
       HRGP,
       COALESCE((CASE WHEN COALESCE(HRGP,'0')='0' THEN HRGN ELSE HRGP END )::INTEGER,0) HRG,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD
FROM (
SELECT PLUN,
       DESK,
       FRACN,
	   UNIT,
       MINJUALN,
       HRGN,
       HRGP
FROM ( 
SELECT PLUP,HRGP,FLAG FROM 
(SELECT DISTINCT PRMD_PRDCD PLUP,
        COALESCE(PRMD_HRGJUAL,'0')::integer HRGP,
        (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
              WHEN (ALK_MEMBER='REGBIRUPLUS' OR ALK_MEMBER='REGBIRU') THEN 'BIRU'
              ELSE 'MERAH' END ) FLAG
FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI SUB39 ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
-- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(CURRENT_DATE +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(CURRENT_DATE -1)
WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date) ) SUB40  WHERE FLAG='BIRU' ) SUB21
RIGHT JOIN ( 
SELECT PRD_PRDCD PLUN,
       PRD_DESKRIPSIPANJANG DESK,
       PRD_FRAC FRACN,
	   PRD_UNIT UNIT,
       PRD_MINJUAL MINJUALN,
       PRD_HRGJUAL HRGN
FROM TBMASTER_PRODMAST) SUB20 ON PLUN=PLUP ) SUB22
LEFT JOIN (
SELECT PLUC,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD 
FROM (
select cbd_kodepromosi kode, 
       cbd_prdcd pluC, 
      (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
            when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
            else cbh_mintotbelanja 
            end ) minrphC, 
       cbd_minstruk minjuALC, 
       (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
       (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
       COALESCE(cbh_cashback,'0')::integer cbh, 
       COALESCE(cbd_cashback,'0')::integer cbd 
from tbtr_cashback_hdr left join tbtr_cashback_dtl SUB40 on cbh_kodepromosi=cbd_kodepromosi 
                       left join tbtr_cashback_alokasi SUB41 on cbh_kodepromosi=cba_kodepromosi 
where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', CURRENT_DATE) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', CURRENT_DATE)  
  and ( COALESCE(cba_REGULER,'0')='1' or COALESCE(cba_REGULER_BIRUPLUS,'0')='1' )  
  and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%'  AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
  and COALESCE(cbd_recordid,'2') <>'1' 
  and  COALESCE(cbd_redeempoint,0)='0' ) SUB19 ) SUB18  ON SUBSTR(PLUN,1,6)||0=PLUC ) SUB17
group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) SUB16 group by pluN, HRGN, HRGP, hrg, qty) SUB15 ) SUB14
ORDER BY PLUBIRU ) SUB41 ON PLU=PLUBIRU
LEFT JOIN (
select PLUPLA,
       HRGPLA,
       CBPLA,
       HRG_NETPLA
from (
select pluN pluPLA, HRGN,
       HRGP,
       (CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END ) HRGPLA,
       qty qtyPLA,
       cb cbPLA,
       (round(((CASE WHEN PLUN LIKE '%0' THEN HRG WHEN PLUN LIKE '%3' THEN HRG ELSE ( HRG * QTY) END )),0)-COALESCE(cb,0)) hrg_netPLA 
from ( 
select pluN,  HRGN,
       HRGP,
       hrg,qty, 
       sum(COALESCE((jmlcbh*cbh),0)+COALESCE((jmlcbd*cbd),0)) CB 
from ( 
select distinct pluN, 
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC, 
       cbd, 
       cbh,
        HRGN,
       HRGP,
       hrg,
       qty,
       sum(case when pluN like '%0' 
            then (case when COALESCE(MINRPHC,'0')<>'0' 
                          then  ( case when (hrg) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            else (case when COALESCE(MINRPHC,'0')<>'0' 
                          then  ( case when (hrg*qty) > MAXRPHC 
                                       then FLOOR(MAXRPHC/MINRPHC) 
                                       else FLOOR((hrg*qty)/MINRPHC) 
                                       end ) 
                          else 0 
                          end )
            end )jmlcbh, 
       sum(case when COALESCE(MINJUALC,'0')<>'0' 
              then  (CASE WHEN UNIT='RCG' THEN FLOOR((QTY*FRACN)/MINJUALC) ELSE
                   ( case when qty > MAXJUALC
                          then FLOOR(MAXJUALC/MINJUALC) 
                          else FLOOR(qty/MINJUALC) 
                          end )END )
             else 0 
             end ) jmlcbd 
from (
SELECT PLUN,
       DESK,
	   FRACN,
	   UNIT,
       (CASE WHEN UNIT LIKE '%RCG%'
             THEN ( 1 * MINJUALN )
             ELSE ( FRACN*MINJUALN) 
             END ) QTY,
       HRGN,
       HRGP,
       COALESCE((CASE WHEN COALESCE(HRGP,'0')='0' THEN HRGN ELSE HRGP END ),0) HRG,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD
FROM (
SELECT PLUN,
       DESK,
       FRACN,
	   UNIT,
       MINJUALN,
       HRGN,
       HRGP
FROM ( 
SELECT PLUP,HRGP,FLAG FROM 
(SELECT DISTINCT PRMD_PRDCD PLUP,
        COALESCE(PRMD_HRGJUAL,0) HRGP,
        (CASE WHEN ALK_MEMBER='PLATINUM' THEN 'PLATINUM'
              WHEN (ALK_MEMBER='REGPLAPLUS' OR ALK_MEMBER='REGPLA') THEN 'BIRU'
              ELSE 'MERAH' END ) FLAG
FROM TBTR_PROMOMD LEFT JOIN TBTR_PROMOMD_ALOKASI SUB42 ON SUBSTR(PRMD_PRDCD,1,6)||0=ALK_PRDCD
-- WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(CURRENT_DATE +1) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(CURRENT_DATE -1)
WHERE DATE_TRUNC('days', PRMD_TGLAWAL)<=(current_date) AND DATE_TRUNC('days', PRMD_TGLAKHIR)>=(current_date)) SUB9 WHERE FLAG='PLATINUM' ) SUB8
RIGHT JOIN ( 
SELECT PRD_PRDCD PLUN,
       PRD_DESKRIPSIPANJANG DESK,
       PRD_FRAC FRACN,
	   PRD_UNIT UNIT,
       PRD_MINJUAL MINJUALN,
       PRD_HRGJUAL HRGN
FROM TBMASTER_PRODMAST) SUB7 ON PLUN=PLUP ) SUB6
LEFT JOIN (
SELECT PLUC,
       MINRPHC,
       MINJUALC,
       MAXJUALC,
       MAXRPHC,
       CBH,
       CBD 
FROM (
select cbd_kodepromosi kode, 
       cbd_prdcd pluC, 
      (case when cbh_minrphprodukpromo < cbh_mintotbelanja then cbh_mintotbelanja 
            when cbh_minrphprodukpromo >0 then cbh_minrphprodukpromo 
            else cbh_mintotbelanja 
            end ) minrphC, 
       cbd_minstruk minjuALC, 
       (case when cbd_maxstruk>'-1' then 999999999 else cbd_maxstruk end) maxjuALC, 
       (case when cbh_maxstrkperhari='999999' then 999999999 else cbh_maxstrkperhari end) maxrphC, 
       COALESCE(cbh_cashback,0) cbh, 
       COALESCE(cbd_cashback,0) cbd 
from tbtr_cashback_hdr left join tbtr_cashback_dtl SUB43 on cbh_kodepromosi=cbd_kodepromosi 
                       left join tbtr_cashback_alokasi SUB44 on cbh_kodepromosi=cba_kodepromosi 
where DATE_TRUNC('days', cbh_tglakhir)>=DATE_TRUNC('days', CURRENT_DATE) and DATE_TRUNC('days', cbh_tglawal)<=DATE_TRUNC('days', CURRENT_DATE)  
   and ( COALESCE(cba_platinum,'0')='1' ) 
  and cbh_namapromosi not like '%UNIQUE%' and cbh_namapromosi not like '%PWP%' and cbh_namapromosi not like '%UNICODE%'  AND CBH_NAMAPROMOSI NOT LIKE 'KLIK%'
  and COALESCE(cbd_recordid,'2') <> '1' 
  and  COALESCE(cbd_redeempoint,'0')='0' ) SUB5 ) SUB4 ON SUBSTR(PLUN,1,6)||0=PLUC ) SUB3 
group by PLUN, MINRPHC, MINJUALC, MAXJUALC, MAXRPHC, cbd, cbh, HRGN, HRGP, hrg, qty ) SUB10 group by pluN, HRGN, HRGP, hrg, qty) SUB11 ) SUB12
ORDER BY PLUPLA ) SUB13 ON PLU=PLUPLA ) SUB2
LEFT JOIN (SELECT * FROM TBMASTER_PRODMAST) SUB1 ON PLU = PRD_PRDCD
ORDER BY 1,2,3,4"; // Ganti dengan query Anda


try {
       $stmt = $conn->prepare($query);
       $stmt->execute();

       // Ambil kolom pertama untuk menentukan header
       $columns = $stmt->fetch(PDO::FETCH_ASSOC);
       if (!$columns) {
              die("Tidak ada data yang ditemukan.");
       }

       // Ambil nama kolom untuk header Excel
       $columnNames = array_map('strtoupper', array_keys($columns));

       // Tentukan nama file dengan tanggal saat ini
       $date = date('Y-m-d');
       $filename = "HJ_SPI_BDL_1R_$date.xlsx";
       $filePath = $tempSavePath . $filename;  // Menentukan lokasi penyimpanan file sementara

       // Inisialisasi objek writer
       $writer = new XLSXWriter();
       $writer->writeSheetHeader('Sheet1', array_combine($columnNames, array_fill(0, count($columnNames), 'string')));

       // Loop untuk menulis setiap row ke Excel
       do {
              $writer->writeSheetRow('Sheet1', $columns);
       } while ($columns = $stmt->fetch(PDO::FETCH_ASSOC));

       // Simpan file sementara di server
       if ($writer->writeToFile($filePath)) {
              // Cek apakah file berhasil disimpan
              if (file_exists($filePath)) {
                     // Log server untuk memastikan file sudah ditemukan
                     error_log("File berhasil disimpan di: $filePath");

                     // Mulai pengunduhan file
                     ob_end_flush(); // Flush output buffer

                     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                     header("Content-Disposition: attachment; filename=\"$filename\"");
                     header('Cache-Control: max-age=0');

                     // Baca file dan kirimkan ke browser
                     readfile($filePath);

                     // Hapus file sementara setelah pengunduhan
                     unlink($filePath);
                     exit;
              } else {
                     // Log error jika file tidak ditemukan
                     error_log("File tidak ditemukan di: $filePath");
                     die("File tidak ditemukan di: $filePath");
              }
       } else {
              // Log error jika gagal menyimpan file
              error_log("Gagal menyimpan file di: $filePath");
              die("Gagal menyimpan file di: $filePath");
       }
} catch (PDOException $e) {
       error_log("Query gagal: " . $e->getMessage());
       die("Query gagal: " . $e->getMessage());
}

$conn = null;
exit;
