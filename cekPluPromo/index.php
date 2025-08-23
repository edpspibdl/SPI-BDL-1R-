<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Ambil kode promosi dari parameter GET jika ada
$kodePromo = '';
if (isset($_GET['kodePromo']) && $_GET['kodePromo'] != "") {
    $kodePromo = strtoupper($_GET['kodePromo']);
}
?>

<section class="section">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h1>Cek PLU Per Promosi</h1>
    </div>

    <div class="container-fluid">
        <!-- Form Pencarian -->
        <div class="card shadow-sm border-0">
            <div class="card-header" style="background-color: #dceaf9;">
                <h5 class="mb-0">🔍 Form Pencarian</h5>
            </div>
            <div class="card-body">
                <?php include 'form.php'; ?>
            </div>
        </div>

        <!-- Hasil Pencarian -->
        <div class="card shadow-sm border-0">
            <div class="card-header" style="background-color: #dceaf9;">
                <h5 class="mb-0">📊 Hasil Pencarian</h5>
            </div>
            <div class="card-body">
                <?php
                if ($kodePromo != "") {
                    include 'tabelquery.php';
                } else {
                    echo '<div class="text-center">Silakan masukkan kode Member / Kode Promosi untuk mencari data.</div>';
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
            "language": {
                "search": "Cari",
                "lengthMenu": "_MENU_ Baris per halaman",
                "zeroRecords": "Data tidak ada",
                "info": "Halaman _PAGE_ dari _PAGES_ halaman (Total Data: _TOTAL_)",
                "infoEmpty": "Data tidak ada",
                "infoFiltered": "(Filter dari _MAX_ data)"
            },
            lengthChange: true,
            lengthMenu: [10],
            paging: true,
            responsive: true,
            buttons: ['copy', 'excel', 'colvis']
        });

        GridView.buttons().container().appendTo('#GridView_wrapper .col-sm-6:eq(0)');
        $('#GridView').show();
        GridView.columns.adjust().draw();
        $("#load").fadeOut();
    });
</script>