<?php
  // ketergantungan : tidak ada

  $viewDetailStruk = 
    "((select 
  dtl_rtype,
  dtl_tanggal,
  dtl_jam,
  dtl_struk,
  dtl_stat,
  dtl_kasir,
  dtl_no_struk,
  dtl_seqno,
  dtl_prdcd_ctn,
  dtl_prdcd,
  dtl_nama_barang,
  dtl_unit,
  dtl_frac,
  dtl_tag,
  dtl_bkp,
  dtl_qty_pcs,
  dtl_qty,
  dtl_harga_jual,
  dtl_diskon,
  case
    when dtl_rtype='S' then dtl_gross
    else dtl_gross * -1
  end as dtl_gross,
  case
    when dtl_rtype='R' then (dtl_netto * -1)
    else dtl_netto
  end as dtl_netto,
  case
    when dtl_rtype='R' then (dtl_hpp * -1)
    else dtl_hpp
  end as dtl_hpp,
  case
    when dtl_rtype='S' then dtl_netto - dtl_hpp
    else (dtl_netto - dtl_hpp) * -1
  end as dtl_margin,
  dtl_k_div,
  dtl_nama_div,
  dtl_k_dept,
  dtl_nama_dept,
  dtl_k_katb,
  dtl_nama_katb,
  dtl_cusno,
  dtl_namamember,
  dtl_memberkhusus,
  dtl_outlet,
  dtl_suboutlet,
  dtl_kategori,
  dtl_sub_kategori,
  dtl_tipemember,
  dtl_group_member,hgb_kodesupplier as dtl_kodesupplier,
    sup_namasupplier as dtl_namasupplier
from (
  select 
    date_trunc('day', trjd_transactiondate) as dtl_tanggal,
    to_char(trjd_transactiondate, 'hh24:mi:ss') as dtl_jam,
    to_char(trjd_transactiondate, 'yyyymmdd') || trjd_create_by || trjd_transactionno || trjd_transactiontype as dtl_struk,
    trjd_cashierstation as dtl_stat,
    trjd_create_by as dtl_kasir,
    trjd_transactionno as dtl_no_struk,
    substr(trjd_prdcd, 1, 6) || '0' as dtl_prdcd_ctn,
    trjd_prdcd as dtl_prdcd,
    prd_deskripsipanjang as dtl_nama_barang,
    prd_unit as dtl_unit,
    prd_frac as dtl_frac,
    coalesce(prd_kodetag, ' ') as dtl_tag,
    trjd_flagtax1 as dtl_bkp,
    trjd_transactiontype as dtl_rtype,
    trim(trjd_divisioncode) as dtl_k_div,
    div_namadivisi as dtl_nama_div,
    substr(trjd_division, 1, 2) as dtl_k_dept,
    dep_namadepartement as dtl_nama_dept,
    substr(trjd_division, 3, 2) as dtl_k_katb,
    kat_namakategori as dtl_nama_katb,
    trjd_cus_kodemember as dtl_cusno,
    cus_namamember as dtl_namamember,
    cus_flagmemberkhusus as dtl_memberkhusus,
    cus_kodeoutlet as dtl_outlet,
    upper(cus_kodesuboutlet) as dtl_suboutlet,
    crm_kategori as dtl_kategori,
    crm_subkategori as dtl_sub_kategori,
    trjd_quantity as dtl_qty,
    trjd_unitprice as dtl_harga_jual,
    trjd_discount as dtl_diskon,
    trjd_seqno as dtl_seqno,
    case
      when cus_jenismember = 'T' then 'TMI'
      when cus_flagmemberkhusus = 'Y' then 'KHUSUS'
      when trjd_create_by in ('IDM', 'ID1', 'ID2') then 'IDM'
      when trjd_create_by in ('OMI', 'BKL') then 'OMI'
      else 'REGULER'
    end as dtl_tipemember,
    case
      when cus_flagmemberkhusus = 'Y' then 'GROUP_1_KHUSUS'
      when trjd_create_by = 'IDM' then 'GROUP_2_IDM'
      when trjd_create_by in ('OMI', 'BKL') then 'GROUP_3_OMI'
      when cus_flagmemberkhusus is null and cus_kodeoutlet = '6' then 'GROUP_4_END_USER'
      else 'GROUP_5_OTHERS'
    end as dtl_group_member,
    case
      when prd_unit = 'KG' and prd_frac = 1000 then trjd_quantity
      else trjd_quantity * prd_frac
    end as dtl_qty_pcs,
    case
      when trjd_flagtax1 = 'Y' and trjd_create_by in ('IDM', 'OMI', 'BKL') then trjd_nominalamt * 11.1 / 10
      else trjd_nominalamt
    end as dtl_gross,
    case
      when trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' then
        case
          when 'Y' = 'Y' then trjd_nominalamt
        end
      else
        case
          when coalesce(tko_kodesbu, 'z') in ('O', 'I') then
            case
              when tko_tipeomi in ('HE', 'HG') then
                trjd_nominalamt - (
                  case
                    when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') and coalesce(prd_kodetag, 'zz') <> 'Q' then
                      (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100))))
                    else 0
                  end
                )
              else trjd_nominalamt
            end
          else
            trjd_nominalamt - (
              case
                when substr(trjd_create_by, 1, 2) = 'EX' then 0
                else
                  case
                    when trjd_flagtax1 = 'Y' and coalesce(trjd_flagtax2, 'z') in ('Y', 'z') and coalesce(prd_kodetag, 'zz') <> 'Q' then
                      (trjd_nominalamt - (trjd_nominalamt / (1 + (coalesce(prd_ppn, 10) / 100))))
                    else 0
                  end
              end
            )
        end
    end as dtl_netto,
    case
      when trjd_divisioncode = '5' and substr(trjd_division, 1, 2) = '39' then
        case
          when 'Y' = 'Y' then
            trjd_nominalamt - (
              case
                when prd_markupstandard is null then (5 * trjd_nominalamt) / 100
                else (prd_markupstandard * trjd_nominalamt) / 100
              end
            )
        end
      else
        (trjd_quantity / case when prd_unit = 'KG' then 1000 else 1 end) * trjd_baseprice
    end as dtl_hpp
  from 
    tbtr_jualdetail
    left join tbmaster_prodmast on trjd_prdcd = prd_prdcd
    left join tbmaster_tokoigr on trjd_cus_kodemember = tko_kodecustomer
    left join tbmaster_customer on trjd_cus_kodemember = cus_kodemember
    left join tbmaster_customercrm on trjd_cus_kodemember = crm_kodemember
    left join tbmaster_divisi on trjd_division = div_kodedivisi
    left join tbmaster_departement on substr(trjd_division, 1, 2) = dep_kodedepartement
    left join tbmaster_kategori  on trjd_division = kat_kodedepartement || kat_kodekategori)sls left join
	(select m.hgb_prdcd hgb_prdcd,
       m.hgb_kodesupplier,
       s.sup_namasupplier
from tbmaster_hargabeli m
left join tbmaster_supplier s
on m.hgb_kodesupplier = s.sup_kodesupplier
where m.hgb_tipe = '2'
and m.hgb_recordid is null)gb on dtl_prdcd_ctn=hgb_prdcd)


) detailstruk";
?>