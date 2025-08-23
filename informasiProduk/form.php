<?php require_once '../layout/_top.php'; ?>

<!-- DataTables & jQuery -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<style>
  fieldset {
    border: 2px solid #ccc;
    border-radius: 8px;
    padding: 20px;
    background-color: #f7f7f7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  legend h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
  }

  label[for="kodePLU"] {
    display: block;
    font-weight: 500;
    margin: 10px 0 5px;
    color: #333;
  }

  form div {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  input[type="text"]#kodePLU {
    padding: 10px 12px;
    font-size: 2rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 4400px;
    height: 50px;
    transition: border-color 0.2s ease;
  }

  input[type="text"]#kodePLU:focus {
    border-color: #007bff;
    outline: none;
  }

  button#openModal {
    padding: 10px 14px;
    background-color: #007bff;
    color: #fff;
    font-size: 1.2rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  button#openModal:hover {
    background-color: #0056b3;
  }

  input[type="submit"] {
    margin-top: 15px;
    padding: 10px 20px;
    font-size: 1rem;
    background-color: #28a745;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  input[type="submit"]:hover {
    background-color: #218838;
  }

  .modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    overflow: auto;
  }

  .modal-content {
    background: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 10px;
    width: 95%;
    max-width: 800px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    position: relative;
  }

  .modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    background-color: #f1f1f1;
    color: #555;
    border-radius: 50%;
    font-size: 16px;
    font-weight: bold;
    line-height: 24px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    transition: background 0.3s;
  }

  .modal-close:hover {
    background-color: #e0e0e0;
  }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <h1>Form Informasi Produk</h1>
  </div>

  <div class="container">
    <fieldset>
      <legend class="text-left">
        <h2>Cari Produk</h2>
      </legend>
      <form action="index.php" method="GET">
        <label for="kodePLU">Masukkan Kode PLU:</label>
        <div>
          <input type="text" id="kodePLU" name="kodePLU" required placeholder="Contoh: 0013500">
          <button type="button" id="openModal" title="Pilih dari daftar">&#128269;</button>
        </div>
        <br>
        <input type="submit" value="Cari Produk">
      </form>
    </fieldset>
  </div>
</section>


<!-- MODAL -->
<div id="pluModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="modal-close" id="modalCloseBtn">&times;</span>
    <h3>Daftar PLU Produk</h3>

    <div style="overflow-x:auto;">
      <table id="pluTable" class="display" style="width:100%;">
        <thead>
          <tr>
            <th>PLU</th>
            <th>Deskripsi Panjang</th>
            <th>Flag Jual</th>
          </tr>
        </thead>
        <tbody>
          <?php
          require_once '../helper/connection.php';
          try {
            $stmt = $conn->query("SELECT PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_FLAGIGR 
                                  FROM TBMASTER_PRODMAST 
                                  WHERE PRD_RECORDID IS NULL 
                                  AND PRD_PRDCD LIKE '%0'
                                  ORDER BY PRD_PRDCD ASC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $prdcd = htmlspecialchars($row['prd_prdcd']);
              $desc = htmlspecialchars($row['prd_deskripsipanjang']);
              $flag = htmlspecialchars($row['prd_flagigr']);
              echo "<tr data-plu='$prdcd'>
                      <td>$prdcd</td>
                      <td>$desc</td>
                      <td>$flag</td>
                    </tr>";
            }
          } catch (PDOException $e) {
            echo '<tr><td colspan="3" style="color:red;">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<script>
  $(document).ready(function() {
    const modal = document.getElementById('pluModal');
    const openBtn = document.getElementById('openModal');
    const closeBtn = document.getElementById('modalCloseBtn');
    const inputPLU = document.getElementById('kodePLU');

    const dataTable = $('#pluTable').DataTable({
      responsive: true,
      paging: true,
      pageLength: 10
    });

    openBtn.addEventListener('click', () => {
      modal.style.display = 'block';
      dataTable.columns.adjust().responsive.recalc();
    });

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    $('#pluTable tbody').on('click', 'tr', function() {
      const plu = $(this).data('plu');
      inputPLU.value = plu;
      modal.style.display = 'none';
    });
  });
</script>