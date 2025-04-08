<?php
include 'config.php';
include 'gta_config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("DELETE FROM accounts WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: admin.php");
exit;
?>