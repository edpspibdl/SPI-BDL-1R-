<?php
// Pastikan koneksi menggunakan variabel yang sudah ada
// $tgl diambil dari file utama (index.php)

$sql = "SELECT 
    all_mr.mr AS MR,
    COALESCE(po.total_nominal, 0) AS total_nominal
FROM (
    SELECT DISTINCT cus_nosalesman AS mr
    FROM tbmaster_customer
    WHERE DATE(cus_tglregistrasi) = '$tgl' AND cus_kodeigr = '2G'
    UNION
    SELECT DISTINCT c.cus_nosalesman AS mr
    FROM tbtr_obi_h obi
    JOIN tbmaster_customer c ON c.cus_kodemember = obi.obi_kdmember
    WHERE obi.obi_tglpb = '$tgl' AND c.cus_kodeigr = '2G'
) AS all_mr
LEFT JOIN (
    SELECT 
        c.cus_nosalesman AS mr,
        SUM(obi.obi_ttlorder+obi.obi_ttlppn-obi.obi_ttldiskon) AS total_nominal
    FROM tbtr_obi_h obi
    JOIN tbmaster_customer c ON c.cus_kodemember = obi.obi_kdmember
    WHERE obi.obi_tglpb = '$tgl'
        AND (obi.obi_recid IS NULL OR obi.obi_recid NOT LIKE '%B%')
        AND c.cus_kodeigr = '2G'
    GROUP BY c.cus_nosalesman
) AS po ON all_mr.mr = po.mr
ORDER BY 2 DESC
LIMIT 9";

$result = pg_query($conn, $sql);
$labels = [];
$values = [];
$colors = ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#FF9F40','#C9CBCF','#28a745','#dc3545'];

while ($row = pg_fetch_assoc($result)) {
    $labels[] = $row['mr'];
    $values[] = (int) $row['total_nominal'];
}
?>

<div style="height: 300px;">
    <canvas id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gunakan fungsi untuk memastikan script jalan setelah canvas render
    (function() {
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($values); ?>,
                    backgroundColor: <?php echo json_encode($colors); ?>,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 12 } }
                    }
                }
            }
        });
    })();
</script>