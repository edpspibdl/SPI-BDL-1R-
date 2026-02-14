<?php

// 1. LOGIKA STREAMING & AJAX (Harus di paling atas)
if (isset($_GET['stream'])) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    $mode = $_GET['mode'];
    $ip   = $_GET['ip'];
    $port = isset($_GET['port']) ? (int)$_GET['port'] : 0;
    $ip_safe = escapeshellarg($ip);

    if ($mode == "paping") {
        // PATH ABSOLUT KE DESKTOP USER
        $paping_path = 'C:\Users\User\Desktop\paping.exe'; 
        
        // Perintah paping dengan port -p dan count -c 10
        $cmd = "\"$paping_path\" $ip_safe -p $port -c 10";
    } elseif ($mode == "tracert") {
        $cmd = "tracert -d -h 10 $ip_safe";
    } else {
        $cmd = "ping -n 4 $ip_safe";
    }

    // Eksekusi Command
    $handle = popen($cmd . " 2>&1", "r"); 
    while (!feof($handle)) {
        $line = fgets($handle);
        if ($line) {
            echo "data: " . $line . "\n\n";
            ob_flush(); flush();
        }
        usleep(30000);
    }
    pclose($handle);

    echo "data: [DONE]\n\n";
    exit;
}

// Logika Check Status (Lampu Indikator) via Background AJAX
if (isset($_POST['check_status'])) {
    $ip = escapeshellarg($_POST['ip']);
    // Cukup ping 1x untuk efisiensi
    echo shell_exec("ping -n 1 $ip");
    exit;
}

require_once '../layout/_top.php';

// Daftar Host Monitoring
$hosts = [
    ["name" => "Server Prod", "ip" => "172.31.146.253", "port" => "5432", "service" => "POSTGRESQL"],
    ["name" => "Server Sim", "ip" => "172.31.146.167", "port" => "5432", "service" => "POSTGRESQL"],
    ["name" => "IAS Server",         "ip" => "172.31.146.190", "port" => "80",   "service" => "WEB SERVICE"],
    ["name" => "Indogrosir LAN",     "ip" => "fo.indogrosir.lan",   "port" => "",  "service" => "FO.INDOGROSIR.LAN"]
];
?>

<style>
    :root { --bg-dark: #0f172a; --card-bg: #1e293b; --success: #10b981; --danger: #ef4444; --primary: #38bdf8; }
    
    .stat-card { background: var(--card-bg); padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #334155; height: 100%; }
    .stat-card span { display: block; font-size: 26px; font-weight: 800; color: #fff; }
    .stat-card label { font-size: 10px; color: #94a3b8; text-transform: uppercase; margin-top: 5px; }

    .net-card-unified { background: var(--card-bg) !important; border: 1px solid #334155 !important; border-radius: 12px; margin-top: 25px; }
    .status-box { background: var(--bg-dark); border: 1px solid #334155; border-radius: 10px; padding: 15px; margin-bottom: 15px; position: relative; transition: 0.3s; }
    .status-box:hover { border-color: var(--primary); }

    .status-badge-container { position: absolute; top: 12px; right: 12px; display: flex; align-items: center; }
    .status-text { font-size: 9px; font-weight: 900; margin-right: 6px; text-transform: uppercase; }
    .indicator { width: 10px; height: 10px; border-radius: 50%; }
    
    .bg-online { background: var(--success); box-shadow: 0 0 10px var(--success); }
    .bg-offline { background: var(--danger); box-shadow: 0 0 10px var(--danger); animation: blink 1s infinite; }
    @keyframes blink { 50% { opacity: 0.3; } }

    .modal-content-cmd { background: #000 !important; border: 1px solid #444; border-radius: 10px; }
    .cmd-window-modal {
        background: #000; color: #d1d5db; 
        font-family: 'Consolas', 'Monaco', monospace;
        padding: 20px; height: 500px; overflow-y: auto; 
        font-size: 13px; line-height: 1.5; white-space: pre-wrap;
    }

    .btn-action { font-size: 9px; font-weight: 800; padding: 7px; border-radius: 4px; border: none; color: #fff; cursor: pointer; flex: 1; margin: 0 2px; }
    .btn-ping { background: #334155; }
    .btn-trace { background: #4338ca; }
    .btn-paping { background: #0369a1; }
    .btn-action:hover { filter: brightness(1.2); }
</style>

<section class="section">
    <div class="section-header"><h1>Network Infrastructure Monitoring</h1></div>

    <div class="row">
        <div class="col-md-3"><div class="stat-card"><span><?= count($hosts) ?></span><label>Total Monitoring</label></div></div>
        <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid var(--success)"><span id="stat-up" class="text-success">0</span><label>Active</label></div></div>
        <div class="col-md-3"><div class="stat-card" style="border-bottom:3px solid var(--danger)"><span id="stat-down" class="text-danger">0</span><label>Down</label></div></div>
        <div class="col-md-3"><div class="stat-card"><span id="stat-loss" class="text-warning">0%</span><label>Packet Loss Rate</label></div></div>
    </div>

    <div class="card net-card-unified">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h4 class="text-white m-0" style="font-size:14px;"><i class="fas fa-server mr-2"></i> Server Managed List</h4>
            <button class="btn btn-sm btn-primary" onclick="autoCheckStatus()"><i class="fas fa-sync-alt"></i> Refresh All</button>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($hosts as $i => $h): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="status-box">
                        <div class="status-badge-container">
                            <span class="status-text" id="txt-<?= $i ?>" style="color:#64748b">Check</span>
                            <div class="indicator" id="ind-<?= $i ?>" style="background:#475569"></div>
                        </div>
                        <div class="host-info">
                            <span class="badge-service" style="font-size:8px; color:var(--primary); font-weight:bold;"><?= $h['service'] ?></span>
                            <h6 class="mt-1 text-white"><?= $h['name'] ?></h6>
                            <small class="text-muted d-block"><i class="fas fa-network-wired mr-1"></i><?= $h['ip'] ?></small>
                            <small class="text-muted"><i class="fas fa-plug mr-1"></i>Port: <?= $h['port'] ?></small>
                        </div>
                        <div class="mt-3 d-flex">
                            <button class="btn-action btn-ping" onclick="startStream('ping','<?= $h['ip'] ?>', '<?= $i ?>')">PING</button>
                            <button class="btn-action btn-trace" onclick="startStream('tracert','<?= $h['ip'] ?>', '<?= $i ?>')">TRACE</button>
                            <button class="btn-action btn-paping" onclick="startStream('paping','<?= $h['ip'] ?>', '<?= $i ?>', '<?= $h['port'] ?>')">PAPING</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalCMD" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-cmd">
            <div class="modal-header py-2 bg-dark border-bottom border-secondary text-white">
                <h6 class="modal-title" style="font-size:12px;"><i class="fas fa-terminal mr-2"></i> Terminal Debugger @SPI-Infrastructure</h6>
                <button type="button" class="close text-white" data-dismiss="modal" onclick="closeStream()">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="cmd-window-modal" id="cmd-box-modal"></div>
            </div>
        </div>
    </div>
</div>

<script>
let eventSource = null;

function autoCheckStatus() {
    <?php foreach ($hosts as $i => $h): ?>
    fetch('', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'check_status=1&ip=<?= $h['ip'] ?>'
    })
    .then(res => res.text())
    .then(data => {
        const ind = document.getElementById('ind-<?= $i ?>');
        const txt = document.getElementById('txt-<?= $i ?>');
        
        const isError = data.includes("Timed out") || data.includes("100% loss") || data.includes("unreachable");
        const isReply = data.includes("Reply from");

        if (isReply && !isError) {
            ind.className = 'indicator bg-online';
            txt.innerText = 'Online'; txt.style.color = '#10b981';
        } else {
            ind.className = 'indicator bg-offline';
            txt.innerText = 'Offline'; txt.style.color = '#ef4444';
        }
        updateDashboard();
    });
    <?php endforeach; ?>
}

function updateDashboard() {
    const up = document.querySelectorAll('.bg-online').length;
    const down = document.querySelectorAll('.bg-offline').length;
    document.getElementById('stat-up').innerText = up;
    document.getElementById('stat-down').innerText = down;
    const total = up + down;
    document.getElementById('stat-loss').innerText = total > 0 ? Math.round((down/total)*100)+'%' : '0%';
}

function startStream(mode, ip, index, port = '') {
    const cmdBox = document.getElementById('cmd-box-modal');
    cmdBox.innerHTML = "Microsoft Windows [Version 10.0.19045]\n(c) Indogrosir IT Infrastructure. Admin@SPI-BDL\n\n<span style='color:#facc15'>C:\\Users\\Admin> " + mode + " " + ip + (port ? " -p " + port : "") + "</span>\n";
    $('#modalCMD').modal('show');

    if (eventSource) eventSource.close();

    eventSource = new EventSource(`?stream=1&mode=${mode}&ip=${ip}&port=${port}`);

    eventSource.onmessage = function(e) {
        if (e.data === "[DONE]") {
            cmdBox.innerHTML += "\n<span class='text-success'>C:\\Users\\Admin></span> <span class='blink'>_</span>";
            eventSource.close();
            setTimeout(autoCheckStatus, 500);
            return;
        }

        let line = e.data;
        // Highlighting Logic
        line = line.replace(/connected/gi, '<span style="color:#10b981; font-weight:bold;">CONNECTED</span>');
        line = line.replace(/timeout/gi, '<span style="color:#ef4444; font-weight:bold;">TIMEOUT</span>');
        line = line.replace(/Reply from/g, '<span style="color:#10b981;">Reply from</span>');
        
        cmdBox.innerHTML += line + "\n";
        cmdBox.scrollTop = cmdBox.scrollHeight;
    };

    eventSource.onerror = function() { eventSource.close(); };
}

function closeStream() {
    if (eventSource) eventSource.close();
}

window.onload = autoCheckStatus;
</script>

<?php require_once '../layout/_bottom.php'; ?>