<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "SELECT LPP.* FROM
          (
          SELECT
            PRD_KODEDIVISI    AS DIV,
            PRD_KODEDEPARTEMENT    AS DEPT,
            PRD_KODEKATEGORIBARANG AS KATB,
            DISPLAY_OMI ,
            cast (ST_PRDCD as numeric )            AS PLU,
            PRD_DESKRIPSIPANJANG AS DESKRIPSI,
            PRD_UNIT             AS UNIT,
            PRD_FRAC             AS FRAC,
            prd_kodetag          AS TAG,
            CASE
              WHEN prd_flagigr='Y'      AND prd_flagomi ='Y'   AND prd_flagidm ='Y'
              THEN 'IGR+OMI+IDM'
              WHEN prd_flagigr='Y'      AND prd_flagomi ='Y'   AND prd_flagidm ='N'
              THEN 'IGR+OMI'
              WHEN prd_flagigr='Y'      AND prd_flagidm ='Y' AND prd_flagomi ='N'
              THEN 'IGR+IDM'
              WHEN prd_flagigr='Y'       AND prd_flagomi ='N'  AND prd_flagidm ='N'
              THEN 'IGR ONLY'
              WHEN prd_flagigr='N'      AND prd_flagomi ='Y'   AND prd_flagidm ='N'
              THEN 'OMI ONLY'
              WHEN prd_flagigr='N'      AND prd_flagidm ='Y' AND prd_flagomi ='N'
              THEN 'IDM ONLY'
            ELSE 'XXX'
            END flag ,
            TO_CHAR(ROUND(ST_AVGCOST), 'FM999G999G999G999') AS ACOST_PCS,
            ST_SALDOAKHIR AS LPP_QTY,
            ST_SALDOAKHIR AS LPP_QTY,
CASE
  WHEN PRD_UNIT = 'KG' THEN 
    TO_CHAR(ROUND(CAST((ST_SALDOAKHIR * ST_AVGCOST) / PRD_FRAC AS numeric), 2), 'FM999G999G999G999D00')
  ELSE 
    TO_CHAR(ROUND(CAST(ST_SALDOAKHIR * ST_AVGCOST AS numeric), 2), 'FM999G999G999G999D00')
END AS LPP_RPH
,
            COALESCE(PQTY,0)      AS PLANO_QTY,
          --  COALESCE(OBI_QTYREALISASI,0) AS TMI_QTY,
            CASE WHEN KLIKREAL IS NOT NULL THEN KLIKREAL ELSE 0 END AS PICKING_KLIK,
            CASE WHEN PICKING_OMI IS NOT NULL THEN PICKING_OMI ELSE 0 END AS PICKING_OMI,
            COALESCE(Omi_recid4,0)AS Omi_recid4,
            COALESCE(qty_rom,0)   AS qty_rom ,
            COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)   AS total_plano,
            TO_CHAR((COALESCE(PQTY, 0) + COALESCE(OMI_RECID4, 0)) * ST_AVGCOST, 'FM999G999G999G999D00') AS RP_PLANO,
            (COALESCE(PQTY,0)+COALESCE(OMI_RECID4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0))- COALESCE(ST_SALDOAKHIR,0) AS SEL_QTY,
          (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0))- COALESCE(st_saldoakhir,0))*ST_AVGCOST)  AS sel_rph  ,
            CASE WHEN  (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0)))*ST_AVGCOST)> (  CASE
              WHEN PRD_UNIT='KG'
              THEN (ST_SALDOAKHIR*ST_AVGCOST)/PRD_FRAC
              ELSE ST_SALDOAKHIR *ST_AVGCOST
            END) then 'PLANO>LPP'
            when  (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0)))*ST_AVGCOST)= (  CASE
              WHEN PRD_UNIT='KG'
              THEN (ST_SALDOAKHIR*ST_AVGCOST)/PRD_FRAC
              ELSE ST_SALDOAKHIR *ST_AVGCOST
            END) then 'LPP=PLANO'
            else 'LPP>PLANO' end as Kategori,
            CASE when (DISPLAY_TOKO is null and DISPLAY_OMI is null) then 'TDK ADA PLANO'
            else 'ADA PLANO' end as Keterangan,
            CURRENT_DATE as lvp_create_dt,'SYS' as lvp_create_by,NULL as lvp_modify_dt,NULL as lvp_modify_by,
            CASE WHEN TMI_REAL IS NOT NULL THEN TMI_REAL ELSE 0 END AS PICKING_TMI
          FROM TBMASTER_PRODMAST
          LEFT JOIN (
            SELECT DISTINCT PRC_PLUIGR
            FROM TBMASTER_PRODCRM
          ) AS ProdCRM
          ON ProdCRM.PRC_PLUIGR=PRD_PRDCD
          LEFT JOIN TBMASTER_STOCK
          ON ST_PRDCD = PRD_PRDCD
          LEFT JOIN (
          select
            lks_prdcd as PLU_DISPLAYTOKO,
            lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut as DISPLAY_TOKO
          from tbmaster_lokasi
          where substr(lks_koderak,1,1) IN ('O','R') and substr(lks_tiperak,1,1) <>'S'
          ) AS DisplayToko ON DisplayToko.PLU_DISPLAYTOKO=PRD_PRDCD
          LEFT JOIN (
          select
            lks_prdcd as PLU_DISPLAYOMI,
            lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut as DISPLAY_OMI
          from tbmaster_lokasi
          where substr(lks_koderak,1,1) IN ('D') and substr(lks_tiperak,1,1) <>'S'
          ) AS DisplayOMI ON DisplayOMI.PLU_DISPLAYOMI=PRD_PRDCD
          LEFT JOIN
          (SELECT LKS_PRDCD, SUM(LKS_QTY) AS PQTY FROM TBMASTER_LOKASI GROUP BY LKS_PRDCD) AS LOKASI ON PRD_PRDCD=LKS_PRDCD
          LEFT JOIN
          (SELECT SUBSTR(pbo_pluigr,1,6)
            ||'0'                AS PLUPB,
            SUM(pbo_qtyrealisasi)AS Omi_recid4
          FROM tbmaster_pbomi
          LEFT JOIN tbtr_omikoli
          ON okl_nokoli                         =pbo_nokoli
          WHERE TO_CHAR(pbo_create_dt,'DD/MM/YYYY') = TO_CHAR(CURRENT_DATE,'DD/MM/YYYY')
          AND pbo_recordid                      ='4'
          AND okl_nokoli                       IS NULL
          GROUP BY SUBSTR(pbo_pluigr,1,6)
            ||'0'
          ) as omikoli ON PLUPB=PRD_PRDCD
          LEFT JOIN
            (SELECT rom_prdcd AS plu_rom,
              SUM(rom_qty)    AS qty_rom
            FROM tbtr_returomi
            WHERE DATE_TRUNC('day',rom_tgldokumen) >= DATE_TRUNC('day',CURRENT_DATE - 1)
            AND rom_qty <> 0
            GROUP BY rom_prdcd
            ) AS ReturOMI
          ON prd_prdcd=plu_rom
          --INTRANSIT KLIK
          LEFT JOIN
          (select substr(obi_prdcd,1,6)||'0' as PLUKLIK,sum(obi_qtyorder) as KLIKORDER,sum(obi_qtyrealisasi) as KLIKREAL
          from tbtr_obi_d
          LEFT JOIN TBTR_OBI_H ON TBTR_OBI_H.OBI_NOTRANS=TBTR_OBI_D.OBI_NOTRANS  AND TBTR_OBI_H.OBI_TGLTRANS=TBTR_OBI_D.OBI_TGLTRANS
          where tbtr_obi_h.obi_recid in ('1','2','3') and obi_qtyrealisasi>0
          and DATE_TRUNC('day',tbtr_obi_h.obi_tgltrans)>=DATE_TRUNC('day',CURRENT_DATE - 31) AND OBI_NOPB NOT LIKE '%TMI%'
          GROUP BY substr(obi_prdcd,1,6)||'0'
          ) AS Klik
          ON PLUKLIK=PRD_PRDCD
          -------PICKING TMI, ITEM TIDAK MASUK INTRANSIT TAPI SUDAH CHEKER DAN BELUM STRUK RECID YANG KAMI AMBIL (3,4,5)-----------------
          LEFT JOIN
          (select substr(obi_prdcd,1,6)||'0' as PLUTMI,sum(obi_qtyorder) as TMI_ORDER,sum(obi_qtyrealisasi) as TMI_REAL
          from tbtr_obi_d
          left join tbtr_obi_h on tbtr_obi_h.obi_notrans=tbtr_obi_d.obi_notrans  and tbtr_obi_h.obi_tgltrans=tbtr_obi_d.obi_tgltrans
          where tbtr_obi_h.obi_recid in ('3','4','5','7') and obi_qtyrealisasi>0
          and DATE_TRUNC('day',tbtr_obi_h.obi_tgltrans)>=DATE_TRUNC('day',CURRENT_DATE - 31)  AND OBI_NOPB LIKE '%TMI%'
          GROUP BY substr(obi_prdcd,1,6)||'0'
          ) AS TMI
          ON PLUTMI=PRD_PRDCD
          --INTRANSIT OMI
          LEFT JOIN
          (
          select substr(pbo_pluigr,1,6)||'0' as PLUOMI,sum(pbo_qtyrealisasi) as PICKING_OMI
          from tbmaster_pbomi
          left join tbtr_realpb on pbo_pluigr=rpb_plu2 and pbo_kodeomi=rpb_kodeomi and pbo_nopb=rpb_nodokumen and pbo_nokoli=rpb_nokoli
          where pbo_recordid in ('4') and rpb_nokoli is null and DATE_TRUNC('day',pbo_create_dt)>=DATE_TRUNC('day',CURRENT_DATE - 31)
          group by substr(pbo_pluigr,1,6)||'0'
          ) AS PickingOMI
          ON PLUOMI = PRD_PRDCD
          WHERE ST_LOKASI              ='01'
          AND (PRD_KODETAG NOT         IN ('X','N','O') or PRD_KODETAG is null)
          AND PRD_KODEDEPARTEMENT NOT IN ('31','32','42')
          AND PRD_UNIT NOT IN ('KG')
          AND ST_AVGCOST<>'0'
          AND prd_prdcd IN (SELECT DISTINCT st_prdcd FROM TBMASTER_STOCK
          LEFT JOIN tbmaster_lokasi ON TBMASTER_STOCK.ST_PRDCD=tbmaster_lokasi.LKS_PRDCD AND st_lokasi='01' AND ST_SALDOAKHIR<>'0')
          ORDER BY ((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0))-ST_SALDOAKHIR)*ST_AVGCOST DESC
          ) AS LPP";

  $stmt = $conn->query($query); // Eksekusi query dengan PDO

} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>

<!-- Styling untuk Tabel -->
<style>
  /* Styling untuk tabel */
  #table-1 {
    width: 100%;
    table-layout: auto;
    /* Menyesuaikan lebar kolom dengan isi konten */
    border-collapse: collapse;
    /* Menggabungkan border antar sel */
  }

  #table-1 th,
  #table-1 td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
    /* Membuat border untuk semua cell */
  }

  #table-1 th {
    background-color: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #333;
    /* Menambahkan pembatas tebal di bawah header */
  }

  #table-1 td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  /* Styling untuk kolom DESK */
  #table-1 .desk-column {
    word-wrap: break-word;
    /* Memastikan teks di kolom DESK membungkus */
    white-space: normal;
    /* Teks dapat membungkus pada kolom DESK */
    max-width: 300px;
    /* Membatasi lebar maksimum kolom DESK */
  }

  /* Responsif untuk tabel */
  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>LPP VS PLANO </h1>
    <a href="../LaporanLaporan/index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th style="text-align:left">No</th>
                  <th style="text-align:left">DIV</th>
                  <th style="text-align:left">DEPT</th>
                  <th style="text-align:left">KATB</th>
                  <th style="text-align:left">DISPLAY</th>
                  <th style="text-align:left">PLU</th>
                  <th style="text-align:left">DESKRIPSI</th>
                  <th style="text-align:left">UNIT</th>
                  <th style="text-align:left">FRAG</th>
                  <th style="text-align:left">TAG</th>
                  <th style="text-align:left">FLAG</th>
                  <th style="text-align:left">ACOST_PCS</th>
                  <th style="text-align:left">LPP_QTY</th>
                  <th style="text-align:left">LPP_RPH</th>
                  <th style="text-align:left">PLANO_QTY</th>
                  <th style="text-align:left">total_plano</th>
                  <th style="text-align:left">RP_PLANO</th>
                  <th style="text-align:left">SEL_QTY</th>
                  <th style="text-align:left">sel_rph</th>
                  <th style="text-align:left">Kategori</th>
                  <th style="text-align:left">Keterangan</th>
                  <th style="text-align:left">PICKING_TMI</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $nomor = 1; // Pastikan variabel $nomor diinisialisasi
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr class='s'>";
                  echo "<td>" . $nomor . "</td>";
                  echo "<td>" . htmlspecialchars($row['div']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['dept']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['katb']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['display_omi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['plu']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['frac']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['tag']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['flag']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['acost_pcs']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['lpp_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['lpp_rph']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['plano_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['total_plano']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['rp_plano']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['sel_qty']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['sel_rph']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['picking_tmi']) . "</td>";
                  echo "</tr>";
                  $nomor++;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: false,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{
        targets: [],
        orderable: false
      }],
      buttons: [{
          extend: 'copy',
          text: 'Copy' // Ubah teks tombol jika diperlukan
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'LPP_VS_PLANO_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
          title: null
        }

      ],
      dom: 'Bfrtip' // Posisi tombol
    });

    // Tambahkan tombol ke wrapper tabel
    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  $(document).ready(function() {
    // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
    var table = $('#table-1').DataTable();
    table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
    $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
  });
</script>