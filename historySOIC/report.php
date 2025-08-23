<?php

require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil tanggal dari input form
$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : date('Y-m-01');
$tanggalAkhir = isset($_GET['tanggalAkhir']) ? $_GET['tanggalAkhir'] : date('Y-m-d');

// Format ke YYYYMMDD untuk query
$tanggalAwalFormatted = date('Ymd', strtotime($tanggalAwal));
$tanggalAkhirFormatted = date('Ymd', strtotime($tanggalAkhir));

// Query SQL
$query = "
SELECT 
    PRD_KODEDIVISI,
    RSO_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_FRAC,
    PRD_UNIT,
    SUM(RSO_QTYRESET) AS QTY_RESET,
    RSO_AVGCOSTRESET
FROM 
    TBTR_RESET_SOIC
LEFT JOIN 
    tbmaster_prodmast ON prd_prdcd = RSO_PRDCD
WHERE 
    RSO_LOKASI = '01'
    AND TO_CHAR(RSO_TGLSO, 'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir
GROUP BY 
    PRD_KODEDIVISI,
    RSO_PRDCD,
    PRD_DESKRIPSIPANJANG,
    PRD_FRAC,
    PRD_UNIT,
    RSO_AVGCOSTRESET
";

try {
  $stmt = $conn->prepare($query);
  $stmt->bindValue(':tanggalAwal', $tanggalAwalFormatted);
  $stmt->bindValue(':tanggalAkhir', $tanggalAkhirFormatted);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Query failed: " . $e->getMessage();
  exit;
}

?>

<style>
  .modal-dialog {
    max-width: 70%;
  }

  #table-1 {
    width: 100%;
    border-collapse: collapse;
  }

  #table-1 th,
  #table-1 td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
  }

  #table-1 th {
    background-color: #f8f9fa;
    font-weight: bold;
  }

  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h3 class="text-center">History SOIC (Periode: <?= htmlspecialchars($tanggalAwal) ?> s/d <?= htmlspecialchars($tanggalAkhir) ?>)</h3>
    <a href="index.php" class="btn btn-primary">BACK</a>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">

          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th><b>DIV</b></th>
                  <th><b>PLUIGR</b></th>
                  <th><b>DESKRIPSI</b></th>
                  <th><b>FRAC</b></th>
                  <th><b>UNIT</b></th>
                  <th><b>QTY RESET</b></th>
                  <th><b>ACOST RESET</b></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($result as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['prd_kodedivisi']) ?></td>
                    <td><?= htmlspecialchars($row['rso_prdcd']) ?></td>
                    <td><?= htmlspecialchars($row['prd_deskripsipanjang']) ?></td>
                    <td><?= htmlspecialchars($row['prd_frac']) ?></td>
                    <td><?= htmlspecialchars($row['prd_unit']) ?></td>
                    <td><?= number_format($row['qty_reset']) ?></td>
                    <td><?= number_format($row['rso_avgcostreset'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: true,
      lengthChange: true,
      lengthMenu: [10, 25, 50, 100],
      buttons: [{
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'History_SOIC_<?= date("Ymd", strtotime($tanggalAwal)) ?>_<?= date("Ymd", strtotime($tanggalAkhir)) ?>',
          title: null
        }
      ],
      dom: 'Bfrtip'
    });

    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });
</script>