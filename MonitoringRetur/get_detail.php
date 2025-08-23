<?php
require_once '../helper/connection.php'; // Pastikan koneksi PDO sudah benar

// Cek apakah ada parameter POST 'kodeSupplier'
if (isset($_POST['kodeSupplier'])) {
    $kodeSupplier = $_POST['kodeSupplier'];

    // Query untuk mengambil detail berdasarkan kodeSupplier
    try {
        $query = "SELECT
    b.btb_kodeigr, 
    b.btb_prdcd, 
    p.PRD_DESKRIPSIPANJANG,
    CASE 
        WHEN p.prd_perlakuanbarang = 'RT' THEN 'RETUR'
        WHEN p.prd_perlakuanbarang = 'PT' THEN 'PUTUS'
        WHEN p.prd_perlakuanbarang = 'TG' THEN 'TUKER GULING'
        ELSE p.prd_perlakuanbarang
    END AS perlakuan_barang,
    P.prd_perlakuanbarang,
    b.btb_nodoc, 
    b.btb_kodesupplier, 
    s.sup_namasupplier, 
    b.btb_tgldoc
FROM tbtr_mstran_btb b
LEFT JOIN TBMASTER_PRODMAST p ON b.btb_prdcd = p.PRD_PRDCD
LEFT JOIN TBMASTER_SUPPLIER s ON b.btb_kodesupplier = s.sup_kodesupplier
WHERE b.btb_istype IS NOT NULL
  AND b.btb_kodesupplier = :kodeSupplier
  AND p.prd_perlakuanbarang NOT IN ('TG','PT')
ORDER BY s.sup_namasupplier, b.btb_tgldoc DESC";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':kodeSupplier', $kodeSupplier, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengambil semua data

        if ($rows) {
            // Tambahkan class 'table' dan id 'supplierDetailTable' untuk DataTables
            echo "<table id='supplierDetailTable' class='table table-bordered table-hover'>";
            echo "<thead class='thead-dark'><tr>
                    <th>Kode IGR</th>
                    <th>Nomor Dokumen</th>
                    <th>PLU</th>
                    <th>Deskripsi Panjang</th>
                    <th>Perlakuan Barang</th>
                    <th>Keterangan</th>
                    <th>Tanggal Dokumen</th>
                  </tr></thead>";
            echo "<tbody>";

            // Iterasi untuk menampilkan setiap baris data
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['btb_kodeigr']) . "</td>";
                echo "<td>" . htmlspecialchars($row['btb_nodoc']) . "</td>";
                echo "<td>" . htmlspecialchars($row['btb_prdcd']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prd_deskripsipanjang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prd_perlakuanbarang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['perlakuan_barang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['btb_tgldoc']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning' role='alert'>Detail tidak ditemukan.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!-- Tambahkan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- Tambahkan Script DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#supplierDetailTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Supplier Detail'
            },
            {
                extend: 'print',
                title: 'Supplier Detail'
            }
        ],
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>
