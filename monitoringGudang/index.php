<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
require_once 'function.php'; // Ensure this file contains all the necessary functions
?>

<style>
  .btn-fixed-width {
    width: 200px;
    /* Adjust width as needed */
    text-align: center;
  }

  /* Style for card layout */
  .card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }

  /* Style for card header */
  .card .card-block {
    padding: 20px;
    color: #fff;
  }

  /* Background colors */
  .bg-c-blue {
    background-color: #007bff;
  }

  .bg-c-green {
    background-color: #28a745;
  }

  .bg-c-ungu {
    background-color: #6f42c1;
  }

  .bg-c-pink {
    background-color: #e83e8c;
  }

  /* Style for card title and value */
  .card h4 {
    font-size: 18px;
    font-weight: 600;
  }

  .card h2 {
    font-size: 32px;
    font-weight: 700;
    margin-top: 10px;
  }

  .card .text-bawah {
    font-size: 14px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .card .text-bawah:hover {
    color: #fff;
    text-decoration: underline;
  }

  /* Responsive design for cards */
  @media (max-width: 768px) {
    .col-md-3 {
      width: 100%;
      margin-bottom: 15px;
    }
  }

  /* Loader style */
  .loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  /* Footer style */
  .card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #ddd;
    padding: 10px;
  }

  /* Modifikasi untuk backdrop yang lebih terang */
  .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.1) !important;
    /* Mengurangi kegelapan */
  }

  /* Menyempurnakan z-index agar modal tetap muncul di atas */
  .modal {
    z-index: 1051 !important;
    /* Modal tetap muncul di atas backdrop */
  }

  /* Pastikan backdrop modal tidak menghalangi interaksi */
  .modal-backdrop {
    pointer-events: none;
  }

  /* Mengubah ukuran modal secara custom */
  .modal-dialog {
    max-width: 67%;
    /* Atur lebar modal sesuai kebutuhan */
  }

  .modal-content {
    height: auto;
    /* Atur tinggi modal jika diperlukan */
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between align-items-center">
    <h1>Monitoring</h1>
  </div>

  <div class="section-body">
    <div class="panel-body fixed-panel">
      <div class="row">
        <?php
        // Call the functions to get values from the database
        $lpp01tlokasi = lpp01tlokasi();
        $lpp02tlokasi = lpp02tlokasi();
        $lpp03aqty = lpp03aqty();
        $lppminus = lppminus();
        $planominus = planominus();
        $acostminus = acostminus();
        $inputkkso = inputkkso();

        // Array with card data
        $cards = [
          ["LPP 01 Tidak Ada Plano", "bg-c-blue", "lpp01-tdk-ada-lokasi", $lpp01tlokasi],
          ["LPP 02 Tidak Ada Plano", "bg-c-green", "lpp02-tdk-ada-lokasi", $lpp02tlokasi],
          ["LPP 03 Ada QTY", "bg-c-ungu", "lpp03-msh-ada-qty", $lpp03aqty],
          ["LPP Minus", "bg-c-pink", "lpp-minus", $lppminus],
          ["Plano Minus", "bg-c-ungu", "plano-minus", $planominus],
          ["Acost Minus", "bg-c-blue", "acost-minus", $acostminus],
          ["Input KKSO Perishable", "bg-c-blue", "input-kkso", $inputkkso],
        ];

        // Loop through the card array and display each card
        foreach ($cards as $card) {
          list($title, $bgClass, $selectorClass, $value) = $card;
          echo "
            <div class='col-md-3'>
                <div class='card $bgClass order-card animate__animated animate__flipInX'>
                    <div class='card-block'>
                        <h4 class='m-b-20'>$title</h4>
                        <h2 class='text-right'>
                            <i class='fas fa-copy f-left'></i>
                            <span><b>$value</b></span>
                        </h2>
                        <hr>
                        <p class='text-bawah $selectorClass'>Lihat Data</p>
                    </div>
                </div>
            </div>";
        }
        ?>
      </div>
    </div>

    <!-- Placeholder to display data -->
    <div id="tabel"></div>
    <div class="card-footer text-right">
      <small class="text-muted">Halaman diperbarui pada <?= date('d M Y H:i:s') ?></small>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="dataModalLabel">Data Detail</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalContent">
            <!-- Data will be dynamically loaded here -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>

<script type="text/javascript">
  $(window).on('load', function() {
    setTimeout(function() {
      $('#load').fadeOut('slow');
    }, 500);
  });

  // Mapping for button classes to PHP files
  const mappings = {
    "lpp01-tdk-ada-lokasi": "1.lpp01-tdk-ada-lokasi.php",
    "lpp02-tdk-ada-lokasi": "2.lpp02-tdk-ada-lokasi.php",
    "lpp03-msh-ada-qty": "3.lpp03-msh-ada-qty.php",
    "lpp-minus": "4.lpp-minus.php",
    "plano-minus": "5.plano-minus.php",
    "acost-minus": "6.acost-minus.php",
    "input-kkso": "7.input-kkso-01.php"
  };

  // Event listener for click events on the buttons
  $(".text-bawah").click(function() {
    const targetClass = $(this).attr("class").split(" ")[1];
    const url = mappings[targetClass];
    if (url) {
      // Show modal before loading data
      $('#dataModal').modal('show');
      // Load data into modal
      AjaxSendForm(url, '#modalContent', '');
    } else {
      alert("URL tidak ditemukan!");
    }
  });

  // Function for sending data via AJAX
  function AjaxSendForm(url, placeholder, form, append) {
    $(placeholder).empty();
    const data = $(form).serialize();
    append = append === undefined ? false : append;
    $.ajax({
      type: 'POST',
      url: url,
      data: data,
      dataType: 'html',
      beforeSend: function() {
        $(placeholder).addClass('loader');
      },
      success: function(data) {
        if (append) {
          $(placeholder).append(data);
        } else {
          $(placeholder).html(data);
        }
      },
      error: function(xhr) {
        alert("Error occurred. Please try again.");
        $(placeholder).append(xhr.statusText + xhr.responseText);
      },
      complete: function() {
        $(placeholder).removeClass('loader');
      }
    });
  }
</script>