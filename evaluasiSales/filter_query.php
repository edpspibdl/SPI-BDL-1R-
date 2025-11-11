<?php
	

	// filter : kode member
	if ($kodeMember != "All" AND $kodeMember != "") {
		$query .= " AND dtl_cusno = '" . $kodeMember ."'";
	}
	
	
	// filter : kode kode plu
	if ($kodePLU != "All" AND $kodePLU != "") {

		$kodePLU = substr('00000000' . $kodePLU, -7);

		$query .= " AND dtl_prdcd_ctn = '" . $kodePLU ."'";
	}
	
	
	// filter : kode supplier
	if ($kodeSupplier != "All" AND $kodeSupplier != "") {
		$query .= " AND dtl_prdcd_ctn in (select hgb_prdcd from tbmaster_hargabeli where hgb_tipe='2' and hgb_kodesupplier = '" . strtoupper($kodeSupplier) ."')";
	}

	// filter : nama supplier
	if ($namaSupplier != "All" AND $namaSupplier != "") {
		$query .= " AND dtl_prdcd_ctn in (select hgb_prdcd from tbmaster_hargabeli where hgb_tipe='2' and hgb_kodesupplier in
					(select distinct sup_kodesupplier from tbmaster_supplier where sup_namasupplier like '%" . strtoupper($namaSupplier) ."%'))";
	}
	
	
	// filter : kode divisi
	if ($kodeDivisi != "All" AND $kodeDivisi != "") {
		$query .= " AND dtl_k_div = '" . $kodeDivisi ."' ";
	}

?>
