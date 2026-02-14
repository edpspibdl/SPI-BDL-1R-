<?php
require_once '../layout/_top.php';
require_once '../helper/ftp_connection.php';

/* =====================================================
    LOCAL FILE SYSTEM LOGIC
===================================================== */
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$local_files = [];
$show_drives = false;

$possible_user_paths = [];
if ($isWindows) {
    $up = getenv('USERPROFILE') ?: (isset($_SERVER['USERPROFILE']) ? $_SERVER['USERPROFILE'] : '');
    if ($up) $possible_user_paths[] = $up;
    $un = getenv('USERNAME') ?: (isset($_SERVER['USERNAME']) ? $_SERVER['USERNAME'] : '');
    if ($un) $possible_user_paths[] = "C:\\Users\\" . $un;
    $possible_user_paths[] = "C:\\Users\\User";
}

if (!isset($_GET['local_dir']) || $_GET['local_dir'] == "This PC") {
    $current_local_path = "This PC";
    $show_drives = true;
    if ($isWindows) {
        $finalDesktop = ""; $finalDocs = "";
        foreach (array_unique($possible_user_paths) as $path) {
            if (@is_dir($path . "\\Desktop")) {
                $finalDesktop = $path . "\\Desktop";
                $finalDocs = $path . "\\Documents";
                break;
            }
        }
        if ($finalDesktop) $local_files['Desktop'] = $finalDesktop;
        if ($finalDocs) $local_files['Documents'] = $finalDocs;
        foreach (range('C', 'Z') as $drive) {
            $dp = $drive . ':\\';
            if (@is_dir($dp)) $local_files[$drive . ' Drive'] = $dp;
        }
    }
} else {
    $current_local_path = $_GET['local_dir'];
    if (@is_dir($current_local_path)) {
        $local_files = @scandir($current_local_path);
    } else {
        header("Location: ?local_dir=This PC");
        exit;
    }
}

$remote_path = isset($_GET['remote']) ? $_GET['remote'] : ".";
$remote_files = @ftp_rawlist($conn_id, $remote_path);
?>

<style>
    :root {
        --ftp-bg: #0f172a;
        --ftp-card: #1e293b;
        --ftp-accent: #38bdf8;
        --ftp-text: #e2e8f0;
        --ftp-border: #334155;
    }

    /* Layout & Section */
    .section-header h1 { color: #1e293b !important; font-weight: 800; }
    
    .ftp-main-card {
        background: var(--ftp-card) !important;
        border-radius: 15px;
        border: 1px solid var(--ftp-border);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    /* Path Bar & Breadcrumbs */
    .ftp-header-bar {
        background: #0f172a !important;
        padding: 12px 20px;
        border-bottom: 1px solid var(--ftp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .path-label {
        font-family: 'Consolas', monospace;
        color: var(--ftp-accent);
        font-size: 13px;
        margin: 0;
    }

    /* Search Input Styling */
    .ftp-search-wrapper {
        padding: 15px 20px 5px 20px;
    }
    
    .ftp-input-group {
        position: relative;
    }

    .ftp-search-input {
        background: #0f172a !important;
        border: 1px solid var(--ftp-border) !important;
        color: #fff !important;
        border-radius: 8px;
        padding-left: 35px;
        height: 40px;
        transition: all 0.3s;
    }

    .ftp-search-input:focus {
        border-color: var(--ftp-accent) !important;
        box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 12px;
        color: #64748b;
    }

    /* Table Area */
    .ftp-table-area {
        height: 500px;
        overflow-y: auto;
        padding: 0 10px 10px 10px;
    }

    /* Custom Scrollbar */
    .ftp-table-area::-webkit-scrollbar { width: 6px; }
    .ftp-table-area::-webkit-scrollbar-track { background: transparent; }
    .ftp-table-area::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .ftp-table-area::-webkit-scrollbar-thumb:hover { background: #475569; }

    .table-ftp {
        color: var(--ftp-text);
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 5px;
    }

    .table-ftp thead th {
        position: sticky;
        top: 0;
        background: var(--ftp-card);
        z-index: 100;
        padding: 12px;
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid var(--ftp-border);
    }

    .table-ftp tbody tr {
        background: rgba(15, 23, 42, 0.4);
        transition: all 0.2s;
    }

    .table-ftp tbody tr:hover {
        background: rgba(56, 189, 248, 0.1);
        transform: scale(1.005);
    }

    .table-ftp td {
        padding: 12px;
        vertical-align: middle;
        border: none;
    }

    .table-ftp tr td:first-child { border-radius: 8px 0 0 8px; }
    .table-ftp tr td:last-child { border-radius: 0 8px 8px 0; }

    /* Links & Icons */
    .file-link {
        color: #f8fafc;
        font-weight: 500;
        text-decoration: none !important;
        display: flex;
        align-items: center;
    }

    .file-link:hover { color: var(--ftp-accent); }
    
    .icon-box {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 12px;
        background: rgba(255,255,255,0.05);
    }

    .btn-action-ftp {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        transition: 0.3s;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-exchange-alt mr-2 text-primary"></i> FTP Explorer</h1>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="ftp-main-card">
                <div class="ftp-header-bar">
                    <h6 class="m-0 text-white"><i class="fas fa-laptop mr-2 text-primary"></i> LOCAL</h6>
                    <?php if ($current_local_path !== "This PC"): ?>
                        <?php 
                            $parent = dirname($current_local_path);
                            $up_link = ($parent == $current_local_path || strlen($current_local_path) <= 3) ? "This PC" : $parent;
                        ?>
                        <a href="?local_dir=<?= urlencode($up_link) ?>&remote=<?= urlencode($remote_path) ?>" class="btn btn-xs btn-outline-light py-0">
                            <i class="fas fa-level-up-alt"></i> Back
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="ftp-search-wrapper">
                    <p class="path-label text-truncate mb-2"><i class="fas fa-terminal mr-2"></i><?= $current_local_path ?></p>
                    <div class="ftp-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="localSearch" class="form-control ftp-search-input" placeholder="Search local files...">
                    </div>
                </div>

                <div class="ftp-table-area">
                    <table class="table-ftp" id="localTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th width="80">Size</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($local_files): foreach ($local_files as $key => $file): 
                                if (!$show_drives && ($file == "." || $file == "..")) continue;
                                if ($show_drives) {
                                    $full_local = $file; $display_name = $key; $isDir = true; $size = "-";
                                    $icon = ($key == 'Desktop') ? 'fa-desktop text-primary' : (($key == 'Documents') ? 'fa-file-invoice text-success' : 'fa-database text-info');
                                } else {
                                    $full_local = rtrim($current_local_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
                                    $display_name = $file; $isDir = is_dir($full_local); $size = $isDir ? "-" : round(@filesize($full_local)/1024, 1)." KB";
                                    $icon = $isDir ? 'fa-folder text-warning' : 'fa-file-alt text-muted';
                                }
                            ?>
                                <tr>
                                    <td>
                                        <a href="?local_dir=<?= urlencode($full_local) ?>&remote=<?= urlencode($remote_path) ?>" 
                                           class="file-link <?= !$isDir ? 'disabled-link' : '' ?>" 
                                           <?= !$isDir ? 'style="pointer-events:none; opacity:0.7;"' : '' ?>>
                                            <div class="icon-box"><i class="fas <?= $icon ?>"></i></div>
                                            <span class="text-truncate" style="max-width: 180px;"><?= $display_name ?></span>
                                        </a>
                                    </td>
                                    <td><small class="text-muted"><?= $size ?></small></td>
                                    <td>
                                        <?php if (!$isDir && !$show_drives): ?>
                                            <a href="upload.php?local=<?= urlencode($full_local) ?>" class="btn btn-primary btn-action-ftp shadow">
                                                <i class="fas fa-upload" style="font-size: 10px;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="ftp-main-card">
                <div class="ftp-header-bar">
                    <h6 class="m-0 text-white"><i class="fas fa-server mr-2 text-success"></i> REMOTE</h6>
                    <?php if ($remote_path != "."): ?>
                        <a href="?remote=<?= urlencode(dirname($remote_path)) ?>&local_dir=<?= urlencode($current_local_path) ?>" class="btn btn-xs btn-outline-light py-0">
                            <i class="fas fa-level-up-alt"></i> Back
                        </a>
                    <?php endif; ?>
                </div>

                <div class="ftp-search-wrapper">
                    <p class="path-label text-truncate mb-2"><i class="fas fa-network-wired mr-2"></i><?= $remote_path ?></p>
                    <div class="ftp-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="remoteSearch" class="form-control ftp-search-input" placeholder="Search remote files...">
                    </div>
                </div>

                <div class="ftp-table-area">
                    <table class="table-ftp" id="remoteTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th width="80">Size</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($remote_files): foreach ($remote_files as $file): 
                                $parts = preg_split("/\s+/", $file, 9); if (count($parts) < 9) continue;
                                $name = $parts[8]; if ($name == "." || $name == "..") continue;
                                $isDir = $parts[0][0] === "d"; $size = $isDir ? "-" : round($parts[4]/1024, 1)." KB";
                                $full_remote = ($remote_path == ".") ? $name : $remote_path . "/" . $name;
                                $icon = $isDir ? 'fa-folder-open text-warning' : 'fa-file-code text-info';
                            ?>
                                <tr>
                                    <td>
                                        <?php if ($isDir): ?>
                                            <a href="?remote=<?= urlencode($full_remote) ?>&local_dir=<?= urlencode($current_local_path) ?>" class="file-link">
                                                <div class="icon-box"><i class="fas <?= $icon ?>"></i></div>
                                                <span class="text-truncate" style="max-width: 180px;"><?= $name ?></span>
                                            </a>
                                        <?php else: ?>
                                            <div class="file-link" style="opacity: 0.8;">
                                                <div class="icon-box"><i class="fas <?= $icon ?>"></i></div>
                                                <span class="text-truncate" style="max-width: 180px;"><?= $name ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= $size ?></small></td>
                                    <td>
                                        <?php if (!$isDir): ?>
                                            <a href="download.php?file=<?= urlencode($full_remote) ?>" class="btn btn-success btn-action-ftp shadow">
                                                <i class="fas fa-download" style="font-size: 10px;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function(){
    // Live Search Local
    $("#localSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#localTable tbody tr").filter(function() {
            $(this).toggle($(this).find('td:first').text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Live Search Remote
    $("#remoteSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#remoteTable tbody tr").filter(function() {
            $(this).toggle($(this).find('td:first').text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<?php
ftp_close($conn_id);
require_once '../layout/_bottom.php';
?>