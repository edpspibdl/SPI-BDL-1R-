<?php
ob_start();

$allowed_ips = ['192.168.170.', '127.0.0.1'];
$user_ip = $_SERVER['REMOTE_ADDR'];

$allowed = false;
foreach ($allowed_ips as $ip_prefix) {
          if (strpos($user_ip, $ip_prefix) === 0) {
                    $allowed = true;
                    break;
          }
}

if (!$allowed) {
          header("Location: ../pageError/notaccess.php");
          exit;
}

ob_end_flush();

require_once '../layout/_top.php';
require_once '../helper/connection.php';

// ——— Ambil Data Member ———
$sql = "
SELECT 
    c.cus_kodemember AS kode_member,
    c.cus_namamember AS nama_member,
    c.cus_alamatmember8 AS wilayah,
    trim(split_part(sub1.crm_koordinat, ',', 1))::float AS latitude,
    trim(split_part(sub1.crm_koordinat, ',', 2))::float AS longitude
FROM tbmaster_customer c
LEFT JOIN (
    SELECT DISTINCT crm_kodemember, crm_koordinat
    FROM tbmaster_customercrm
) sub1 ON c.cus_kodemember = sub1.crm_kodemember
WHERE c.cus_recordid IS NULL
  AND c.cus_flagmemberkhusus = 'Y'
  AND c.cus_kodeigr = '1R'
  AND c.cus_namamember <> 'NEW'
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$members = [];
foreach ($result as $row) {
          $lat = floatval($row['latitude']);
          $lng = floatval($row['longitude']);

          // Validasi koordinat
          if ($lat && $lng) {
                    $members[] = [
                              'kode'    => $row['kode_member'],
                              'name'    => $row['nama_member'],
                              'wilayah' => $row['wilayah'],
                              'lat'     => $lat,
                              'lng'     => $lng
                    ];
          }
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

<section class="section">
          <div class="section-header">
                    <h1>Koordinat Member SPI BDL 1R</h1>
          </div>
          <div id="map" style="height:80vh; width:100%; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2);"></div>
</section>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
          const members = <?php echo json_encode($members); ?>;

          // Inisialisasi map
          const map = L.map('map', {
                    zoom: 6,
                    maxZoom: 21
          });

          // Basemap
          const baseLayers = {
                    "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                              maxZoom: 19,
                              attribution: '&copy; OpenStreetMap contributors'
                    }),
                    "Google Streets": L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                              maxZoom: 21,
                              subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                              attribution: 'Map data &copy; Google'
                    }),
                    "Google Satellite": L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                              maxZoom: 21,
                              subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                              attribution: 'Map data &copy; Google'
                    }),
          };
          baseLayers["Google Streets"].addTo(map);
          L.control.layers(baseLayers, null, {
                    position: 'topleft'
          }).addTo(map);

          // Cluster marker
          const markers = L.markerClusterGroup({
                    showCoverageOnHover: false,
                    spiderfyOnMaxZoom: true,
                    disableClusteringAtZoom: 18
          });
          const markerByKode = new Map();
          const norm = s => (s ?? '').toString().trim().toUpperCase();

          // Icon merah untuk member
          const locationIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
          });

          // Tambahkan marker member
          members.forEach(m => {
                    const marker = L.marker([m.lat, m.lng], {
                              icon: locationIcon
                    }).bindPopup(`
        <b>Kode:</b> ${m.kode}<br>
        <b>Nama:</b> ${m.name}<br>
        <b>Wilayah:</b> ${m.wilayah??'-'}<br>
        <b>Koordinat:</b> ${m.lat}, ${m.lng}
    `);
                    markers.addLayer(marker);
                    markerByKode.set(norm(m.kode), marker);
          });
          map.addLayer(markers);

          // Titik pusat manual (kantor)
          const centerLat = -5.1055300321903525;
          const centerLng = 105.3365039821061;
          // Titik pusat manual (kantor) pakai ikon online
          const centerMarker = L.marker([centerLat, centerLng], {
                    icon: L.icon({
                              iconUrl: 'https://cdn-icons-png.flaticon.com/512/3103/3103446.png', // contoh icon office/gudang
                              iconSize: [40, 40], // ukuran gambar
                              iconAnchor: [20, 40], // titik pangkal marker
                              popupAnchor: [0, -35] // posisi popup
                    })
          }).bindPopup("STOCK POIN INDOGROSIR METRO").addTo(map);

          // Fit map ke semua marker
          if (members.length > 0) {
                    map.fitBounds(markers.getBounds());
          } else {
                    map.setView([centerLat, centerLng], 10);
          }

          // Legend pencarian kode member
          const legendMember = L.control({
                    position: 'topright'
          });
          legendMember.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'info legend');
                    div.style.background = 'white';
                    div.style.padding = '10px';
                    div.style.borderRadius = '8px';
                    div.style.marginBottom = '10px';
                    div.style.boxShadow = '0 0 6px rgba(0,0,0,0.2)';
                    div.innerHTML = `
        <b>Cari Kode Member</b><br>
        <input type="text" id="legendSearchMember" placeholder="Masukkan kode member..."
            style="width:100%;padding:6px;margin:6px 0;border:1px solid #ccc;border-radius:6px;">
        <button id="btnCariMember"
            style="width:100%;padding:8px;border:none;background:#0d6efd;color:white;border-radius:6px;cursor:pointer;">
            Cari
        </button>
    `;

                    function focusToMarker(marker) {
                              if (!marker) return;
                              markers.zoomToShowLayer(marker, function() {
                                        map.flyTo(marker.getLatLng(), 20, {
                                                  duration: 0.5
                                        });
                                        setTimeout(() => marker.openPopup(), 250);
                              });
                    }
                    div.querySelector("#btnCariMember").addEventListener("click", () => {
                              const kodeInput = div.querySelector("#legendSearchMember").value;
                              const marker = markerByKode.get(norm(kodeInput));
                              if (marker) focusToMarker(marker);
                              else alert("Kode member tidak ditemukan!");
                    });
                    div.querySelector("#legendSearchMember").addEventListener("keypress", e => {
                              if (e.key === "Enter") div.querySelector("#btnCariMember").click();
                    });
                    L.DomEvent.disableClickPropagation(div);
                    L.DomEvent.disableScrollPropagation(div);
                    return div;
          };
          legendMember.addTo(map);
</script>

<?php require_once '../layout/_bottom.php'; ?>