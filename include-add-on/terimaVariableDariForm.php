<?php
  	// variable untuk menghitung total nilai
    $marginPersen      = 0;
    $hargaNetto        = 0;

    $totalQtyInPcs     = 0;
    $totalKunjungan    = 0;
    $totalMember       = 0;
    $totalStruk        = 0;
    $totalProduk       = 0;
    $totalPlu          = 0;
    $totalGross        = 0;
    $totalNetto        = 0;
    $totalMargin       = 0;
  
    // atur nilai default tanggal

    $tanggalHariIni       = date("Ymd");  
    $tglMulai         = date("Ymd");
    $tglSelesai       = date("Ymd");

    $tanggalPromosi       = date("Ymd");

    $tglMulaiPromosi         = date("Ymd");
    $tglSelesaiPromosi       = date("Ymd");

    $tglMulaiSebelumPromosi         = date("Ymd");
    $tglSelesaiSebelumPromosi       = date("Ymd");

    $tglMulaiSetelahPromosi         = date("Ymd");
    $tglSelesaiSetelahPromosi       = date("Ymd");

  
  	// atur nilai default
  	$lokasiStock = "01";
  	$groupSales	 = "All";
  	$statusTag   = "All";
  	$statusQty   = "All";

	  // barang
	  
  	$namaBarang           = "All";
  	$kodePLU              = "All";
  	$kodeBarcode          = "All";
    $kodeMonitoringPLU    = "All";
    $kodeTag              = "All";

  	// div-dept-katb
  	$kodeDivisi           = "All"; 
  	$kodeDepartemen       = "All"; 
  	$kodeKategoriBarang   = "All"; //belum

  	
    //supplier
  	$kodeSupplier         = "All";
  	$namaSupplier         = "All"; 
  	$kodeMonitoringSupplier = "All"; 

    // member
    $kodeMember           = "All";
    $namaMember           = "All";
    $kodeMonitoringMember = "All"; 
    $jenisMember          = "All";
    $kodeOutlet           = "All"; 
    $kodeSubOutlet        = "All";

    // toko omi
    $kodeTokoOMI          = "All";
  	
    $exportExcel          = "Off";
  	$jenisLaporan   	    = "All"; 
  	$sortBy          	    = "All";

    $itemOMI              = "Off";
    $discount2            = "Off"; 
    $promoMD              = "Off";
    $marginNegatif        = "Off";
    $hargaJualNol         = "Off";
    $promoMahal           = "Off";
    $poOutstanding        = "Off";
    $stockKosong          = "Off";

    $tanggalPromosi       = "All";
    $jenisMarginNegatif   = "All";
    
    


  
	// atur nilai sesuai dengan request dari form

  //tanggal
  if(isset($_GET['tglMulai'])) {if ($_GET['tglMulai'] !=""){$tglMulai = $_GET['tglMulai']; }}
  if(isset($_GET['tglSelesai'])) {if ($_GET['tglSelesai'] !=""){$tglSelesai = $_GET['tglSelesai']; }}
  //tanggal promosi
  if(isset($_GET['tanggalPromosi'])) {if ($_GET['tanggalPromosi'] !=""){$tanggalPromosi = $_GET['tanggalPromosi']; }}
  if(isset($_GET['tglMulaiPromosi'])) {if ($_GET['tglMulaiPromosi'] !=""){$tglMulaiPromosi = $_GET['tglMulaiPromosi']; }}
  if(isset($_GET['tglSelesaiPromosi'])) {if ($_GET['tglSelesaiPromosi'] !=""){$tglSelesaiPromosi = $_GET['tglSelesaiPromosi']; }}
  if(isset($_GET['tglMulaiSebelumPromosi'])) {if ($_GET['tglMulaiSebelumPromosi'] !=""){$tglMulaiSebelumPromosi = $_GET['tglMulaiSebelumPromosi']; }}
  if(isset($_GET['tglSelesaiSebelumPromosi'])) {if ($_GET['tglSelesaiSebelumPromosi'] !=""){$tglSelesaiSebelumPromosi = $_GET['tglSelesaiSebelumPromosi']; }}
  if(isset($_GET['tglMulaiSetelahPromosi'])) {if ($_GET['tglMulaiSetelahPromosi'] !=""){$tglMulaiSetelahPromosi = $_GET['tglMulaiSetelahPromosi']; }}
  if(isset($_GET['tglSelesaiSetelahPromosi'])) {if ($_GET['tglSelesaiSetelahPromosi'] !=""){$tglSelesaiSetelahPromosi = $_GET['tglSelesaiSetelahPromosi']; }}

	
	
	if(isset($_GET['lokasiStock'])) {if ($_GET['lokasiStock'] !=""){$lokasiStock = $_GET['lokasiStock']; }}
	if(isset($_GET['groupSales'])) {if ($_GET['groupSales'] !=""){$groupSales = $_GET['groupSales']; }}
	if(isset($_GET['statusTag'])) {if ($_GET['statusTag'] !=""){$statusTag = $_GET['statusTag']; }}
	if(isset($_GET['statusQty'])) {if ($_GET['statusQty'] !=""){$statusQty = $_GET['statusQty']; }}

  //member
  if(isset($_GET['kodeMonitoringMember'])) {if ($_GET['kodeMonitoringMember'] !=""){$kodeMonitoringMember = $_GET['kodeMonitoringMember']; }}
  if(isset($_GET['kodeMember'])) {if ($_GET['kodeMember'] !=""){$kodeMember = $_GET['kodeMember']; }}
  if(isset($_GET['namaMember'])) {if ($_GET['namaMember'] !=""){$namaMember = $_GET['namaMember']; }}

  if(isset($_GET['jenisMember'])) {if ($_GET['jenisMember'] !=""){$jenisMember = $_GET['jenisMember']; }}
  if(isset($_GET['kodeOutlet'])) {if ($_GET['kodeOutlet'] !=""){$kodeOutlet = $_GET['kodeOutlet']; }}
  if(isset($_GET['kodeSubOutlet'])) {if ($_GET['kodeSubOutlet'] !=""){$kodeSubOutlet = $_GET['kodeSubOutlet']; }}
  	if(isset($_GET['Namakategori'])) {if ($_GET['Namakategori'] !=""){$Namakategori = $_GET['Namakategori']; }}
	if(isset($_GET['Subkategori'])) {if ($_GET['Subkategori'] !=""){$Subkategori = $_GET['Subkategori']; }}

  //toko omi
  if(isset($_GET['kodeTokoOMI'])) {if ($_GET['kodeTokoOMI'] !=""){$kodeTokoOMI = $_GET['kodeTokoOMI']; }}


	//produk atau barang
	
	if(isset($_GET['namaBarang'])) {if ($_GET['namaBarang'] !=""){$namaBarang = $_GET['namaBarang']; }}
	if(isset($_GET['kodePLU'])) {if ($_GET['kodePLU'] !=""){$kodePLU = $_GET['kodePLU']; }}
	if(isset($_GET['kodeBarcode'])) {if ($_GET['kodeBarcode'] !=""){$kodeBarcode = $_GET['kodeBarcode']; }}
  if(isset($_GET['kodeMonitoringPLU'])) {if ($_GET['kodeMonitoringPLU'] !=""){$kodeMonitoringPLU = $_GET['kodeMonitoringPLU']; }}
  if(isset($_GET['kodeTag'])) {if ($_GET['kodeTag'] !=""){$kodeTag = $_GET['kodeTag']; }}

	//divisi, departemen dan kategori
	if(isset($_GET['kodeDivisi'])) {if ($_GET['kodeDivisi'] !=""){$kodeDivisi = $_GET['kodeDivisi']; }}
	if(isset($_GET['kodeDepartemen'])) {if ($_GET['kodeDepartemen'] !=""){$kodeDepartemen = $_GET['kodeDepartemen']; }}
	if(isset($_GET['kodeKategoriBarang'])) {if ($_GET['kodeKategoriBarang'] !=""){$kodeKategoriBarang = $_GET['kodeKategoriBarang']; }}
  if(isset($_GET['kodeTag'])) {if ($_GET['kodeTag'] !=""){$kodeTag = $_GET['kodeTag']; }}



	//supplier
	if(isset($_GET['kodeSupplier'])) {if ($_GET['kodeSupplier'] !=""){$kodeSupplier = $_GET['kodeSupplier']; }}
	if(isset($_GET['namaSupplier'])) {if ($_GET['namaSupplier'] !=""){$namaSupplier = $_GET['namaSupplier']; }}
	if(isset($_GET['kodeMonitoringSupplier'])) {if ($_GET['kodeMonitoringSupplier'] !=""){$kodeMonitoringSupplier = $_GET['kodeMonitoringSupplier']; }}

  //jenis laporan
  if(isset($_GET['exportExcel'])) {if ($_GET['exportExcel'] !=""){$exportExcel = $_GET['exportExcel']; }}
	if(isset($_GET['jenisLaporan'])) {if ($_GET['jenisLaporan'] !=""){$jenisLaporan = $_GET['jenisLaporan']; }}
	if(isset($_GET['sortBy'])) {if ($_GET['sortBy'] !=""){$sortBy = $_GET['sortBy']; }}


  //pilihan filter 
  if(isset($_GET['itemOMI'])) {if ($_GET['itemOMI'] !=""){$itemOMI = $_GET['itemOMI']; }}
  if(isset($_GET['discount2'])) {if ($_GET['discount2'] !=""){$discount2 = $_GET['discount2']; }}
  if(isset($_GET['promoMD'])) {if ($_GET['promoMD'] !=""){$promoMD = $_GET['promoMD']; }}
  if(isset($_GET['marginNegatif'])) {if ($_GET['marginNegatif'] !=""){$marginNegatif = $_GET['marginNegatif']; }}
  if(isset($_GET['hargaJualNol'])) {if ($_GET['hargaJualNol'] !=""){$hargaJualNol = $_GET['hargaJualNol']; }}
  if(isset($_GET['promoMahal'])) {if ($_GET['promoMahal'] !=""){$promoMahal = $_GET['promoMahal']; }}
  if(isset($_GET['poOutstanding'])) {if ($_GET['poOutstanding'] !=""){$poOutstanding = $_GET['poOutstanding']; }}
  if(isset($_GET['stockKosong'])) {if ($_GET['stockKosong'] !=""){$stockKosong = $_GET['stockKosong']; }}

  if(isset($_GET['jenisMarginNegatif'])) {if ($_GET['jenisMarginNegatif'] !=""){$jenisMarginNegatif = $_GET['jenisMarginNegatif']; }}

	


	
	  
	//validasi
  //ganti spasi menjadi % agar pencarian berhasil
  $namaBarang = str_replace(" ","%",$namaBarang);
  $namaSupplier = str_replace(" ","%",$namaSupplier);

  

  $exportExcel = strtoupper($exportExcel);
?>