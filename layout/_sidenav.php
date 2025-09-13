<div class="main-sidebar sidebar-style-2">
  <style>
    #sidebar-wrapper::-webkit-scrollbar {
      display: none;
    }

    .floatingCard a {
      text-decoration: none;
      color: #343a40;
      transition: color 0.3s ease, transform 0.2s ease;
      font-weight: 600;
    }

    .floatingCard a:hover {
      color: #007bff;
      transform: translateX(4px);
    }

    .floatingCard {
      background: #fff;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      border-radius: 16px;
      position: fixed;
      top: 80px;
      left: 260px;
      width: fit-content;
      /* Menyesuaikan isi */
      min-width: 400px;
      /* Lebar minimal */
      max-width: 90vw;
      /* Opsional: agar tidak terlalu lebar */
      z-index: 1050;
      opacity: 0;
      transform: translateY(-15px);
      pointer-events: none;
      transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      padding: 1.5rem 2rem;
      border: 1px solid #e1e4e8;
    }



    .floatingCard.show {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    #tokoCard,
    #promoCard,
    #infoCard {
      background: #ffffff;
      border: 1px solid #e1e4e8;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
    }

    .floatingCard h6 {
      background-color: rgba(0, 106, 245, 0.57);
      /* warna biru */
      color: whitesmoke;
      /* supaya fontnya kontras */
      padding: 5px;
      /* biar ada ruang di dalam */
      border-radius: 8px;
      /* biar agak membulat sudutnya */
      font-size: 20px;
      border-bottom: 2px solid #dee2e6;
      padding-bottom: 12px;
      margin-bottom: 1.2rem;
      text-align: center;
      font-weight: 700;
      letter-spacing: 0.05em;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .floatingCard .list-group-item {
      font-size: 16px;
      /* Font langsung diperbesar */
      padding: 0.0rem 0.2rem;
      /* Atur padding agar tidak terlalu renggang */
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: background-color 0.25s ease, color 0.25s ease;
      border-radius: 8px;
      margin-bottom: 2px;
      /* Kurangi jarak antar item */
    }


    .floatingCard .list-group-item:hover {
      background-color: #e9f1ff;
      color: rgb(1, 52, 107);
      box-shadow: 0 2px 8px rgba(0, 86, 179, 0.2);
    }

    .floatingCard .list-group-item a {
      font-size: 16px;
      /* Font besar sesuai permintaan */
      display: flex;
      align-items: center;
      gap: 5px;
      color: inherit;
      width: 100%;
      margin-bottom: 0;
    }

    .sidebar-menu .nav-link.active {
      background-color: #007bff;
      color: white !important;
      font-size: 18px;
      border-radius: 5px;
      font-weight: 600;
      box-shadow: 0 0 10px rgba(0, 123, 255, 0.7);
    }

    .sidebar-menu .nav-link.active:hover {
      color: #007bff !important;
    }

    .menu-heading {
      background-color: #74b9ff;
      color: white;
      font-weight: bold;
      text-align: center;
      padding: 14px 20px;
      /* Perbesar padding atas/bawah */
      border-radius: 6px;
      min-width: 300px;
      display: inline-block;
      font-size: 14px;
      /* Perbesar ukuran font */
      margin-bottom: 10px;
      /* Tambah jarak antar header */
      margin-top: 12px;
      /* Tambah jarak atas agar tidak terlalu mepet */
    }
  </style>

  <aside id="sidebar-wrapper" style="padding: 20px 0 60px; max-height: 100vh; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
    <div class="sidebar-brand" style="margin-bottom: 20px;">
      <a href="../landingPage/index.php"><img src="../assets/img/logo-spi.webp" alt="logo" width="200"></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm"><a href="index.php">SPI</a></div>

    <ul class="sidebar-menu">
      <li class="menu-header">Main Menu</li>
      <li><a href="#" class="nav-link" id="tokoBtn"><i class="fas fa-store"></i> <span>Store</span></a></li>
      <li><a href="#" class="nav-link" id="kasirBtn"><i class="fas fa-money-bill"></i> <span>Kasir</span></a></li>
      <li><a href="#" class="nav-link" id="logistikBtn"><i class="fas fa-warehouse"></i> <span>Logistik</span></a></li>
      <li><a href="#" class="nav-link" id="aktivitasBtn"><i class="fas fa-desktop"></i> <span>Monitoring Aktivitas</span></a></li>

      <!-- Garis pemisah -->
      <li>
        <hr style="border: 1px solid #ddd; margin: 12px 0;">
      </li>

      <li class="menu-header">Laporan Menu</li>
      <!-- Kelompok 2 (Laporan) -->
      <li><a href="../laporanLaporan/" class="nav-link"><i class="fas fa-file-alt"></i> <span>Laporan Laporan</span></a></li>
      <li><a href="../MonitoringIPP/" class="nav-link"><i class="fas fa-truck"></i> <span>Monitoring IPP</span></a></li>

      <!-- Garis pemisah -->
      <li>
        <hr style="border: 1px solid #ddd; margin: 12px 0;">
      </li>

      <li class="menu-header">Planogram</li>
      <!-- Kelompok 3 (Planogram) -->
      <li><a class="nav-link" href="../plano/"><i class="fas fa-dolly"></i> <span>Planogram SPI BDL</span></a></li>

    </ul>
    <div style="height:60px"></div>
  </aside>
</div>

<!-- Floating Card - TOKO -->
<div id="tokoCard" class="floatingCard">
  <table cellspacing="0" cellpadding="5">
    <tr>
      <td valign="top">
        <h6 class="menu-heading">SPI</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../dashboard/">PB SPI</a></li>
          <li class="list-group-item"><a href="../masterPB/">Master PB SPI</a></li>
          <!-- <li class="list-group-item"><a href="../informasiProduk/form.php">Informasi Produk (Done)</a></li> -->
          <li class="list-group-item"><a href="../laporanLaporan/">Laporan Laporan</a></li>
          <li class="list-group-item"><a href="../cekOrderPlu/">Cek PLU Order</a></li>
          <li class="list-group-item"><a href="../pembatasanItem/">Pembatasan Item</a></li>
          <li class="list-group-item"><a href="../cekProdukBaru/">Cek Produk Baru</a></li>
        </ul>
        <hr>
        <h6 class="menu-heading">Member</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../memberCRM/">Member CRM</a></li>
          <li class="list-group-item"><a href="../memberCRM/mappingMember.php">Mapping Member</a></li>
          <li class="list-group-item"><a href="../getMember/">Get Member By MR</a></li>
          <li class="list-group-item"><a href="../poinMember/">Poin Member</a></li>
          <li class="list-group-item"><a href="../transaksiMember/">History Transaksi Member</a></li>
          <li class="list-group-item"><a href="../cekCashback/">Perolehan Cashback Member</a></li>
        </ul>
      </td>

      <td width="50">&nbsp;</td>

      <td valign="top">
        <h6 class="menu-heading">Info Produk & Promo</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <a href="../informasiProduk/form.php">Informasi Produk</a>
          </li>
          <li class=" list-group-item"><a href="../alokasiPromosi/">Alokasi Promo</a>
          </li>
          ---------------------------------------------------

          <li class="list-group-item"><a href="../cekPromoGift/">Cek Promo Gift</a></li>
          <li class=" list-group-item"><a href="../masterGift/">Promo Gift All</a></li>
          <li class="list-group-item"><a href="../pluPoin/">All Plu Poin</a></li>
          <li class="list-group-item"><a href="../giftOut/">Gift Hadiah Out</a></li>
          ---------------------------------------------------
          <li class="list-group-item"><a href="../promoPerLokasi/">Promo Per Lokasi</a></li>
          <li class="list-group-item"><a href="../cekPluPromo/">Cek Plu Per Promo</a></li>
          <li class="list-group-item"><a href="../allPluCsbck/">All Plu Cashback</a></li>
          ---------------------------------------------------

        </ul>

      </td>
      <td width="50">&nbsp;</td>
      <td valign="top">
        <h6 class="menu-heading">Service Level</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../serviceLevelKlik/">Service Level PB</a></li>
          <li class="list-group-item"><a href="../serviceLevelPO/">Service Level PO</a></li>
        </ul>
        <hr>

        <h6 class="menu-heading">Evaluasi Sales</h6>
        <ul class="list-group list-group-flush">
          <!-- <li class="list-group-item"><a href="">Evaluasi Sales</a></li> -->
          <li class="list-group-item"><a href="../salesTigaBulan/">Ev Sales 3 Bulan Inc Larangan</a></li>
          <li class="list-group-item"><a href="../salesPromo/">Ev Sales Di Luar Larangan</a></li>
        </ul>
    </tr>
  </table>
</div>

<!-- Floating Card - KASIR -->
<div id="kasirCard" class="floatingCard">
  <h6 class="menu-heading">Kasir</h6>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><a href="../SalesPerDay/">Laporan Monit MM</a></li>
    <li class="list-group-item"><a href="../Sales/">Sales Today</a></li>
    <li class="list-group-item"><a href="../Sales/sales.php">Sales By Kasir</a></li>
    <li class="list-group-item"><a href="../pluNoSales/">PLU No Sales</a></li>
    <li class="list-group-item"><a href="../scanTTB/">Scan TTB</a></li>
  </ul>
</div>

<div id="logistikCard" class="floatingCard">
  <table cellspacing="0" cellpadding="5">
    <tr>
      <td valign="top">
        <h6 class="menu-heading">LPP VS PLANO</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../HistoryBO/">History BackOffice</a></li>
          <li class="list-group-item"><a href="../lppvsPlano/lppvsPlano.php">LPP vs Plano</a></li>
          <li class="list-group-item"><a href="../lppRokok/">LPP Rokok</a></li>
          <li class="list-group-item"><a href="../poinMember/">Plano Rokok</a></li>
          <li class="list-group-item"><a href="../rekapLPP">Rekap LPP vs Plano (On Coming)</a></li>

        </ul>
        <hr>
        <h6 class="menu-heading">SPB & SLP</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../spbPerTanggal/">SPB Per Tanggal</a></li>
          <li class="list-group-item"><a href="../historySLP/">History SLP</a></li>
          <li class="list-group-item"><a href="../spbBelumReal/">SPB Belum Realisasi</a></li>
          <li class="list-group-item"><a href="../slpBelumReal/">SLP Belum Realisasi</a></li>
        </ul>
      </td>
      <td width="50">&nbsp;</td>

      <td valign="top">
        <h6 class="menu-heading">Monitoring Logistik</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../soHarian/">Stok Opname Harian</a></li>
          <li class="list-group-item"><a href="../historySO/">Histori Stok Opname</a></li>
          <li class="list-group-item"><a href="../monitoringED/">Cek EXP Barang</a></li>
          <li class="list-group-item"><a href="../monitoringGudang/">Monitoring Gudang</a></li>

        </ul>
        <hr>
        <h6 class="menu-heading">Terima & Keluar Barang</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../produkBaru/">Penerimaman Produk Baru</a></li>
          <li class="list-group-item"><a href="../poBelumKirim/">Po Belum Kirim</a></li>
          <li class="list-group-item"><a href="../btb_hari_ini/">BTB Hari Ini</a></li>
          <li class="list-group-item"><a href="../penerimaanVsSlp/">Penerimaan vs SLP</a></li>
          <li class="list-group-item"><a href="../monitoringRetur/">Cek Retur</a></li>
        </ul>
      </td>
      <td width="50">&nbsp;</td>

      <td valign="top">
        <h6 class="menu-heading">Master Lokasi</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../masterLokasi/">Master Lokasi</a></li>
          <li class="list-group-item"><a href="../maslokDouble/">Master Lokasi Ganda</a></li>
        </ul>
        <hr>
        <h6 class="menu-heading">IC</h6>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="../resetIC/">Reset IC Per Plu</a></li>
          <li class="list-group-item"><a href="../historySOIC/">History SOIC</a></li>
          <li class="list-group-item"><a href="../penerimaanVsSlp/">Penerimaan vs SLP</a></li>
          <li class="list-group-item"><a href="../monitoringRetur/">Cek Retur</a></li>
        </ul>
      </td>
    </tr>
  </table>
</div>

<!-- Floating Card - KASIR -->
<div id="aktivitasCard" class="floatingCard">
  <h6 class="menu-heading">ALL</h6>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><a href="../job/">All Job</a></li>
    <li class="list-group-item"><a href="../Me/">Month End</a></li>
    <li class="list-group-item"><a href="../cekPacking/">Cek Jam Packing</a></li>
    <li class="list-group-item"><a href="../ranking_Pick/">Liga Picking SPI</a></li>
  </ul>
</div>

<!-- JS Toggle Behavior -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const buttons = ['tokoBtn', 'kasirBtn', 'logistikBtn', 'aktivitasBtn'];
    const cards = ['tokoCard', 'kasirCard', 'logistikCard', 'aktivitasCard'];

    buttons.forEach((btnId, idx) => {
      const btn = document.getElementById(btnId);
      btn.addEventListener('click', e => {
        e.preventDefault();
        const cardId = cards[idx];
        const card = document.getElementById(cardId);

        const isActive = card.classList.contains('show');

        cards.forEach(cId => document.getElementById(cId).classList.remove('show'));
        buttons.forEach(bId => document.getElementById(bId).classList.remove('active'));

        if (!isActive) {
          card.classList.add('show');
          btn.classList.add('active');
        }
      });
    });

    document.addEventListener('click', e => {
      let clickedInside = false;
      cards.forEach(cardId => {
        const card = document.getElementById(cardId);
        const btn = document.getElementById(cardId.replace('Card', 'Btn'));
        if (card.contains(e.target) || btn.contains(e.target)) {
          clickedInside = true;
        }
      });
      if (!clickedInside) {
        cards.forEach(cardId => document.getElementById(cardId).classList.remove('show'));
        buttons.forEach(bId => document.getElementById(bId).classList.remove('active'));
      }
    });
  });
</script>