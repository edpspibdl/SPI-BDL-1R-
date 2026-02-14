<?php
require_once '../helper/ftp_connection.php';

if (isset($_GET['file'])) {

    $file = $_GET['file'];
    $temp = tempnam(sys_get_temp_dir(), 'ftp');

    if (ftp_get($conn_id, $temp, $file, FTP_BINARY)) {

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
        readfile($temp);

        unlink($temp);
    } else {
        echo "Download gagal";
    }
}

ftp_close($conn_id);
?>
