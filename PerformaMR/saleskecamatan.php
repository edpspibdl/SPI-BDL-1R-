<?php
require_once '../_/connection.php';

// 1. Ambil Nama Bulan secara dinamis untuk Label Grafik
$label_bln3 = date('F', strtotime('-2 month'));
$label_bln2 = date('F', strtotime('-1 month'));
$label_bln1 = date('F');

// 2. Query Utama (Persis sesuai permintaan Anda)
$query_grafik = "
SELECT 
    kecamatan,
    TRUNC(SUM(CASE WHEN DATE_PART('month', dtl_tanggal) = DATE_PART('month', CURRENT_DATE - INTERVAL '2 month') THEN dtl_netto ELSE 0 END)) AS netto_3,
    TRUNC(SUM(CASE WHEN DATE_PART('month', dtl_tanggal) = DATE_PART('month', CURRENT_DATE - INTERVAL '1 month') THEN dtl_netto ELSE 0 END)) AS netto_2,
    TRUNC(SUM(CASE WHEN DATE_PART('month', dtl_tanggal) = DATE_PART('month', CURRENT_DATE) THEN dtl_netto ELSE 0 END)) AS netto_1
FROM (
    SELECT * FROM (
        SELECT * FROM (
            SELECT * FROM (
                SELECT DISTINCT
                    dtl_tanggal, dtl_no_struk, dtl_cusno, kecamatan,
                    CASE WHEN dtl_rtype = 'R' THEN (dtl_netto * -1) ELSE dtl_netto END AS dtl_netto
                FROM (
                    SELECT DISTINCT
                        DATE_TRUNC('day', trjd_transactiondate) AS dtl_tanggal,
                        trjd_transactionno AS dtl_no_struk,
                        trjd_cus_kodemember AS dtl_cusno,
                        trjd_transactiontype AS dtl_rtype,
                        CASE 
                            WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN CASE WHEN 'Y' = 'Y' THEN trjd_nominalamt END 
                            ELSE 
                                CASE 
                                    WHEN COALESCE(tko_kodesbu, 'z') IN ('O', 'I') THEN 
                                        CASE WHEN tko_tipeomi IN ('HE', 'HG') THEN trjd_nominalamt - (CASE WHEN trjd_flagtax1 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100)))) ELSE 0 END) ELSE trjd_nominalamt END 
                                    ELSE 
                                        trjd_nominalamt - (CASE WHEN SUBSTR(trjd_create_by, 1, 2) = 'EX' THEN 0 ELSE CASE WHEN trjd_flagtax1 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100)))) ELSE 0 END END) 
                                END 
                        END AS dtl_netto
                    FROM tbtr_jualdetail
                    LEFT JOIN tbmaster_prodmast ON trjd_prdcd = prd_prdcd
                    LEFT JOIN tbmaster_tokoigr ON trjd_cus_kodemember = tko_kodecustomer
                    LEFT JOIN tbmaster_customer ON trjd_cus_kodemember = cus_kodemember
                ) sls
                LEFT JOIN (
                    SELECT KDMM, KECAMATAN_TOKO AS kecamatan FROM (
                        SELECT cus_kodemember AS KDMM, cus_alamatmember7 AS KODEPOS_TOKO, UPPER(cus_alamatmember8) AS KELURAHAN_TOKO FROM TBMASTER_CUSTOMER WHERE CUS_KODEIGR='2G'
                    ) DT_MM
                    LEFT JOIN (
                        SELECT POS_KODE, UPPER(pos_kelurahan) AS pskelu, POS_KECAMATAN AS KECAMATAN_TOKO FROM TBMASTER_KODEPOS
                    ) KDPOS ON KODEPOS_TOKO = POS_KODE AND pskelu = KELURAHAN_TOKO
                ) PSKECA ON dtl_cusno = KDMM
            ) AS detailstruk
        ) VSSQ1
    ) AS sq1
    LEFT JOIN (
        SELECT To_char(trjd_transactiondate, 'yyyymmdd') || trjd_cashierstation || trjd_create_by || trjd_transactionno || trjd_transactiontype AS dtl_struk_PB, trjd_transactiondate, trjd_transactionno, trjd_cashierstation 
        FROM TBTR_JUALDETAIL
        INNER JOIN TBTR_OBI_H ON OBI_TGLSTRUK = trjd_transactiondate AND OBI_NOSTRUK = trjd_transactionno AND OBI_KDSTATION = trjd_cashierstation
    ) AS PBH ON dtl_no_struk = dtl_struk_PB
) detailstruk
WHERE dtl_tanggal >= DATE_TRUNC('month', CURRENT_DATE - INTERVAL '2 month')
GROUP BY kecamatan
ORDER BY kecamatan ASC";

$res_grafik = pg_query($conn, $query_grafik);
$data_kecamatan = [];
while ($row = pg_fetch_assoc($res_grafik)) {
    $data_kecamatan[$row['kecamatan']] = [
        (float)$row['netto_3'], 
        (float)$row['netto_2'], 
        (float)$row['netto_1']
    ];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                
                <div class="card card-main shadow">
                    
                    
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4 col-sm-6">
                                <label class="fw-bold mb-1">Pilih Kecamatan:</label>
                                <select id="selectKecamatan" class="form-select border-dark shadow-sm">
                                    <?php foreach (array_keys($data_kecamatan) as $kec): ?>
                                        <option value="<?= htmlspecialchars($kec) ?>"><?= htmlspecialchars($kec ?: 'TIDAK TERDEFINISI') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="chart-box" style="height: 300px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    // Data dari PHP ke JS
    const rawData = <?= json_encode($data_kecamatan) ?>;
    const labels = ["<?= $label_bln3 ?>", "<?= $label_bln2 ?>", "<?= $label_bln1 ?>"];

    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChart;

    function renderChart(kecamatan) {
        const dataSales = rawData[kecamatan];

        if (salesChart) {
            salesChart.destroy(); // Hapus grafik lama sebelum membuat baru
        }

        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Netto Sales (Rp) - ' + kecamatan,
                    data: dataSales,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 10,
                    barThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.raw);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Inisialisasi awal
    const firstKec = document.getElementById('selectKecamatan').value;
    renderChart(firstKec);

    // Event Listener saat dropdown berubah
    document.getElementById('selectKecamatan').addEventListener('change', function() {
        renderChart(this.value);
    });
</script>