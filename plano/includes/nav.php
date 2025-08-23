    <?php require_once '../includes/my-count-bubbles.php'; ?>
    <?php include '../_/connection.php'; ?>
    
    <?php
      $bubbleSLP = jumlahSLP();
      $bubbleManual = jumlahSPBManual();
	  $bubbleStorageToStorage = jumlahStorageToStorage();
      $bubbleDisplay = jumlahDisplayToDisplay();
      $bubbleSK = jumlahSK();
      $bubbleStorage = jumlahStorage();
	  $bubbleStorageToko = jumlahStorageToko();
	  $bubbleStorageGudang = jumlahStorageGudang();
    ?>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default nav-transx navbar-fixed-bottom bayang  " role="navigation">
      <div >
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="">Planogram SPI BDL</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            
            <li><a href="../slp-storage" >SLP <span class="badge badge-up"><?php  if ($bubbleSLP <> 0) { echo $bubbleSLP; } ?></span></a></li>
            
            
            <li><a href="../forklift">Storage ke Display <span class="badge badge-up"><?php  if ($bubbleStorage <> 0) { echo $bubbleStorage; } ?></span></a></li>
			<li><a href="../storage_to_storage">Storage ke Storage <span class="badge badge-up"><?php  if ($bubbleStorageToStorage <> 0) { echo $bubbleStorageToStorage; } ?></span></a></li>
            <li><a href="../sk-display">Storage Kecil ke display <span class="badge badge-up"><?php  if ($bubbleSK <> 0) { echo $bubbleSK; } ?></span></a></li>
            <li><a href="../display">Display ke display <span class="badge badge-up"><?php  if ($bubbleDisplay <> 0) { echo $bubbleDisplay; } ?></span></a></li>
            <li><a href="../spb-manual">Manual <span class="badge badge-up"><?php  if ($bubbleManual <> 0) { echo $bubbleManual; } ?></span></a></li>
			<li><a href="../forklift_toko">SPB TOKO<span class="badge badge-up"><?php  if ($bubbleStorageToko <> 0) { echo $bubbleStorageToko; } ?></span></a></li>
			<li><a href="../forklift_gudang">SPB GUDANG<span class="badge badge-up"><?php  if ($bubbleStorageGudang <> 0) { echo $bubbleStorageGudang; } ?></span></a></li>	
			<li><a href="../optimalisasi">OPTIMALISASI<span class="badge badge-up"><?php  //if ($x <> 0) { echo $x-1; } ?></span></a></li>	
			<!--<li><a href="../inq">PLANO<span class="badge badge-up"></span></a></li>	-->
            
          </ul>

        </div><!--/.nav-collapse -->
      </div>
    </nav>