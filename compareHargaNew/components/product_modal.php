<?php
// FILE: components/product_modal.php
// Membutuhkan variabel: $conn (koneksi database)

// Query untuk data modal produk
$produkQuery = $conn->query("
    SELECT PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_FLAGIGR 
    FROM TBMASTER_PRODMAST 
    WHERE PRD_RECORDID IS NULL 
    AND PRD_PRDCD LIKE '%0'
    ORDER BY PRD_PRDCD ASC
");
?>

<div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 shadow-lg">
            
            <div class="modal-header p-0 border-0 pb-2">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-success px-3" id="produkModalLabel" style="font-size: 24pt;">
                        Pilih Produk Master
                    </h5>
                </div>
                <hr class="w-100 mx-3 mb-0">
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="produkTable" class="display table table-bordered table-striped table-hover w-100">
                        <thead class="text-center bg-light">
                            <tr>
                                <th>PLU</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($produk = $produkQuery->fetch(PDO::FETCH_ASSOC)) {
                                $plu = htmlspecialchars($produk['prd_prdcd']);
                                $desk = htmlspecialchars($produk['prd_deskripsipanjang']);
                                $unit = htmlspecialchars($produk['prd_flagigr'] === 'Y' ? 'IGR' : 'REG');
                                
                                echo "<tr class='produk-row' data-plu='{$plu}'>
                                        <td class='fw-medium'>{$plu}</td>
                                        <td>{$desk}</td>
                                        <td class='text-center'>{$unit}</td>
                                    </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer p-3">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
            
        </div>
    </div>
</div>