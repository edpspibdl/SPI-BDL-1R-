<?php
require_once '../helper/ftp_connection.php';

if (isset($_GET['local'])) {

    $local_file  = $_GET['local'];
    $remote_path = isset($_GET['remote']) ? $_GET['remote'] : ".";
    $filename    = basename($local_file);

    $remote_file = ($remote_path == ".")
        ? $filename
        : $remote_path . "/" . $filename;

    if (ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY)) {
        header("Location: index.php?remote=" . urlencode($remote_path));
    } else {
        echo "Upload gagal";
    }
}

ftp_close($conn_id);
?>
