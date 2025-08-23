<?php
  
  // Create connection to Oracle
		include '../_/connection.php';
		$stmt = $conn->prepare($query);
		$stmt->execute();
?>
  
  <div class="panel bayang">
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr  class="info">
          <th>#</th>
          <th>Lokasi</th>
          <th>Max Palet</th>
          <th>Nama Barang</th>
        </tr>
      </thead>
      <tbody>
          <?php
            $noUrut = 0;
            // Fetch each row in an associative array
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $noUrut ++; ?>
              <tr>
                <td align="center"><h4><?=$noUrut;?></h4></td>
                
                <td>
                  <h4 align="left"><?=$row['lokasi']?></h4>
                  <div align="right"> <?=$row['lks_qty']?> PCS <br></div> 
                  <?php $cartonan = $row['lks_qty'] / $row['prd_frac'];?> 
                  <div align="right"><?=$cartonan.' '.$row['prd_unit']?></div>
                </td>

                <td class="text-nowrap">
                  <h4><?=$row['lks_expdate'];?></h4>
                  <div align="right"> <?=$row['max_palet']?> PCS <br></div> 
                  <?php $cartonpalet = $row['max_palet'] / $row['prd_frac'];?>
                  <div align="right"><?= $cartonpalet. ' ' .$row['prd_unit']; ?></div>
                </td>

                <td>
                  <h4><?=$row['prd_deskripsipanjang']?></h4>
                  <?= $row['lks_prdcd'] .' - '.$row['prd_unit'] .' / '.$row['prd_frac']  ;?>
                </td>

              </tr>
           <?php }?>
        
      </tbody>
    </table>
  </div>
  </div>
