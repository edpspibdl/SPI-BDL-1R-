<?php

function jumlahSLP() {
    $query = "SELECT Count(slp_prdcd) AS slp_jumlah
              FROM tbtr_slp
              WHERE slp_flag IS NULL";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['slp_jumlah'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}


	function jumlahSPBManual() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_manual
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND spb_jenis = 'MANUAL'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_manual'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
	function jumlahStorageToStorage() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_stos
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND SPB_LOKASIASAL LIKE '%.S%'
                    AND SPB_LOKASITUJUAN LIKE '%.S%'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_stos'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}


	function jumlahDisplayToDisplay() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_display
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND spb_lokasiasal NOT LIKE '%S%'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_display'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}

	function jumlahSK() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_display
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') = '0'
                    AND spb_lokasiasal LIKE '%S%'
                    AND spb_lokasiasal LIKE '%C%'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_display'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}

	function jumlahStorage() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_display
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND spb_lokasiasal LIKE '%S%'
                    AND spb_lokasiasal NOT LIKE '%C%'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_display'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
	
	
	function jumlahStorageToko() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_display
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND spb_lokasiasal LIKE '%S%'
                    AND spb_lokasiasal NOT LIKE '%C%'
                    AND (spb_lokasitujuan LIKE 'R%' OR spb_lokasitujuan LIKE 'O%')";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_display'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
	
	function jumlahStorageGudang() {
    $query = "SELECT COUNT(spb_prdcd) AS spb_jumlah_display
              FROM tbtemp_antrianspb
              WHERE spb_prdcd IS NOT NULL
                    AND COALESCE(spb_recordid, '0') IN ('0', '3')
                    AND spb_lokasiasal LIKE '%S%'
                    AND spb_lokasiasal NOT LIKE '%C%'
                    AND spb_lokasitujuan LIKE 'D%'";

    // Include PostgreSQL PDO connection
    include '../_/connection.php';

    try {
        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch number of rows
        $numRows = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $numRows = $row['spb_jumlah_display'];
        }

        return $numRows;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
	
	//count rekomendasi
	
	
?>