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
		font-size: 20px;
	}
	h3 {
		padding:1px;
		font-size:15px;
	}
	</style>
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>



    <article class="container">

      <div class="row">
        <div class="col-md-12">
          
          <h2>SPB Manual</small></h2>
          
        </div>

      </div>
      
    
      <div class="row">
        <!-- daftar spb -->
        <div class="col-md-10 col-md-push-2">
          <h3>Belum dikerjakan</h3>
          <?php include 'query-belum-turun.php';?>
          <?php include 'table-belum-turun.php';?>
          <hr>
          <h3>Belum direalisasi</h3>
          <?php include 'query-belum-real.php';?>
          <?php include 'table-belum-real.php';?>
        </div>

        <!-- navigasi per rak -->
        <div class="col-md-2 col-md-pull-10">
          <h3><small>Lokasi: </small><?php echo $spbLokasi; ?></h3>
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
