<?php
  
   // atur nilai default
  $kodeRak  = 'All'; 
  $kodeSubRak  = '01'; 
    

    
  // atur nilai sesuai dengan request dari form
  if(isset($_GET['kodeRak'])) {if ($_GET['kodeRak'] !=""){$kodeRak = $_GET['kodeRak']; }}
  if(isset($_GET['kodeSubRak'])) {if ($_GET['kodeSubRak'] !=""){$kodeSubRak = $_GET['kodeSubRak']; }}
  
    
    //validasi
    $kodeRak = strtoupper($kodeRak);

?>
  

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>
  </head>

  <body>

    <?php //include '../includes/nav.php'; ?>



    <div class="container-fluid">
      
      <h2>Rak Display</h2>

    
      <div class="row">
          <div class="col-md-10 col-md-push-2">

            <?php
              if ($kodeRak  == 'ALL') {
                echo '<p>Pilih Kode Rak dari list di sebelah kiri</p>';
              } else {
                include 'query-sub-rak.php';
                include 'table-sub-rak.php';

                include 'query-detail.php';
                include 'table-detail.php';
              }
            ?>

            
            <?php //include 'query-forklift.php';?>
            <?php //include 'table-forklift.php';?>
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
    <script src="../_/js/plano.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
