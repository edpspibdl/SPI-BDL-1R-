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
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>



    <div class="container">

      <div class="row">
        <div class="col-xs-10">
          <h2>SPB sudah diturunkan oleh Forklift <br><small>tapi belum direalisasi Pramuniaga</small>
          <br><small>Lokasi: </small><?php echo $spbLokasi; ?></h2>
        </div>
        <div class="col-xs-2">
          <?php include 'query-spb-belum-turun.php';?>
          <h2>
            
            <?php 
              //$spbBelumRealisasi = 0;
              if ($spbBelumTurun > 0) {
                echo '<a href="../forklift" class="btn btn-default pull-right bayang" role="button">';
                echo '<span class="badge">';
                echo $spbBelumTurun; 
                echo '</span> SPB belum diturunkan Operator Forklift</a>';
              }
            ?>
            
          </h2>
        </div>
      </div>
      
    
      <div class="row">
        
          
          <div class="col-md-10 col-md-push-2">

            
            <?php include 'query-forklift.php';?>
            <?php include 'table-forklift.php';?>
            <!-- tampilkan photo disini -->
            

          </div>
          <div class="col-md-2 col-md-pull-10">

            
            <?php include 'query-rak-rekap.php';?>
            <?php include 'table-rak-rekap.php';?>
            
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
