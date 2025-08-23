<?php
function lpp01tlokasi()
{
    // SQL Query to fetch the PLU count
    $sql = "
    SELECT
    prd_kodedivisi,
    st_prdcd,
    prd_deskripsipanjang,
    prd_frac || '/' || prd_unit AS Frac,
    prd_kodetag,
    flag_main,
    st_saldoakhir
FROM
    tbmaster_stock
    LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
    LEFT JOIN (
        SELECT 
            PRD_PRDCD AS PLU_flag,
            CASE 
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+IDM+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+IDM+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+IDM+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IDM+KLIK+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+IDM'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IGR+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'IDM+OMI'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IDM+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'OMI+KLIK'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IGR ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'IDM ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                THEN 'KLIK ONLY'
                WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                THEN 'OMI ONLY'
                ELSE 'BLANK'
            END AS FLAG_MAIN
        FROM 
            TBMASTER_PRODMAST
        WHERE 
            PRD_PRDCD LIKE '%0'
    ) AS flag_table ON flag_table.PLU_flag = st_prdcd
WHERE
    st_lokasi = '01'
    AND st_saldoakhir <> 0
    AND st_prdcd NOT IN (
        SELECT DISTINCT PLU
        FROM (
            SELECT lks_prdcd AS PLU
            FROM tbmaster_lokasi
            WHERE lks_prdcd IS NOT NULL
            UNION
            SELECT lso_prdcd AS PLU
            FROM tbtr_lokasi_so
            WHERE DATE(LSO_TGLSO) = '2024-06-23'
            AND lso_lokasi = '01'
        ) AS combined_plu
    )
ORDER BY st_prdcd ASC
"; // Added alias for the subquery

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;  // Default value if no data is found
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu_count'];
    }

    return $data;
}


function lpp02tlokasi()
{
    $sql = "SELECT COUNT(st_prdcd) AS PLU 
	FROM (
        SELECT
            st_lokasi,
            st_prdcd,
            prd_deskripsipanjang,
            prd_kodetag,
            flag_main,
            st_saldoakhir
        FROM
            tbmaster_stock
            JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
            JOIN (
                SELECT PRD_PRDCD AS PLU_flag,
                CASE 
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IGR+IDM+KLIK+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IGR+KLIK+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IGR+IDM+KLIK'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IGR+IDM+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IDM+KLIK+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IGR+IDM'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IGR+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'IDM+OMI'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IGR+KLIK'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IDM+KLIK'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'OMI+KLIK'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'Y' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IGR ONLY'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'Y') = 'Y' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'IDM ONLY'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'Y' AND COALESCE(PRD_FLAGOMI, 'N') = 'N')
                    THEN 'KLIK ONLY'
                    WHEN (COALESCE(PRD_FLAGIGR, 'N') = 'N' AND COALESCE(PRD_FLAGIDM, 'N') = 'N' AND COALESCE(PRD_FLAGOBI, 'N') = 'N' AND COALESCE(PRD_FLAGOMI, 'N') = 'Y')
                    THEN 'OMI ONLY'
                    ELSE 'BLANK'
                END AS FLAG_MAIN
                FROM TBMASTER_PRODMAST
                WHERE PRD_PRDCD LIKE '%0'
            ) AS subquery ON plu_flag = st_prdcd
        WHERE
            st_lokasi = '02'
            AND st_saldoakhir <> 0
            AND st_prdcd NOT IN (
                SELECT lso_prdcd
                FROM tbtr_lokasi_so
                WHERE DATE(lso_tglso) = '2025-11-23'
                AND lso_lokasi = '02'
            )
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}

function lpp03aqty()
{
    $sql = "SELECT COUNT(st_prdcd) AS PLU FROM (
        SELECT
            st_lokasi,
            st_prdcd,
            prd_deskripsipanjang,
            st_saldoakhir
        FROM
            tbmaster_stock
            LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
        WHERE
            st_saldoakhir <> 0
            AND st_lokasi = '03'
        ORDER BY
            st_lokasi,
            st_prdcd
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}


function lppminus()
{
    $sql = "SELECT COUNT(st_prdcd) AS PLU FROM (
        SELECT
            st_lokasi,
            st_prdcd,
            prd_deskripsipanjang,
            st_saldoakhir
        FROM
            tbmaster_stock
            LEFT JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
        WHERE
            st_saldoakhir < 0
        ORDER BY
            st_lokasi,
            st_prdcd
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}


function planominus()
{
    $sql = "SELECT COUNT(lks_prdcd) AS PLU FROM (
        SELECT
            lks_koderak
            || '.' || 
            lks_kodesubrak
            || '.' || 
            lks_tiperak
            || '.' || 
            lks_shelvingrak AS lokasi,
            lks_prdcd,
            prd_deskripsipanjang,
            lks_qty
        FROM
            tbmaster_lokasi
            JOIN tbmaster_prodmast ON prd_prdcd = lks_prdcd
        WHERE
            lks_qty < 0
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}


function acostminus()
{
    $sql = "SELECT COUNT(st_prdcd) AS PLU FROM (
        SELECT
            st_lokasi,
            st_prdcd,
            prd_deskripsipanjang,
            st_saldoakhir,
            st_avgcost
        FROM
            tbmaster_stock
            JOIN tbmaster_prodmast ON prd_prdcd = st_prdcd
        WHERE
            st_avgcost <= 0
            AND st_saldoakhir <> 0
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}


function inputkkso()
{
    $sql = "SELECT COUNT(lks) AS PLU FROM (
        SELECT
            prd_kodedivisi,
            lso_koderak
            || '.'
            || lso_kodesubrak
            || '.'
            || lso_tiperak
            || '.'
            || lso_shelvingrak
            || '.'
            || lso_nourut AS lks,
            lso_prdcd,
            prd_deskripsipanjang,
            prd_frac,
            prd_unit,
            st_saldoakhir
        FROM
            tbtr_lokasi_so
            JOIN tbmaster_prodmast ON prd_prdcd = lso_prdcd
            JOIN (
                SELECT
                    *
                FROM
                    tbmaster_stock
                WHERE
                    st_lokasi = '01'
            ) AS stock ON st_prdcd = lso_prdcd
        WHERE
            DATE(lso_tglso) = '2024-10-23'
            AND lso_flagsarana = 'K'
            AND lso_koderak LIKE 'P%'
        ORDER BY
            lso_koderak,
            lso_kodesubrak,
            lso_tiperak,
            lso_shelvingrak,
            lso_nourut ASC
    ) AS subquery";

    include '../helper/connection.php';

    // Using PDO to execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $data = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['plu'];
    }

    return $data;
}
