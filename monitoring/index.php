<?php
ob_start();

// --- Cek IP ---
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
?>

<!-- CSS Dashboard Monitoring -->
<style>
.monitoring-dashboard { font-family: Inter, system-ui, Arial; color:#111827; margin:24px; }
.monitoring-dashboard h1 { margin:0 0 18px 0; font-size:28px; }
.monitoring-dashboard .row { display:flex; gap:16px; align-items:stretch; margin-bottom:16px; }
.monitoring-dashboard .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 6px 14px rgba(2,6,23,0.06); flex:1; }
.monitoring-dashboard .big { font-size:32px; color:#2563eb; margin-bottom:6px; }
.monitoring-dashboard .muted { color:#6b7280; }
.monitoring-dashboard .layout { display:grid; grid-template-columns: 2fr 1fr; gap:16px; }
.monitoring-dashboard .charts { display:flex; gap:16px; }
.monitoring-dashboard .table-card { padding:8px 18px 18px 18px; }
.monitoring-dashboard table { width:100%; border-collapse:collapse; background:#fff; }
.monitoring-dashboard table th, 
.monitoring-dashboard table td { padding:10px; border-bottom:1px solid #eef2f6; text-align:left; }
.monitoring-dashboard .search { float:right; margin-bottom:8px; }
@media (max-width:900px) {
  .monitoring-dashboard .layout { grid-template-columns: 1fr; }
  .monitoring-dashboard .row { flex-direction:column; }
}
</style>

<!-- CDN Chart.js & DataTables -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<section class="section">
  <div class="monitoring-dashboard">
    <div class="section-header d-flex justify-content-between align-items-center">
      <h1>Dashboard (Real Time)</h1>
    </div>

    <div class="section-body">
      <h2 class="section-title">Website Monitoring</h2>
      <div class="row">
        <div class="card">
          <div class="big" id="totalVisits">0</div>
          <div class="muted">Total Kunjungan</div>
        </div>
        <div class="card">
          <div class="big" id="uniqueVisitors">0</div>
          <div class="muted">Pengunjung Unik</div>
        </div>
        <div class="card">
          <div class="big" id="errorRate">0%</div>
          <div class="muted">Error Rate (5xx+4xx)</div>
        </div>
      </div>

      <div class="layout">
        <div class="card">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <strong>Visits Over Time</strong>
            <small class="muted" id="lastUpdated">-</small>
          </div>
          <div style="height:300px; margin-top:12px;">
            <canvas id="visitsChart"></canvas>
          </div>

          <div style="display:flex; gap:16px; margin-top:18px;">
            <div style="flex:1;">
              <strong>Halaman Terpopuler</strong>
              <table id="topPagesTable" style="margin-top:8px;">
                <thead><tr><th>Halaman</th><th style="text-align:right">Akses</th></tr></thead>
                <tbody id="topPagesBody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="margin-bottom:16px;">
            <strong>Status HTTP</strong>
            <div style="height:220px; margin-top:12px;">
              <canvas id="statusPie"></canvas>
            </div>
          </div>

          <div class="card table-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <strong>Visitor Logs</strong>
              <input id="searchbox" class="search" placeholder="Search..." style="padding:8px; border-radius:6px; border:1px solid #e6eef8" />
            </div>
            <table id="logsTable" class="display monitoring-table" style="width:100%; margin-top:8px;">
              <thead>
                <tr><th>IP Address</th><th>Timestamp</th><th>Method</th><th>Page</th><th>Status</th></tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const API = './logs.php'; // API backend
let visitsChart = null;
let statusChart = null;
let logsTable = null;

function formatNumber(n) {
  return Intl.NumberFormat('id-ID').format(n);
}
function computeErrorRate(statusCounts, total) {
  if (!total || total === 0) return 0;
  let errors = 0;
  for (const k in statusCounts) {
    const code = parseInt(k);
    if (code >= 400) errors += statusCounts[k];
  }
  return (errors / total) * 100;
}
function renderTopPages(topPages) {
  const tbody = document.getElementById('topPagesBody');
  tbody.innerHTML = '';
  for (const [page, count] of Object.entries(topPages)) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeHtml(page)}</td><td style="text-align:right">${formatNumber(count)}</td>`;
    tbody.appendChild(tr);
  }
}
function escapeHtml(s) {
  return String(s).replace(/[&<>"'`]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;', '`':'&#96;'}[c]));
}

async function loadData() {
  try {
    const res = await fetch(API + '?_=' + Date.now());
    const data = await res.json();
    if (data.error) return console.error(data.error);

    const summary = data.summary || {};
    const total = summary.totalVisits || 0;
    const unique = summary.uniqueVisitors || 0;
    const statusCounts = summary.statusCounts || {};
    const visitsOverTime = summary.visitsOverTime || {labels:[], values:[]};

    document.getElementById('totalVisits').textContent = formatNumber(total);
    document.getElementById('uniqueVisitors').textContent = formatNumber(unique);
    document.getElementById('errorRate').textContent = computeErrorRate(statusCounts, total).toFixed(2) + '%';
    document.getElementById('lastUpdated').textContent = new Date().toLocaleString();

    renderTopPages(summary.topPages || {});

    // Line Chart
    const ctx = document.getElementById('visitsChart').getContext('2d');
    if (!visitsChart) {
      visitsChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: visitsOverTime.labels,
          datasets: [{
            label: 'Kunjungan',
            data: visitsOverTime.values,
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            backgroundColor: 'rgba(37,99,235,0.08)',
            borderColor: 'rgba(37,99,235,1)'
          }]
        },
        options: { plugins: { legend: { display: false } }, scales: { x:{}, y:{ beginAtZero:true } } }
      });
    } else {
      visitsChart.data.labels = visitsOverTime.labels;
      visitsChart.data.datasets[0].data = visitsOverTime.values;
      visitsChart.update();
    }

    // Pie Chart
    const ctx2 = document.getElementById('statusPie').getContext('2d');
    const labels = Object.keys(statusCounts);
    const values = Object.values(statusCounts);
    if (!statusChart) {
      statusChart = new Chart(ctx2, {
        type: 'pie',
        data: { labels, datasets: [{ data: values }] },
        options: { plugins: { legend: { position: 'bottom' } } }
      });
    } else {
      statusChart.data.labels = labels;
      statusChart.data.datasets[0].data = values;
      statusChart.update();
    }

    // Table
    const logs = data.logs || [];
    if (!logsTable) {
      logsTable = $('#logsTable').DataTable({
        data: logs,
        columns: [
          { data: 'ip' },
          { data: 'timestamp' },
          { data: 'method' },
          { data: 'url' },
          { data: 'status' }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [10,25,50,100]
      });
      $('#searchbox').on('keyup', function() { logsTable.search(this.value).draw(); });
    } else {
      logsTable.clear();
      logsTable.rows.add(logs);
      logsTable.draw(false);
    }

  } catch (err) {
    console.error('Failed to load API', err);
  }
}

loadData();
setInterval(loadData, 10000);
</script>

<?php require_once '../layout/_bottom.php'; ?>
