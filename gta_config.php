<?php
$gta_servername = "localhost";
$gta_username = "root";
$gta_password = "";
$gta_database = "dbvrp";

$gtaConn = new mysqli($gta_servername, $gta_username, $gta_password, $gta_database);
if ($gtaConn->connect_error) {
    die("Koneksi ke database GTA gagal: " . $gtaConn->connect_error);
}
?>
