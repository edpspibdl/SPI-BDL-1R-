<?php
// FILE: index.php

// Memuat header/top layout dan koneksi database
require_once '../layout/_top.php'; 
require_once '../helper/connection.php'; // Memuat koneksi dan konfigurasi
require_once 'query/product_query.php'; // Memuat definisi query SQL

$kodePLU = '';
$data = [];
$error_messages = []; 
$search_executed = false;

// Ambil variabel dari session & connection.php
$db_target = $_SESSION['db_target'] ?? 'prod'; 
$branch_target = $_SESSION['branch_target'] ?? 'unknown';

// Ambil semua konfigurasi dan koneksi dari connection.php
$all_branch_configs = $ALL_BRANCH_CONFIGS;
$remote_connections = $remote_connections ?? [];
$remote_branch_names = $remote_branch_names ?? [];

// 🔍 LOGIKA PENCARIAN PRODUK UTAMA
if (!empty($_GET['kodePLU'])) {
    $inputPLU = trim($_GET['kodePLU']);
    $search_executed = true;

    // Format input PLU ke 7 digit → ambil 6 digit untuk LIKE
    $kodePLU = str_pad($inputPLU, 7, '0', STR_PAD_LEFT);
    $base_like = substr($kodePLU, 0, 6) . '%';
    
    $data_lokal = [];
    $final_data = [];

    // --- Eksekusi Query Lokal (Menggunakan QUERY_LOKAL_KOMPLEKS dari product_query.php) ---
    try {
        $stmt_lokal = $conn->prepare($QUERY_LOKAL_KOMPLEKS);
        $stmt_lokal->bindValue(':base_plu', $base_like, PDO::PARAM_STR); 
        $stmt_lokal->execute();
        $data_lokal = $stmt_lokal->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_messages[$branch_target] = "Error query cabang lokal ({$remote_branch_names[$branch_target]}): " . $e->getMessage();
    }

    // --- Eksekusi Query Cabang Lain (Remote) ---
    // Ganti alias kolom untuk remote
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
            // Hanya simpan error jika koneksi gagal atau query gagal
            $error_messages[$code] = "Error query {$remote_branch_names[$code]} ($code): " . $e->getMessage();
        }
    }

    // --- Gabungkan Hasil Lokal dan Remote ---
    $final_data = [];
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
?>

<style>
/* Pindahkan semua CSS ke file CSS eksternal jika memungkinkan. */
/* Jika tidak, biarkan di sini sementara, tetapi minimalisir. */
body { overflow-x: hidden; }
.produk-row:hover {
    cursor: pointer;
    background-color: #f0f8ff;
    transform: scale(1.01);
    transition: all 0.2s;
}
.table-responsive td { white-space: normal; }
.dataTables_wrapper { overflow-x: hidden; }
legend {
    width: auto;
    padding: 0 10px;
    font-size: 1.25rem;
}
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
                            value="<?= htmlspecialchars($kodePLU) ?>" required 
                            style="max-width: 300px;">
                        
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="fas fa-play-circle me-1"></i> Cari
                        </button>

                        <button type="button" class="btn btn-secondary btn-lg shadow-sm" 
                                data-toggle="modal" data-target="#produkModal">
                            <i class="fas fa-list-check me-1"></i> Pilih Produk
                        </button>
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

                    <?php 
                        // Memuat komponen tabel hasil komparasi
                        require_once 'components/comparison_table.php'; 
                    ?>

                </fieldset>
            </div>
        </div>
    </div>
</section>

<?php 
// Memuat komponen Modal Pilih Produk
require_once 'components/product_modal.php'; 
?>

<?php require_once '../layout/_bottom.php'; ?>

<script>
$(document).ready(function() {
    // DataTables hasil pencarian
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
            ordering: false
        });
    }

    // DataTables modal produk
    $('#produkTable').DataTable({
        language: {
            search: "Cari Produk:",
            zeroRecords: "Produk tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ produk",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Sebelum" }
        },
        pageLength: 5,
        lengthChange: false,
        responsive: true
    });

    // Klik baris produk → isi input dan submit
    $('#produkTable tbody').on('click', '.produk-row', function() {
        var plu = $(this).data('plu');
        $('#kodePLU').val(plu);
        $('#produkModal').modal('hide');
        $('#kodePLU').closest('form').submit();
    });
});
</script>