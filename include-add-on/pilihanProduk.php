<!-- pilihan produk -->

<div class="panel panel-default">
  <div class="panel-heading ">Produk</div>
  <div class="panel-body">

    <input type="text" class="form-control" name="namaBarang" id="namaBarang" placeholder="Nama Barang">
    <input type="number" min="1" max="9999999" class="form-control" name="kodePLU" id="kodePLU" placeholder="PLU">
    <input type="number" class="form-control" name="kodeBarcode" id="kodeBarcode" placeholder="Barcode">
    <input type="text" class="form-control" name="kodeMonitoringPLU" id="kodeMonitoringPLU" placeholder="Kode Monitoring PLU">

    <!-- list divisi -->
    <?php include 'list-divisi.php'; ?>

    <!-- list departemen -->
    <?php include 'list-departemen.php'; ?>

    <!-- list kategori -->
    <?php include 'list-kategori.php'; ?>

    <!-- list kode tag -->
    <?php include 'list-kode-tag.php'; ?>

  </div> <!-- panel body-->           
</div> <!-- panel -->    