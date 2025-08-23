<?php
require 'query.php'; // ambil variabel $stmt dari query.php
?>

<!-- Styling tabel -->
<style>
          #table-1 {
                    width: 100%;
                    table-layout: auto;
                    /* Penting agar lebar kolom mengikuti konten */
                    border-collapse: collapse;
          }

          #table-1 th,
          #table-1 td {
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #ddd;
          }

          #table-1 th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                    border-bottom: 2px solid #333;
          }

          #table-1 td.desk-column {
                    white-space: nowrap !important;
                    word-break: break-word;
                    min-width: 200px;
                    /* Bisa ubah jadi nilai minimum */
          }


          .table-responsive {
                    overflow-x: auto;
          }
</style>


<div class="table-responsive">
          <table class="table table-hover table-striped" id="table-1">
                    <thead>
                              <tr>
                                        <th>#</th>
                                        <th>DIV</th>
                                        <th>DEPT</th>
                                        <th>KATB</th>
                                        <th>PLU</th>
                                        <th>DESK</th>
                                        <th>FRAC</th>
                                        <th>TAG</th>
                                        <th>EXP TERDEKAT</th>
                                        <th>ALAMAT</th>
                                        <th>QTY PCS PLANO</th>
                                        <th>QTY CTN PLANO</th>
                                        <th>LPP</th>
                                        <th>LPP CTN</th>
                              </tr>
                    </thead>
                    <tbody>
                              <?php
                              $nomor = 1;
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                    <td>{$nomor}</td>
                    <td>{$row['div']}</td>
                    <td>{$row['dept']}</td>
                    <td>{$row['katb']}</td>
                    <td>{$row['plu']}</td>
                    <td class='desk-column'>{$row['desk']}</td>
                    <td>{$row['frac']}</td>
                    <td>{$row['tag']}</td>
                    <td>{$row['exp_terdekat']}</td>
                    <td>{$row['alamat']}</td>
                    <td class='text-right'>{$row['qty_lks']}</td>
                    <td class='text-right'>{$row['qty_lks_ctn']}</td>
                    <td class='text-right'>{$row['lpp']}</td>
                    <td class='text-right'>{$row['lpp_ctn']}</td>
                </tr>";
                                        $nomor++;
                              }
                              ?>
                    </tbody>
          </table>
</div>