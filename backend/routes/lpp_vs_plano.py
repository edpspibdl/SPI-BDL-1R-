from flask import Blueprint, jsonify
import psycopg2
from db import get_conn   # ambil koneksi dari db.py

lpp_bp = Blueprint("lpp_bp", __name__)

@lpp_bp.route("/lpp-vs-plano", methods=["GET"])
@lpp_bp.route("/lpp-vs-plano/", methods=["GET"])   # biar support pakai / atau tanpa /
def get_lpp():
    conn = get_conn()
    cur = conn.cursor()

    query = """SELECT LPP.* FROM
          (
          SELECT
            PRD_KODEDIVISI    AS DIV,
            PRD_KODEDEPARTEMENT    AS DEPT,
            PRD_KODEKATEGORIBARANG AS KATB,
            DISPLAY_OMI ,
            cast (ST_PRDCD as numeric )            AS PLU,
            PRD_DESKRIPSIPANJANG AS DESKRIPSI,
            PRD_UNIT             AS UNIT,
            PRD_FRAC             AS FRAC,
            prd_kodetag          AS TAG,
            CASE
              WHEN prd_flagigr='Y'      AND prd_flagomi ='Y'   AND prd_flagidm ='Y'
              THEN 'IGR+OMI+IDM'
              WHEN prd_flagigr='Y'      AND prd_flagomi ='Y'   AND prd_flagidm ='N'
              THEN 'IGR+OMI'
              WHEN prd_flagigr='Y'      AND prd_flagidm ='Y' AND prd_flagomi ='N'
              THEN 'IGR+IDM'
              WHEN prd_flagigr='Y'       AND prd_flagomi ='N'  AND prd_flagidm ='N'
              THEN 'IGR ONLY'
              WHEN prd_flagigr='N'      AND prd_flagomi ='Y'   AND prd_flagidm ='N'
              THEN 'OMI ONLY'
              WHEN prd_flagigr='N'      AND prd_flagidm ='Y' AND prd_flagomi ='N'
              THEN 'IDM ONLY'
            ELSE 'XXX'
            END flag ,
            TO_CHAR(ROUND(ST_AVGCOST), 'FM999G999G999G999') AS ACOST_PCS,
            ST_SALDOAKHIR AS LPP_QTY,
            ST_SALDOAKHIR AS LPP_QTY,
CASE
  WHEN PRD_UNIT = 'KG' THEN 
    TO_CHAR(ROUND(CAST((ST_SALDOAKHIR * ST_AVGCOST) / PRD_FRAC AS numeric), 2), 'FM999G999G999G999D00')
  ELSE 
    TO_CHAR(ROUND(CAST(ST_SALDOAKHIR * ST_AVGCOST AS numeric), 2), 'FM999G999G999G999D00')
END AS LPP_RPH
,
            COALESCE(PQTY,0)      AS PLANO_QTY,
          --  COALESCE(OBI_QTYREALISASI,0) AS TMI_QTY,
            CASE WHEN KLIKREAL IS NOT NULL THEN KLIKREAL ELSE 0 END AS PICKING_KLIK,
            CASE WHEN PICKING_OMI IS NOT NULL THEN PICKING_OMI ELSE 0 END AS PICKING_OMI,
            COALESCE(Omi_recid4,0)AS Omi_recid4,
            COALESCE(qty_rom,0)   AS qty_rom ,
            COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)   AS total_plano,
            TO_CHAR((COALESCE(PQTY, 0) + COALESCE(OMI_RECID4, 0)) * ST_AVGCOST, 'FM999G999G999G999D00') AS RP_PLANO,
            (COALESCE(PQTY,0)+COALESCE(OMI_RECID4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0))- COALESCE(ST_SALDOAKHIR,0) AS SEL_QTY,
          (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0))- COALESCE(st_saldoakhir,0))*ST_AVGCOST)  AS sel_rph  ,
            CASE WHEN  (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0)))*ST_AVGCOST)> (  CASE
              WHEN PRD_UNIT='KG'
              THEN (ST_SALDOAKHIR*ST_AVGCOST)/PRD_FRAC
              ELSE ST_SALDOAKHIR *ST_AVGCOST
            END) then 'PLANO>LPP'
            when  (((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0)+COALESCE(KLIKREAL,0)+COALESCE(TMI_REAL,0)+COALESCE(PICKING_OMI,0)))*ST_AVGCOST)= (  CASE
              WHEN PRD_UNIT='KG'
              THEN (ST_SALDOAKHIR*ST_AVGCOST)/PRD_FRAC
              ELSE ST_SALDOAKHIR *ST_AVGCOST
            END) then 'LPP=PLANO'
            else 'LPP>PLANO' end as Kategori,
            CASE when (DISPLAY_TOKO is null and DISPLAY_OMI is null) then 'TDK ADA PLANO'
            else 'ADA PLANO' end as Keterangan,
            CURRENT_DATE as lvp_create_dt,'SYS' as lvp_create_by,NULL as lvp_modify_dt,NULL as lvp_modify_by,
            CASE WHEN TMI_REAL IS NOT NULL THEN TMI_REAL ELSE 0 END AS PICKING_TMI
          FROM TBMASTER_PRODMAST
          LEFT JOIN (
            SELECT DISTINCT PRC_PLUIGR
            FROM TBMASTER_PRODCRM
          ) AS ProdCRM
          ON ProdCRM.PRC_PLUIGR=PRD_PRDCD
          LEFT JOIN TBMASTER_STOCK
          ON ST_PRDCD = PRD_PRDCD
          LEFT JOIN (
          select
            lks_prdcd as PLU_DISPLAYTOKO,
            lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut as DISPLAY_TOKO
          from tbmaster_lokasi
          where substr(lks_koderak,1,1) IN ('O','R') and substr(lks_tiperak,1,1) <>'S'
          ) AS DisplayToko ON DisplayToko.PLU_DISPLAYTOKO=PRD_PRDCD
          LEFT JOIN (
          select
            lks_prdcd as PLU_DISPLAYOMI,
            lks_koderak||'.'||lks_kodesubrak||'.'||lks_tiperak||'.'||lks_shelvingrak||'.'||lks_nourut as DISPLAY_OMI
          from tbmaster_lokasi
          where substr(lks_koderak,1,1) IN ('D') and substr(lks_tiperak,1,1) <>'S'
          ) AS DisplayOMI ON DisplayOMI.PLU_DISPLAYOMI=PRD_PRDCD
          LEFT JOIN
          (SELECT LKS_PRDCD, SUM(LKS_QTY) AS PQTY FROM TBMASTER_LOKASI GROUP BY LKS_PRDCD) AS LOKASI ON PRD_PRDCD=LKS_PRDCD
          LEFT JOIN
          (SELECT SUBSTR(pbo_pluigr,1,6)
            ||'0'                AS PLUPB,
            SUM(pbo_qtyrealisasi)AS Omi_recid4
          FROM tbmaster_pbomi
          LEFT JOIN tbtr_omikoli
          ON okl_nokoli                         =pbo_nokoli
          WHERE TO_CHAR(pbo_create_dt,'DD/MM/YYYY') = TO_CHAR(CURRENT_DATE,'DD/MM/YYYY')
          AND pbo_recordid                      ='4'
          AND okl_nokoli                       IS NULL
          GROUP BY SUBSTR(pbo_pluigr,1,6)
            ||'0'
          ) as omikoli ON PLUPB=PRD_PRDCD
          LEFT JOIN
            (SELECT rom_prdcd AS plu_rom,
              SUM(rom_qty)    AS qty_rom
            FROM tbtr_returomi
            WHERE DATE_TRUNC('day',rom_tgldokumen) >= DATE_TRUNC('day',CURRENT_DATE - 1)
            AND rom_qty <> 0
            GROUP BY rom_prdcd
            ) AS ReturOMI
          ON prd_prdcd=plu_rom
          --INTRANSIT KLIK
          LEFT JOIN
          (select substr(obi_prdcd,1,6)||'0' as PLUKLIK,sum(obi_qtyorder) as KLIKORDER,sum(obi_qtyrealisasi) as KLIKREAL
          from tbtr_obi_d
          LEFT JOIN TBTR_OBI_H ON TBTR_OBI_H.OBI_NOTRANS=TBTR_OBI_D.OBI_NOTRANS  AND TBTR_OBI_H.OBI_TGLTRANS=TBTR_OBI_D.OBI_TGLTRANS
          where tbtr_obi_h.obi_recid in ('1','2','3') and obi_qtyrealisasi>0
          and DATE_TRUNC('day',tbtr_obi_h.obi_tgltrans)>=DATE_TRUNC('day',CURRENT_DATE - 31) AND OBI_NOPB NOT LIKE '%TMI%'
          GROUP BY substr(obi_prdcd,1,6)||'0'
          ) AS Klik
          ON PLUKLIK=PRD_PRDCD
          -------PICKING TMI, ITEM TIDAK MASUK INTRANSIT TAPI SUDAH CHEKER DAN BELUM STRUK RECID YANG KAMI AMBIL (3,4,5)-----------------
          LEFT JOIN
          (select substr(obi_prdcd,1,6)||'0' as PLUTMI,sum(obi_qtyorder) as TMI_ORDER,sum(obi_qtyrealisasi) as TMI_REAL
          from tbtr_obi_d
          left join tbtr_obi_h on tbtr_obi_h.obi_notrans=tbtr_obi_d.obi_notrans  and tbtr_obi_h.obi_tgltrans=tbtr_obi_d.obi_tgltrans
          where tbtr_obi_h.obi_recid in ('3','4','5','7') and obi_qtyrealisasi>0
          and DATE_TRUNC('day',tbtr_obi_h.obi_tgltrans)>=DATE_TRUNC('day',CURRENT_DATE - 31)  AND OBI_NOPB LIKE '%TMI%'
          GROUP BY substr(obi_prdcd,1,6)||'0'
          ) AS TMI
          ON PLUTMI=PRD_PRDCD
          --INTRANSIT OMI
          LEFT JOIN
          (
          select substr(pbo_pluigr,1,6)||'0' as PLUOMI,sum(pbo_qtyrealisasi) as PICKING_OMI
          from tbmaster_pbomi
          left join tbtr_realpb on pbo_pluigr=rpb_plu2 and pbo_kodeomi=rpb_kodeomi and pbo_nopb=rpb_nodokumen and pbo_nokoli=rpb_nokoli
          where pbo_recordid in ('4') and rpb_nokoli is null and DATE_TRUNC('day',pbo_create_dt)>=DATE_TRUNC('day',CURRENT_DATE - 31)
          group by substr(pbo_pluigr,1,6)||'0'
          ) AS PickingOMI
          ON PLUOMI = PRD_PRDCD
          WHERE ST_LOKASI              ='01'
          AND (PRD_KODETAG NOT         IN ('X','N','O') or PRD_KODETAG is null)
          AND PRD_KODEDEPARTEMENT NOT IN ('31','32','42')
          AND PRD_UNIT NOT IN ('KG')
          AND ST_AVGCOST<>'0'
          AND prd_prdcd IN (SELECT DISTINCT st_prdcd FROM TBMASTER_STOCK
          LEFT JOIN tbmaster_lokasi ON TBMASTER_STOCK.ST_PRDCD=tbmaster_lokasi.LKS_PRDCD AND st_lokasi='01' AND ST_SALDOAKHIR<>'0')
          ORDER BY ((COALESCE(PQTY,0)+COALESCE(Omi_recid4,0))-ST_SALDOAKHIR)*ST_AVGCOST DESC
          ) AS LPP
    """  # coba query kecil dulu

    cur.execute(query)
    rows = cur.fetchall()

    # ambil nama kolom supaya JSON lebih rapi
    colnames = [desc[0].lower() for desc in cur.description]
    data = [dict(zip(colnames, row)) for row in rows]

    cur.close()
    conn.close()

    return jsonify(data)
