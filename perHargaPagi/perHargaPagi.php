<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
    $query = "SELECT
    alamat_toko,
    alamat_counter,
    prd_kodedivisi,
    prd_kodedepartement,
    prd_kodekategoribarang,
    prd_prdcd,
    prd_deskripsipanjang,
    prd_unit,
    prd_frac,
    sj_lama_nol,
    sj_lama_satu,
    sj_lama_dua,
    sj_lama_tiga,
    sj_baru_0,
    sj_baru_1,
    sj_baru_2,
    sj_baru_3,
    tgl_perubahan,
    jam_perubahan
FROM
    ( SELECT
    prd_kodedivisi,
    prd_kodedepartement,
    prd_kodekategoribarang,
    prd_prdcd,
    prd_deskripsipanjang,
    prd_unit,
    prd_frac,
    coalesce(harga_lama0, 0)                       sj_lama_nol,
    coalesce(harga_lama1, 0)                       sj_lama_satu,
    coalesce(harga_lama2, 0)                       sj_lama_dua,
    coalesce(harga_lama3, 0)                       sj_lama_tiga,
    coalesce(harga_baru0, 0)                       sj_baru_0,
    coalesce(harga_baru1, 0)                       sj_baru_1,
    coalesce(harga_baru2, 0)                       sj_baru_2,
    coalesce(harga_baru3, 0)                       sj_baru_3,
    tgl_perubahan,
    to_char(tgl_perubahan, 'HH: MI: SS')      jam_perubahan
FROM
    (
        SELECT
            prd_prdcd,
            prd_kodedivisi,
            prd_kodedepartement,
            prd_kodekategoribarang,
            prd_deskripsipanjang,
            prd_unit,
            prd_frac,
            prd_modify_dt tgl_perubahan
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
            prd_prdcd LIKE '%0'
    )aa
    LEFT JOIN (
        SELECT
            prd_prdcd                      plu,
            coalesce(prd_hrgjual, 0)       harga_baru0,
            coalesce(prd_hrgjual2, 0)      harga_lama0
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%0'
    )ab ON prd_prdcd = plu
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0                           plu1,
            coalesce(prd_hrgjual, 0)       harga_baru1,
            coalesce(prd_hrgjual2, 0)      harga_lama1
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%1'
    )ac ON prd_prdcd = plu1
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0          plu2,
            prd_hrgjual   harga_baru2,
            prd_hrgjual2  harga_lama2
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%2'
    )ad ON prd_prdcd = plu2
    LEFT JOIN (
        SELECT
            substr(prd_prdcd, 1, 6)
            || 0          plu3,
            prd_hrgjual   harga_baru3,
            prd_hrgjual2  harga_lama3
        FROM
            tbtemp_prodmast_pagi_hari
        WHERE
                date_trunc('day', prd_modify_dt) = date_trunc('day', current_date)
            AND prd_prdcd LIKE '%3'
    )ae ON prd_prdcd = plu3
WHERE
    coalesce(harga_lama0, 0) + coalesce(harga_lama1, 0) + coalesce(harga_lama2, 0) + coalesce(harga_lama3, 0) + coalesce(harga_baru0,
    0) + coalesce(harga_baru1, 0) + coalesce(harga_baru2, 0) + coalesce(harga_baru3, 0) <> '0'
)bc left join
    (
        SELECT
            lks_kodeigr,
            lks_prdcd       plutko,
            lks_koderak
            || '.'
            || lks_kodesubrak
            || '.'
            || lks_tiperak
            || '.'
            || lks_shelvingrak
            || '.'
            || lks_nourut   AS alamat_toko
        FROM
            tbmaster_lokasi
        WHERE
            ( lks_koderak LIKE 'R%' )
            AND lks_koderak NOT LIKE '%C'
            AND lks_tiperak <> 'S'
    )bb on prd_prdcd = plutko
    left join
    (
        SELECT
            lks_kodeigr,
            lks_prdcd       plucounter,
            lks_koderak
            || '.'
            || lks_kodesubrak
            || '.'
            || lks_tiperak
            || '.'
            || lks_shelvingrak
            || '.'
            || lks_nourut   AS alamat_counter
        FROM
            tbmaster_lokasi
        WHERE
            ( lks_koderak LIKE 'O%' )
            AND lks_koderak NOT LIKE '%C'
            AND lks_tiperak <> 'S'
    )ba on prd_prdcd = plucounter
ORDER BY
    1,
    2";

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
    <h1>PERUBAHAN HARGA PAGI HARI </h1>
    <a href="../LaporanLaporan/index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
            <thead>

<tr class="active">
  <th rowspan="2" class="text-center">#</th>
  <th colspan="7" class="text-center">Produk</th>
  <th colspan="4" class="text-center">Harga Lama</th>
  <th colspan="4" class="text-center">Harga Baru</th>
  <th colspan="2" class="text-center">Waktu</th>
  </tr>

<tr class="active">
  
  <th class="text-center">Div</th>
  <th class="text-center">Dept</th>
  <th class="text-center">Katb</th>
  <th class="text-center">Plu</th>
  <th class="text-center">Desk</th>
  <th class="text-center">Unit</th>
  <th class="text-center">Frac</th>
  
  <th class="text-center">0</th>
  <th class="text-center">1</th>
  <th class="text-center">2</th>
  <th class="text-center">3</th>
  <th class="text-center">0</th>
  <th class="text-center">1</th>
  <th class="text-center">2</th>
  <th class="text-center">3</th>
  <th class="text-center">Tanggal</th>
  <th class="text-center">Jam</th>
  

  
</tr>
</thead> 
              <tbody>
                <?php
                $noUrut = 1; // Pastikan variabel $nomor diinisialisasi
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr class='s'>";
                  echo '<td align="right">'  . $noUrut . '</td>';
                  echo '<td align="center">' . $row['prd_kodedivisi'] . '</td>';
                  echo '<td align="left">' . $row['prd_kodedepartement'] . '</td>';
                  echo '<td align="center">' . $row['prd_kodekategoribarang'] . '</td>';
                  echo '<td align="left">' . $row['prd_prdcd'] . '</td>';
                  echo '<td align="left">' . $row['prd_deskripsipanjang'] . '</td>';
                  echo '<td align="center">' . $row['prd_unit'] . '</td>';
      
                  echo '<td align="center">' . $row['prd_frac'] . '</td>';
      
                  echo '<td align="right">'  . number_format($row['sj_lama_nol'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_lama_satu'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_lama_dua'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_lama_tiga'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_baru_0'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_baru_1'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_baru_2'], 0, '.', ',') . '</td>';
                  echo '<td align="right">'  . number_format($row['sj_baru_3'], 0, '.', ',') . '</td>';
                  echo '<td align="center">' . $row['tgl_perubahan'] . '</td>';
                  echo '<td align="center">' . $row['jam_perubahan'] . '</td>';
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

<!-- Add the required CSS and JS libraries -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = $('#table-1').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [
        {
          targets: [4], // Kolom "DESK" tidak dapat diurutkan
          orderable: false
        }
      ],
      buttons: [
        {
          extend: 'copy',
          text: 'Copy' // Ubah teks tombol jika diperlukan
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'PERUBAHAN_HARGA_PAGI_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
          title: null
        }
        
      ],
      dom: 'Bfrtip' // Posisi tombol
    });

    // Tambahkan tombol ke wrapper tabel
    table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
  });

  $(document).ready(function(){
    // Pastikan tabel diinisialisasi dengan fungsionalitas tombol
    var table = $('#table-1').DataTable();
    table.columns.adjust().draw(); // Sesuaikan kolom dengan konten
    $("#load").fadeOut(); // Sembunyikan spinner loading jika ada
  });
</script>
