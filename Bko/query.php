<?php
require_once '../helper/connection.php';
$tglMulai = $_GET['tanggalMulai'] ?? date('Y-m-d');
$tglSelesai = $_GET['tanggalSelesai'] ?? date('Y-m-d');
$jenisTransaksi = $_GET['jenisTransaksi'] ?? 'All';
$where = "WHERE h.tanggal BETWEEN :tglMulai AND :tglSelesai";
if ($jenisTransaksi !== 'All') {
    $where .= " AND h.jenis_transaksi = :jenisTransaksi";
}
$sql = "
    SELECT h.kode_produk, p.nama_produk, SUM(h.qty) AS total_qty
    FROM tbtr_history_backoffice h
    JOIN tbmaster_prodmast p ON h.kode_produk = p.kode_produk
    $where
    GROUP BY h.kode_produk, p.nama_produk
    ORDER BY h.kode_produk
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':tglMulai', $tglMulai);
$stmt->bindParam(':tglSelesai', $tglSelesai);
if ($jenisTransaksi !== 'All') {
    $stmt->bindParam(':jenisTransaksi', $jenisTransaksi);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
