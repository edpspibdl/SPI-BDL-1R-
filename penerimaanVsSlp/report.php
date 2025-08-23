<?php

require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil tanggal dari input form
$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : date('Y-m-01'); // Default awal bulan
$tanggalAkhir = isset($_GET['tanggalAkhir']) ? $_GET['tanggalAkhir'] : date('Y-m-d'); // Default hari ini

// Pastikan tanggal tidak kosong dan di-format untuk query (YYYYMMDD)
$tanggalAwalFormatted = !empty($tanggalAwal) ? date('Ymd', strtotime($tanggalAwal)) : null;
$tanggalAkhirFormatted = !empty($tanggalAkhir) ? date('Ymd', strtotime($tanggalAkhir)) : null;

// Query SQL dengan parameterized query
$query = "
    SELECT
        penerimaan.mstd_prdcd,
        slp_data.slp_prdcd,
        slp_data.SLP_DESKRIPSI,
        penerimaan.PENERIMAANHARIINI,
        slp_data.KEBEAMGUDANG,
        slp_data.KESTORAGEGUDANG,
        slp_data.KEBEAMTOKO,
        slp_data.KEINNERTOKO,
        slp_data.KESTORANGETOKO
    FROM
        (SELECT
            mstd_prdcd,
            SUM(mstd_qty) AS PENERIMAANHARIINI
        FROM
            tbtr_mstran_d
        WHERE
            TO_CHAR(DATE(mstd_create_dt), 'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir
            AND mstd_typetrn = 'B'
        GROUP BY
            mstd_prdcd) AS penerimaan
    LEFT JOIN
        (SELECT
            slp_prdcd,
            SLP_DESKRIPSI,
            COALESCE(SUM(BEAMGUDANG), 0) AS KEBEAMGUDANG,
            COALESCE(SUM(STORAGEGUDANG), 0) AS KESTORAGEGUDANG,
            COALESCE(SUM(BEAMTOKO), 0) AS KEBEAMTOKO,
            COALESCE(SUM(INNERTOKO), 0) AS KEINNERTOKO,
            COALESCE(SUM(STORANGETOKO), 0) AS KESTORANGETOKO
        FROM
            (SELECT
                slp_prdcd,
                SLP_DESKRIPSI,
                CASE WHEN LOKASI = 'BEAM' AND LOKASI2 = 'GUDANG' THEN SLP_QTYPCS END AS BEAMGUDANG,
                CASE WHEN LOKASI = 'STORAGE' AND LOKASI2 = 'GUDANG' THEN SLP_QTYPCS END AS STORAGEGUDANG,
                CASE WHEN LOKASI = 'BEAM' AND LOKASI2 = 'TOKO' THEN SLP_QTYPCS END AS BEAMTOKO,
                CASE WHEN LOKASI = 'INNER' AND LOKASI2 = 'TOKO' THEN SLP_QTYPCS END AS INNERTOKO,
                CASE WHEN LOKASI = 'STORAGE' AND LOKASI2 = 'TOKO' THEN SLP_QTYPCS END AS STORANGETOKO
            FROM
                (SELECT
                    slp_prdcd,
                    SLP_DESKRIPSI,
                    slp_koderak || '.' || slp_kodesubrak || '.' || slp_tiperak || '.' || slp_shelvingrak AS RAK,
                    CASE
                        WHEN slp_tiperak = 'B' THEN 'BEAM'
                        WHEN slp_tiperak = 'S' THEN 'STORAGE'
                        WHEN slp_tiperak LIKE 'I%' THEN 'INNER'
                    END AS LOKASI,
                    CASE
                        WHEN slp_koderak LIKE 'R%' OR slp_koderak LIKE 'O%' THEN 'TOKO'
                        WHEN slp_koderak LIKE 'G%' OR slp_koderak LIKE 'D%' THEN 'GUDANG'
                    END AS LOKASI2,
                    slp_qtypcs
                FROM
                    tbtr_slp
                WHERE
                    TO_CHAR(DATE(slp_create_dt), 'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir
                    AND SLP_JENIS = 'O'
                    AND (SLP_FLAG IS NULL OR SLP_FLAG <> 'C')) AS slp_inner_data) AS location_data
        GROUP BY
            slp_prdcd, SLP_DESKRIPSI) AS slp_data
    ON
        penerimaan.mstd_prdcd = slp_data.slp_prdcd
    ORDER BY slp_data.slp_prdcd ASC -- Tambahkan ORDER BY untuk konsistensi
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
    /* Atur lebar modal sesuai dengan persentase */
  }

  #table-1 {
    width: 100%;
    table-layout: auto;
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
    border-bottom: 2px solid #333;
  }

  #table-1 td {
    /* overflow: hidden; */
    /* text-overflow: ellipsis; */
    /* white-space: nowrap; */
    /* Nonaktifkan ini jika deskripsi bisa panjang dan butuh word-wrap */
  }

  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h3 class="text-center">Penerimaan VS Slp (Periode: <?= htmlspecialchars($tanggalAwal) ?> s/d <?= htmlspecialchars($tanggalAkhir) ?>)</h3>
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
                  <th>
                    <font size="2"><b>NO</b></font>
                  </th>
                  <th>
                    <font size="2"><b>PLU</b></font>
                  </th>
                  <th>
                    <font size="2"><b>DESKRIPSI</b></font>
                  </th>
                  <th>
                    <font size="2"><b>BPB HARI INI</b></font>
                  </th>
                  <th>
                    <font size="2"><b>KE BEAM GUDANG</b></font>
                  </th>
                  <th>
                    <font size="2"><b>KE STORAGE GUDANG</b></font>
                  </th>
                  <th>
                    <font size="2"><b>KE BEAM TOKO</b></font>
                  </th>
                  <th>
                    <font size="2"><b>KE INNER TOKO</b></font>
                  </th>
                  <th>
                    <font size="2"><b>KE STORAGE TOKO</b></font>
                  </th>
                  <th>
                    <font size="2"><b>LIHAT LOKASI</b></font>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 0;
                foreach ($result as $row):
                  $no++;
                ?>
                  <tr>
                    <td align="right">
                      <font size="2"><?= $no ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= htmlspecialchars($row['slp_prdcd'] ?: $row['mstd_prdcd']) ?></font>
                    </td>
                    <td align="left">
                      <font size="2"><?= htmlspecialchars($row['slp_deskripsi']) ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['penerimaanhariini'], 0, '.', ',') ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['kebeamgudang'], 0, '.', ',') ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['kestoragegudang'], 0, '.', ',') ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['kebeamtoko'], 0, '.', ',') ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['keinnertoko'], 0, '.', ',') ?></font>
                    </td>
                    <td align="right">
                      <font size="2"><?= number_format($row['kestorangetoko'], 0, '.', ',') ?></font>
                    </td>
                    <td>
                      <button class="btn btn-info btn-sm btn-detail" data-toggle="modal" data-target="#modalDetail"
                        data-slp_prdcd="<?= htmlspecialchars($row['slp_prdcd'] ?: $row['mstd_prdcd']) ?>"
                        data-tanggalawal="<?= htmlspecialchars($tanggalAwal) ?>"
                        data-tanggalakhir="<?= htmlspecialchars($tanggalAkhir) ?>">
                        LIHAT LOKASI
                      </button>
                    </td>
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

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailLabel">Detail Lokasi <span id="modalPlu"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modalContent">Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php require_once '../layout/_bottom.php'; ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: true,
      lengthChange: true, // Memungkinkan perubahan jumlah entri
      lengthMenu: [10, 25, 50, 100],
      buttons: [{
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'Laporan_Penerimaan_SLP_<?= date("Ymd", strtotime($tanggalAwal)) ?>_<?= date("Ymd", strtotime($tanggalAkhir)) ?>',
          title: null // Hapus judul default jika tidak diinginkan
        }
      ],
      dom: 'Bfrtip'
    });

    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  $('#modalDetail').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const slp_prdcd = button.data('slp_prdcd');
    const tanggalAwal = button.data('tanggalawal');
    const tanggalAkhir = button.data('tanggalakhir');

    $('#modalPlu').text(slp_prdcd); // Tampilkan PLU di judul modal
    $('#modalContent').html('Loading...'); // Reset konten modal

    // Request data detail ke get_detail.php
    $.ajax({
      url: 'get_detail.php',
      method: 'POST',
      data: {
        slp_prdcd: slp_prdcd,
        tanggalAwal: tanggalAwal,
        tanggalAkhir: tanggalAkhir
      },
      success: function(response) {
        $('#modalContent').html(response);
      },
      error: function() {
        $('#modalContent').html('<div class="alert alert-danger" role="alert">Gagal memuat detail. Silakan coba lagi.</div>');
      }
    });
  });
</script>