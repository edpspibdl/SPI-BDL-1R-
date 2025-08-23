<?php require_once '../includes/my-function.php'; ?>
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
      
      <h2>Master Display
      <br><small>Lokasi: </small><?php echo $spbLokasi; ?></h2>

    
      <div class="row">


          <div class="col-md-12 col-sm-2">
          <!-- tabs left -->
      <div class="tabbable tabs-left">
        <ul class="nav nav-tabs">
          <li><a href="#a" data-toggle="tab">One</a></li>
          <li class="active"><a href="#b" data-toggle="tab">Two</a></li>
          <li><a href="#c" data-toggle="tab">Twee</a></li>
        </ul>
        <div class="tab-content">
         <div class="tab-pane active" id="a">Lorem ipsum dolor sit amet, charetra varius quam sit amet vulputate. 
         Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero.</div>
         <div class="tab-pane" id="b">Secondo sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. 
         Aliquam in felis sit amet augue.</div>
         <div class="tab-pane" id="c">Thirdamuno, ipsum dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. 
         Quisque mauris augue, molestie tincidunt condimentum vitae. </div>
        </div>
      </div>
      <!-- /tabs -->
          <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
              <li class=""><a aria-expanded="false" href="#display" data-toggle="tab">Display</a></li>
              <li class="active"><a aria-expanded="true" href="#storage_kecil" data-toggle="tab">Storage Kecil</a></li>
              <li class=""><a aria-expanded="true" href="#storage_toko" data-toggle="tab">Storage Toko</a></li>
              <li class=""><a aria-expanded="true" href="#gudang" data-toggle="tab">Storage Gudang</a></li>
              <li class=""><a aria-expanded="true" href="#omi" data-toggle="tab">OMI</a></li>
            </ul>

            <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade" id="display">
                <p>
                <?php
                    include 'query-master-rak.php'; 

                    // Create connection to Oracle
                    require_once '../_/connection.php';
                    $stid = oci_parse($conn, $query);
                    oci_execute($stid);
                    // Fetch each row in an associative array
                    while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
                        $rak = $row['LKS_KODERAK'];
                        $jumlah = $row['LKS_KODESUBRAK'];

                        $active = '';
                        //if ($spbLokasi == $rak) {
                        //  $active = 'active';
                        //}
                        echo '<a href="index.php?spbLokasi=' . $rak .  '" class="btn btn-link  btn-xs ">'. $rak . ' <span class="badge">' . $jumlah . '</span></a>';
                      //echo '<a href="index.php?spbLokasi=' . $rak .  '" class="list-group-item ' . $active .  '">' . $rak . ' <span class="badge">' . $jumlah . '</span></a>';

                    } 
                    
                  ?>
                </p>
              </div>
              <div class="tab-pane fade active in" id="storage_kecil">
                <p>storage_kecil Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
              </div>
              <div class="tab-pane fade" id="storage_toko">
                <p>storage_toko Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork.</p>
              </div>
              <div class="tab-pane fade" id="gudang">
                <p>gudang Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater.</p>
              </div>
              <div class="tab-pane fade" id="omi">
                <p>omi Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater. Fanny pack portland seitan DIY, art party locavore wolf cliche high life echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before they sold out farm-to-table VHS viral locavore cosby sweater.</p>
              </div>
            </div>

            </div>
           
            <?php include 'query-master-rak.php';?>
            <?php include 'table-master-rak2.php';?>
            <?php //include 'query-rak.php';?>
            <?php //include 'table-rak.php';?>
            <!-- tampilkan photo disini -->
            

          </div>

          <div class="col-md-9 col-sm-10">

            
            <?php include 'query-forklift.php';?>
            <?php include 'table-forklift.php';?>
            <!-- tampilkan photo disini -->
            

          </div>
        </div> <!-- row -->
      </div> <!-- container -->

    

    <footer class="footer">
      <div class="container">
        <p class="text-muted">IT-igrsmg 2014</p>
      </div>
    </footer>
    



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../_/js/jquery.min.js"></script>
    <script src="../_/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
