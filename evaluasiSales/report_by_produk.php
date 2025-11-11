<?php

require_once '../layout/_top.php';
require_once '../helper/connection.php';
// Assuming $viewDetailStruk is defined in this file or a linked file
include '../views/view_detail_struk.php';


$tanggalMulai   = $_GET['tanggalMulai'] ?? '';
$tanggalSelesai = $_GET['tanggalSelesai'] ?? '';

$kodeMember   = $_GET['kodeMember']   ?? 'All';
$kodePLU      = $_GET['kodePLU']      ?? 'All';
$kodeSupplier = $_GET['kodeSupplier'] ?? 'All';
$namaSupplier = $_GET['namaSupplier'] ?? 'All';
$kodeDivisi   = $_GET['kodeDivisi']   ?? 'All';

// --- Format Tanggal untuk SQL ---
$tanggalMulaiFormatted = $tanggalMulai ? date('Ymd', strtotime($tanggalMulai)) : '';
$tanggalSelesaiFormatted = $tanggalSelesai ? date('Ymd', strtotime($tanggalSelesai)) : '';

if (!isset($viewDetailStruk) || empty($viewDetailStruk)) {
    $viewDetailStruk = 'nama_view_default';
}

// --- Query Awal ---
$query = "
    SELECT
        CAST(dtl_prdcd_ctn AS NUMERIC) AS dtl_prdcd_ctn,
        dtl_nama_barang,
        dtl_k_div,
        dtl_k_dept,
        dtl_k_katb,
        pkm.pkm_pkmt,
        COUNT(DISTINCT dtl_tanggal) AS kunjungan,
        COUNT(DISTINCT dtl_cusno) AS jml_member,
        COUNT(DISTINCT dtl_struk) AS struk,
        SUM(dtl_qty_pcs) AS qty_in_pcs,
        SUM(dtl_gross) AS dtl_gross,
        SUM(dtl_netto) AS dtl_netto,
        SUM(dtl_margin) AS dtl_margin,
        ROUND(SUM(dtl_margin) / NULLIF(SUM(dtl_netto), 0) * 100, 2) AS dtl_margin_persen
    FROM " . $viewDetailStruk . "
    LEFT JOIN tbmaster_prodmast prd ON dtl_prdcd_ctn = prd.prd_prdcd
    LEFT JOIN (
        SELECT st_prdcd, st_saldoakhir
        FROM tbmaster_stock
        WHERE st_lokasi = '01'
    ) stk ON dtl_prdcd_ctn = stk.st_prdcd
    LEFT JOIN (
        SELECT pkm_prdcd, pkm_pkmt, pkm_minorder, pkm_leadtime, pkm_mindisplay
        FROM tbmaster_kkpkm
    ) pkm ON dtl_prdcd_ctn = pkm.pkm_prdcd
    -- Klausa WHERE awal
    WHERE to_char(dtl_tanggal, 'yyyymmdd') BETWEEN :tanggalMulai AND :tanggalSelesai
";

include 'filter_query.php';
// --- Tambahkan GROUP BY dan HAVING ---
$query .= "
    GROUP BY
        dtl_prdcd_ctn,
        dtl_nama_barang,
        dtl_k_div,
        dtl_k_dept,
        dtl_k_katb,
        pkm_pkmt
    HAVING COALESCE(SUM(dtl_netto), 0) <> 0
";


// --- Execute Query menggunakan Prepared Statement ---
$result = [];
try {
    // $conn diasumsikan adalah objek PDO
    $stmt = $conn->prepare($query);

    // 1. Bind nilai Tanggal
    $stmt->bindValue(':tanggalMulai', $tanggalMulaiFormatted);
    $stmt->bindValue(':tanggalSelesai', $tanggalSelesaiFormatted);
    
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Query failed: " . $e->getMessage() . "</div>";
    exit;
}

// Initialize total variables before the table output
$totalKunjungan = 0;
$totalMember = 0;
$totalStruk = 0;
$totalQtyInPcs = 0;
$totalGross = 0;
$totalNetto = 0;
$totalMargin = 0;
$noUrut = 0; // Initialize product counter

?>

<?php include './tabel/tabel_produk.php'; ?>

<?php require_once '../layout/_bottom.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#table-1').DataTable({
            responsive: false,
            lengthMenu: [10, 25, 50, 100],
            autoWidth: true,
            columnDefs: [{
                targets: [4],
                orderable: false
            }],
            buttons: [
                { extend: 'copy', text: 'Copy' },
                {
                    extend: 'excel',
                    text: 'Excel',
                    filename: 'Sales_Per_Produk_' + new Date().toISOString().split('T')[0],
                    title: null
                }
            ],
            dom: 'Bfrtip',
            initComplete: function() {
                this.api().columns.adjust().draw();
            }
        });

        // Append the buttons container to the top-left area of the table wrapper
        table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });
</script>