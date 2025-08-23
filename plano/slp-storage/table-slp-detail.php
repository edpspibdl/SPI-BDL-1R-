<?php
// Define the query based on the report type
switch ($slpStatus) {
    case " ":
        include 'query-slp-belum.php'; 
        $judulPanel = "SLP Belum Realisasi";
        break;
    case "P":
        include 'query-slp-sudah-.php'; 
        $judulPanel = "SLP Sudah Realisasi";
        break;
    case "C":
        include 'query-slp-batal-.php'; 
        $judulPanel = "SLP Batal";
        break;
    case "R":
        include 'query-slp-report-.php'; 
        $judulPanel = "Report SLP";
        break;
    default:
        include 'query-slp-belum.php';
}

// Create connection to PostgreSQL
include '../_/connection.php';

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<div class="panel bayang">
    <div class="table-responsive">
        <table id="GridView" class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="info">
                    <th>#</th>
                    <th>Produk</th>
                    <th>Quantity</th>
                    <th>Lokasi Penyimpanan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $noUrut = 0;
                // Fetch each row in an associative array
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $noUrut++;

                    $lokasi  = $row['slp_koderak'] . ' - ';
                    $lokasi .= $row['slp_kodesubrak'] . ' - ';
                    $lokasi .= $row['slp_tiperak'] . ' - ';
                    $lokasi .= $row['slp_shelvingrak'] . ' - ';
                    $lokasi .= $row['slp_nourut'];

                    $infoProduk  = $row['slp_prdcd'] . ' - ';
                    $infoProduk .= $row['slp_unit'] . ' / ';
                    $infoProduk .= $row['slp_frac'] . ' ';
                    $infoProduk .= $row['slp_kodetag'];

                    $infoTertunda = '';
                    switch ($row['slp_hari_tertunda']) {
                        case "0":
                            if ($row['SLP_JAM_TERTUNDA'] >= 2) {
                                $infoTertunda = '<span class="label label-danger"> ' . $row['SLP_JAM_TERTUNDA'] . ' jam yang lalu</span>';
                            } elseif ($row['SLP_JAM_TERTUNDA'] == 1) {
                                $infoTertunda = '<span class="label label-primary"> ' . $row['SLP_JAM_TERTUNDA'] . ' jam yang lalu</span>';
                            }
                            break;
                        case "1":
                            $infoTertunda = '<span class="label label-danger"> Kemarin</span>';
                            break;
                        default:
                            $infoTertunda = '<span class="label label-danger"> ' . $row['slp_hari_tertunda'] . ' hari yang lalu</span>';
                    }

                    $infoQuantity = ' ';
                    if ($row['slp_qtycrt'] != 0) {
                        $infoQuantity  = $row['slp_qtycrt'] . '<br>';
                        $infoQuantity .= '<span class="badge">' . $row['slp_unit'] . '</span>';
                    }
                    if ($row['slp_qtypcs'] != 0) {
                        $infoQuantity .= $row['slp_qtypcs'] . '<br>';
                        $infoQuantity .= '<span class="badge">PCS</span>';
                    }

                    $iconLokasi = '';
                    if (substr($row['slp_koderak'], 0, 1) != 'G' && substr($row['slp_koderak'], 0, 1) != 'D') {
                        $iconLokasi = '<span class="glyphicon glyphicon-shopping-cart"></span>';
                    } else {
                        $iconLokasi = '<span class="glyphicon glyphicon-list"></span>';
                    }

                    echo '<tr>';
                    echo '<td align="right"><span class="badge">' . $noUrut . '</span></td>';
                    echo '<td align="left"><h4>' . $row['slp_deskripsi'] . '</h4>' . $infoProduk . '&nbsp;' . $infoTertunda . '</td>';
                    echo '<td align="center"><h4>' . $infoQuantity . '</h4></td>';
                    echo '<td align="left" class="text-nowrap"><h4>' . $iconLokasi . ' ' . $lokasi . '</h4>' . $row['slp_id'] . '</td>';
                    echo '</tr>';
                } // end while
                ?>
            </tbody>
        </table>
    </div><!-- table responsive -->
</div><!-- panel -->

<?php if ($noUrut == 0) { echo '<button type="button" class="btn btn-success">Semua SLP ke Rak Storage sudah selesai.</button>';} ?>
