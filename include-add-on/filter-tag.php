<?php
	
	if (isset($tanggalMulai)) {
		echo '<p> Periode: ';
	    echo '<span class="badge">' . tanggalIND($tanggalMulai) . '</span>';
	    echo ' s.d. ';
	    echo '<span class="badge">' . tanggalIND($tanggalSelesai) . '</span>';
    }
    


    // tag : kode toko OMI
    if (isset($kodeTokoOMI)) {
		if ($kodeTokoOMI != "All" AND $kodeTokoOMI != "") {
			echo ' <span class="badge">Kode OMI: ' . $kodeTokoOMI . '</span>';
		}
	}

	// tag : kode monitoring plu
	if (isset($kodeMonitoringPLU)) {
		if ($kodeMonitoringPLU != "All" AND $kodeMonitoringPLU != "") {
			echo ' <span class="badge"> Monitoring PLU: ' . $kodeMonitoringPLU . '</span>';
		}
	}

		// tag : nama barang
	if (isset($namaBarang)) {
		if ($namaBarang != "All" AND $namaBarang != "") {
			echo ' <span class="badge">Nama Barang: ' . $namaBarang . '</span>';
		}
	}
		
		// tag : kode kode plu
	if (isset($kodePLU)) {
		if ($kodePLU != "All" AND $kodePLU != "") {
			echo ' <span class="badge">PLU: ' . $kodePLU . '</span>';
		}
	}

		// tag : kode barcode
	if (isset($kodeBarcode)) {
		if ($kodeBarcode != "All" AND $kodeBarcode != "") {
			echo ' <span class="badge">Barcode: ' . $kodeBarcode . '</span>';
		}
	}


		// tag : kode divisi
	if (isset($kodeDivisi)) {
		if ($kodeDivisi != "All" AND $kodeDivisi != "") {
			echo ' <span class="badge">Divisi: ' . $kodeDivisi . '</span>';
		}
	}

		// tag : kode departemen
	if (isset($kodeDepartemen)) {
		if ($kodeDepartemen != "All" AND $kodeDepartemen != "") {
			echo ' <span class="badge">Departemen: ' . $kodeDepartemen . '</span>';
		}
	}

	if (isset($kodeKategoriBarang)) {
		if ($kodeKategoriBarang != "All" AND $kodeKategoriBarang != "") {
			echo ' <span class="badge">Kategori: ' . $kodeKategoriBarang . '</span>';
		}
	}


		// tag : kode tag
	if (isset($kodeTag)) {
		if ($kodeTag != "All" AND $kodeTag != "") {
			echo ' <span class="badge">Kode Tag: ' . $kodeTag . '</span>';
		}
	}

		// tag : kode supplier
	if (isset($kodeSupplier)) {
		if ($kodeSupplier != "All" AND $kodeSupplier != "") {
			echo ' <span class="badge">Kode Supplier: ' . $kodeSupplier . '</span>';
		}
	}

		// tag : nama supplier
	if (isset($namaSupplier)) {
		if ($namaSupplier != "All" AND $namaSupplier != "") {
			echo ' <span class="badge">Nama Supplier: ' . $namaSupplier . '</span>';
		}
	}

		// tag : kode monitoring supplier
	if (isset($kodeMonitoringSupplier)) {
		if ($kodeMonitoringSupplier != "All" AND $kodeMonitoringSupplier != "") {
			echo ' <span class="badge">Monitoring Supplier: ' . $kodeMonitoringSupplier . '</span>';
		}
	}
		
		// tag : kode loyalty promo
	if (isset($kodeLoyalty)) {
		if ($kodeLoyalty != "All" AND $kodeLoyalty != "") {
			echo ' <span class="badge">Loyalty: ' . $kodeLoyalty . '</span>';
		}
	}

		
	if (isset($kodeUniqueCB)) {
		if ($kodeUniqueCB != "All" AND $kodeUniqueCB != "") {
			echo ' <span class="badge">Unique Code: ' . $kodeUniqueCB . '</span>';
		}
	}

	if (isset($namaMember)) {
		if ($namaMember != "All" AND $namaMember != "") {
			echo ' <span class="badge">Kode Member: ' . $namaMember . '</span>';
		}
	}

	if (isset($kodeMember)) {
		if ($kodeMember != "All" AND $kodeMember != "") {
			echo ' <span class="badge">Kode Member: ' . $kodeMember . '</span>';
		}
	}

	if (isset($jenisMember)) {
		if ($jenisMember != "All" AND $jenisMember != "") {
			echo ' <span class="badge">Jenis Member: ' . $jenisMember . '</span>';
		}
	}

	if (isset($kodeOutlet)) {
		if ($kodeOutlet != "All" AND $kodeOutlet != "") {
			echo ' <span class="badge">Outlet: ' . $kodeOutlet . '</span>';
		}
	}

	if (isset($kodeSubOutlet)) {
		if ($kodeSubOutlet != "All" AND $kodeSubOutlet != "") {
			echo ' <span class="badge">Sub Outlet: ' . $kodeSubOutlet . '</span>';
		}
	}

	echo '</p>';
?>