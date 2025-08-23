<?php
	$query = "SELECT * 
FROM (
    SELECT 
        DATE_TRUNC('day', slp_create_dt) AS slp_tanggal,
        COALESCE(slp_flag, 'R') AS slp_progress,  
        COUNT(DISTINCT slp_prdcd) AS slp_item,
        COUNT(1) AS slp_jumlah,
        ROUND(SUM(EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - COALESCE(slp_create_dt, CURRENT_TIMESTAMP))) / 60) / COUNT(1)) AS slp_waktu
    FROM tbtr_slp
    WHERE DATE_TRUNC('day', slp_create_dt) >= DATE_TRUNC('day', CURRENT_DATE) - INTERVAL '3 days'
    GROUP BY DATE_TRUNC('day', slp_create_dt), COALESCE(slp_flag, 'R')
) AS subquery
WHERE slp_tanggal = DATE_TRUNC('day', CURRENT_TIMESTAMP)";

	// Include PostgreSQL connection
include '../_/connection.php';

// Execute PostgreSQL query
$result = pg_query($conn, $query);
if (!$result) {
    // Query execution failed
    $error_message = pg_last_error($conn);
    echo "Error in executing PostgreSQL query: " . $error_message;
} else {
    // Query execution successful
    while ($row = pg_fetch_assoc($result)) {
        // Process each row
        // Example: echo $row['slp_tanggal'];
    }
}
?>