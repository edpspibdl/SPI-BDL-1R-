<?php
require_once '../helper/connection.php';

$kodePLU = isset($_GET['kodePLU']) ? str_pad($_GET['kodePLU'], 7, '0', STR_PAD_LEFT) : '';
$dataTag = [];

if ($kodePLU !== '') {
          try {
                    $query = "
            SELECT DISTINCT ON (prd_prdcd)
                tag_kodeigr,
                tag_kodetag,
                tag_keterangan,
                tag_tidakbolehorder,
                tag_tidakbolehjual
            FROM tbmaster_prodmast
            LEFT JOIN tbmaster_divisi ON prd_kodedivisi = div_kodedivisi
            LEFT JOIN tbmaster_departement ON prd_kodedepartement = dep_kodedepartement
            LEFT JOIN tbmaster_kategori ON prd_kodekategoribarang = kat_kodekategori
            LEFT JOIN tbmaster_tag ON prd_kodetag = tag_kodetag
            WHERE prd_prdcd LIKE :kodePLU
        ";

                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':kodePLU', $kodePLU, PDO::PARAM_STR);
                    $stmt->execute();
                    $dataTag = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
                    die("Query gagal: " . $e->getMessage());
          }
}
?>

<!-- Modal HTML -->
<div class="modal fade" id="modalInfoTag" tabindex="-1" role="dialog" aria-labelledby="modalInfoTagLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">

                              <!-- Modal Header -->
                              <div class="modal-header bg-light ">
                                        <h5 class="modal-title" id="modalInfoTagLabel">Informasi Tag Produk</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                                                  <span aria-hidden="true">&times;</span>
                                        </button>
                              </div>

                              <!-- Modal Body -->
                              <div class="modal-body">
                                        <table class="table table-bordered table-striped">
                                                  <thead class="table-primary text-center">
                                                            <tr>
                                                                      <th>KODE IGR</th>
                                                                      <th>KODE TAG</th>
                                                                      <th>KETERANGAN</th>
                                                                      <th>TIDAK BOLEH ORDER</th>
                                                                      <th>TIDAK BOLEH JUAL</th>
                                                            </tr>
                                                  </thead>
                                                  <tbody>
                                                            <?php if (!empty($dataTag)): ?>
                                                                      <?php foreach ($dataTag as $row): ?>
                                                                                <tr class="text-center">
                                                                                          <td><?= htmlspecialchars($row['tag_kodeigr'] ?? '-') ?></td>
                                                                                          <td><?= htmlspecialchars($row['tag_kodetag'] ?? '-') ?></td>
                                                                                          <td class="text-start"><?= htmlspecialchars($row['tag_keterangan'] ?? '-') ?></td>
                                                                                          <td><?= $row['tag_tidakbolehorder'] == 'Y' ? 'YA' : 'Tidak' ?></td>
                                                                                          <td><?= $row['tag_tidakbolehjual'] == '' ? 'Tidak' : 'YA' ?></td>
                                                                                </tr>
                                                                      <?php endforeach; ?>
                                                            <?php else: ?>
                                                                      <tr>
                                                                                <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                                                      </tr>
                                                            <?php endif; ?>
                                                  </tbody>
                                        </table>
                              </div>

                              <!-- Modal Footer -->
                              <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                              </div>

                    </div>
          </div>
</div>