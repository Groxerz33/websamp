<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "dbwebsamp";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi ke database web gagal: " . $conn->connect_error);
}
?>
