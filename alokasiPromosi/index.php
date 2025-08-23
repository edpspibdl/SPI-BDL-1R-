<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

try {
    $stmt1 = $conn->prepare("
        SELECT *
        FROM (
            SELECT 
                kode AS kode,
                nama AS nama,
                tglawal AS tglawal,
                tglakhir AS tglakhir,
                alokasi AS alokasi,
                terpakai AS terpakai,
                CASE 
                    WHEN terpakai = 0 THEN alokasi 
                    ELSE sisa 
                END AS sisa,
                alokasi_rp AS alokasi_rp,
                CASE 
                    WHEN alokasi_rp <> 0 THEN terpakai_rp 
                    ELSE 0 
                END AS terpakai_rp,
                CASE 
                    WHEN sisa_rp <> 0 THEN sisa_rp 
                    ELSE 0 
                END AS sisa_rp
            FROM (
                SELECT 
                    cbh_kodepromosi AS kode,
                    cbh_namapromosi AS nama,
                    TO_CHAR(cbh_tglawal, 'dd-MON-yyyy') AS tglawal,
                    TO_CHAR(cbh_tglakhir, 'dd-MON-yyyy') AS tglakhir,
                    cba_alokasijumlah AS alokasi,
                    COALESCE(terpakai, 0) AS terpakai,
                    (cba_alokasijumlah - COALESCE(terpakai, 0)) AS sisa,
                    cba_alokasinilai AS alokasi_rp,
                    (cbh_cashback * COALESCE(terpakai, 0)) AS terpakai_rp,
                    (cba_alokasinilai - (cbh_cashback * COALESCE(terpakai, 0))) AS sisa_rp
                FROM 
                    tbtr_cashback_hdr
                LEFT JOIN tbtr_cashback_alokasi 
                    ON cbh_kodepromosi = cba_kodepromosi
                LEFT JOIN (
                    SELECT 
                        kd_promosi, 
                        SUM(CASE WHEN kelipatan IS NULL THEN 0 ELSE kelipatan END) AS terpakai
                    FROM m_promosi_h
                    GROUP BY kd_promosi
                ) subq2 
                    ON cbh_kodepromosi = kd_promosi
                WHERE 
                    cbh_tglakhir >= current_date
                    AND cba_alokasijumlah <> 0
            ) sub1
            ORDER BY kode
        ) x
    ");
    $stmt1->execute();
    $adaAlokasi = $stmt1->fetchAll(PDO::FETCH_ASSOC);


    $stmt2 = $conn->prepare("
        SELECT * 
FROM (
    SELECT 
        KODE,
        AWAL,
        AKHIR,
        NAMA,
        PLU,
        DESKRIPSI,
        ALOKASI,
        MINSTRUK,
        ALOKASI_CTN,
        KELUAR_CTN,
        (ALOKASI_CTN - KELUAR_CTN) AS SISA
    FROM (
        SELECT 
            D.CBD_KODEPROMOSI AS KODE,
            TO_CHAR(H.CBH_TGLAWAL, 'DD-MON-YYYY') AS AWAL,
            TO_CHAR(H.CBH_TGLAKHIR, 'DD-MON-YYYY') AS AKHIR,
            H.CBH_NAMAPROMOSI AS NAMA,
            D.CBD_PRDCD AS PLU,
            P.PRD_DESKRIPSIPANJANG AS DESKRIPSI,
            D.CBD_ALOKASISTOK AS ALOKASI,
            D.CBD_MINSTRUK AS MINSTRUK,
            (D.CBD_ALOKASISTOK / D.CBD_MINSTRUK) AS ALOKASI_CTN,
            SUM(M.KELIPATAN) AS KELUAR_CTN
        FROM 
            TBTR_CASHBACK_DTL D
            LEFT JOIN TBTR_CASHBACK_HDR H 
                ON D.CBD_KODEPROMOSI = H.CBH_KODEPROMOSI
            LEFT JOIN M_PROMOSI_D M 
                ON D.CBD_PRDCD = M.KD_PLU 
               AND D.CBD_KODEPROMOSI = M.KD_PROMOSI
            LEFT JOIN TBMASTER_PRODMAST P 
                ON D.CBD_PRDCD = P.PRD_PRDCD
        WHERE 
            CURRENT_DATE BETWEEN H.CBH_TGLAWAL AND H.CBH_TGLAKHIR
            AND H.CBH_JENISPROMOSI = '0'
            AND D.CBD_ALOKASISTOK <> 0
        GROUP BY 
            D.CBD_KODEPROMOSI,
            H.CBH_TGLAWAL,
            H.CBH_TGLAKHIR,
            D.CBD_PRDCD,
            P.PRD_DESKRIPSIPANJANG,
            D.CBD_ALOKASISTOK,
            D.CBD_MINSTRUK,
            H.CBH_JENISPROMOSI,
            H.CBH_NAMAPROMOSI,
            (D.CBD_ALOKASISTOK / D.CBD_MINSTRUK)
    ) sub1
    ORDER BY KODE, PLU
) subq1
    ");
    $stmt2->execute();
    $alokPerItem = $stmt2->fetchAll(PDO::FETCH_ASSOC);


    $stmt3 = $conn->prepare("SELECT * 
FROM (
    SELECT 
        kode,
        nama,
        tglawal,
        tglakhir,
        alokasi,
        terpakai,
        CASE 
            WHEN terpakai = 0 THEN alokasi 
            WHEN terpakai <> 0 THEN alokasi - terpakai 
            ELSE sisa 
        END AS sisa
        --, alokasi_rp
        --, CASE WHEN alokasi_rp <> 0 THEN terpakai_rp ELSE 0 END AS terpakai_rp
        --, CASE WHEN sisa_rp <> 0 THEN sisa_rp ELSE 0 END AS sisa_rp
    FROM (
        SELECT 
            cbh_kodepromosi AS kode,
            cbh_namapromosi AS nama,
            TO_CHAR(cbh_tglawal, 'DD-MON-YYYY') AS tglawal,
            TO_CHAR(cbh_tglakhir, 'DD-MON-YYYY') AS tglakhir,
            CASE 
                WHEN cba_alokasijumlah = 0 THEN 99999999 
                ELSE 0 
            END AS alokasi,
            CASE 
                WHEN terpakai IS NULL THEN 0 
                ELSE terpakai 
            END AS terpakai,
            (cba_alokasijumlah - terpakai) AS sisa
            --, cba_alokasinilai AS alokasi_rp
            --, (cbh_cashback * terpakai) AS terpakai_rp
            --, (cba_alokasinilai - (cbh_cashback * terpakai)) AS sisa_rp
        FROM 
            tbtr_cashback_hdr
            LEFT JOIN (
                SELECT * 
                FROM tbtr_cashback_alokasi
            ) sub1 ON cbh_kodepromosi = cba_kodepromosi
            LEFT JOIN (
                SELECT 
                    kd_promosi, 
                    SUM(CASE 
                        WHEN kelipatan IS NULL THEN 0 
                        ELSE kelipatan 
                    END) AS terpakai
                FROM m_promosi_h
                GROUP BY kd_promosi
            ) sub2 ON cbh_kodepromosi = kd_promosi
        WHERE 
            cbh_tglakhir::DATE >= CURRENT_DATE::DATE
            -- AND cba_alokasijumlah <> 999999
    ) subq1
    WHERE alokasi = 99999999
    ORDER BY kode
) subq1
    ");
    $stmt3->execute();
    $nonAlokasi = $stmt3->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<style>
    /* Global Styles for Promo Section */
    .promo-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: bold;
        font-size: 14px;
        background-color: #f9f9f9;
        /* Consistent background */
    }

    .promo-section .container {
        width: 100%;
        margin: auto;
    }

    .promo-section input,
    .promo-section select {
        width: 100%;
        padding: 4px;
        box-sizing: border-box;
    }

    /* Table Styles */
    .promo-section table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        margin-bottom: 20px;
        /* Space between tables */
    }

    .promo-section table,
    .promo-section th,
    .promo-section td {
        border: 1px solid #ccc;
    }

    .promo-section th {
        background-color: #0074D9;
        color: white;
        padding: 8px;
        /* Consistent padding */
        text-align: center;
        white-space: nowrap;
        /* Prevent wrapping in headers */
    }

    .promo-section td {
        padding: 4px;
        text-align: center;
        /* Default center align */
    }

    /* Specific Table Headers */
    .promo-section .header-table td {
        text-align: left;
        background-color: #f1f1f1;
    }

    /* Section Box Styling */
    .promo-section .section-box {
        margin-top: 10px;
        padding: 8px;
        border: 1px solid #ccc;
        background: #fff;
    }

    /* Modal Specific Styles (if used in a modal context) */
    .promo-section .modal-backdrop {
        z-index: 1040 !important;
    }

    .promo-section .modal {
        z-index: 1050 !important;
    }

    .promo-section .modal-dialog {
        z-index: 1060 !important;
    }

    /* Custom Text Alignment for Table Cells */
    .promo-section .text-left-td {
        text-align: left !important;
    }

    .promo-section .text-right-td {
        text-align: right !important;
    }

    /* Style for 'Data tidak ditemukan' row */
    .promo-section .no-data-row td {
        text-align: center;
        font-style: italic;
        color: #777;
        padding: 10px;
    }

    /* Section Titles */
    .promo-section .section-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        margin-top: 20px;
        /* Space above each section title */
    }
</style>

<head>
    <title>Alokasi Promosi Cashback</title>
</head>

<section class="section promo-section">
    <div class="section-header d-flex justify-content-between">
        <h1>Alokasi Promosi Cashback</h1>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-0">
        <div class="section-title">Sisa CashBack (Ada Alokasi)</div>
    </div>
    <table>
        <thead>
            <tr class="info">
                <th rowspan="2">#</th>
                <th colspan="4">Promosi</th>
                <th colspan="3">Cashback</th>
                <th colspan="3">Rupiah</th>
            </tr>
            <tr class="info">
                <th>Kode</th>
                <th>Nama</th>
                <th>Awal</th>
                <th>Akhir</th>
                <th>Alokasi</th>
                <th>Keluar</th>
                <th>Sisa</th>
                <th>Alokasi</th>
                <th>Keluar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($adaAlokasi)): ?>
                <?php foreach ($adaAlokasi as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row['kode'] ?? '') ?></td>
                        <td class="text-left-td"><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tglawal'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tglakhir'] ?? '') ?></td>
                        <td class="text-right-td"><?= number_format($row['alokasi'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['terpakai'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['sisa'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['alokasi_rp'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['terpakai_rp'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['sisa_rp'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="no-data-row">
                    <td colspan="11">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-0">
        <div class="section-title">Sisa CashBack (Ada Alokasi Per Item)</div>
    </div>
    <table>
        <thead>
            <tr class="info">
                <th rowspan="2">#</th>
                <th colspan="4">Promosi</th>
                <th colspan="2">Barang</th>
                <th colspan="5">Rupiah</th>
            </tr>
            <tr class="info">
                <th>Kode</th>
                <th>Nama</th>
                <th>Awal</th>
                <th>Akhir</th>
                <th>Plu</th>
                <th>Desk</th>
                <th>Awal</th>
                <th>Min.Struk</th>
                <th>Alokasi</th>
                <th>Keluar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($alokPerItem)): ?>
                <?php foreach ($alokPerItem as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row['kode'] ?? '') ?></td>
                        <td class="text-left-td"><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['awal'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['akhir'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['plu'] ?? '') ?></td>
                        <td class="text-left-td"><?= htmlspecialchars($row['desk'] ?? '') ?></td>
                        <td class="text-right-td"><?= number_format($row['alokasi'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['minstruk'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['alokasi_ctn'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['keluar_ctn'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['sisa'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="no-data-row">
                    <td colspan="12">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-0">
        <div class="section-title">Sisa CashBack (Non Alokasi)</div>
    </div>
    <table>
        <thead>
            <tr class="info">
                <th rowspan="2">#</th>
                <th colspan="4">Promosi</th>
                <th colspan="3">Cashback</th>
            </tr>
            <tr class="info">
                <th>Kode</th>
                <th>Nama</th>
                <th>Awal</th>
                <th>Akhir</th>
                <th>Alokasi</th>
                <th>Keluar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($nonAlokasi)): ?>
                <?php foreach ($nonAlokasi as $i => $row) { ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row['kode'] ?? '') ?></td>
                        <td class="text-left-td"><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tglawal'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['tglakhir'] ?? '') ?></td>
                        <td class="text-right-td"><?= number_format($row['alokasi'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['terpakai'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right-td"><?= number_format($row['sisa'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php } ?>
            <?php else: ?>
                <tr class="no-data-row">
                    <td colspan="8">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</section>

<?php
require_once '../layout/_bottom.php';
?>