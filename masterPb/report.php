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
        (OBI_TTLORDER + OBI_TTLPPN - COALESCE(SUM(CASHBACK_ORDER), 0)) AS RPH_ORDERHEADER,
        (OBI_REALORDER + OBI_REALPPN - COALESCE(SUM(CASHBACK_REAL), 0)) AS RPH_REALISASI,
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
  /* Container Styling */
  .section-header h3 {
    font-weight: 700;
    color: #1e293b;
  }

  /* Modern Table Styling */
  #table-1 {
    border: none !important;
    border-collapse: separate;
    border-spacing: 0 8px; /* Memberikan jarak antar baris */
    width: 100% !important;
  }

  #table-1 thead th {
    background-color: #f8fafc !important;
    color: #64748b;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.05em;
    font-weight: 700;
    padding: 12px 15px;
    border: none;
  }

  #table-1 tbody tr {
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
  }

  #table-1 tbody tr:hover {
    background-color: #f1f5f9 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  }

  #table-1 tbody td {
    padding: 14px 15px;
    vertical-align: middle;
    border: none;
    color: #334155;
    font-size: 0.85rem;
  }

  /* Rounded corners untuk baris */
  #table-1 tbody tr td:first-child { border-radius: 8px 0 0 8px; }
  #table-1 tbody tr td:last-child { border-radius: 0 8px 8px 0; }

  /* Badge Customization */
  .badge-status {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
  }

  .text-amount {
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    color: #0f172a;
  }

  .text-sub {
    display: block;
    font-size: 11px;
    color: #94a3b8;
  }

  /* Button Styling */
  .btn-action {
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    letter-spacing: 0.3px;
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
                  <th>Member</th>
                  <th>Rph Order</th>
                  <th>Rph Real</th>
                  <th>Jarak</th>
                  <th>Pembayaran</th>
                  <th>Detail</th>

                </tr>
              </thead>
              <tbody>
  <?php
  $no = 0;
  foreach ($result as $row):
    $no++; 
    
    // Logika warna status
    $status_class = 'badge-secondary';
    if($row['status_pb'] == 'Selesai Struk') $status_class = 'badge-success';
    if($row['status_pb'] == 'Siap Picking') $status_class = 'badge-warning';
    if(strpos($row['status_pb'], 'Siap') !== false) $status_class = 'badge-info';
  ?>
    <tr>
      <td class="text-muted"><?= $no ?></td>
      <td>
        <span class="badge badge-status <?= $status_class ?>">
            <?= htmlspecialchars($row["status_pb"]) ?>
        </span>
      </td>
      <td>
        <span class="font-weight-bold"><?= htmlspecialchars($row["obi_nopb"]) ?></span>
        <span class="text-sub">Queue: <?= htmlspecialchars($row["pb"]) ?></span>
      </td>
      <td>
        <div style="max-width: 180px; overflow: hidden; text-overflow: ellipsis;">
            <strong><?= explode(' - ', $row["member"])[1] ?? $row["member"] ?></strong>
            <span class="text-sub"><?= explode(' - ', $row["member"])[0] ?></span>
        </div>
      </td>
      <td class="text-right">
        <span class="text-amount">Rp <?= number_format($row["rph_orderheader"], 0, '.', ',') ?></span>
      </td>
      <td class="text-right">
        <span class="text-amount text-primary">Rp <?= number_format($row["rph_realisasi"], 0, '.', ',') ?></span>
      </td>
      <td class="text-center">
        <span class="badge badge-light text-muted font-weight-bold"><?= number_format($row["cus_jarak"], 1, '.', ',') ?> Km</span>
      </td>
      <td>
        <span class="text-sub font-weight-bold" style="color:#475569"><?= htmlspecialchars($row["tipe_bayar"]) ?></span>
      </td>
      <td style="width: 200px;"> <div class="d-flex justify-content-start align-items-center">
    
    <button class="btn btn-outline-info btn-sm btn-detail-modern mr-2" 
            data-toggle="modal" 
            data-target="#modalDetail" 
            data-pb="<?= htmlspecialchars($row['pb']) ?>">
        <i class="fas fa-box-open mr-1"></i> <span>PRODUK</span>
    </button>

    <button class="btn btn-outline-success btn-sm btn-detail-modern" 
            data-toggle="modal" 
            data-target="#modalCSBK" 
            data-pb="<?= htmlspecialchars($row['pb']) ?>">
        <i class="fas fa-percentage mr-1"></i> <span>PROMO</span>
    </button>

  </div>
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
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document"> 
        <div class="modal-content" style="border-radius: 10px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            
            <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 20px;">
                <h5 class="modal-title" id="modalDetailLabel" style="font-weight: 700; color: #334155; font-size: 15px;">
                    <i class="fas fa-shopping-basket mr-2 text-primary"></i> Rincian Produk
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none; font-size: 1.2rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="background-color: #ffffff; padding: 0;">
                <div id="modalContent">
                    <div class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted" style="font-size: 12px;">Memuat data barang...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 10px 20px;">
                <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal" style="border-radius: 5px; font-weight: 600; font-size: 12px; border: 1px solid #cbd5e1;">
                    Tutup
                </button>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modalCSBK" tabindex="-1" role="dialog" aria-labelledby="modalCSBKLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 700px;"> 
        <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            
            <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 15px;">
                <h5 class="modal-title" id="modalCSBKLabel" style="font-weight: 700; color: #334155; font-size: 14px;">
                    <i class="fas fa-info-circle mr-2 text-primary"></i> Detail Promo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.2rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="background-color: #ffffff; padding: 15px;">
                <div id="modalContentCSBK">
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted" style="font-size: 12px;">Mengambil data...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 10px 15px;">
                <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal" style="border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid #e2e8f0;">Tutup</button>
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
  pageLength: 10,
  dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
  buttons: [
    {
      extend: 'excel',
      text: '<i class="fas fa-file-excel mr-1"></i> Export Excel',
      className: 'btn btn-sm btn-success shadow-sm',
      titleAttr: 'Export ke Excel'
    },
    {
      extend: 'copy',
      text: '<i class="fas fa-copy mr-1"></i> Copy',
      className: 'btn btn-sm btn-light shadow-sm'
    }
  ],
  language: {
    search: "_INPUT_",
    searchPlaceholder: "Cari data transaksi...",
    paginate: {
      previous: "<i class='fas fa-chevron-left'></i>",
      next: "<i class='fas fa-chevron-right'></i>"
    }
  }
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