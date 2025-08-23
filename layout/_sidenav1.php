<div class="main-sidebar sidebar-style-2">
  <style>
    #sidebar-wrapper::-webkit-scrollbar {
      display: none;
    }
  </style>
  <aside id="sidebar-wrapper"
    style="padding-top: 20px; padding-bottom: 60px; max-height: 100vh; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">

    <div class="sidebar-brand">
      <a href="../landingPage/index.php">
        <img src="../assets/img/logo-spi.webp" alt="logo" width="200">
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="index.php">SPI</a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Toko</li>
      <li><a class="nav-link" href="../dashboard/"><i class="fas fa-store"></i> <span>PB SPI</span></a></li>
      <li><a class="nav-link" href="../masterPB/"><i class="fas fa-database"></i> <span>Master PB SPI</span></a></li>
      <li><a class="nav-link" href="../informasiProduk/form.php"><i class="fas fa-info"></i> <span>Informasi Produk</span>
      <li><a class="nav-link" href="../alokasiPromosi/"><i class="fas fa-book"></i> <span>Cek Alokasi Promosi</span>
        </a>
      </li>
      <li><a class="nav-link" href="../laporanLaporan/"><i class="fas fa-file-alt"></i> <span>Laporan Laporan</span></a></li>
      <!-- Submenu Kasir -->
      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-money-bill"></i> <span>Kasir</span></a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../SalesPerDay/">Laporan Monit MM</a></li>
          <li><a class="nav-link" href="../Sales/">Sales Today</a></li>
          <li><a class="nav-link" href="../Sales/sales.php">Sales By Kasir</a></li>
          <li><a class="nav-link" href="../pluNoSales/">Plu No Sales</a></li>
          <li><a class="nav-link" href="../scanTTB/">Scan TTB</a></li>
        </ul>
      </li>


      <!-- Submenu Member -->
      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-address-book"></i> <span>Member</span></a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../memberCRM/">Member CRM</a></li>
          <li><a class="nav-link" href="../getMember/">Get Member By MR</a></li>
          <li><a class="nav-link" href="../poinMember/">Poin Member</a></li>
          <li><a class="nav-link" href="../transaksiMember/">History Transaksi Member</a></li>
        </ul>
      </li>

      <!-- Menu Logistik dengan Submenu -->
      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-tags"></i> <span>Promo</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../promoPerLokasi/"> <span>Promo Per Lokasi</span></a></li>

          <!-- Submenu Cashback -->
          <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">Cashback</a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="../cekPluPromo/">Cek Plu Promo</a></li>
              <li><a class="nav-link" href="../cekCashback/">Perolehan Member</a></li>
            </ul>
          </li>

          <!-- Submenu Poin -->
          <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">Poin</a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="../pluPoin/">All PLU Poin</a></li>
            </ul>
          </li>
        </ul>
      </li>

      <li class="menu-header">IPP</li>
      <li><a class="nav-link" href="../monitoringIPP/"><i class="fas fa-truck"></i> <span>Monitoring IPP</span></a></li>



      <li class="menu-header">Planogram</li>
      <li><a class="nav-link" href="../plano/"><i class="fas fa-dolly"></i> <span>Planogram SPI BDL</span></a></li>


      <li class="menu-header">FO & BO</li>

      <li><a class="nav-link" href="../HistoryBO/"><i class="fas fa-building"></i> <span>History Back Office</span></a></li>

      <li><a class="nav-link" href="../serviceLevel/"><i class="fas fa-star"></i> <span>Service Level PB</span></a></li>

      <li><a class="nav-link" href="../evalSales/"><i class="fas fa-chart-line"></i> <span>Evaluasi Sales</span></a></li>
      <li><a class="nav-link" href="../monitoringED/"><i class="fas fa-chart-line"></i> <span>ED</span></a></li>

      <!-- Submenu Laporan -->

      <!-- Submenu Monitoring -->
      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-binoculars"></i> <span>Monitoring</span></a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../job/">All Job</a></li>
          <li><a class="nav-link" href="../Me/">Month End</a></li>
          <li><a class="nav-link" href="../cekPacking/">Cek Jam Packing</a></li>
          <li><a class="nav-link" href="../ranking_Pick/">Monitoring Picking</a></li>
        </ul>
      </li>

      <!-- Menu Logistik dengan Submenu -->
      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-warehouse"></i> <span>Logistik</span>
        </a>
        <ul class="dropdown-menu">

          <!-- Submenu Monitoring -->
          <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">Monitoring</a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="../monitoringRetur/">Monitoring PLU Retur</a></li>
              <li><a class="nav-link" href="../monitoringGudang/">Monitoring Gudang</a></li>
              <li><a class="nav-link" href="../poBelumKirim/">PO Belum Kirim</a></li>
              <li><a class="nav-link" href="../btb_hari_ini/">BTB Hari Ini</a></li>
            </ul>
          </li>


          <!-- Submenu Stock Opname -->
          <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">Stock Opname</a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="../historySO/">History SO</a></li>
              <li><a class="nav-link" href="../soHarian/">Form SO Harian</a></li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>

    <!-- Spacer bawah untuk tambahan jarak -->
    <div style="height: 60px;"></div>
  </aside>
</div>