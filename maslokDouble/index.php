<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "    SELECT
    l.lks_koderak,
    l.lks_kodesubrak,
    l.lks_tiperak,
    l.lks_shelvingrak,
    l.lks_nourut,
    l.lks_prdcd,
    p.prd_deskripsipanjang AS lks_nama_barang,
    p.prd_unit             AS lks_unit,
    p.prd_frac             AS lks_frac,
    COALESCE(p.prd_kodetag, ' ') AS lks_tag -- Nvl diganti dengan COALESCE
FROM
    tbmaster_lokasi l
LEFT JOIN
    tbmaster_prodmast p ON l.lks_prdcd = p.prd_prdcd
WHERE
    l.lks_prdcd IN (
        SELECT
            l_sub.lks_prdcd
        FROM
            tbmaster_lokasi l_sub -- Menggunakan alias berbeda untuk subquery
        WHERE
            l_sub.lks_tiperak NOT IN ('S', 'Z')
            AND l_sub.lks_koderak NOT LIKE 'D%'
            AND l_sub.lks_prdcd IS NOT NULL
        GROUP BY
            l_sub.lks_prdcd
        HAVING
            COUNT(l_sub.lks_prdcd) > 1
    )
    AND l.lks_tiperak NOT IN ('S', 'Z')
    AND l.lks_koderak NOT LIKE 'D%'
ORDER BY
    lks_prdcd,
    lks_koderak";

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
    <h1>Laporan Lokasi Ganda Produk</h1>
    <a href="../LaporanLaporan/index.php" class="btn btn-primary">Kembali</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Kode Rak</th>
                  <th>Sub Rak</th>
                  <th>Tipe Rak</th>
                  <th>Shelving Rak</th>
                  <th class="text-center">No. Urut</th>
                  <th>PLU</th>
                  <th class="deskripsi-column">Deskripsi Barang</th>
                  <th class="text-center">Unit</th>
                  <th class="text-center">Frac</th>
                  <th class="text-center">Tag</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($data)): ?>
                  <?php $nomor = 1; ?>
                  <?php foreach ($data as $row): ?>
                    <tr>
                      <td class="text-center"><?= $nomor++ ?></td>
                      <td><?= htmlspecialchars($row['lks_koderak'] ?? '') ?></td>
                      <td><?= htmlspecialchars($row['lks_kodesubrak'] ?? '') ?></td>
                      <td><?= htmlspecialchars($row['lks_tiperak'] ?? '') ?></td>
                      <td><?= htmlspecialchars($row['lks_shelvingrak'] ?? '') ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['lks_nourut'] ?? '') ?></td>
                      <td><?= htmlspecialchars($row['lks_prdcd'] ?? '') ?></td>
                      <td class="deskripsi-column"><?= htmlspecialchars($row['lks_nama_barang'] ?? '') ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['lks_unit'] ?? '') ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['lks_frac'] ?? '') ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['lks_tag'] ?? '') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr class="no-data-row">
                    <td colspan="11" class="text-center">Tidak ada data lokasi ganda ditemukan.</td>
                  </tr>
                <?php endif; ?>
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
    const tableBody = document.querySelector('#table-1 tbody');
    const hasData = tableBody && tableBody.querySelectorAll('tr:not(.no-data-row)').length > 0;

    const options = {
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{
        targets: [7],
        orderable: false
      }],
      buttons: [{
          extend: 'copy',
          text: 'Copy'
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'LPP_VS_PLANO_' + new Date().toISOString().split('T')[0],
          title: null
        }
      ],
      dom: 'Bfrtip',
      language: {
        zeroRecords: ""
      }
    };

    const table = $('#table-1').DataTable(
      hasData ?
      options :
      {
        searching: false,
        paging: false,
        info: false,
        ordering: false,
        buttons: [],
        language: {
          zeroRecords: "Tidak ada data yang tersedia."
        }
      }
    );

    if (!hasData) {
      $('#table-1').removeClass('dataTable');
      $('#table-1 .no-data-row td').attr('colspan', $('#table-1 thead th').length);
    }

    if (table.buttons?.container && options.buttons.length) {
      table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    }
  });

  $(document).ready(function() {
    const table = $('#table-1').DataTable();
    if ($.fn.DataTable.isDataTable('#table-1')) table.columns.adjust().draw();
    $("#load").fadeOut();
  });
</script>