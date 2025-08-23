<?php
  // Atur nilai default
  $spbLokasi  = 'ALL'; 

  // Atur nilai sesuai dengan request dari form
  if (isset($_GET['spbLokasi']) && $_GET['spbLokasi'] != "") {
    $spbLokasi = $_GET['spbLokasi'];
  }

  // Validasi
  $spbLokasi = strtoupper($spbLokasi);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include '../includes/head.php'; ?>

    <style>
      @media screen and (max-width: 460px) {
        h2 {
          padding: 0px;
          font-size: 15px;
        }
      }
    </style>
  </head>

  <body>
    <?php include '../includes/nav.php'; ?>

    <article class="container">
      <div class="row">
        <div class="col-sm-10 col-xs-6">
          <h2>
            SPB Storage ke Display 
            <br><small>belum diturunkan oleh Forklift</small>
            <br><small>Lokasi: </small> <?php echo $spbLokasi; ?>
          </h2>
        </div>
        <div class="col-md-2 col-xs-6">
          <?php include 'query-spb-belum-realisasi.php'; ?>

          <?php 
            if ($spbBelumRealisasi > 0) {
              echo '<h2>';
              echo '<a href="../forklift-belum-realisasi" class="btn btn-default btn-sm pull-right bayang" role="button">';
              echo '<span class="badge">' . $spbBelumRealisasi . '</span> Penurunan belum direalisasi';
              echo '</a>';
              echo '</h2>';
            }
          ?>
        </div>
      </div>

      <div class="row">
        <!-- daftar spb -->
        <div class="col-md-10 col-md-push-2">
          <?php include 'query-forklift.php'; ?>
          <?php include 'table-forklift.php'; ?>
        </div>

        <!-- navigasi per rak -->
        <div class="col-md-2 col-md-pull-10">
          <?php include 'query-rak-rekap.php'; ?>
          <?php include 'table-rak-rekap.php'; ?>

          <?php include 'query-rak.php'; ?>
          <?php include 'table-rak.php'; ?>
        </div>
      </div> <!-- row -->
    </article> <!-- container -->

    <?php include '../includes/plano-footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="../_/js/jquery.min.js"></script>
    <script src="../_/js/bootstrap.min.js"></script>
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
