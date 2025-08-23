<?php
// Include PostgreSQL connection file
include "../helper/connection.php";

// Prepare the SQL query
$query = "SELECT 
            obi_tglpb AS tgl_pb,
            obi_recid,
            CASE 
                WHEN obi_recid IS NULL THEN 'Siap Picking'
                WHEN substring(obi_recid, 1, 1) = '1' THEN 'Siap Picking'
                WHEN substring(obi_recid, 1, 1) = '2' THEN 'Siap Packing'
                WHEN substring(obi_recid, 1, 1) = '3' THEN 'Siap Draft Struk'
                WHEN substring(obi_recid, 1, 1) = '4' THEN 'Konfirmasi Pembayaran'
                WHEN substring(obi_recid, 1, 1) = '5' THEN 'Siap Struk'
                WHEN substring(obi_recid, 1, 1) = '6' THEN 'Selesai Struk'
                WHEN substring(obi_recid, 1, 1) = '7' THEN 'Set Ongkir'
                WHEN substring(obi_recid, 1, 1) = 'B' THEN 'Transaksi Batal'
            END AS status,
            obi_kdmember AS kode_member,
            CASE 
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'KlikIGR' THEN
                    CASE 
                        WHEN COALESCE(cus_jenismember, 'N') = 'T' THEN 'TMI'
                        WHEN COALESCE(cus_flagmemberkhusus, 'N') = 'Y' THEN 'Member Merah'
                        ELSE 'Member Umum'
                    END
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'Corp' THEN 'Corporate'
                WHEN COALESCE(obi_attribute2, 'KlikIGR') = 'TMI' THEN 'TMI'
                ELSE 'Member Merah'
            END AS tipe_member, 
            obi_nopb AS no_pb,
            obi_notrans AS no_trans,
            COALESCE(obi_nopo, '-') AS no_po,
            CASE 
                WHEN COALESCE(obi_freeongkir, 'N') = 'Y' THEN 'Free Ongkir'
                WHEN COALESCE(obi_freeongkir, 'N') = 'N' THEN 'Kena Ongkir'
                ELSE 'Ambil Di Toko'
            END AS ongkir,
            CASE 
                WHEN COALESCE(obi_tipebayar, 'TRF') = 'COD' THEN 'COD'
                WHEN UPPER(COALESCE(obi_tipebayar, 'X')) = 'TOP' THEN 'Kredit'
                ELSE
                    CASE 
                        WHEN COALESCE(cus_flagkredit, '-') = 'Y' AND COALESCE(obi_flagbayar, 'N') <> 'Y' THEN 'Kredit'
                        ELSE 'Tunai'
                    END
            END AS tipe_bayar,
            to_char(obi_tgltrans, 'DD-MM-YYYY') AS tgltrans,
            COALESCE(obi_attribute2, 'KlikIGR') AS kodeweb,
            COALESCE(cus_flagkredit, '-') AS tipe_kredit,
            COALESCE(obi_alasanbtl, '-') AS alasan_batal,
            obi_itemorder AS item_order,
            obi_realitem AS item_real,
            ROUND(obi_ttlorder + obi_ttlppn, 0) AS total_order,
            ROUND(obi_realorder + obi_realppn, 0) AS total_real,
            obi_ttlorder AS dpp_order,
            obi_realorder AS dpp_real,
            obi_ttlppn AS ppn_order,
            obi_realppn AS ppn_real,
            obi_ttldiskon AS diskon_order,
            obi_realdiskon AS diskon_real,
            obi_ekspedisi AS ekspedisi,
            '-' AS member_obi,
            obi_zona AS zona,
            COALESCE(obi_kdekspedisi, '-') AS kdekspedisi,
            COALESCE(obi_jrkekspedisi, 0) AS jarakkirim,
            COALESCE(obi_flagbayar, 'N') AS flagbayar
          FROM tbtr_obi_h
          LEFT JOIN tbmaster_customer ON obi_kdmember = cus_kodemember
          WHERE obi_tgltrans = TO_DATE(:tgl, 'DD-MM-YYYY')
          ORDER BY 2 DESC";

// Prepare and execute the query

$stmt = $conn->prepare($query);
$stmt->bindParam(':tgl', $tgl);
$stmt->execute();

// Fetch all rows into an associative array
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="table-responsive">
    <table id="GridView"
        class="table table-responsive table-striped table-hover table-bordered table-nonfluid compact webgrid-table-hidden"
        style="width:100%; font-size:12px">
        <thead>
            <tr>
                <th> TGL PB </th>
                <th> RECID </th>
                <th> STATUS </th>
                <th> KD MEMBER </th>
                <th> TIPE </th>
                <th> NO PB </th>
                <th> NO TRANS </th>
                <th> NO PO </th>
                <th> ONGKIR </th>
                <th> TIPE BAYAR </th>
                <th> TGL TRANS </th>
                <th> KODEWEB </th>
                <th> TIPE KREDIT </th>
                <th> ALASAN BATAL </th>
                <th> ITEM ORDER </th>
                <th> ITEM REAL </th>
                <th> TTL ORDER </th>
                <th> TTL REAL </th>
                <th> DPP ORDER </th>
                <th> DPP REAL </th>
                <th> PPN ORDER </th>
                <th> PPN REAL </th>
                <th> DISC ORDER </th>
                <th> DISC REAL </th>
                <th> EKSPEDISI </th>
                <th> MEMBER OBI </th>
                <th> ZONA </th>
                <th> KD EKSPEDISI </th>
                <th> JARAK KIRIM </th>
                <th> FLAGBAYAR </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) : ?>
            <tr>
                <td> <?= $row["tgl_pb"] ?> </td>
                <td> <?= $row["obi_recid"] ?> </td>
                <td> <?= $row["status"] ?> </td>
                <td> <?= $row["kode_member"] ?> </td>
                <td> <?= $row["tipe_member"] ?> </td>
                <td> <?= $row["no_pb"] ?> </td>
                <td> <?= $row["no_trans"] ?> </td>
                <td> <?= $row["no_po"] ?> </td>
                <td> <?= $row["ongkir"] ?> </td>
                <td> <?= $row["tipe_bayar"] ?> </td>
                <td> <?= $row["tgltrans"] ?> </td>
                <td> <?= $row["kodeweb"] ?> </td>
                <td> <?= $row["tipe_kredit"] ?> </td>
                <td> <?= $row["alasan_batal"] ?> </td>
                <td> <?= $row["item_order"] ?> </td>
                <td> <?= $row["item_real"] ?> </td>
                <td> <?= $row["total_order"] ?> </td>
                <td> <?= $row["total_real"] ?> </td>
                <td> <?= $row["dpp_order"] ?> </td>
                <td> <?= $row["dpp_real"] ?> </td>
                <td> <?= $row["ppn_order"] ?> </td>
                <td> <?= $row["ppn_real"] ?> </td>
                <td> <?= $row["diskon_order"] ?> </td>
                <td> <?= $row["diskon_real"] ?> </td>
                <td> <?= $row["ekspedisi"] ?> </td>
                <td> <?= $row["member_obi"] ?> </td>
                <td> <?= $row["zona"] ?> </td>
                <td> <?= $row["kdekspedisi"] ?> </td>
                <td> <?= $row["jarakkirim"] ?> </td>
                <td> <?= $row["flagbayar"] ?> </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>