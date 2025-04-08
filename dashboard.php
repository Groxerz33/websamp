<?php
session_start();
include 'config.php';      // Koneksi ke database website
include 'gta_config.php';  // Koneksi ke database GTA (oceanrp)

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil data akun dari database website
$stmt = $conn->prepare("SELECT Email, Avatar, Role, VerifyCode FROM accounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek status verifikasi akun
$isVerified = is_null($user['VerifyCode']);

// Ambil data karakter pertama dari tabel 'characters' berdasarkan Username
$charStmt = $gtaConn->prepare("SELECT `Character`, Admin, Money, BankMoney, FROM_UNIXTIME(RegisterDate) AS RegiDate, FROM_UNIXTIME(LastLogin) AS LastOn FROM characters WHERE Username = ? LIMIT 1");
$charStmt->bind_param("s", $username);
$charStmt->execute();
$charResult = $charStmt->get_result();
$charData = $charResult->fetch_assoc();

// Ambil data dari tabel 'accounts' untuk menampilkan statistik
$accountStmt = $gtaConn->prepare("SELECT Username, Email, Admin, VerifyCode FROM accounts WHERE Username = ?");
$accountStmt->bind_param("s", $username);
$accountStmt->execute();
$accountResult = $accountStmt->get_result();
$accountData = $accountResult->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?= htmlspecialchars($username); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            color: #ffffff;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #2c2c2c;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .box {
            background: #3b3b3b;
            padding: 20px;
            margin-top: 15px;
            border-radius: 8px;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .alert-danger {
            background-color: #aa0000;
        }
        .alert-success {
            background-color: #1f6f20;
        }
        a {
            color: #4dc3ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-logout {
            padding: 10px 20px;
            border: none;
            background: #aa0000;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-back {
            padding: 10px 20px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            margin-left: 10px; /* Memberikan jarak antara tombol */
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Selamat datang, <?= htmlspecialchars($username); ?></h2>

    <!-- Tampilkan Avatar -->
    <?php if ($user['Avatar']): ?>
        <img src="avatars/<?= htmlspecialchars($user['Avatar']); ?>" alt="Avatar" class="avatar">
    <?php endif; ?>

    <!-- Notifikasi verifikasi akun -->
    <?php if (!$isVerified): ?>
        <div class="alert alert-danger">
            ⚠️ Akun kamu belum diverifikasi. <a href="resend_verifikasi.php">Kirim ulang verifikasi</a>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            ✅ Akun sudah terverifikasi.
        </div>
    <?php endif; ?>

    <!-- Statistik dari database GTA -->
    <?php if ($charData): ?>
        <div class="box">
            <strong>📊 Statistik Karakter GTA:</strong><br><br>
            <b>Nama Karakter:</b> <?= htmlspecialchars($charData['Character']); ?><br>
            <b>Status Admin:</b> <?= $charData['Admin'] ? 'Ya' : 'Tidak'; ?><br>
            <b>Terdaftar Sejak:</b> <?= htmlspecialchars($charData['RegiDate']); ?><br>
            <b>Login Terakhir:</b> <?= htmlspecialchars($charData['LastOn']); ?><br>
            <b>Uang di Tangan:</b> $<?= number_format($charData['Money']); ?><br>
            <b>Uang di Bank:</b> $<?= number_format($charData['BankMoney']); ?>
        </div>
    <?php else: ?>
        <div class="box alert-danger">
            ⚠️ Data karakter GTA tidak ditemukan.
        </div>
    <?php endif; ?>

    <!-- Statistik Akun -->
    <div class="box">
        <strong>📊 Statistik Akun:</strong><br><br>
        <b>Username:</b> <?= htmlspecialchars($accountData['Username']); ?><br>
        <b>Email:</b> <?= htmlspecialchars($accountData['Email']); ?><br>
        <b>Status Admin:</b> <?= $accountData['Admin'] ? 'Ya' : 'Tidak'; ?><br>
        <b>Kode Verifikasi:</b> <?= htmlspecialchars($accountData['VerifyCode'] ?? 'Belum ada'); ?>
    </div>

    <!-- Menu pengaturan akun -->
    <div class="box">
        <strong>⚙️ Pengaturan Akun:</strong><br><br>
        <a href="ganti_password.php">🔒 Ganti Password</a><br>
        <a href="ubah_avatar.php">🖼️ Ubah Avatar</a><br>
        <?php if ($user['Role'] === 'admin'): ?>
            <a href="admin/">🔧 Panel Admin</a><br>
        <?php endif; ?>
    </div>

    <!-- Tombol Kembali ke Beranda dan Logout -->
    <div style="margin-top: 20px;">
        <form action="index.php" method="get" style="display: inline;">
            <button type="submit" class="btn-back">Kembali ke Beranda</button>
        </form>
        <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</div>
</body>
</html>