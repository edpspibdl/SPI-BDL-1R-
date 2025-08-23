<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>

    <div class="container">

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
        <div class="row">
          <div class="col-md-6">
            <h2>SLP: Slip Lokasi Penyimpanan</h2>
            <!-- tampilkan photo disini -->
          </div>
          <div class="col-md-6">
            
            <?php include 'query-slp.php';?>
            <?php include 'table-slp.php';?>
          </div>
        </div> <!-- row -->
      </div><!-- jumbotron -->



    </div> <!-- /container -->

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
