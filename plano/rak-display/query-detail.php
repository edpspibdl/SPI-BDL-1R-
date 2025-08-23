<?php
	
	// include '../views/view_spb.php';
	$query = "   SELECT l.lks_koderak,
       l.lks_kodesubrak,
       l.lks_tiperak,
       l.lks_shelvingrak,
       l.lks_nourut,
       l.lks_prdcd,
       p.prd_deskripsipanjang  AS lks_nama_barang,
       p.prd_unit              AS lks_unit,
       p.prd_frac              AS lks_frac,
       Nvl(p.prd_kodetag, ' ') AS lks_tag,
       l.lks_tirkirikanan,
       l.lks_tirdepanbelakang,
       l.lks_tiratasbawah,
       l.lks_maxdisplay,
       l.lks_qty,
       l.lks_expdate,
       l.lks_minpct,
       l.lks_maxplano
FROM   tbmaster_lokasi l,
       tbmaster_prodmast p
WHERE  l.lks_prdcd = p.prd_prdcd (+)
       AND Substr(l.lks_tiperak, 1, 1) IN ( 'B', 'I', 'N' )
       AND Substr(l.lks_koderak, 1, 1) NOT IN ( 'D', 'G' )
       AND l.lks_koderak = '{$kodeRak}'
       AND l.lks_kodesubrak = '{$kodeSubRak}'
ORDER  BY lks_koderak,
          lks_kodesubrak,
          lks_tiperak,
          lks_shelvingrak,
          lks_nourut,
          lks_prdcd ";


?>