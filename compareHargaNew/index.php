<?php
// FILE: index.php

// Memuat header/top layout dan koneksi database
require_once '../layout/_top.php'; 
require_once '../helper/connection.php'; 
require_once 'query/product_query.php';

$kodePLU = '';
$data = [];
$error_messages = []; 
$search_executed = false;

$db_target = $_SESSION['db_target'] ?? 'prod'; 
$branch_target = $_SESSION['branch_target'] ?? 'unknown';

$all_branch_configs = $ALL_BRANCH_CONFIGS;
$remote_connections = $remote_connections ?? [];
$remote_branch_names = $remote_branch_names ?? [];

// ======================================================================
// ★ TAMBAHAN – MODE TAMPILKAN SEMUA
// ======================================================================
if (!empty($_GET['show_all'])) {
    $search_executed = true;
    $base_like = '%'; // ambil semua PLU

    $data_lokal = [];
    $final_data = [];

    // --- Query Lokal untuk Semua ---
    try {
        $stmt_lokal = $conn->prepare($QUERY_LOKAL_KOMPLEKS);
        $stmt_lokal->bindValue(':base_plu', $base_like, PDO::PARAM_STR);
        $stmt_lokal->execute();
        $data_lokal = $stmt_lokal->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_messages[$branch_target] = "Error query lokal: " . $e->getMessage();
    }

    // --- Query Remote untuk Semua ---
    $query_remote_complex = str_replace("AS harga_final_lokal", "AS harga_final_remote", $QUERY_LOKAL_KOMPLEKS);
    $remote_plu_data = [];

    foreach ($remote_connections as $code => $r_conn) {
        try {
            $stmt_remote = $r_conn->prepare($query_remote_complex);
            $stmt_remote->bindValue(':base_plu', $base_like, PDO::PARAM_STR);
            $stmt_remote->execute();
            $remote_results = $stmt_remote->fetchAll(PDO::FETCH_ASSOC);
            $remote_plu_data[$code] = array_column($remote_results, 'harga_final_remote', 'prd_prdcd');
        } catch (PDOException $e) {
            $error_messages[$code] = "Error remote {$remote_branch_names[$code]}: " . $e->getMessage();
        }
    }

    // --- Gabungkan ---
    foreach ($data_lokal as $d_lokal) {
        $plu = $d_lokal['prd_prdcd'];
        $row = $d_lokal;
        foreach ($remote_plu_data as $code => $map) {
            $row["harga_{$code}"] = $map[$plu] ?? null;
        }
        $final_data[] = $row;
    }

    $data = $final_data;

    goto END_SEARCH; // hentikan eksekusi ke blok kodePLU
}

// ======================================================================
// LOGIKA PENCARIAN NORMAL (BERDASARKAN PLU)
// ======================================================================

if (!empty($_GET['kodePLU'])) {
    $inputPLU = trim($_GET['kodePLU']);
    $search_executed = true;

    $kodePLU = str_pad($inputPLU, 7, '0', STR_PAD_LEFT);
    $base_like = substr($kodePLU, 0, 6) . '%';
    
    $data_lokal = [];
    $final_data = [];

    // Query Lokal
    try {
        $stmt_lokal = $conn->prepare($QUERY_LOKAL_KOMPLEKS);
        $stmt_lokal->bindValue(':base_plu', $base_like, PDO::PARAM_STR);
        $stmt_lokal->execute();
        $data_lokal = $stmt_lokal->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_messages[$branch_target] = "Error query cabang lokal: " . $e->getMessage();
    }

    // Query Remote
    $query_remote_complex = str_replace("AS harga_final_lokal", "AS harga_final_remote", $QUERY_LOKAL_KOMPLEKS);
    $remote_plu_data = [];

    foreach ($remote_connections as $code => $r_conn) {
        try {
            $stmt_remote = $r_conn->prepare($query_remote_complex);
            $stmt_remote->bindValue(':base_plu', $base_like, PDO::PARAM_STR);
            $stmt_remote->execute();
            $remote_results = $stmt_remote->fetchAll(PDO::FETCH_ASSOC);
            $remote_plu_data[$code] = array_column($remote_results, 'harga_final_remote', 'prd_prdcd');
        } catch (PDOException $e) {
            $error_messages[$code] = "Error remote {$remote_branch_names[$code]}: " . $e->getMessage();
        }
    }

    // Gabungkan
    foreach ($data_lokal as $d_lokal) {
        $plu = $d_lokal['prd_prdcd'];
        $row_data = $d_lokal;
        foreach ($remote_plu_data as $code => $remote_map) {
            $row_data["harga_{$code}"] = $remote_map[$plu] ?? null;
        }
        $final_data[] = $row_data;
    }
    
    $data = $final_data;
}

// End Search Label
END_SEARCH:
?>

<style>
body { overflow-x: hidden; }
.produk-row:hover {
    cursor: pointer;
    background-color: #f0f8ff;
    transform: scale(1.01);
    transition: all 0.2s;
}
.table-responsive td { white-space: normal; }
.dataTables_wrapper { overflow-x: hidden; }
legend { width: auto; padding: 0 10px; font-size: 1.25rem; }
</style>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Cek PLU Promosi & Komparasi Harga</h1>
    </div>

    <div class="container-fluid">

        <div class="card shadow-lg border-0 mb-4 rounded-3">
            <div class="card-body p-4">
                <fieldset class="border rounded-3 p-3">
                    <legend class="fw-bold text-primary px-2">
                        Cari Produk
                    </legend>

                    <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                        
                        <input type="text" name="kodePLU" id="kodePLU" 
                            class="form-control form-control-lg border-primary" 
                            placeholder="Masukkan Kode PLU (misal: 0013500)" 
                            value="<?= htmlspecialchars($kodePLU) ?>" 
                            style="max-width: 300px;">

                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="fas fa-play-circle me-1"></i> Cari
                        </button>

                        <button type="button" class="btn btn-secondary btn-lg shadow-sm" 
                                data-toggle="modal" data-target="#produkModal">
                            <i class="fas fa-list-check me-1"></i> Pilih Produk
                        </button>

                        <!-- ================================================================= -->
                        <!-- ★ TAMBAHAN BUTTON SHOW ALL -->
                        <!-- ================================================================= -->
                        <a href="index.php?show_all=1" 
                           class="btn btn-primary btn-lg shadow-sm">
                           <i class="fas fa-table me-1"></i> Tampilkan Semua
                        </a>

                    </form>
                </fieldset>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-4">
                <fieldset class="border rounded-3 p-3">
                    <legend class="fw-bold text-success px-2">
                        Hasil Komparasi PLU: <?= htmlspecialchars($kodePLU) ?>
                    </legend>

                    <?php require_once 'components/comparison_table.php'; ?>

                </fieldset>
            </div>
        </div>
    </div>
</section>

<?php 
require_once 'components/product_modal.php'; 
require_once '../layout/_bottom.php'; 
?>

<script>
$(document).ready(function() {

    /* === GRIDVIEW === */
    if ($('#GridView').length) {
        $('#GridView').DataTable({
            language: {
                search: "Filter:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Sebelum" }
            },
            scrollX: true,
            paging: true,
            searching: true,
            info: true,
            ordering: false,

            /* === Tambahkan Excel Export === */
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Export_GridView'
                }
            ]
        });
    }

    /* === PRODUK TABLE === */
    $('#produkTable').DataTable({
        language: {
            search: "Cari Produk:",
            zeroRecords: "Produk tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ produk",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Sebelum" }
        },
        pageLength: 5,
        lengthChange: false,
        responsive: true,

        /* === Tambahkan Excel Export juga kalau mau === */
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Export_Produk'
            }
        ]
    });


    /* === EVENT KLIK PRODUK === */
    $('#produkTable tbody').on('click', '.produk-row', function() {
        var plu = $(this).data('plu');
        $('#kodePLU').val(plu);
        $('#produkModal').modal('hide');
        $('#kodePLU').closest('form').submit();
    });
});
</script>