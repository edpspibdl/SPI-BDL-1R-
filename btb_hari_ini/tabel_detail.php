<?php
$query = "SELECT 
    m.mstd_typetrn AS trn_type,
    m.mstd_tgldoc AS trn_tgldoc,
    m.mstd_nodoc AS trn_nodoc,
    m.mstd_nopo AS trn_nopo,
    m.mstd_tglpo::DATE AS trn_tglpo,
    m.mstd_seqno AS trn_seqno,
    p.prd_kodedivisi AS trn_div,
    p.prd_kodedepartement AS trn_dept,
    p.prd_kodekategoribarang AS trn_katb,
    m.mstd_prdcd AS trn_prdcd,
    p.prd_deskripsipanjang AS trn_nama_barang,
    m.mstd_unit AS trn_unit,
    m.mstd_frac AS trn_frac,
    COALESCE(p.prd_kodetag, ' ') AS trn_tag,
    m.mstd_qty AS trn_qty,
    COALESCE(m.mstd_qtybonus1, 0) AS trn_qty_bonus1,
    COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus2,
    COALESCE(m.mstd_qtybonus1, 0) + COALESCE(m.mstd_qtybonus2, 0) AS trn_qty_bonus,
    m.mstd_hrgsatuan AS trn_harga_satuan,
    m.mstd_gross AS trn_gross,
    COALESCE(m.mstd_discrph, 0) AS trn_discount,
    COALESCE(m.mstd_ppnrph, 0) AS trn_ppn,
    p.prd_kodetag AS trn_tagigr,
    COALESCE(m.mstd_flagdisc1, ' ') AS trn_flag1,
    COALESCE(m.mstd_flagdisc2, ' ') AS trn_flag2,
    m.mstd_kodesupplier AS trn_kode_supplier,
    s.sup_namasupplier AS trn_nama_supplier
FROM 
    tbtr_mstran_d m
LEFT JOIN 
    tbmaster_prodmast p ON m.mstd_prdcd = p.prd_prdcd
LEFT JOIN 
    tbmaster_supplier s ON m.mstd_kodesupplier = s.sup_kodesupplier
WHERE 
    m.mstd_recordid IS NULL
    AND m.mstd_typetrn = 'B'
    AND m.mstd_tgldoc::DATE = CURRENT_DATE
ORDER BY 
    m.mstd_nodoc";

include "../helper/connection.php";
$stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<hr> 

<div class="table-responsive">
    <table id="GridView" class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden" style="width:100%">
        
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">#</th>
                        
                        <th colspan="5" class="text-center">Produk</th>
                        
                        <th colspan="3" class="text-center">Quantity</th>
                        <th colspan="5" class="text-center">Rupiah</th>
                    </tr>
                  <tr>

                      <th class="text-center">PLU</th>
                      <th class="text-center">Nama</th>
                      <th class="text-center">Unit</th>
                      <th class="text-center">Frac</th>
                      <th class="text-center">Tag</th>

                      <th class="text-center">CTN</th>
                      <th class="text-center">Pcs</th>
                      <th class="text-center">Bonus</th>

                      <th class="text-center">Harga</th>
                      <th class="text-center">Discount</th>
                      <th class="text-center">Netto</th>
                      <th class="text-center">PPN</th>
                      <th class="text-center">Total</th>

                      <th class="text-center">Nomor Dokumen</th>
                      <th class="text-center">Tgl Dokumen</th>
                     
                      <th class="text-center">Nomor PO</th>
                      <th class="text-center">Tgl PO</th>

                      <th class="text-center">Kode Supp</th>
                      <th class="text-center">Nama Supp</th>
                  </tr>
                </thead> 
                
                <tbody>
                  <?php
                        $rphGross     = 0;
                        $rphDiscount  = 0;
                        $rphPPN       = 0;
                    // Fetch each row in an associative array
                    $noUrut = 0;
                    foreach ($rows as $row) {
                      $noUrut ++;
                      
                       print '<tr>';
                       echo '<td align="right">'  . $noUrut . '</td>';

                       echo '<td align="center">' . $row['trn_prdcd'] . '</td>';

                       echo '<td align="left" class="text-nowrap">'   . $row['trn_nama_barang'] . '</td>';
                       echo '<td align="left">' . $row['trn_unit'] . '</td>';
                       echo '<td align="left">' . $row['trn_frac'] . '</td>';
                       echo '<td align="left">' . $row['trn_tag'] . '</td>';

                       echo '<td align="right">'  . number_format(($row['trn_qty'] - $row['trn_qty'] % $row['trn_frac']) / $row['trn_frac'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_qty'] % $row['trn_frac'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_qty_bonus'], 0, '.', ',') . '</td>';
                     
                       echo '<td align="right">'  . number_format($row['trn_gross'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_discount'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_gross'] - $row['trn_discount'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_ppn'], 0, '.', ',') . '</td>';
                       echo '<td align="right">'  . number_format($row['trn_gross'] - $row['trn_discount'] + $row['trn_ppn'], 0, '.', ',') . '</td>';

                        echo '<td align="center">' . $row['trn_nodoc'] . '</td>';
                       echo '<td align="center" class="text-nowrap">' . $row['trn_tgldoc'] . '</td>';
                       
                       echo '<td align="center">' . $row['trn_nopo'] . '</td>';
                       echo '<td align="center" class="text-nowrap">' . $row['trn_tglpo'] . '</td>';

                       echo '<td align="left">'  . $row['trn_kode_supplier'] . '</td>';
                       echo '<td align="left" class="text-nowrap">'  . $row['trn_nama_supplier'] . '</td>';

                      print '</tr>';
                      
                      // hitung total nilai disini
                      

                        $rphGross     += $row['trn_gross']; 
                        $rphDiscount  += $row['trn_discount']; 
                        $rphPPN       += $row['trn_ppn']; 
                    
                    }
                  ?>
                </tbody>
                
                
            </table>
        </div>

        <script type="text/javascript">
$(document).ready(function(){
    var GridView = $("#GridView").DataTable({
         "language": {
            "search": "Cari",
            "lengthMenu": "_MENU_ Baris per halaman",
            "zeroRecords": "Data tidak ada",
            "info": "Halaman _PAGE_ dari _PAGES_ halaman",
            "infoEmpty": "Data tidak ada",
            "infoFiltered": "(Filter dari _MAX_ data)"
        },
        lengthChange: true,
        lengthMenu: [10, 25, 50, 75, 100],
        paging: true,
        responsive: true,
        buttons: ["copy", "excel"],
        "columnDefs": [
            { "targets": 0, "orderable": false }, // Kolom pertama tidak bisa diurutkan
            { "targets": 1, "className": "text-center" }, // Menambahkan kelas pada kolom tertentu
            { "targets": 2, "className": "text-center" },
            { "targets": 3, "className": "text-center" },
            { "targets": 4, "className": "text-center" },
            { "targets": 5, "className": "text-center" }
        ]
    });
   
});
</script>
