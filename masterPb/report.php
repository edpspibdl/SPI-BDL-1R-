<?php

require_once '../layout/_top.php'; // Include top layout (menu, etc.)
require_once '../helper/connection.php'; // Pastikan path benar

// Ambil tanggal dari input form
$tanggalAwal = isset($_GET['tanggalAwal']) ? $_GET['tanggalAwal'] : '';
$tanggalAkhir = isset($_GET['tanggalAkhir']) ? $_GET['tanggalAkhir'] : '';

// Pastikan tanggal tidak kosong sebelum di-format
$tanggalAwalFormatted = !empty($tanggalAwal) ? date('Ymd', strtotime($tanggalAwal)) : null;
$tanggalAkhirFormatted = !empty($tanggalAkhir) ? date('Ymd', strtotime($tanggalAkhir)) : null;

// Query SQL dengan parameterized query
$query = "SELECT 
    STATUS_PB, 
    OBI_NOPB,
    PB,
    TGL_PB,
    MEMBER,
    JENIS_MEMBER,
    RPH_ORDERHEADER,
    RPH_REALISASI,
    RPH_ONGKIR,
    COALESCE(TIPE_BAYAR, 'TUNAI') AS TIPE_BAYAR,
    SERVICE,
    OBI_MAXDELIVERYTIME,
    CUS_JARAK
FROM (
    SELECT 
        CASE 
            WHEN OBI_RECID IS NULL THEN 'Siap Send HH'
            WHEN OBI_RECID = '1' THEN 'Siap Picking'
            WHEN OBI_RECID = '2' THEN 'Siap Packing'
            WHEN OBI_RECID = '3' THEN 'Siap Draft Struk'
            WHEN OBI_RECID = '4' THEN 'Siap Konf. Pembayaran'
            WHEN OBI_RECID = '5' THEN 'Siap Struk'
            WHEN OBI_RECID = '6' THEN 'Selesai Struk'
            WHEN OBI_RECID = '7' THEN 'Set Ongkir'
            ELSE 'Pembatalan / Expired'
        END AS STATUS_PB,
        OBI_NOPB,
        TO_CHAR(DATE(OBI_TGLPB), 'DDMMYYYY') || OBI_NOTRANS AS PB,
        TO_CHAR(DATE(OBI_TGLPB), 'DDMMYYYY') || OBI_NOTRANS AS TGL_PB,
        OBI_KDMEMBER || ' - ' || cus_namamember AS MEMBER,
        CASE 
            WHEN cus_flagmemberkhusus = 'Y' THEN 'MEMBER MERAH' 
            ELSE 'MEMBER BIRU' 
        END AS JENIS_MEMBER,
        OBI_TTLORDER + OBI_TTLPPN  - SUM(COALESCE(CASHBACK_ORDER,0)) AS RPH_ORDERHEADER,
        OBI_REALORDER + OBI_REALPPN  - SUM(COALESCE(CASHBACK_REAL,0)) AS RPH_REALISASI,
        OBI_ITEMORDER,
        OBI_REALITEM,
        CASE 
            WHEN OBI_FREEONGKIR = 'Y' THEN 'Free'
            WHEN OBI_FREEONGKIR = 'N' THEN 'Ongkir'
            WHEN OBI_FREEONGKIR = 'T' THEN 'Ambil Di Toko'
            ELSE 'CEK'
        END AS Ongkir,
        OBI_EKSPEDISI AS RPH_ONGKIR,
        CASE 
            WHEN obi_shippingservice = 'N' THEN 'NEXT DAY'
            WHEN obi_shippingservice = 'S' THEN 'SAME DAY'
            WHEN obi_shippingservice IS NULL THEN ''
            ELSE 'CEK'
        END AS service,
        OBI_MAXDELIVERYTIME,
        STRING_AGG(DISTINCT COALESCE(TIPE_BAYAR, 'TUNAI'), ', ') AS TIPE_BAYAR,
        CUS_JARAK
    FROM tbtr_obi_h
    LEFT JOIN tbmaster_customer ON cus_kodemember = OBI_KDMEMBER
    LEFT JOIN PAYMENT_KLIKIGR ON NO_PB = OBI_NOPB
    LEFT JOIN PROMO_KLIKIGR PKI ON PKI.NO_PB = OBI_NOPB
    WHERE TO_CHAR(DATE(OBI_TGLPB), 'YYYYMMDD') BETWEEN :tanggalAwal AND :tanggalAkhir
    GROUP BY 
        OBI_RECID, OBI_NOPB, OBI_TGLPB, OBI_NOTRANS, OBI_KDMEMBER, cus_namamember, cus_flagmemberkhusus, 
        OBI_TTLORDER, OBI_TTLPPN, OBI_TTLDISKON, OBI_REALORDER, OBI_REALPPN, OBI_REALDISKON, OBI_ITEMORDER, 
        OBI_REALITEM, OBI_FREEONGKIR, OBI_EKSPEDISI, obi_shippingservice, OBI_MAXDELIVERYTIME, CUS_JARAK
    ORDER BY TO_CHAR(DATE(OBI_TGLPB), 'YYYYMMDD') || ', Trx: ' || OBI_NOTRANS
) AS result";

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
<!-- Styling untuk Tabel -->
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
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .table-responsive {
    overflow-x: auto;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h3 class="text-center">Master PB SPI</h3>
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
                  <th>#</th>
                  <th>Status</th>
                  <th>No PB</th>
                  <th>PB Queue</th>
                  <th>Member</th>
                  <th>Rph Order</th>
                  <th>Rph Real</th>
                  <th>Jarak</th>
                  <th>Ongkir</th>
                  <th>Pembayaran</th>
                  <th>Detail</th>

                </tr>
              </thead>
              <tbody>
                <?php
                $no = 0; // Inisialisasi nomor
                foreach ($result as $row):
                  $no++; ?>
                  <tr>
                    <td><?= $no ?></td>
                    <td><?= htmlspecialchars($row["status_pb"]) ?></td>
                    <td><?= htmlspecialchars($row["obi_nopb"]) ?></td>
                    <td><?= htmlspecialchars($row["pb"]) ?></td>
                    <td><?= htmlspecialchars($row["member"]) ?></td>
                    <td><?= number_format($row["rph_orderheader"], 0, '.', ',') ?></td>
                    <td><?= number_format($row["rph_realisasi"], 0, '.', ',') ?></td>
                    <td><?= number_format($row["cus_jarak"], 0, '.', ',') ?></td>
                    <td><?= number_format($row["rph_ongkir"], 0, '.', ',') ?></td>
                    <td><?= htmlspecialchars($row["tipe_bayar"]) ?></td>
                    <td>
                      <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#modalDetail' data-pb="<?= htmlspecialchars($row['pb']) ?>">Produk</button>
                      <button class='btn btn-success btn-sm' data-toggle='modal' data-target='#modalCSBK' data-pb="<?= htmlspecialchars($row['pb']) ?>">Cashback</button>
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

<!-- Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- Menambahkan kelas modal-lg -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailLabel">Detail Order </h5>
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


<!-- Modal CSBK -->
<div class="modal fade" id="modalCSBK" tabindex="-1" role="dialog" aria-labelledby="modalCSBKLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- Modal besar -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCSBKLabel">Detail CSBK</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modalContentCSBK">Loading...</div> <!-- Ini untuk isi konten dari get_csbk.php -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




<?php require_once '../layout/_bottom.php'; ?>
<!-- Add the required CSS and JS libraries -->
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
      lengthMenu: [10, 25, 50, 100],
      buttons: [{
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: '_' + new Date().toISOString().split('T')[0],
          title: null
        }
      ],
      dom: 'Bfrtip'
    });

    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  $('#modalDetail').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const pb = button.data('pb');

    // Request data detail
    $.ajax({
      url: 'get_detail.php',
      method: 'POST',
      data: {
        pb
      },
      success: function(response) {
        $('#modalContent').html(response);
      },
      error: function() {
        $('#modalContent').html('Failed to load details.');
      }
    });
  });


  // Ketika tombol CSBK ditekan
  $('#modalCSBK').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget); // Tombol yang men-trigger modal
    var pb = button.data('pb'); // Ambil data-pb dari tombol

    var modal = $(this);
    modal.find('#modalContentCSBK').html('Loading...'); // Saat loading

    // Panggil file get_csbk.php
    $.ajax({
      url: 'get_csbk.php',
      method: 'POST',
      data: {
        pb
      },
      success: function(response) {
        modal.find('#modalContentCSBK').html(response);
      },
      error: function(xhr, status, error) {
        modal.find('#modalContentCSBK').html('Gagal load data.');
      }
    });
  });
</script>