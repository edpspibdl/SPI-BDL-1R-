<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) && $_GET['kodePLU'] !== "" ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
$data = [];

if ($kodePLU !== '') {
    try {
        // Query 1: Informasi produk utama
        $stmt = $conn->prepare("SELECT DISTINCT ON (prd_prdcd)
            prd_prdcd, 
            prd_deskripsipanjang,
            prd_kategoritoko,
            prd_kodecabang, 
            prd_flaggudang,
            prd_create_dt,
            prd_kodedivisi || '   ' || COALESCE(div_namadivisi, '') || ' - ' || 
            COALESCE(prd_kodekategoribarang, '') || '  ' || COALESCE(kat_namakategori, '') || ' - ' ||
            COALESCE(prd_kodedepartement, '') || '  ' || COALESCE(dep_namadepartement, '') AS div_dept_kat
        FROM tbmaster_prodmast
        LEFT JOIN tbmaster_divisi ON prd_kodedivisi = div_kodedivisi
        LEFT JOIN tbmaster_departement ON prd_kodedepartement = dep_kodedepartement
        LEFT JOIN tbmaster_kategori ON prd_kodekategoribarang = kat_kodekategori
        WHERE prd_prdcd = :kodePLU");
        $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Query 2: Barcode and Promo Price Data
        $stmt2 = $conn->prepare("SELECT 
    pm.PRD_KODEDIVISI,
    pm.PRD_KODEDEPARTEMENT,
    pm.PRD_PRDCD,
    pm.PRD_DESKRIPSIPANJANG,
    pm.PRD_UNIT,
    pm.PRD_FRAC,
    pm.PRD_HRGJUAL,
    pm.PRD_KODETAG,
    pc.PRC_KODETAG,
    pm.PRD_FLAG_AKTIVASI,
    pm.PRD_AVGCOST,
    pm.PRD_LASTCOST,
    pm.PRD_MINJUAL,
    md.PRMD_HRGJUAL,
    md.PRMD_TGLAWAL,
    md.PRMD_TGLAKHIR,
    bc.BRC_BARCODE,

    -- Hitung margin berdasarkan flag BKP
    CASE 
        WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' 
            THEN ((pm.PRD_HRGJUAL - (pm.PRD_AVGCOST * 1.11)) / pm.PRD_HRGJUAL * 100)
        WHEN pm.PRD_FLAGBKP1 IS NULL AND pm.PRD_FLAGBKP2 IN ('N','C') 
            THEN ((pm.PRD_HRGJUAL - pm.PRD_AVGCOST) / pm.PRD_HRGJUAL * 100)
    END AS MARGIN,

            ROUND(
            CASE         
                    WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN ((pm.PRD_HRGJUAL - pm.PRD_AVGCOST * 1.11) / pm.PRD_HRGJUAL) * 100   
                    WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' THEN ((pm.PRD_HRGJUAL - pm.PRD_AVGCOST) / pm.PRD_HRGJUAL) * 100    
                    WHEN pm.PRD_FLAGBKP1 = 'N' THEN ((pm.PRD_HRGJUAL - pm.PRD_AVGCOST) / pm.PRD_HRGJUAL) * 100    
                END, 2
            ) AS MARGINACOST, 

            ROUND(
                CASE         
                    WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN ((pm.PRD_HRGJUAL - pm.PRD_LASTCOST * 1.11) / pm.PRD_HRGJUAL) * 100  
                    WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 <> 'Y' THEN ((pm.PRD_HRGJUAL - pm.PRD_LASTCOST) / pm.PRD_HRGJUAL) * 100  
                    WHEN pm.PRD_FLAGBKP1 = 'N' THEN ((pm.PRD_HRGJUAL - pm.PRD_LASTCOST) / pm.PRD_HRGJUAL) * 100        
                END, 2
            ) AS MARGINLCOST,

    -- KALI: nilai pengali PPN
    CASE 
        WHEN pm.PRD_FLAGBKP1 = 'Y' AND pm.PRD_FLAGBKP2 = 'Y' THEN 1.11
        WHEN pm.PRD_FLAGBKP1 = 'N' AND pm.PRD_FLAGBKP2 IN ('N','C') THEN 1
    END AS KALI,

    -- ST_HARGA_NETTO
    CASE 
        WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
            THEN pm.PRD_HRGJUAL / 11.1 * 10
        ELSE pm.PRD_HRGJUAL
    END AS ST_HARGA_NETTO,

    -- ST_MD_NETTO
    CASE 
        WHEN COALESCE(pm.prd_flagbkp1,'T') = 'Y' AND COALESCE(pm.prd_flagbkp2,'T') = 'Y' 
            THEN md.PRMD_HRGJUAL / 11.1 * 10
        ELSE md.PRMD_HRGJUAL
    END AS ST_MD_NETTO,

    -- Kolom satuan jual berdasarkan digit terakhir PRD_PRDCD
    CASE 
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '0' THEN '0'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '1' THEN '1'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '2' THEN '2'
        WHEN RIGHT(pm.PRD_PRDCD, 1) = '3' THEN '3'
        ELSE NULL
    END AS sj,

    pm.PRD_FLAGBKP1,
    pm.PRD_FLAGBKP2

FROM tbmaster_prodmast pm

LEFT JOIN (
    SELECT prmd_prdcd, prmd_hrgjual, prmd_tglawal, prmd_tglakhir
    FROM tbtr_promomd
    WHERE CURRENT_DATE BETWEEN prmd_tglawal AND prmd_tglakhir
) md ON pm.prd_prdcd = md.prmd_prdcd

LEFT JOIN tbmaster_prodcrm pc ON pm.prd_prdcd = pc.prc_pluigr

-- Ambil hanya 1 barcode per produk menggunakan DISTINCT ON
LEFT JOIN (
    SELECT DISTINCT ON (brc_prdcd) brc_prdcd, brc_barcode
    FROM tbmaster_barcode
    ORDER BY brc_prdcd, brc_barcode  -- Pilih barcode terkecil (atau ubah sesuai kebutuhan)
) bc ON pm.prd_prdcd = bc.brc_prdcd

WHERE pm.PRD_PRDCD LIKE :kodePLU

ORDER BY pm.PRD_MINJUAL, pm.PRD_AVGCOST DESC");

        // Update kodePLU for LIKE clause
        $kodePLU = substr($kodePLU, 0, 6) . '%';
        $stmt2->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
        $stmt2->execute();


    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
?>

<!-- STYLE -->
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; background-color: #f9f9f9; }
    .container { width: 100%; margin: auto; }
    .section-title { font-weight: bold; margin-top: 10px; margin-bottom: 5px; }
    input, select { width: 100%; padding: 4px; box-sizing: border-box; }
    table { width: 100%; border-collapse: collapse; background-color: white; }
    table, th, td { border: 1px solid #ccc; }
    th { background-color: #0074D9; color: white; }
    td, th { padding: 4px; text-align: center; }
    .header-table td { text-align: left; background-color: #f1f1f1; }
    .section-box { margin-top: 10px; padding: 8px; border: 1px solid #ccc; background: #fff; }

    .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
        .modal-dialog {
            z-index: 1060 !important;
        }
</style>

<head><title>Informasi & History Product</title></head>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Informasi Promosi Dan Produk</h1>
    </div>

    <!-- Tombol Toggle -->
    <div class="d-flex justify-content-between align-items-center">
        <div class="section-title">Produk</div>
        <button class="btn btn-sm btn-outline-secondary" onclick="toggleProduk()">Hide</button>
    </div>

    <!-- Kontainer yang akan di-hide/unhide -->
    <div id="produkContainer">
        <table>
            <tr>
                <td>PLU</td>
                <td><input type="text" id="kodePLU" value="<?= htmlspecialchars($data['prd_prdcd'] ?? '') ?>" readonly></td>
                <td>Flag Gdg</td>
                <td><input type="text" value="<?= htmlspecialchars($data['prd_flaggudang'] ?? '') ?>" readonly></td>
                <td>Kd Cabang</td>
                <td><input type="text" value="<?= htmlspecialchars($data['prd_kodecabang'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Product</td>
                <td colspan="3"><input type="text" value="<?= htmlspecialchars($data['prd_deskripsipanjang'] ?? '') ?>" readonly></td>
                <td>Kat. Toko</td>
                <td><input type="text" value="<?= htmlspecialchars($data['prd_kategoritoko'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Kat.Brg</td>
                <td colspan="3"><input type="text" value="<?= htmlspecialchars($data['div_dept_kat'] ?? '') ?>" readonly></td>
                <td>Upd.</td>
                <td><input type="text" value="<?= htmlspecialchars($data['prd_create_dt'] ?? '') ?>" readonly></td>
            </tr>
        </table>
    </div>


    <div id="promoContainer">
    <table>
        <thead>
            <tr class="primary">
                <th rowspan="2" class="text-center">#</th>
                <th rowspan="2" class="text-center">Kode</th>
                <th colspan="2" class="text-center">Periode</th>
                <th colspan="3" class="text-center">Min Belanja</th>
                <th colspan="6" class="text-center">Hadiah</th>
                <th rowspan="2" class="text-center">Jenis Member</th>
            </tr>
            <tr class="primary">
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Qty</th>
                <th>Sponsor</th>
                <th>Struk Rp.</th>
                <th>PLU</th>
                <th>Nama</th>
                <th>Jml</th>
                <th>Klpt</th>
                <th>Alokasi</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inStore)): ?>
                <?php $noUrut = 1; ?>
                <?php foreach ($inStore as $i => $row): ?>
                    <tr>
                        <td class="text-center"><?= $noUrut++ ?></td>
                        <td><?= htmlspecialchars($row['isd_kodepromosi']) ?> - <?= htmlspecialchars($row['isd_jenispromosi']) ?></td>
                        <td><?= htmlspecialchars($row['ish_tglawal']) ?></td>
                        <td><?= htmlspecialchars($row['ish_tglakhir']) ?></td>
                        <td class="text-end"><?= number_format($row['isd_minpcs'], 0, '.', ',') ?></td>
                        <td class="text-end"><?= number_format($row['isd_minrph'], 0, '.', ',') ?></td>
                        <td class="text-end"><?= number_format($row['ish_minstruk'], 0, '.', ',') ?></td>
                        <td><?= htmlspecialchars($row['ish_prdcdhadiah']) ?></td>
                        <td><?= htmlspecialchars($row['bprp_ketpanjang']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['ish_jmlhadiah']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['ish_kelipatanhadiah']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['ish_qtyalokasi']) ?></td>
                        <td class="text-end"><?= number_format(($row['ish_qtyalokasi'] ?? 0) - ($row['ish_qtyalokasiout'] ?? 0), 0, '.', ',') ?></td>
                        <td>
                            <?php
                                $jenisMember = '';
                                if ($row['ish_reguler'] == '1') $jenisMember .= 'REG ';
                                if ($row['ish_regulerbiruplus'] == '1') $jenisMember .= 'RB+ ';
                                if ($row['ish_freepass'] == '1') $jenisMember .= 'FRE ';
                                if ($row['ish_retailer'] == '1') $jenisMember .= 'RET ';
                                if ($row['ish_silver'] == '1') $jenisMember .= 'SIL ';
                                if ($row['ish_gold1'] == '1') $jenisMember .= 'GD1 ';
                                if ($row['ish_gold2'] == '1') $jenisMember .= 'GD2 ';
                                if ($row['ish_gold3'] == '1') $jenisMember .= 'GD3 ';
                                if ($row['ish_platinum'] == '1') $jenisMember .= 'PLA ';
                                echo htmlspecialchars($jenisMember);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="15" class="text-center">Data tidak ditemukan</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    <!-- Tombol buka modal -->
    <button type="button" class="btn btn-primary mt-3" onclick="loadModalInfoProduk()">
        Tambah Info Produk
    </button>
</section>

<script>
function toggleProduk() {
    const container = document.getElementById('produkContainer');
    const btn = event.target;

    if (container.style.display === 'none') {
        container.style.display = 'block';
        btn.textContent = 'Hide';
    } else {
        container.style.display = 'none';
        btn.textContent = 'Show';
    }
}
</script>


<?php
require_once '../layout/_bottom.php';
?>
