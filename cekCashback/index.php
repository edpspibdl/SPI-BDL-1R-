<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Variabel default
$kodeMember = '';
if (isset($_GET['kodeMember']) && $_GET['kodeMember'] != "") {
    $kodeMember = strtoupper($_GET['kodeMember']);
}
?>

<section class="section">ntent-between align-items-center">
        <h1 class="text-dark">Cek Data Perolehan Cashback Member</h1>
    </div>
    <div class="section-header d-flex justify-co

    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header" style="background-color: #dceaf9;">
                <h6 class="mb-0">🔍 Form Pencarian</h6>
            </div>
            <div class="card-body">
                <?php include 'form.php'; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header" style="background-color: #dceaf9;">
                <h6 class="mb-0"> 📊 Hasil Pencarian</h6>
            </div>
            <div class="card-body">
                <?php
                if ($kodeMember != "") {
                    // Pastikan tabel di 'tabelquery.php' memiliki id="GridView"
                    include 'tabelquery.php';
                } else {
                    echo '<div class="text-center text-muted">Silakan masukkan kode Member / Kode Promosi untuk mencari data.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<style>
    /* Styling untuk tabel */
    #GridView {
        /* DIUBAH DARI #table-1 MENJADI #GridView */
        width: 100%;
        table-layout: auto;
        /* Menyesuaikan lebar kolom dengan isi konten */
        border-collapse: collapse;
        /* Menggabungkan border antar sel */
    }

    #GridView th,
    /* DIUBAH DARI #table-1 MENJADI #GridView */
    #GridView td {
        /* DIUBAH DARI #table-1 MENJADI #GridView */
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
        /* Membuat border untuk semua cell */
    }

    #GridView th {
        /* DIUBAH DARI #table-1 MENJADI #GridView */
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
        /* Menambahkan pembatas tebal di bawah header */
    }

    #GridView td {
        /* DIUBAH DARI #table-1 MENJADI #GridView */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Styling untuk kolom DESK */
    #GridView .desk-column {
        /* DIUBAH DARI #table-1 MENJADI #GridView */
        word-wrap: break-word;
        /* Memastikan teks di kolom DESK membungkus */
        white-space: normal;
        /* Teks dapat membungkus pada kolom DESK */
        max-width: 300px;
        /* Membatasi lebar maksimum kolom DESK */
    }

    /* Responsif untuk tabel */
    .table-responsive {
        overflow-x: auto;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        var GridView = $('#GridView').DataTable({
            language: {
                search: "Cari",
                lengthMenu: "_MENU_ Baris per halaman",
                zeroRecords: "Data tidak ada",
                info: "Halaman _PAGE_ dari _PAGES_ halaman",
                infoEmpty: "Data tidak ada",
                infoFiltered: "(Filter dari _MAX_ data)"
            },
            lengthMenu: [10],
            paging: true,
            responsive: true,
            buttons: ['copy', 'excel', 'colvis']
        });

        GridView.buttons().container().appendTo('#GridView_wrapper .col-sm-6:eq(0)');
        $('#GridView').show();
        GridView.columns.adjust().draw();
    });
</script>