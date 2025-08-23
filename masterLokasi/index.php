<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid


// Ambil nilai filter dari parameter URL, default 'D'
$jenisrak = $_GET['jenisrak'] ?? 'D';


try {
    $query = "SELECT 
    prd.prd_kodedivisi         AS lks_div,
    prd.prd_kodedepartement    AS lks_dept,
    prd.prd_kodekategoribarang AS lks_kat,
    CONCAT(
        lks.lks_koderak, '.', 
        lks.lks_kodesubrak, '.', 
        lks.lks_tiperak, '.', 
        lks.lks_shelvingrak, '.', 
        lks.lks_nourut
    ) AS LOKASI,
    lks.lks_noid,
    lks.lks_prdcd,
    prd.prd_deskripsipanjang   AS lks_namabarang,
    prd.prd_unit               AS lks_unit,
    prd.prd_frac               AS lks_frac,
    prd.prd_kodetag            AS lks_kodetag,
    lks.lks_jenisrak,
    lks.lks_qty,
    lks.lks_maxplano,
    COALESCE(st.st_saldoakhir, 0) AS lks_lpp, -- Jika saldo akhir tidak ada, tampilkan 0
    hgb.hgb_kodesupplier       AS lks_kodesupplier,
    hgb.sup_namasupplier       AS lks_namasupplier,
    prd.prd_flagomi            AS lks_flagomi
FROM 
    tbmaster_lokasi lks
JOIN 
    tbmaster_prodmast prd ON lks.lks_prdcd = prd.prd_prdcd
LEFT JOIN 
    (SELECT * FROM tbmaster_stock WHERE st_lokasi = '01') st ON lks.lks_prdcd = st.st_prdcd
LEFT JOIN 
    (SELECT 
         hgb_prdcd,
         hgb_kodesupplier,
         sup_namasupplier
     FROM 
         tbmaster_hargabeli hgb
     JOIN 
         tbmaster_supplier sup ON hgb.hgb_kodesupplier = sup.sup_kodesupplier
     WHERE 
         hgb.hgb_tipe = '2') hgb ON lks.lks_prdcd = hgb.hgb_prdcd
WHERE 
    lks.lks_prdcd IS NOT NULL
      AND lks_jenisrak = :jenisrak
ORDER BY 
    lks.lks_koderak,
    lks.lks_kodesubrak,
    lks.lks_tiperak,
    lks.lks_shelvingrak,
    lks.lks_nourut";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':jenisrak', $jenisrak);
    $stmt->execute();

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
    <h1>Master Lokasi</h1>
     <?php if ($jenisrak == 'D') : ?>
  <a href="?jenisrak=S" class="btn btn-primary">Lokasi Storage</a>
<?php elseif ($jenisrak == 'S') : ?>
  <a href="?jenisrak=D" class="btn btn-danger">Display Rak</a>
<?php endif; ?>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th>#</th>
                  <th>DIV</th>
                  <th>DEPT</th>
                  <th>KATB</th>
                  <th>LOKASI</th>
                  <th>NOID</th>
                  <th>PLU</th>
                  <th>DESK</th>
                  <th>UNIT</th>
                  <th>FRAC</th>
                  <th>TAG</th>
                  <th>JENIS RAK</th>
                  <th>PLANO</th>
                  <th>LPP</th>
                  <th>KODE</th>
                  <th>SUPP</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Loop melalui hasil query dan tampilkan data
                $no = 1; // Menambahkan variabel nomor urut
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>";
                    echo "<td>" . $no++ . "</td>"; // Menambahkan nomor urut
                    echo "<td>" . htmlspecialchars($row["lks_div"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_dept"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_kat"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lokasi"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_noid"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_prdcd"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_namabarang"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_unit"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_frac"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_kodetag"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_jenisrak"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_qty"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_lpp"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_kodesupplier"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["lks_namasupplier"]) . "</td>";
                  echo "</tr>";
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
          filename: 'Master_Lokasi_Beam_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
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
