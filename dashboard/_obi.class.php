<?php
require_once '_config.php';


class OBI extends DBConfig
{
  public function headerTgl($id)
{
    $query  = "SELECT
    to_char(obi_tgltrans, 'DD-MON-YYYY') AS obi_tgl,
    SUM(coalesce(pb_masuk, 0)) AS pb_masuk,
    SUM(coalesce(siap_send, 0)) AS siap_send,
    SUM(coalesce(siap_pick, 0)) AS siap_pick,
    SUM(coalesce(siap_pack, 0)) AS siap_pack,
    SUM(coalesce(siap_struk, 0)) AS siap_struk,
    SUM(coalesce(ssai_struk, 0)) AS ssai_struk,
    SUM(coalesce(pb_batal, 0)) AS pb_batal
FROM (
    SELECT
        obi_tgltrans,
        CASE WHEN hdr.obi_tgltrans IS NOT NULL THEN 1 ELSE 0 END AS pb_masuk,
        CASE 
            WHEN hdr.obi_recid IS NULL OR (substring(hdr.obi_recid, 1, 1) = '1' AND obi_sendpick IS NULL) THEN 1 
            ELSE 0 
        END AS siap_send,
        CASE 
            WHEN substring(hdr.obi_recid, 1, 1) = '1' AND obi_sendpick IS NOT NULL THEN 1 
            ELSE 0 
        END AS siap_pick,
        CASE 
            WHEN substring(hdr.obi_recid, 1, 1) = '2' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 1 
            ELSE 0 
        END AS siap_pack,
        CASE 
            WHEN substring(hdr.obi_recid, 1, 1) = '5' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 1 
            ELSE 0 
        END AS siap_struk,
        CASE 
            WHEN substring(hdr.obi_recid, 1, 1) = '6' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL AND obi_tglstruk IS NOT NULL THEN 1 
            ELSE 0 
        END AS ssai_struk,
        CASE 
            WHEN substring(hdr.obi_recid, 1, 1) = 'B' THEN 1 
            ELSE 0 
        END AS pb_batal
    FROM
        tbtr_obi_h hdr
    WHERE
        obi_tgltrans >= CURRENT_DATE - INTERVAL '{$id} days'
) AS subquery
GROUP BY
    obi_tgltrans
ORDER BY
    obi_tgltrans;
";
    $stmt = $this->conn->prepare($query);
    try {
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
    return $result;
}


    public function rekapTgl($id)
{
    $query  = "SELECT
    to_char(obi_tgltrans, 'DD-MON-YYYY') AS obi_tgl,
    COUNT(DISTINCT pb_b) AS pb_b,
    COUNT(DISTINCT item_b) AS item_b,
    COALESCE(SUM(qty_b), 0) AS qty_b,
    COALESCE(SUM(rph_b), 0) AS rph_b,
    COUNT(DISTINCT pb_s) AS pb_s,
    COUNT(DISTINCT item_s) AS item_s,
    COALESCE(SUM(qty_s), 0) AS qty_s,
    COALESCE(SUM(rph_s), 0) AS rph_s,
    COUNT(DISTINCT pb_r) AS pb_r,
    COUNT(DISTINCT item_r) AS item_r,
    COALESCE(SUM(qty_r), 0) AS qty_r,
    COALESCE(SUM(rph_r), 0) AS rph_r
FROM (
    SELECT
        hdr.obi_tgltrans,
        CASE WHEN substr(hdr.obi_recid, 1, 1) = 'B' THEN hdr.obi_nopb END AS pb_b,
        CASE WHEN substr(hdr.obi_recid, 1, 1) = 'B' THEN substr(OBI_PRDCD, 1, 6) || '0' END AS item_b,
        CASE WHEN substr(hdr.obi_recid, 1, 1) = 'B' THEN obi_qtyorder END AS qty_b,
        CASE WHEN substr(hdr.obi_recid, 1, 1) = 'B' THEN obi_hargasatuan * 
        obi_qtyorder END AS rph_b,
        CASE WHEN substr(hdr.obi_recid, 1, 1) <> 'B' THEN hdr.obi_nopb END AS pb_s,
        CASE WHEN substr(hdr.obi_recid, 1, 1) <> 'B' THEN substr(OBI_PRDCD, 1, 6) || '0' END AS item_s,
        CASE WHEN substr(hdr.obi_recid, 1, 1) <> 'B' THEN obi_qtyorder END AS qty_s,
        CASE WHEN substr(hdr.obi_recid, 1, 1) <> 'B' THEN obi_hargasatuan * obi_qtyorder END AS rph_s,
        CASE WHEN substr(coalesce(hdr.obi_recid, '1'), 1, 1) >= '6' AND substr(hdr.obi_recid, 1, 1) <> 'B' THEN hdr.obi_nopb END AS pb_r,
        CASE WHEN substr(coalesce(hdr.obi_recid, '1'), 1, 1) >= '6' AND substr(hdr.obi_recid, 1, 1) <> 'B' THEN substr(obi_prdcd, 1, 6) || '0' END AS item_r,
        CASE WHEN substr(coalesce(hdr.obi_recid, '1'), 1, 1) >= '6' AND substr(hdr.obi_recid, 1, 1) <> 'B' THEN obi_qtyorder END AS qty_r,
        CASE WHEN substr(coalesce(hdr.obi_recid, '1'), 1, 1) >= '6' AND substr(hdr.obi_recid, 1, 1) <> 'B' THEN obi_hargasatuan * obi_qtyorder END AS rph_r
    FROM tbtr_obi_h hdr
    JOIN tbtr_obi_d dtl ON hdr.obi_notrans = dtl.obi_notrans AND hdr.obi_tgltrans = dtl.obi_tgltrans
    WHERE date_trunc('day', hdr.obi_tgltrans) >= current_date - interval '{$id} days'
) AS subquery_alias
GROUP BY obi_tgltrans
";
    
    $stmt = $this->conn->prepare($query);
    try {
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
    return $result;
}


    public function detailTgl($tg, $st)
{
    // Base query with a placeholder for the date
    $query = "SELECT 
    * 
FROM 
    (SELECT
        obi_tgltrans,
        obi_nopb,
        CASE 
            WHEN obi_recid IS NULL OR (substring(obi_recid, 1, 1) = '1' AND obi_sendpick IS NULL) THEN 'Siap Send' 
            WHEN substring(obi_recid, 1, 1) = '1' AND obi_sendpick IS NOT NULL THEN 'Siap Picking' 
            WHEN substring(obi_recid, 1, 1) = '2' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 'Siap Packing' 
            WHEN substring(obi_recid, 1, 1) = '3' THEN 'Siap Draft Struk'
            WHEN substring(obi_recid, 1, 1) = '4' THEN 'Konfirmasi Pembayaran'
            WHEN substring(obi_recid, 1, 1) = '5' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL THEN 'Siap Struk' 
            WHEN substring(obi_recid, 1, 1) = '6' AND obi_sendpick IS NOT NULL AND obi_selesaipick IS NOT NULL AND obi_tglstruk IS NOT NULL THEN 'Selesai Struk' 
            WHEN substring(obi_recid, 1, 1) = '7' THEN 'Set Ongkir'
            WHEN substring(obi_recid, 1, 1) = 'B' THEN 'Batal' 
        END AS STATUS1,
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
        cus_namamember,
        CASE
            WHEN coalesce(obi_attribute2, 'KlikIGR') = 'KlikIGR' THEN
                CASE
                    WHEN coalesce(cus_jenismember, 'N') = 'T' THEN 'TMI'
                    WHEN coalesce(cus_flagmemberkhusus, 'N') = 'Y' THEN 'Member Merah'
                    ELSE 'Member Umum'
                END
            WHEN coalesce(obi_attribute2, 'KlikIGR') = 'Corp' THEN 'Corporate'
            WHEN coalesce(obi_attribute2, 'KlikIGR') = 'TMI' THEN 'TMI'
            ELSE 'Member Merah'
        END AS tipe_member,
        coalesce(obi_alasanbtl, ' ') AS alasan_batal,
        obi_itemorder AS item_order,
        obi_realitem AS item_real,
        (OBI_TTLORDER + OBI_TTLPPN - COALESCE(SUM(CASHBACK_ORDER), 0)) AS total_order,
        (OBI_REALORDER + OBI_REALPPN - COALESCE(SUM(CASHBACK_REAL), 0)) AS total_real,
        CASE 
            WHEN coalesce(obi_tipebayar, 'TRF') = 'COD' THEN 'COD' 
            WHEN coalesce(obi_tipebayar, 'TRF') = 'COD-VA' THEN 'COD-VA' 
            WHEN upper(coalesce(obi_tipebayar, 'X')) = 'TOP' THEN 'Kredit' 
            ELSE 
                CASE 
                    WHEN coalesce(cus_flagkredit, '-') = 'Y' AND coalesce(obi_flagbayar, 'N') <> 'Y' THEN 'Kredit' 
                    ELSE 'Tunai' 
                END 
        END AS TIPE_BAYAR,
        CASE 
            WHEN obi_shippingservice = 'S' THEN 'Sameday' 
            WHEN obi_shippingservice = 'N' THEN 'Nextday' 
            ELSE '' 
        END AS service,
        to_char(obi_maxdeliverytime, 'DD-MM-YYYY HH24:MI:SS') AS MAX_SERAH_TERIMA,
        CASE 
            WHEN obi_shippingservice = 'S' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') < '12:00:00' THEN to_char(obi_mindeliverytime, 'DD-MM-YYYY') || ' 13:30:00' 
            WHEN obi_shippingservice = 'N' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') < '12:00:00' THEN to_char(obi_mindeliverytime, 'DD-MM-YYYY') || ' 17:30:00' 
            WHEN obi_shippingservice = 'S' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') > '12:00:00' THEN to_char(obi_mindeliverytime + interval '1 day', 'DD-MM-YYYY') || ' 13:30:00' 
            WHEN obi_shippingservice = 'N' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') > '12:00:00' THEN to_char(obi_mindeliverytime + interval '1 day', 'DD-MM-YYYY') || ' 17:30:00' 
        END AS max_dsp,
        CASE 
            WHEN obi_shippingservice = 'S' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') < '12:00:00' THEN to_char(obi_mindeliverytime, 'DD-MM-YYYY') || ' 14:00:00' 
            WHEN obi_shippingservice = 'N' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') < '12:00:00' THEN to_char(obi_mindeliverytime, 'DD-MM-YYYY') || ' 18:00:00' 
            WHEN obi_shippingservice = 'S' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') > '12:00:00' THEN to_char(obi_mindeliverytime + interval '1 day', 'DD-MM-YYYY') || ' 14:00:00' 
            WHEN obi_shippingservice = 'N' AND to_char(obi_mindeliverytime, 'HH24:MI:SS') > '12:00:00' THEN to_char(obi_mindeliverytime + interval '1 day', 'DD-MM-YYYY') || ' 18:00:00' 
        END AS max_ser,
        to_char(obi_mindeliverytime, 'DD-MM-YYYY') AS tglpb,
        to_char(obi_mindeliverytime, 'HH24:MI:SS') AS jampb,
        CASE 
            WHEN coalesce(obi_freeongkir, 'N') = 'Y' THEN 'Free Ongkir' 
            WHEN coalesce(obi_freeongkir, 'N') = 'N' THEN 'Ongkir' 
            ELSE 'Ambil Di Toko'  
        END AS ongkir
    FROM
        tbtr_obi_h
        LEFT JOIN tbmaster_customer ON obi_kdmember = cus_kodemember
        LEFT JOIN PROMO_KLIKIGR PKI ON PKI.NO_PB = OBI_NOPB
    WHERE
        to_char(obi_tgltrans, 'DD-MON-YYYY') = :tg
	GROUP BY
        obi_tgltrans, 
        obi_nopb, 
        obi_recid, 
        obi_sendpick, 
        obi_selesaipick, 
        obi_tglstruk, 
        obi_kdmember, 
        cus_namamember, 
        obi_attribute2, 
        cus_jenismember, 
        cus_flagmemberkhusus, 
        obi_alasanbtl, 
        obi_itemorder, 
        obi_realitem, 
        OBI_TTLORDER, 
        OBI_TTLPPN, 
        OBI_REALORDER, 
        OBI_REALPPN, 
        obi_tipebayar, 
        cus_flagkredit, 
        obi_flagbayar, 
        obi_shippingservice, 
        obi_maxdeliverytime, 
        obi_mindeliverytime, 
        obi_freeongkir
) subquery
";
    
    // Append status condition if needed
    if ($st != "PB MASUK") {
        $query .= " WHERE STATUS1 = :st";
    }

    $query .= " ORDER BY 1 DESC";

    $stmt = $this->conn->prepare($query);
    try {
        // Bind parameters
        $stmt->bindParam(':tg', $tg);
        if ($st != "PB MASUK") {
            $stmt->bindParam(':st', $st);
        }
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
    return $result;
}


    public function notifdspb($id)
{
    $query = "SELECT
                to_char(obi_tgltrans, 'DD-MON-YYYY') AS obi_tgl,
                COUNT(*) AS belum
            FROM
                tbtr_obi_h hdr
            WHERE
                obi_tgltrans >= current_date - interval '3 day'
                AND obi_tgltrans <= current_date - interval '{$id} days'
                AND (substr(coalesce(hdr.obi_recid, '1'), 1, 1) < '6'
                     OR hdr.obi_recid IS NULL
                     OR substr(hdr.obi_recid, 1, 1) <> 'B')
            GROUP BY
                obi_tgltrans
            ORDER BY
                obi_tgltrans";

    $stmt = $this->conn->prepare($query);
    try {
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
    return $result;
}

}
