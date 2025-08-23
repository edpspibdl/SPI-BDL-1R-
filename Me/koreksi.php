<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query pertama untuk mengambil data dari tabel tbreport_setting_pagi_hari
try {
    $query1 = "SELECT *
FROM (
    SELECT 
        lpp_prdcd, 
        prd_frac,
        prd_deskripsipendek,
        ROUND(lpp_qtybegbal, 2) AS SALDOAWALQTY,
        ROUND(COALESCE(lpp_rphbegbal, 0), 2) AS SALDOAWALRp, 
        ROUND(lpp_qtyintransit, 2) AS INTRANSITQTY, 
        ROUND(lpp_rphintransit, 2) AS INTRANSITRp,
        ROUND((
            lpp_rphakhir - (
                (COALESCE(lpp_rphbegbal, 0) + lpp_rphbeli + lpp_rphtrmcb + lpp_rphretursales + lpp_rphrepack + lpp_rphlainin)
                - (lpp_rphsales + lpp_rphkirim + lpp_rphprepacking + lpp_rphhilang + lpp_rphlainout) 
                + lpp_rphintransit + lpp_rphadj
            ) - LPP_RPH_SELISIH_SOIC
        ), 2) AS KOREKSI,
        ROUND(lpp_qtybeli, 2) AS BPBQTY, 
        ROUND(lpp_rphbeli, 2) AS BPBRp,  
        ROUND(lpp_qtytrmcb, 2) AS TACQTY,
        ROUND(lpp_rphtrmcb, 2) AS TACRp,
        ROUND(lpp_qtyretursales, 2) AS RETURIDMQTY, 
        ROUND(lpp_rphretursales, 2) AS RETURIDMRp,
        ROUND(lpp_qtyrepack, 2) AS REPACKINQTY, 
        ROUND(lpp_rphrepack, 2) AS REPACKINRp,
        ROUND(lpp_qtylainin, 2) AS LAININQTY, 
        ROUND(lpp_rphlainin, 2) AS LAININRp,
        ROUND(lpp_qtysales, 2) AS SALESQTY, 
        ROUND(lpp_rphsales, 2) AS SALESRp,
        ROUND(lpp_qtykirim, 2) AS SJQTY, 
        ROUND(lpp_rphkirim, 2) AS SJRp,
        ROUND(lpp_qtyprepacking, 2) AS REPACKOUTQTY, 
        ROUND(lpp_rphprepacking, 2) AS REPACKOUTRp,
        ROUND(lpp_qtyhilang, 2) AS HILANGQTY, 
        ROUND(lpp_rphhilang, 2) AS HILANGRp,
        ROUND(lpp_qtylainout, 2) AS LAINOUTQTY, 
        ROUND(lpp_rphlainout, 2) AS LAINOUTRp,
        ROUND(LPP_RPH_SELISIH_SO, 2) AS SELISIH_SO,
        ROUND(lpp_qtyadj, 2) AS ADJUSQTY, 
        ROUND(lpp_rphadj, 2) AS ADJUSRp,
        ROUND(lpp_qtyakhir, 2) AS SALDOAKHIRQTY, 
        ROUND(lpp_rphakhir, 2) AS SALDOAKHIRRp,
        ROUND(lpp_avgcost, 2) AS ACOSTLPP
    FROM tbtr_lpp
    LEFT JOIN tbmaster_prodmast ON tbmaster_prodmast.prd_prdcd = tbtr_lpp.lpp_prdcd
    WHERE TO_CHAR(lpp_tgl1, 'MM-YYYY') = TO_CHAR(CURRENT_DATE, 'MM-YYYY')
) AS SUBQUERY
WHERE KOREKSI < -1000
ORDER BY KOREKSI ASC
LIMIT 5;
";
    $stmt1 = $conn->prepare($query1);
    $stmt1->execute();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Query kedua untuk mengambil data dari tabel job_log_all
try {
    $query2 = "SELECT *
FROM (
    SELECT 
        lpp_prdcd, 
        prd_frac,
        prd_deskripsipendek,
        lpp_qtybegbal AS SALDOAWALQTY,
        COALESCE(lpp_rphbegbal, 0) AS SALDOAWALRp, 
        lpp_qtyintransit AS INTRANSITQTY, 
        lpp_rphintransit AS INTRANSITRp,
        (
            lpp_rphakhir - (
                (COALESCE(lpp_rphbegbal, 0) + lpp_rphbeli + lpp_rphtrmcb + lpp_rphretursales + lpp_rphrepack + lpp_rphlainin)
                - (lpp_rphsales + lpp_rphkirim + lpp_rphprepacking + lpp_rphhilang + lpp_rphlainout) 
                + lpp_rphintransit + lpp_rphadj
            ) - LPP_RPH_SELISIH_SOIC
        ) AS KOREKSI,
        lpp_qtybeli AS BPBQTY, 
        lpp_rphbeli AS BPBRp,  
        lpp_qtytrmcb AS TACQTY,
        lpp_rphtrmcb AS TACRp,
        lpp_qtyretursales AS RETURIDMQTY, 
        lpp_rphretursales AS RETURIDMRp,
        lpp_qtyrepack AS REPACKINQTY, 
        lpp_rphrepack AS REPACKINRp,
        lpp_qtylainin AS LAININQTY, 
        lpp_rphlainin AS LAININRp,
        lpp_qtysales AS SALESQTY, 
        lpp_rphsales AS SALESRp,
        lpp_qtykirim AS SJQTY, 
        lpp_rphkirim AS SJRp,
        lpp_qtyprepacking AS REPACKOUTQTY, 
        lpp_rphprepacking AS REPACKOUTRp,
        lpp_qtyhilang AS HILANGQTY, 
        lpp_rphhilang AS HILANGRp,
        lpp_qtylainout AS LAINOUTQTY, 
        lpp_rphlainout AS LAINOUTRp,
        LPP_RPH_SELISIH_SO AS SELISIH_SO,
        lpp_qtyadj AS ADJUSQTY, 
        lpp_rphadj AS ADJUSRp,
        lpp_qtyakhir AS SALDOAKHIRQTY, 
        lpp_rphakhir AS SALDOAKHIRRp,
        lpp_avgcost AS ACOSTLPP
    FROM tbtr_lpp
    LEFT JOIN tbmaster_prodmast ON tbmaster_prodmast.prd_prdcd = tbtr_lpp.lpp_prdcd
WHERE TO_CHAR(lpp_tgl1, 'MM-YYYY') = TO_CHAR(CURRENT_DATE, 'MM-YYYY' )
-- WHERE TO_CHAR(lpp_tgl1, 'MM-YYYY') = TO_CHAR(CURRENT_DATE - INTERVAL '1 months', 'MM-YYYY') KALAU ME TGL 1
) AS SUBQUERY
 WHERE KOREKSI > 500
ORDER BY KOREKSI DESC LIMIT 5;
";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

try {
    $query3 = "select sum(koreksi) aa from (SELECT *
FROM (
    SELECT 
        lpp_prdcd, 
        prd_frac,
        prd_deskripsipendek,
        lpp_qtybegbal AS SALDOAWALQTY,
        COALESCE(lpp_rphbegbal, 0) AS SALDOAWALRp, 
        lpp_qtybeli AS BPBQTY, 
        lpp_rphbeli AS BPBRp,  
        lpp_qtytrmcb AS TACQTY,
        lpp_rphtrmcb AS TACRp,
        lpp_qtyretursales AS RETURIDMQTY, 
        lpp_rphretursales AS RETURIDMRp,
        lpp_qtyrepack AS REPACKINQTY, 
        lpp_rphrepack AS REPACKINRp,
        lpp_qtylainin AS LAININQTY, 
        lpp_rphlainin AS LAININRp,
        lpp_qtysales AS SALESQTY, 
        lpp_rphsales AS SALESRp,
        lpp_qtykirim AS SJQTY, 
        lpp_rphkirim AS SJRp,
        lpp_qtyprepacking AS REPACKOUTQTY, 
        lpp_rphprepacking AS REPACKOUTRp,
        lpp_qtyhilang AS HILANGQTY, 
        lpp_rphhilang AS HILANGRp,
        lpp_qtylainout AS LAINOUTQTY, 
        lpp_rphlainout AS LAINOUTRp,
        lpp_qtyintransit AS INTRANSITQTY, 
        lpp_rphintransit AS INTRANSITRp,
        LPP_RPH_SELISIH_SO AS SELISIH_SO,
        lpp_qtyadj AS ADJUSQTY, 
        lpp_rphadj AS ADJUSRp,
        lpp_qtyakhir AS SALDOAKHIRQTY, 
        lpp_rphakhir AS SALDOAKHIRRp,
        lpp_avgcost AS ACOSTLPP,
        (
            lpp_rphakhir - (
                (COALESCE(lpp_rphbegbal, 0) + lpp_rphbeli + lpp_rphtrmcb + lpp_rphretursales + lpp_rphrepack + lpp_rphlainin)
                - (lpp_rphsales + lpp_rphkirim + lpp_rphprepacking + lpp_rphhilang + lpp_rphlainout) 
                + lpp_rphintransit + lpp_rphadj
            ) - LPP_RPH_SELISIH_SOIC
        ) AS KOREKSI
    FROM tbtr_lpp
    LEFT JOIN tbmaster_prodmast ON tbmaster_prodmast.prd_prdcd = tbtr_lpp.lpp_prdcd
WHERE TO_CHAR(lpp_tgl1, 'MM-YYYY') = TO_CHAR(CURRENT_DATE, 'MM-YYYY')
-- WHERE TO_CHAR(lpp_tgl1, 'MM-YYYY') = TO_CHAR(CURRENT_DATE - INTERVAL '1 months', 'MM-YYYY') KALAU ME TGL 1
) AS SUBQUERY
-- WHERE KOREKSI < -1000 OR KOREKSI > 1000
ORDER BY KOREKSI) as aw
";
    $stmt3 = $conn->prepare($query3);
    $stmt3->execute();
    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
    $jumlahKoreksi = $result3['aa']; // Ambil hasil jumlah koreksi
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Styling untuk Tabel -->
<style>
    /* Styling untuk tabel */
    #table-1 {
      width: 100%;
      table-layout: auto; /* Menyesuaikan lebar kolom dengan isi konten */
      border-collapse: collapse; /* Menggabungkan border antar sel */
    }

    #table-1 th, #table-1 td {
      padding: 8px;
      text-align: left;
      border: 1px solid #ddd; /* Membuat border untuk semua cell */
    }

    #table-1 th {
      background-color: #f8f9fa;
      font-weight: bold;
      border-bottom: 2px solid #333; /* Menambahkan pembatas tebal di bawah header */
    }

    #table-1 td {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* Styling untuk kolom DESK */
    #table-1 .desk-column {
      word-wrap: break-word;  /* Memastikan teks di kolom DESK membungkus */
      white-space: normal;    /* Teks dapat membungkus pada kolom DESK */
      max-width: 300px;       /* Membatasi lebar maksimum kolom DESK */
    }

    /* Responsif untuk tabel */
    .table-responsive {
      overflow-x: auto;
    }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>KOREKSI LPP 01 SPI BDL 1R</h1>
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <!-- Information Card -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Informasi Koreksi</h5>
          <ul>
          <?php
            $bgColor = $jumlahKoreksi >= 0 ? 'blue' : 'red';
          ?>
                <strong>Total Rupiah Koreksi: Rp. </strong> 
                    <span style="background-color: <?php echo $bgColor; ?>;  color: white; padding: 2px 5px; border-radius: 10px;">
                <?php echo number_format($jumlahKoreksi, 2); ?>
            </span>

          </ul>
        </div>
      </div>

      <!-- Data Tables Stacked (Vertical Layout) -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title"> 5 Koreksi Minus Terbesar</h5>
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1" style="table-layout: auto;">
              <thead>
                <tr class="text-center">
                  <th>NO</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>Saldo Awal Qty</th>
                  <th>Saldo Awal Rp</th>
                  <th>In Transit Qty</th>
                  <th>In Transit Rp</th>
                  <th>Koreksi</th>
                  <th>BPB Qty</th>
                  <th>BPB Rp</th>
                  <th>TAC Qty</th>
                  <th>TAC Rp</th>
                  <th>Return Qty</th>
                  <th>Return Rp</th>
                  <th>Repack In Qty</th>
                  <th>Repack In Rp</th>
                  <th>Repack Out Qty</th>
                  <th>Repack Out Rp</th>
                  <th>Sales Qty</th>
                  <th>Sales Rp</th>
                  <th>Saldo Akhir Qty</th>
                  <th>Saldo Akhir Rp</th>
                  <th>Avg Cost</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $noUrut = 1;
                while ($data = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
    echo '<td align="right">' . $noUrut++ . '</td>';
    echo '<td>' . htmlspecialchars($data['lpp_prdcd']) . '</td>';
    echo '<td>' . htmlspecialchars($data['prd_deskripsipendek']) . '</td>';
    echo '<td align="right">' . number_format($data['saldoawalqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoawalrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['intransitqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['intransitrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['koreksi'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['bpbqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['bpbrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['tacqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['tacrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['returidmqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['returidmrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackinqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackinrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackoutqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackoutrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['salesqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['salesrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoakhirqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoakhirrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['acostlpp'], 2) . '</td>';
    echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Data Table 2 (job_log_all) -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title"> 5 Koreksi Plus Terbesar</h5>
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-2" style="table-layout: auto;">
              <thead>
                <tr class="text-center">
                <th>NO</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>Saldo Awal Qty</th>
                  <th>Saldo Awal Rp</th>
                  <th>In Transit Qty</th>
                  <th>In Transit Rp</th>
                  <th>Koreksi</th>
                  <th>BPB Qty</th>
                  <th>BPB Rp</th>
                  <th>TAC Qty</th>
                  <th>TAC Rp</th>
                  <th>Return Qty</th>
                  <th>Return Rp</th>
                  <th>Repack In Qty</th>
                  <th>Repack In Rp</th>
                  <th>Repack Out Qty</th>
                  <th>Repack Out Rp</th>
                  <th>Sales Qty</th>
                  <th>Sales Rp</th>
                  <th>Saldo Akhir Qty</th>
                  <th>Saldo Akhir Rp</th>
                  <th>Avg Cost</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $noUrut = 1;
                while ($data = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
    echo '<td align="right">' . $noUrut++ . '</td>';
    echo '<td>' . htmlspecialchars($data['lpp_prdcd']) . '</td>';
    echo '<td>' . htmlspecialchars($data['prd_deskripsipendek']) . '</td>';
    echo '<td align="right">' . number_format($data['saldoawalqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoawalrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['intransitqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['intransitrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['koreksi'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['bpbqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['bpbrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['tacqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['tacrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['returidmqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['returidmrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackinqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackinrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackoutqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['repackoutrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['salesqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['salesrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoakhirqty'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['saldoakhirrp'], 2) . '</td>';
    echo '<td align="right">' . number_format($data['acostlpp'], 2) . '</td>';
    echo '</tr>';
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
