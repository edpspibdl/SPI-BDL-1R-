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



    <div class="container">
      <div class="row">  
        <div class="col-xs-6">
        <h2>SPB Storage Kecil ke Display
      <br><small>Lokasi: </small><?php echo $spbLokasi; ?></h2>
        </div>
        <div class="col-xs-6 hidden-sm">
          <h5 class='text-right'>SPB dari Storage Kecil langsung direalisasi oleh Pramuniaga,<br>
    tidak perlu masuk menu penurunan.</h5>
        </div>
      </div>
      

      
    
      <div class="row">
          <div class="col-md-10 col-md-push-2">

            
            <?php include 'query-forklift.php';?>
            <?php include 'table-forklift.php';?>
            <!-- tampilkan photo disini -->
            

          </div>

          


          <div class="col-md-2 col-md-pull-10">

            
            <?php include 'query-rak.php';?>
            <?php include 'table-rak.php';?>
            <!-- tampilkan photo disini -->
            

          </div>
          
        </div> <!-- row -->
      </div> <!-- container -->

    

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
