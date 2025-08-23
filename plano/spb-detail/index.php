<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>
  </head>

  <body>

    <?php include '../includes/nav.php'; ?>



    <div class="container">
      
    </div>
      <div class="row">
          <div class="col-md-2">

            <?php include 'query-spb.php';?>
            <?php include 'table-spb.php';?>
            
          </div>
          <div class="col-md-10">
            <?php //include 'query-slp-belum.php';?>
            <?php include 'table-spb-detail.php';?>
            <!-- tampilkan photo disini -->
          </div>
        </div> <!-- row -->
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
