<?php
// Fungsi untuk menampilkan SweetAlert dari PHP
function sweetAlert($type, $title, $message) {
    echo "<script>showAlert('$type', '$title', '$message');</script>";
}

function sweetAlertError($message, $title = "Oops...") {
    sweetAlert('error', $title, $message);
}

function sweetAlertSuccess($message, $title = "Berhasil") {
    sweetAlert('success', $title, $message);
}

function sweetAlertWarning($message, $title = "Peringatan") {
    sweetAlert('warning', $title, $message);
}
?>
