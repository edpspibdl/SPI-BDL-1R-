<?php

$ftp_server = "172.20.28.30";     // contoh: 192.168.1.10
$ftp_user   = "ftpigrho";
$ftp_pass   = "Xsd1-ftp1grh0X";
$ftp_port   = 21;

$conn_id = ftp_connect($ftp_server, $ftp_port, 10);

if (!$conn_id) {
    die("Tidak bisa connect ke FTP server");
}

$login = ftp_login($conn_id, $ftp_user, $ftp_pass);

if (!$login) {
    die("Login FTP gagal");
}

// Passive mode (WAJIB biasanya untuk hosting)
ftp_pasv($conn_id, true);

?>
