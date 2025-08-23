
<?php
  
   // atur nilai default
  $spbLokasi  = 'ALL'; 
    

    
  // atur nilai sesuai dengan request dari form
  if(isset($_GET['spbLokasi'])) {if ($_GET['spbLokasi'] !=""){$spbLokasi = $_GET['spbLokasi']; }}
  
    
    //validasi
    $spbLokasi = strtoupper($spbLokasi);

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
	h5 {
		padding:15px;
		font-size:9px;
	}
	</style>
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>



    <article class="container">

      <div class="row">
        <div class="col-md-10 col-xs-6">
          
          <h2>SPB Storage ke Display TOKO <br><small>belum diturunkan oleh Forklift</small>
          <br><small>Lokasi Asal: </small><?php echo $spbLokasi; ?></h2>
        </div>
        <div class="col-md-2 col-xs-6">
          <?php include 'query-spb-belum-realisasi.php';?>
          <h2>
            
            <?php 
              //$spbBelumRealisasi = 0;
              if ($spbBelumRealisasi > 0) {
                echo '<a href="../forklift-belum-realisasi" class="btn btn-default btn-sm pull-right bayang" role="button">';
                echo '<span class="badge">';
                echo $spbBelumRealisasi; 
                echo '</span> Penurunan belum direalisasi</a>';
              }
            ?>
            
          </h2>
        </div>
      </div>
      
    
      <div class="row">
        <!-- daftar spb -->
        <div class="col-md-10 col-md-push-2">

          <?php include 'query-forklift.php';?>
          <?php include 'table-forklift.php';?>
        </div>

        <!-- navigasi per rak -->
        <div class="col-md-2 col-md-pull-10">
          <?php include 'query-rak-rekap.php';?>
          <?php include 'table-rak-rekap.php';?>

          <?php include 'query-rak.php';?>
          <?php include 'table-rak.php';?>
        </div>
      </div> <!-- row -->
    </article> <!-- container -->

    

    <?php include '../includes/plano-footer.php';?>
    



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../_/js/jquery.min.js"></script>
    <script src="../_/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
