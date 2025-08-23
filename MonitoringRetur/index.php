  <?php
  require_once '../layout/_top.php';
  require_once '../helper/connection.php'; // Pastikan connection.php berisi koneksi PDO yang valid

  // Menggunakan exception handling pada query
  try {
      $query = "SELECT DISTINCT ON (s.sup_namasupplier)
          b.btb_kodeigr, 
          b.btb_nodoc, 
          b.btb_kodesupplier, 
          s.sup_namasupplier, 
          b.btb_tgldoc
      FROM tbtr_mstran_btb b
      LEFT JOIN TBMASTER_PRODMAST p ON b.btb_prdcd = p.PRD_PRDCD
      LEFT JOIN TBMASTER_SUPPLIER s ON b.btb_kodesupplier = s.sup_kodesupplier
      WHERE b.btb_istype IS NOT NULL
        AND s.sup_kodesupplier NOT IN ('I0929')
        AND p.prd_perlakuanbarang NOT IN ('TG')
      ORDER BY s.sup_namasupplier, b.btb_tgldoc DESC";

      $stmt = $conn->query($query); // Eksekusi query dengan PDO
  } catch (PDOException $e) {
      die("Error: " . $e->getMessage());
  }
  ?>

  <!-- Styling untuk Tabel -->
  <style>
    .modal-dialog {
    max-width: 70%; /* Atur lebar modal sesuai dengan persentase */
    }
      #table-1 {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
      }

      #table-1 th, #table-1 td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
      }

      #table-1 th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-bottom: 2px solid #333;
      }

      #table-1 td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .table-responsive {
        overflow-x: auto;
      }
  </style>

  <section class="section">
    <div class="section-header d-flex justify-content-between">
      <h1>Monitoring Retur</h1>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover table-striped" id="table-1">
                <thead>
                  <tr>
                    <th>NO</th>
                    <th>KODE IGR</th>
                    <th>KODE SUPP</th>
                    <th>NAMA SUPP</th>
                    <th>DETAIL</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $nomor = 1; 
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $nomor . "</td>";
                    echo "<td>" . htmlspecialchars($row['btb_kodeigr']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['btb_kodesupplier']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sup_namasupplier']) . "</td>";
                    echo "<td><button class='btn btn-info btn-sm' data-toggle='modal' data-target='#modalDetail' data-kodesupplier='" . htmlspecialchars($row['btb_kodesupplier']) . "'>Detail</button></td>";
                    echo "</tr>";
                    $nomor++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Menambahkan kelas modal-lg -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetailLabel">Detail Item Supplier <?= $row['kodeSupplier'] ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="modalContent">Loading...</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  <?php
  require_once '../layout/_bottom.php';
  ?>

  <!-- Add the required CSS and JS libraries -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.0/css/buttons.dataTables.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.0/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.0/js/buttons.html5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const table = $('#table-1').DataTable({
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        buttons: [
          { extend: 'copy', text: 'Copy' },
          { extend: 'excel', text: 'Excel', filename: 'MONIT_RETUR_' + new Date().toISOString().split('T')[0], title: null }
        ],
        dom: 'Bfrtip'
      });

      table.buttons().container().appendTo('#table-1_wrapper .col-md-6:eq(0)');
    });

    $('#modalDetail').on('show.bs.modal', function(event) {
      const button = $(event.relatedTarget);
      const kodeSupplier = button.data('kodesupplier');

      // Perbarui judul modal dengan kode supplier
    $('#modalDetailLabel').text('Detail Item Supplier: ' + kodeSupplier);

      // Request data detail
      $.ajax({
        url: 'get_detail.php',
        method: 'POST',
        data: { kodeSupplier },
        success: function(response) {
          $('#modalContent').html(response);
        },
        error: function() {
          $('#modalContent').html('Failed to load details.');
        }
      });
    });
  </script>
