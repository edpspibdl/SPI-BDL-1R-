<?php   
    // atur nilai default
    $slpStatus   = ' '; 
    $slpLokasi   = 'All'; 
    $judulPanel  = "SLP ke Rak Storage Belum Realisasi";
    // null: belum realisasi 
    //    c: batal
    //    p: sudah diproses
  // atur nilai sesuai dengan request dari form
  if(isset($_GET['slpStatus'])) {if ($_GET['slpStatus'] !=""){$slpStatus = $_GET['slpStatus']; }}
  if(isset($_GET['slpLokasi'])) {if ($_GET['slpLokasi'] !=""){$slpLokasi = $_GET['slpLokasi']; }}
    //validasi
    $slpStatus = strtoupper($slpStatus);
    //$slpLokasi = strtoupper($slpLokasi);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>
	<style>
	@media screen and (max-width: 460px) {
 
	h2 {
		padding:0px;
		font-size: 15px;
	}
	</style>
	
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>



    <div class="container">
      <div class="row">
        <div class="col-md-10 col-xs-6">
          <h2><?php echo $judulPanel; ?>        
          <br><small>Lokasi: </small><?php echo $slpLokasi; ?></h2>
        </div>
        <div class="col-md-2 col-xs-6">
          <?php include 'query-jml-slp-bukan-storage.php';?>
          <h2>
            <?php 
              //$spbBelumRealisasi = 100;
              if ($jumlahSlpBukanStorage > 0) {
                echo '<a href="../slp-bukan-storage" class="btn btn-default btn-sm pull-right bayang" role="button">';
                echo '<span class="badge">';
                echo $jumlahSlpBukanStorage; 
                echo '</span> SLP ke Rak Display belum realisasi</a>';
              }
            ?>          
          </h2>
        </div>
      </div> <!-- akhir dari row judul -->
      <div class="row">
        <div class="col-md-10 col-md-push-2">
          <?php //include 'query-slp-belum.php';?>
          <?php include 'table-slp-detail.php';?>
          
        </div>

        <div class="col-md-2 col-md-pull-10">
          <?php include 'query-rak-rekap.php';?>
          <?php include 'table-rak-rekap.php';?>

          <?php include 'query-rak.php';?>
          <?php include 'table-rak.php';?>

          <?php //include 'query-slp.php';?>
          <?php //include 'table-slp.php';?> 
           
        </div>
   
      </div> <!-- row -->
      
    </div>
 
    <?php include '../includes/plano-footer.php';?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../_/js/jquery.min.js"></script>
    <script src="../_/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../_/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
