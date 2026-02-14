<?php
require_once '../helper/ftp_connection.php';

if (isset($_GET['file'])) {

    $file = $_GET['file'];

    if (ftp_delete($conn_id, $file)) {
        header("Location: index.php?msg=File berhasil dihapus");
    } else {
        echo "Gagal hapus file";
    }
}

ftp_close($conn_id);
?>
