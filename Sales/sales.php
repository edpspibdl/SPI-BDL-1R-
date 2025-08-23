<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>
<meta http-equiv="refresh" content="60">
<title>Sales Today - SPI BDL 1R</title>
<style>
    .table {
        font-size: 13px;
    }

    .vcenter {
        display: inline-block;
        vertical-align: middle;
        float: none;
    }
</style>
<script>
    var date = new Date();
    var hari = date.getDay();
    var tanggal = date.getDate();
    var bulan = date.getMonth();
    var tahun = date.getFullYear();

    switch (hari) {
        case 0:
            hari = "Minggu";
            break;
        case 1:
            hari = "Senin";
            break;
        case 2:
            hari = "Selasa";
            break;
        case 3:
            hari = "Rabu";
            break;
        case 4:
            hari = "Kamis";
            break;
        case 5:
            hari = "Jum'at";
            break;
        case 6:
            hari = "Sabtu";
            break;
    }
    switch (bulan) {
        case 0:
            bulan = "Januari";
            break;
        case 1:
            bulan = "Februari";
            break;
        case 2:
            bulan = "Maret";
            break;
        case 3:
            bulan = "April";
            break;
        case 4:
            bulan = "Mei";
            break;
        case 5:
            bulan = "Juni";
            break;
        case 6:
            bulan = "Juli";
            break;
        case 7:
            bulan = "Agustus";
            break;
        case 8:
            bulan = "September";
            break;
        case 9:
            bulan = "Oktober";
            break;
        case 10:
            bulan = "November";
            break;
        case 11:
            bulan = "Desember";
            break;
    }

    var tampilTanggal = hari + ", " + tanggal + " " + bulan + " " + tahun;
</script>

<section class="section">
    <div class="section-header d-flex justify-content-between">
        <h1>Sales Today By Kasir</h1>
    </div>
    <div class="container-fluid">
        <div class="panel panel-primary">
            <div class="panel-body fixed-panel">
                <div class="form-group">
                    <h1 style='text-align:center;background:#3399FF;margin-top:0;padding:10px;font-size:24px;'>
                        PREVIEW TRANSAKSI HARI INI <script>
                            document.write(tampilTanggal);
                        </script>
                        <span style='color:#ffff00'> ( Live )</span>
                    </h1>
                    <div class="row">
                        <?php
                        try {
                            $sql = "
                                    SELECT 
                                        js_cashierid AS id_kasir,
                                        username AS nama_kasir,
                                        js_cashierstation AS kassa,
                                        COALESCE(js_totsalesamt, 0) AS total_transaksi,
                                        COALESCE(e_wallet, 0) AS e_wallet,
                                        COALESCE(js_totcashsalesamt, 0) AS tunai,
                                        COALESCE(js_totdebitamt, 0) AS kdebit,
                                        COALESCE((js_totcc1amt + js_totcc2amt), 0) AS kkredit,
                                        COALESCE(js_totcreditsalesamt, 0) AS kredit,
                                        COALESCE(js_freqcashdrawl, 0) AS jumlah_ns,
                                        COALESCE(js_cashdrawalamt, 0) AS ns,
                                        COALESCE(ppob, 0) AS ppob,
                                        CASE 
                                            WHEN js_cashdrawerend IS NULL AND js_cashierid NOT IN ('IDM', 'OMI', 'BKL', 'ONL') THEN 'OPEN'
                                            ELSE 'CLOSING'
                                        END AS status
                                    FROM tbtr_jualsummary 
                                    LEFT JOIN tbmaster_user ON js_cashierid = userid 
                                    LEFT JOIN (
                                        SELECT dpp_create_by, 
                                            SUBSTRING(dpp_stationkasir FROM 1 FOR 2) AS stat, 
                                            SUM(dpp_jumlahdeposit) AS ppob 
                                        FROM tbtr_deposit_mitraigr
                                        WHERE DATE(dpp_create_dt) = CURRENT_DATE
                                        GROUP BY dpp_create_by, SUBSTRING(dpp_stationkasir FROM 1 FOR 2)
                                    ) AS deposit_data 
                                    ON js_cashierid = dpp_create_by AND js_cashierstation = js_cashierstation
                                    LEFT JOIN (
                                        SELECT js_cashierid AS id, 
                                            SUM(js_isaku_amt + js_ewallet_totamount) AS e_wallet 
                                        FROM tbtr_jualsummary
                                        WHERE js_transactiondate::date = CURRENT_DATE
                                        GROUP BY js_cashierid
                                    ) AS wallet 
                                    ON wallet.id = js_cashierid
                                    WHERE DATE(js_transactiondate) = CURRENT_DATE
                                    ORDER BY 
                                        CASE 
                                            WHEN js_cashdrawerend IS NULL AND js_cashierid NOT IN ('IDM', 'OMI', 'BKL', 'ONL') THEN 'OPEN'
                                            ELSE 'CLOSING' 
                                        END DESC";

                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                        } catch (Exception $e) {
                            die("Error: " . $e->getMessage());
                        }
                        ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Data Transaksi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="GridView" class="table table-sm table-bordered table-striped table-hover compact" style="width:100%">
                                            <thead>
                                                <th>Kassa</th>
                                                <th>ID</th>
                                                <th>Kasir</th>
                                                <th class="text-center">TOTAL TRANSAKSI</th>
                                                <th class="text-center">E-WALLET</th>
                                                <th class="text-center">TUNAI</th>
                                                <th class="text-center">K.DEBIT</th>
                                                <th class="text-center">K.KREDIT</th>
                                                <th class="text-center">KREDIT</th>
                                                <th class="text-center">NS PROGRESS</th>
                                                <th class="text-center">JUMLAH NS</th>
                                                <th class="text-center">NOMINAL NS</th>
                                                <th class="text-center">PPOB</th>
                                                <th class="text-center">STATUS</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $TOTAL_TRANSAKSI = 0;
                                                $TUNAI = 0;
                                                $KDEBIT = 0;
                                                $KKREDIT = 0;
                                                $KREDIT = 0;
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $no++;

                                                    if ($row['status'] == "Closing") {
                                                        echo "<tr style='background:#c0c0c0'>";
                                                    } else {
                                                        echo "<tr>";
                                                    }
                                                ?>
                                                    <td><?= $row['kassa'] ?></td>
                                                    <td><?= $row['id_kasir'] ?></td>
                                                    <td><?= $row['nama_kasir'] ?></td>
                                                    <td align="right"><?= number_format($row['total_transaksi'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['e_wallet'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['tunai'], 0, '.', ',') ?></td>
                                                    <?php
                                                    $batastunay = substr($row['tunai'], -12);
                                                    error_reporting(0);
                                                    $persen = $batastunay / 10000000 * 100;
                                                    ?>
                                                    <td align="right"><?= number_format($row['kdebit'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['kkredit'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['kredit'], 0, '.', ',') ?></td>
                                                    <td style="width:200px;">
                                                        <div class="progress" style="margin-bottom:0px; margin-top:0px; height: 19px;">
                                                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                                                                aria-valuenow="<?= round($persen) ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= round($persen) ?>%">
                                                                <?= round($persen) ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td align="center"><?= number_format($row['jumlah_ns'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['ns'], 0, '.', ',') ?></td>
                                                    <td align="right"><?= number_format($row['ppob'], 0, '.', ',') ?></td>
                                                    <td align="center">
                                                        <?php
                                                        $ket = $row['status'];
                                                        if ($ket == 'OPEN') {
                                                            echo "<span class='badge bg-success'>" . $ket . "</span>";
                                                        } else {
                                                            echo "<span class='badge bg-danger'>" . $ket . "</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    </tr>
                                                <?php
                                                    $TOTAL_TRANSAKSI += $row['total_transaksi'];
                                                    $E_WALLET       += $row['e_wallet'];
                                                    $TUNAI          += $row['tunai'];
                                                    $KDEBIT         += $row['kdebit'];
                                                    $KKREDIT        += $row['kkredit'];
                                                    $KREDIT         += $row['kredit'];
                                                    $NS             += $row['ns'];
                                                    $PPOB           += $row['ppob'];
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" align="center"><strong>TOTAL</td>
                                                    <td align="right"><strong><?= number_format($TOTAL_TRANSAKSI, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($E_WALLET, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($TUNAI, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($KDEBIT, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($KKREDIT, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($KREDIT, 0, '.', ',') ?></strong></td>
                                                    <td colspan="2" align="center"></td>
                                                    <td align="right"><strong><?= number_format($NS, 0, '.', ',') ?></strong></td>
                                                    <td align="right"><strong><?= number_format($PPOB, 0, '.', ',') ?></strong></td>
                                                    <td align="center"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="col-md-12">
                                        <marquee scrollamount="5" behavior="scroll" direction="left">
                                            <strong style="color:green;">Progress Transaksi SPIBDL1R :</strong>
                                            <span style="color:red; font-size:20px;">
                                                <?= number_format($TOTAL_TRANSAKSI, 0, '.', ',') ?>
                                            </span>
                                        </marquee>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-footer -->
                </div>
            </div>
            <h3 class="page-title"></h3>
        </div>
    </div>
    </div>
    </div>
    </div>
</section>

<?php require_once '../layout/_bottom.php'; ?>