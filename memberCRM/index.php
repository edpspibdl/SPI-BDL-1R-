<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

// Menggunakan exception handling pada query
try {
  $query = "SELECT 
    crm_koordinat AS koordinat, 
    cus_kodemember AS kode_member,                                                                                                                                                                                         
    cus_namamember AS nama,                      
    cus_alamatmember5 AS alamat_surat,   
    crm_jeniskelamin AS jenis_kelamin,  
    crm_alamatusaha1 AS alamat_usaha,              
    cus_alamatmember8 AS kelurahan,                                                                                                                                                                                    
    pos_kecamatan AS kecamatan,                                                                                                                                                                                    
    cus_alamatmember6 AS kota,                                                                                                                                                                                  
    cus_alamatmember7 AS kpos,                                                                                                                                                                                  
    cus_jarak AS jarak,                                                                                                                                                                                  
    cus_kodeoutlet AS outlet,                                                                                                                                                                                      
    cus_kodesuboutlet AS sub_outlet,      
    cus_noktp AS no_ktp,         
    cus_npwp AS NPWP,      
    cus_flagpkp AS Status_Pkp,      
    cus_jenismember AS jenis_member,      
    grp_kategori AS kategori,                                                                                                                                                                                  
    grp_subkategori AS subkategori,                                                                                                                                                                                  
    DATE(cus_tglregistrasi) AS tgl_registrasi,                                                                                                                                                                                  
    DATE(cus_modify_dt) AS tgl_update,                                                                                                                                                                                  
    cus_tlpmember AS no_telp,                                                                                                                                                                                  
    cus_hpmember AS no_hp, 
    cus_TGLLAHIR,                                                                                                                                                                                  
    DATE(kunjungan_terakhir) AS kunjungan_terakhir,                                                                                                                                                                              
    cus_nosalesman AS salesman,                                                                                                                                                                                  
    COALESCE(point, 0) AS point,
    COALESCE(tukar_point, 0) AS tukar_point,
    crm_idsegment AS segmen,                                                                                                                                                                                  
    fba_namafasilitasbank AS namabank,            
    cus_flag_verifikasi AS verifikasi,      
    cus_flag_mypoin AS mypoin,      
    cus_flag_isaku AS isaku,      
    cus_mypoin_dt AS tgl_verifikasi_mypoin,      
    cus_isaku_dt AS tgl_verifikasi_isaku,      
    COALESCE(sales, 0) AS sales,                                                                                                                                                                                         
    COALESCE(kunj, 0) AS kunj,                                                                                                                                                                                  
    COALESCE(produk_mix, 0) AS produk_mix                                                                                                                                                                                  
FROM tbmaster_customer c                                                                                                                                                                                       
LEFT JOIN (
    SELECT DISTINCT 
        crm_kodemember, 
        crm_jeniskelamin, 
        crm_idgroupkat, 
        grp_kategori,                                                                                                                                                                                  
        grp_subkategori, 
        crm_alamatusaha1,  
        crm_idsegment,                                                                                                                                                                                  
        crm_koordinat  
    FROM tbmaster_customercrm 
    LEFT JOIN tbtabel_groupkategori ON crm_idgroupkat = grp_idgroupkat
) sub1 ON c.cus_kodemember = sub1.crm_kodemember

LEFT JOIN tbmaster_kodepos p ON c.cus_alamatmember7 = p.pos_kode AND c.cus_alamatmember8 = p.pos_kelurahan                                                                                                                                                                                       
LEFT JOIN tbmaster_user u ON c.cus_nosalesman = u.userid 

LEFT JOIN (
    SELECT DISTINCT
        cub_kodemember, 
        cub_kodefasilitasbank, 
        fba_namafasilitasbank 
    FROM tbmaster_customerfasilitasbank 
    LEFT JOIN tbmaster_fasilitasperbankan ON cub_kodefasilitasbank = fba_kodefasilitasbank
) sub2 ON c.cus_kodemember = sub2.cub_kodemember                                                                                                                                                                                      

LEFT JOIN (
    SELECT jh_cus_kodemember, 
           SUM(jh_transactionamt) AS sales, 
           COUNT(DISTINCT DATE(jh_transactiondate)) AS kunj
    FROM tbtr_jualheader                    
    WHERE DATE(jh_transactiondate) BETWEEN CURRENT_DATE - 900 AND CURRENT_DATE              
    GROUP BY jh_cus_kodemember
) sub3 ON c.cus_kodemember = sub3.jh_cus_kodemember    

LEFT JOIN (
    SELECT trjd_cus_kodemember, 
           MAX(trjd_transactiondate) AS kunjungan_terakhir          
    FROM tbtr_jualdetail 
    GROUP BY trjd_cus_kodemember
) sub4 ON c.cus_kodemember = sub4.trjd_cus_kodemember 

LEFT JOIN (
    SELECT trjd_cus_kodemember,  
           COUNT(DISTINCT SUBSTR(trjd_prdcd,1,6) || '0') AS produk_mix                                                                                                                                                                                  
    FROM tbtr_jualdetail                     
    WHERE DATE(trjd_transactiondate) BETWEEN CURRENT_DATE - 900 AND CURRENT_DATE                   
    GROUP BY trjd_cus_kodemember
) sub5 ON c.cus_kodemember = sub5.trjd_cus_kodemember   

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

WHERE c.cus_recordid IS NULL                                                                                                                                                                                         
AND c.cus_flagmemberkhusus = 'Y'                                                                                                                                                                  
AND c.cus_kodeigr = '1R'                                                                                                                                                                                       
AND c.cus_namamember <> 'NEW'                                                                                                                                                                                     

ORDER BY koordinat DESC";

  $stmt = $conn->prepare($query);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $count_crm_null = 0;
  $count_crm = 0;
  foreach ($result as $row) {
    if (!empty($row['koordinat']) && trim($row['koordinat']) !== '') {
      $count_crm++;
    } else {
      $count_crm_null++;
    }
  }
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
  <div class="section-header">
    <h1>Member CRM SPI BDL 1R</h1>
  </div>
  <div class="alert alert-danger mt-2">
    <strong>Total Records CRM Kordinat Null: <?= $count_crm_null ?></strong>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="table-1">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Koordinat</th>
                  <th>Kode Member</th>
                  <th>Nama Member</th>
                  <th>Alamat Surat</th>
                  <th>Jenis Kelamin</th>
                  <th>Alamat Usaha</th>
                  <th>Kelurahan</th>
                  <th>Kecamatan</th>
                  <th>Kota</th>
                  <th>Kode Pos</th>
                  <th>Jarak</th>
                  <th>Kode Outlet</th>
                  <th>Kode Suboutlet</th>
                  <th>No KTP</th>
                  <th>NPWP</th>
                  <th>Status PKP</th>
                  <th>Jenis Member</th>
                  <th>Kategori</th>
                  <th>Subkategori</th>
                  <th>Tanggal Registrasi</th>
                  <th>Tanggal Update</th>
                  <th>No Telepon</th>
                  <th>No HP</th>
                  <th>Tanggal Lahir</th>
                  <th>Kunjungan Terakhir</th>
                  <th>No Salesman</th>
                  <th>Saldo Point</th>
                  <th>Point Ditukar</th>
                  <th>ID Segment</th>
                  <th>Nama Bank</th>
                  <th>Verifikasi</th>
                  <th>MyPoin</th>
                  <th>iSaku</th>
                  <th>Tgl Verifikasi MyPoin</th>
                  <th>Tgl Verifikasi iSaku</th>
                  <th>Sales</th>
                  <th>Kunjungan</th>
                  <th>Produk Mix</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($result)): ?>
                  <?php $nomor = 1; ?>
                  <?php foreach ($result as $row): ?>
                    <tr>
                      <td><?= $nomor++ ?></td>
                      <td><?= htmlspecialchars($row["koordinat"]) ?></td>
                      <td><?= htmlspecialchars($row["kode_member"]) ?></td>
                      <td><?= htmlspecialchars($row["nama"]) ?></td>
                      <td><?= htmlspecialchars($row["alamat_surat"]) ?></td>
                      <td><?= htmlspecialchars($row["jenis_kelamin"]) ?></td>
                      <td><?= htmlspecialchars($row["alamat_usaha"]) ?></td>
                      <td><?= htmlspecialchars($row["kelurahan"]) ?></td>
                      <td><?= htmlspecialchars($row["kecamatan"]) ?></td>
                      <td><?= htmlspecialchars($row["kota"]) ?></td>
                      <td><?= htmlspecialchars($row["kpos"]) ?></td>
                      <td><?= htmlspecialchars($row["jarak"]) ?></td>
                      <td><?= htmlspecialchars($row["outlet"]) ?></td>
                      <td><?= htmlspecialchars($row["sub_outlet"]) ?></td>
                      <td><?= htmlspecialchars($row["no_ktp"]) ?></td>
                      <td><?= htmlspecialchars($row["npwp"]) ?></td>
                      <td><?= htmlspecialchars($row["status_pkp"]) ?></td>
                      <td><?= htmlspecialchars($row["jenis_member"]) ?></td>
                      <td><?= htmlspecialchars($row["kategori"]) ?></td>
                      <td><?= htmlspecialchars($row["subkategori"]) ?></td>
                      <td><?= htmlspecialchars($row["tgl_registrasi"]) ?></td>
                      <td><?= htmlspecialchars($row["tgl_update"]) ?></td>
                      <td><?= htmlspecialchars($row["no_telp"]) ?></td>
                      <td><?= htmlspecialchars($row["no_hp"]) ?></td>
                      <td><?= htmlspecialchars($row["cus_tgllahir"]) ?></td>
                      <td><?= htmlspecialchars($row["kunjungan_terakhir"]) ?></td>
                      <td><?= htmlspecialchars($row["salesman"]) ?></td>
                      <td><?= htmlspecialchars($row["point"]) ?></td>
                      <td><?= htmlspecialchars($row["tukar_point"]) ?></td>
                      <td><?= htmlspecialchars($row["segmen"]) ?></td>
                      <td><?= htmlspecialchars($row["namabank"]) ?></td>
                      <td><?= htmlspecialchars($row["verifikasi"]) ?></td>
                      <td><?= htmlspecialchars($row["mypoin"]) ?></td>
                      <td><?= htmlspecialchars($row["isaku"]) ?></td>
                      <td><?= htmlspecialchars($row["tgl_verifikasi_mypoin"]) ?></td>
                      <td><?= htmlspecialchars($row["tgl_verifikasi_isaku"]) ?></td>
                      <td><?= htmlspecialchars($row["sales"]) ?></td>
                      <td><?= htmlspecialchars($row["kunj"]) ?></td>
                      <td><?= htmlspecialchars($row["produk_mix"]) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="20" class="text-center">Tidak ada data</td>
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
    const table = $('#table-1').DataTable({
      responsive: false,
      lengthMenu: [15, 25, 50, 100],
      columnDefs: [{
        targets: [4], // Kolom "DESK" tidak dapat diurutkan
        orderable: false
      }],
      buttons: [{
          extend: 'copy',
          text: 'Copy' // Ubah teks tombol jika diperlukan
        },
        {
          extend: 'excel',
          text: 'Excel',
          filename: 'MEMBER_CRM_' + new Date().toISOString().split('T')[0], // Nama file dengan tanggal saat ini
          title: null
        },
        {
          extend: 'colvis',
          text: 'Column Visibility' // Tombol untuk menampilkan/sembunyikan kolom
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
</body>