
<?php
  
   // atur nilai default
  $divisi  = 'ALL'; 

    
  // atur nilai sesuai dengan request dari form
  if(isset($_GET['divisi'])) {
	  if ($_GET['divisi'] !=""){
		  $divisi = $_GET['divisi']; 
		  }
	  
	  }

    
  //   //validasi
    //$divisi = strtoupper($divisi);

?>
  

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>
  </head>

  <body>

    



    <article class="container">

      <div class="row">
        <div class="col-md-10 col-xs-6">
          
          <h2>REKOMENDASI OPTIMALISASI STORAGE GUDANG<br>
         <!-- <br><small>Lokasi Storage Gudang: </small></h2> -->
        </div>
        <div class="col-md-2 col-xs-6">
         
          <h2>
            
           
            
          </h2>
        </div>
      </div>
	  
	  
      
    
      <div class="row">
        <!-- daftar spb -->
        <div class="col-md-10 col-md-push-2">
		
			
          <?php 
				if ($divisi == 'ALL') {
					//include 'table-forklift.php';
					echo '<h5><strong>SILAHKAN PILIH DIVISI DI MENU SEBELAH KIRI</strong></h5>';
              include 'query-optimalisasi.php';
            } 
        else {
							include 'query-optimalisasi.php';
						}
				include 'tabel-optimalisasi.php';
						
		  ?>
        </div>

        <!-- navigasi per rak -->
       <div class="col-md-2 col-md-pull-10">

      <h4 class="text-center">DIVISI ITEM</h4>
  			 <div class="list-group bayang">
          <a href="index.php" class="list-group-item <?php if ($divisi == 'ALL'){echo 'active';}?> " >ALL DIVISI </a>
          <a href="index.php?divisi=1" class="list-group-item <?php if ($divisi == '1'){echo 'active';}?> " >FOOD </a>
          <a href="index.php?divisi=2" class="list-group-item <?php if ($divisi == '2'){echo 'active';}?> " >NONFOOD</a>
          <a href="index.php?divisi=3" class="list-group-item <?php if ($divisi == '3'){echo 'active';}?> " >GMS</a>
         </div>
		  </div>
      </div> <!-- row -->
	  
	
	  
    </article> <!-- container -->

	   	

    <?php include '../includes/plano-footer.php';?>
    <?php include '../includes/nav.php'; ?>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../_/js/jquery.min.js"></script>
    <script src="../_/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
