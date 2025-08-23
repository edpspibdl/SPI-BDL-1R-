<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Query pertama
try {
  $query = "WITH date_series AS (
    SELECT GENERATE_SERIES(CURRENT_DATE - INTERVAL '6 days', CURRENT_DATE, INTERVAL '1 day')::DATE AS tanggal
),
detailed_data AS (
    SELECT
        ts.tanggal,
        tf.trf_namafile AS NamaFile,
        tf.trf_namadbf AS NamaDBF, 
        tf.trf_create_by,
        tf.trf_jammulai,
        tf.trf_jamakhir,
        tf.trf_create_dt,
        CASE
            WHEN (tf.trf_jammulai IS NULL OR tf.trf_jamakhir IS NULL OR TRIM(tf.trf_jammulai) = '' OR TRIM(tf.trf_jamakhir) = '')
            THEN 'Kolom trf_jammulai atau trf_jamakhir kosong atau NULL, perlu pengecekan lebih lanjut.'
            ELSE NULL
        END AS keterangan
    FROM
        date_series ts
    LEFT JOIN
        tbtr_transferfile tf ON DATE(tf.trf_create_dt) = ts.tanggal
),
namadbf_counts AS ( 
    SELECT
        tanggal,
        NamaFile,
        COUNT(NamaDBF) AS NamaDBF_Count
    FROM
        detailed_data
    GROUP BY
        tanggal, NamaFile
),
aggregated_data AS (
    SELECT
        dd.tanggal,
        dd.trf_create_by,
        MAX(dd.NamaFile) AS NamaFile,
        MAX(dd.keterangan) AS keterangan,
        COUNT(*) FILTER (
            WHERE dd.trf_create_by = 'JOB'
        ) AS job_count,
        COUNT(*) FILTER (
            WHERE dd.trf_create_by != 'JOB'
        ) AS bypass_count,
        COUNT(*) FILTER (
            WHERE dd.keterangan IS NOT NULL
        ) AS problematic_count,
        MAX(ndc.NamaDBF_Count) AS NamaDBF_TotalCount 
    FROM
        detailed_data dd
    LEFT JOIN
        namadbf_counts ndc ON dd.tanggal = ndc.tanggal AND dd.NamaFile = ndc.NamaFile
    GROUP BY
        dd.tanggal, dd.trf_create_by
),
final_status AS (
    SELECT
        ds.tanggal,
        MAX(CASE
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.tanggal = ds.tanggal AND ad.problematic_count > 1
            ) THEN 'LAKUKAN PENGECEKAN'
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.trf_create_by != 'JOB' AND ad.tanggal = ds.tanggal
            ) THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad2
                        WHERE ad2.trf_create_by = 'JOB' AND ad2.tanggal = ds.tanggal
                    ) THEN 'PROSES BYPASS DAN BYJOB BERHASIL'
                    ELSE 'BYPASS BERHASIL'
                END
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME < TIME '21:15:00' THEN 'JOB BELUM DIJALANKAN'
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME >= TIME '21:15:00' AND CURRENT_TIME < TIME '22:00:00' THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad
                        WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by = 'JOB'
                    ) THEN 'JOB OK'
                    ELSE 'JOB BELUM DIJALANKAN'
                END
            WHEN CURRENT_DATE = ds.tanggal AND CURRENT_TIME >= TIME '22:00:00' THEN
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM aggregated_data ad
                        WHERE ad.tanggal = ds.tanggal AND ad.trf_create_by = 'JOB'
                    ) THEN 'JOB OK'
                    ELSE 'TIDAK JALAN'
                END
            WHEN EXISTS (
                SELECT 1
                FROM aggregated_data ad
                WHERE ad.trf_create_by = 'JOB'
                  AND ad.tanggal = ds.tanggal
            ) THEN 'JOB OK'
            ELSE 'TIDAK JALAN'
        END) AS status,
        STRING_AGG(DISTINCT trf_create_by, ', ') AS created_by,
        STRING_AGG(DISTINCT NamaFile, ', ') AS NamaFile,
        MAX(NamaDBF_TotalCount) AS NamaDBF_Count, 
        CASE
            WHEN MAX(CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM aggregated_data ad
                            WHERE ad.tanggal = ds.tanggal AND ad.problematic_count > 1
                        ) THEN 'LAKUKAN PENGECEKAN'
                        ELSE NULL
                    END) = 'LAKUKAN PENGECEKAN'
            THEN STRING_AGG(DISTINCT keterangan, ', ')
            ELSE NULL
        END AS keterangan
    FROM
        date_series ds
    LEFT JOIN
        aggregated_data ad ON ad.tanggal = ds.tanggal
    GROUP BY
        ds.tanggal
)
SELECT
    tanggal,
    NamaFile,
    NamaDBF_Count, 
    status,
    created_by AS created,
    keterangan
FROM
    final_status
ORDER BY
    tanggal DESC";
  $stmt = $conn->prepare($query);
  $stmt->execute();
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}

// Query kedua
try {
  $query2 = "SELECT * 
FROM job_log_all 
WHERE job_name LIKE '%DTA4%' 
  AND DATE(job_start) >= CURRENT_DATE - INTERVAL '1 day'
ORDER BY job_start DESC";
  $stmt2 = $conn->prepare($query2);
  $stmt2->execute();
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}
?>


<style>
  /* Styling untuk tabel */
  /* Target both tables with the same styles */
  #table-1,
  #table-2 {
    width: 100%;
    table-layout: auto;
    /* Menyesuaikan lebar kolom dengan isi konten */
    border-collapse: collapse;
    /* Menggabungkan border antar sel */
  }

  #table-1 th,
  #table-1 td,
  #table-2 th,
  #table-2 td {
    /* Apply to both tables */
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
    /* Membuat border untuk semua cell */
  }

  #table-1 th,
  #table-2 th {
    /* Apply to headers of both tables */
    background-color: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #333;
    /* Menambahkan pembatas tebal di bawah header */
  }

  #table-1 td,
  #table-2 td {
    /* Apply to cells of both tables */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  /* Styling untuk kolom DESK - This might only apply to table-1 or specific columns, keep if needed for distinct behavior */
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
    <h1>JOB DTA5 SPI BDL 1R</h1>
    <a href="./index.php" class="btn btn-primary">BACK</a>
  </div>
  <div class="row">
    <div class="col-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Informasi Penting</h5>
          <p class="card-text">Job ini wajib dijalankan setiap hari. Tabel terkait untuk Job ini adalah:</p>
          <ul>
            <li><a href="#" onclick="showTable(event, 'tbtr_transferfile_table', 'job_log_all_table')"><strong>tbtr_transferfile</strong></a></li>
            <li><a href="#" onclick="showTable(event, 'job_log_all_table', 'tbtr_transferfile_table')"><strong>job_log_all</strong></a></li>
          </ul>
        </div>
      </div>



      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tabel tbtr_transferfile</h5>
          <div class="table-responsive" id="tbtr_transferfile_table" style="display: block;">
            <table class="table table-hover table-striped w-100" id="table-1">
              <thead>
                <tr class="text-center">
                  <th>NO</th>
                  <th>TANGGAL</th>
                  <th>NAMA FILE</th>
                  <th>JML FILE TRF</th>
                  <th>CREATED_BY</th>
                  <th>STATUS</th>
                  <th>KETERANGAN</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // PHP code to fetch and display data for table-1
                // Ensure $stmt is properly set up from your database query
                $noUrut = 1;
                if (isset($stmt) && $stmt instanceof PDOStatement) {
                  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td align="right">' . $noUrut++ . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['tanggal']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['namafile']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['namadbf_count']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['created']) . '</td>';
                    echo '<td align="left">' . htmlspecialchars($data['status']) . '</td>';
                    echo '<td align="left">' . htmlspecialchars($data['keterangan']) . '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="7" class="text-center">Tidak ada data atau terjadi kesalahan saat mengambil data.</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>



      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Data Tabel job_log_all</h5>
          <div class="table-responsive" id="job_log_all_table" style="display: none;">
            <table class="table table-hover table-striped w-100" id="table-2">
              <thead>
                <tr class="text-center">
                  <th>NO</th>
                  <th>JOB START</th>
                  <th>JOB NAME</th>
                  <th>JOB MESSAGE</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // PHP code to fetch and display data for table-2
                // Ensure $stmt2 is properly set up from your database query
                $noUrut = 1;
                if (isset($stmt2) && $stmt2 instanceof PDOStatement) {
                  while ($data = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td align="right">' . $noUrut++ . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['job_start']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['job_name']) . '</td>';
                    echo '<td align="center">' . htmlspecialchars($data['job_message']) . '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="4" class="text-center">Tidak ada data atau terjadi kesalahan saat mengambil data.</td></tr>';
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
  function showTable(event, tableToShowId, tableToHideId) {
    event.preventDefault();
    const tableToShow = document.getElementById(tableToShowId);
    const tableToHide = document.getElementById(tableToHideId);
    const loading = document.createElement('div');
    loading.id = 'loading';
    loading.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    document.body.appendChild(loading);

    // Simulate a loading delay
    setTimeout(() => {
      document.body.removeChild(loading);
      if (tableToShow) tableToShow.style.display = "block";
      if (tableToHide) tableToHide.style.display = "none";
    }, 500); // Adjust timeout as needed
  }
</script>