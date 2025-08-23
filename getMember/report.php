<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Ensure this is the correct path

// Get the start and end date from the form input
$salesman = isset($_GET['salesman']) ? $_GET['salesman'] : '';
$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : '';
$tanggalAkhir = isset($_GET['tanggalAkhir']) ? $_GET['tanggalAkhir'] : '';

// Check if dates are not empty before proceeding with formatting
if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
    // Convert the dates to the format that SQL expects (YYYYMMDD)
    $tanggalAwalFormatted = date('Ymd', strtotime($tanggalAwal));
    $tanggalAkhirFormatted = date('Ymd', strtotime($tanggalAkhir));
} else {
    $tanggalAwalFormatted = '';
    $tanggalAkhirFormatted = '';
}

$query = "
    SELECT DISTINCT ON (c.cus_kodemember) 
    c.cus_kodemember,
    c.cus_namamember,
    cc.crm_email,
    c.cus_nosalesman,
    c.cus_tgllahir,
    c.cus_noktp,
    cc.crm_alamatusaha1,
    cc.crm_alamatusaha3,
    cc.crm_alamatusaha2,
    cc.crm_alamatusaha4,
    kp.pos_kecamatan,
    c.cus_jarak,
    c.cus_kodeoutlet,
    c.cus_kodesuboutlet,
    gk.grp_kategori,
    gk.grp_subkategori,
    CONCAT(
        TO_CHAR(c.cus_tglregistrasi, 'DD-'),
        CASE 
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '01' THEN 'JANUARI'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '02' THEN 'FEBRUARI'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '03' THEN 'MARET'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '04' THEN 'APRIL'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '05' THEN 'MEI'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '06' THEN 'JUNI'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '07' THEN 'JULI'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '08' THEN 'AGUSTUS'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '09' THEN 'SEPTEMBER'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '10' THEN 'OKTOBER'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '11' THEN 'NOVEMBER'
            WHEN TO_CHAR(c.cus_tglregistrasi, 'MM') = '12' THEN 'DESEMBER'
        END,
        '-',
        TO_CHAR(c.cus_tglregistrasi, 'YYYY')
    ) AS cus_tglregistrasi,
    CONCAT(
        TO_CHAR(c.cus_tglmulai, 'DD-'),
        CASE 
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '01' THEN 'JANUARI'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '02' THEN 'FEBRUARI'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '03' THEN 'MARET'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '04' THEN 'APRIL'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '05' THEN 'MEI'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '06' THEN 'JUNI'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '07' THEN 'JULI'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '08' THEN 'AGUSTUS'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '09' THEN 'SEPTEMBER'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '10' THEN 'OKTOBER'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '11' THEN 'NOVEMBER'
            WHEN TO_CHAR(c.cus_tglmulai, 'MM') = '12' THEN 'DESEMBER'
        END,
        '-',
        TO_CHAR(c.cus_tglmulai, 'YYYY')
    ) AS cus_tglmulai,
    c.cus_tlpmember,
    c.cus_hpmember,    
    CONCAT(
        TO_CHAR(sub4.kunjungan_terakhir, 'DD-'),
        CASE 
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '01' THEN 'JANUARI'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '02' THEN 'FEBRUARI'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '03' THEN 'MARET'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '04' THEN 'APRIL'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '05' THEN 'MEI'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '06' THEN 'JUNI'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '07' THEN 'JULI'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '08' THEN 'AGUSTUS'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '09' THEN 'SEPTEMBER'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '10' THEN 'OKTOBER'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '11' THEN 'NOVEMBER'
            WHEN TO_CHAR(sub4.kunjungan_terakhir, 'MM') = '12' THEN 'DESEMBER'
        END,
        '-',
        TO_CHAR(sub4.kunjungan_terakhir, 'YYYY')
    ) AS kunjungan_terakhir,
    COALESCE(p1.point, 0) - COALESCE(p2.tukar_point, 0) AS poc_saldopoint,
    cc.crm_koordinat
FROM tbmaster_customer c
LEFT JOIN tbmaster_customercrm cc ON c.cus_kodemember = cc.crm_kodemember
LEFT JOIN tbmaster_kodepos kp ON cc.crm_alamatusaha3 = kp.pos_kode
LEFT JOIN (
    SELECT DISTINCT ON (trjd_cus_kodemember) 
        trjd_cus_kodemember, 
        MAX(trjd_transactiondate) AS kunjungan_terakhir
    FROM tbtr_jualdetail 
    GROUP BY trjd_cus_kodemember
) sub4 ON c.cus_kodemember = sub4.trjd_cus_kodemember 
LEFT JOIN (
    SELECT por_kodemember, SUM(por_perolehanpoint) AS point
    FROM tbtr_perolehanmypoin 
    GROUP BY por_kodemember
) p1 ON c.cus_kodemember = p1.por_kodemember
LEFT JOIN (
    SELECT pot_kodemember, SUM(pot_penukaranpoint) AS tukar_point
    FROM tbtr_penukaranmypoin 
    GROUP BY pot_kodemember
) p2 ON c.cus_kodemember = p2.pot_kodemember
LEFT JOIN tbtabel_groupkategori gk ON cc.crm_idgroupkat = gk.grp_idgroupkat
WHERE c.cus_recordid IS NULL
AND to_char(cus_tglmulai,'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir
AND c.cus_nosalesman = :salesman
AND c.cus_flagmemberkhusus = 'Y' 
AND c.cus_kodeigr = '1R' 
AND c.cus_namamember <> 'NEW'
ORDER BY c.cus_kodemember, cc.crm_koordinat DESC
";

try {
    $stmt = $conn->prepare($query);
    if (!empty($tanggalAwalFormatted) && !empty($tanggalAkhirFormatted)) {
        $stmt->bindValue(':tanggalAwal', $tanggalAwalFormatted);
        $stmt->bindValue(':tanggalAkhir', $tanggalAkhirFormatted);
    }
    $stmt->bindValue(':salesman', $salesman);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    exit;
}

?>
<!-- Styling -->
<style>
  .table-responsive {
    overflow-x: auto;
  }

  table th, table td {
    white-space: nowrap;
  }

  .dataTables_wrapper .dt-buttons {
    margin-bottom: 10px;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h3>Laporan Get Member</h3>
    <a href="../pluNoSales/index.php" class="btn btn-primary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="pluNoSalesTable" class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th>NO</th>
              <th>KODE MEMBER</th>
              <th>NAMA</th>
              <th>EMAIL</th>
              <th>SALES</th>
              <th>TGL LAHIR</th>
              <th>NO KTP</th>
              <th>ALAMAT USAHA</th>
              <th>KECAMATAN</th>
              <th>KATEGORI</th>
              <th>SUBKATEGORI</th>
              <th>TGL REGISTRASI</th>
              <th>TGL MULAI</th>
              <th>HP</th>
              <th>LAST KUNJUNGAN</th>
              <th>POIN</th>
              <th>KOORDINAT</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($result as $row): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['cus_kodemember']) ?></td>
                <td><?= htmlspecialchars($row['cus_namamember']) ?></td>
                <td><?= htmlspecialchars($row['crm_email']) ?></td>
                <td><?= htmlspecialchars($row['cus_nosalesman']) ?></td>
                <td><?= htmlspecialchars($row['cus_tgllahir']) ?></td>
                <td><?= htmlspecialchars($row['cus_noktp']) ?></td>
                <td><?= htmlspecialchars($row['crm_alamatusaha1'] . ' ' . $row['crm_alamatusaha2'] . ' ' . $row['crm_alamatusaha3'] . ' ' . $row['crm_alamatusaha4']) ?></td>
                <td><?= htmlspecialchars($row['pos_kecamatan']) ?></td>
                <td><?= htmlspecialchars($row['grp_kategori']) ?></td>
                <td><?= htmlspecialchars($row['grp_subkategori']) ?></td>
                <td><?= htmlspecialchars($row['cus_tglregistrasi']) ?></td>
                <td><?= htmlspecialchars($row['cus_tglmulai']) ?></td>
                <td><?= htmlspecialchars($row['cus_hpmember']) ?></td>
                <td><?= htmlspecialchars($row['kunjungan_terakhir']) ?></td>
                <td><?= htmlspecialchars($row['poc_saldopoint']) ?></td>
                <td><?= htmlspecialchars($row['crm_koordinat']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($result)): ?>
              <tr><td colspan="17" class="text-center">Tidak ada data untuk kriteria yang dipilih.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = $('#pluNoSalesTable').DataTable({
      responsive: false,
      lengthMenu: [15, 25, 50, 100],
      columnDefs: [
        { targets: [4], orderable: false }
      ],
      buttons: [
        {
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'MEMBER_CRM_' + new Date().toISOString().split('T')[0],
          title: null
        },
        {
          extend: 'colvis',
          text: 'Tampilkan/Sembunyikan Kolom'
        }
      ],
      dom: 'Bfrtip'
    });

    table.buttons().container().appendTo('#pluNoSalesTable_wrapper .col-md-6:eq(0)');
  });
</script>