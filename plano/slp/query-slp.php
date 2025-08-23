<?php
	$query = "SELECT * 
FROM (
    SELECT 
        DATE_TRUNC('day', slp_create_dt) AS slp_tanggal,
        COALESCE(slp_flag, 'R') AS slp_progress,  
        COUNT(DISTINCT slp_prdcd) AS slp_item,
        COUNT(1) AS slp_jumlah,
        ROUND(
            SUM(24 * 60 * (EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) - EXTRACT(EPOCH FROM COALESCE(slp_create_dt, CURRENT_TIMESTAMP))) / 60) / COUNT(1)
        ) AS slp_waktu
    FROM tbtr_slp
    WHERE DATE_TRUNC('day', slp_create_dt) >= DATE_TRUNC('day', CURRENT_DATE) - INTERVAL '3 days'
    GROUP BY DATE_TRUNC('day', slp_create_dt), COALESCE(slp_flag, 'R')
    ORDER BY slp_tanggal DESC, slp_progress DESC
) AS view_slp_waktu
WHERE slp_tanggal = DATE_TRUNC('day', CURRENT_DATE) ";

	// Create connection to Oracle	
	include '../_/connection.php';
	 $stmt = $conn->prepare($query);
?>