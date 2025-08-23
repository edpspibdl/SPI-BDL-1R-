<?php
  include '../views/view_spb.php';
  
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
        <div class="col-md-10 col-xs-6">
          
          <h2>SPB Display ke display <small>Belum diturunkan</small><br>
          <small>Lokasi: </small><?php echo $spbLokasi; ?></h2>
        </div>
        <div class="col-md-2 col-xs-6">
          <?php include 'query-jumlah-belum-realisasi.php';?>
          <h2>
            
            <?php 
              //$spbBelumRealisasi = 100;
              if ($jumlahSpbBelumRealisasi > 0) {
                echo '<a href="../display-belum-realisasi" class="btn btn-default btn-sm pull-right bayang" role="button">';
                echo '<span class="badge">';
                echo $jumlahSpbBelumRealisasi; 
                echo '</span> SPB Display ke Display belum realisasi</a>';
              }
            ?>
            
          </h2>
        </div>
      </div> <!-- akhir dari row judul -->





    
      <div class="row">
          <div class="col-md-10 col-md-push-2">

            
            <?php include 'query-forklift.php';?>
            <?php include 'table-forklift.php';?>
          </div>

          <div class="col-md-2 col-md-pull-10">

            
            <?php include 'query-rak.php';?>
            <?php include 'table-rak.php';?>
            
            

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
