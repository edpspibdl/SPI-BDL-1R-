<?php
require_once '../layout/_top.php';
require_once 'query.php';

$kodePLU = $_GET['kodePLU'] ?? '';
$kodePLU = $kodePLU !== '' ? str_pad($kodePLU, 7, '0', STR_PAD_LEFT) : '';

$result = getProductAndSalesData($conn, $kodePLU);
$data = $result['data'];
$trensale = $result['trensale'];
?>

<head><title>Informasi & History Product</title></head>

<h1>Informasi Promosi Dan Produk</h1>

<?php include 'form.php'; ?>

<?php if ($kodePLU !== ''): ?>
    <?php include 'tabel.php'; ?>
<?php endif; ?>

<?php
require_once '../layout/_bottom.php';
?>
