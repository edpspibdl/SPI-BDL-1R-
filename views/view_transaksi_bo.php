<?php
	//ketergantungan :

	$viewTransaksiBO =
  	"( SELECT
    m.mstd_typetrn                         AS trn_type,
    m.mstd_tgldoc                          AS trn_tgldoc,
    m.mstd_nodoc                           AS trn_nodoc,
    m.mstd_nopo                            AS trn_nopo,
    m.mstd_tglpo                           AS trn_tglpo,
    m.mstd_seqno                           AS trn_seqno,
    p.prd_kodedivisi                       AS trn_div,
    p.prd_kodedepartement                  AS trn_dept,
    p.prd_kodekategoribarang               AS trn_katb,
    m.mstd_prdcd                           AS trn_prdcd,
    p.prd_deskripsipanjang                 AS trn_nama_barang,
    m.mstd_unit                            AS trn_unit,
    m.mstd_frac                            AS trn_frac,
    coalesce(p.prd_kodetag, ' ')           AS trn_tag,
    m.mstd_qty                             AS trn_qty,
    coalesce(m.mstd_qtybonus1, 0)          AS trn_qty_bonus1,
    coalesce(m.mstd_qtybonus2, 0)          AS trn_qty_bonus2,
    m.mstd_hrgsatuan                       AS trn_harga_satuan,
    m.mstd_gross                           AS trn_gross,
    coalesce(m.mstd_discrph, 0)            AS trn_discount,
    coalesce(m.mstd_ppnrph, 0)             AS trn_ppn,
    coalesce(m.mstd_flagdisc1, ' ')        AS trn_flag1,
    coalesce(m.mstd_flagdisc2, ' ')        AS trn_flag2,
    coalesce(m.mstd_dis4rr, 0)             AS trn_dis4rr,
    coalesce(m.mstd_dis4jr, 0)             AS trn_dis4jr,
    coalesce(m.mstd_dis4cr, 0)             AS trn_dis4cr,
    coalesce(m.mstd_ppnbtlrph, 0)          AS trn_ppnbtlrph,
    coalesce(m.mstd_ppnbmrph, 0)           AS trn_ppnbmrph,
    m.mstd_kodesupplier                    AS trn_kode_supplier,
    s.sup_namasupplier                     AS trn_nama_supplier,
    m.mstd_noref3                          refrensi,M.MSTD_KETERANGAN keterangan
FROM
    tbtr_mstran_d      m
    LEFT JOIN tbmaster_prodmast  p ON m.mstd_prdcd = p.prd_prdcd
    LEFT JOIN tbmaster_supplier  s ON m.mstd_kodesupplier = s.sup_kodesupplier
WHERE
    m.mstd_recordid IS NULL
)bo";
?>