<?php
session_start();
include 'config.php';
include 'gta_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/PHPMailer.php';
require 'libs/PHPMailer/SMTP.php';
require 'libs/PHPMailer/Exception.php';

// Redirect kalau belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";

// Ambil data akun
$stmt = $conn->prepare("SELECT Email, VerifyCode FROM accounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$currentEmail = $user['Email'];
$isVerified = is_null($user['VerifyCode']);

function generateVerifyCode($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Proses ubah email
if (isset($_POST['ubah_email'])) {
    $newEmail = $_POST['email'];

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "<p style='color:orange;'>Email tidak valid.</p>";
    } else {
        $newVerifyCode = generateVerifyCode();

        // Update email & VerifyCode
        $update = $conn->prepare("UPDATE accounts SET Email = ?, VerifyCode = ? WHERE Username = ?");
        $update->bind_param("sss", $newEmail, $newVerifyCode, $username);

        if ($update->execute()) {
            // Kirim ulang email verifikasi
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'nitasaswi1602@gmail.com'; // Email pengirim
                $mail->Password = 'nrkboccelcppesf';         // App password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('nitasaswi1602@gmail.com', 'SA-MP Website');
                $mail->addAddress($newEmail, $username);
                $mail->isHTML(true);
                $mail->Subject = 'Verifikasi Ulang Email SA-MP Kamu';
                $mail->Body = "
                    <p>Hai <strong>$username</strong>,</p>
                    <p>Kamu baru saja mengubah email. Klik link berikut untuk verifikasi email baru kamu:</p>
                    <p><a href='http://localhost/WebSAmp/verify.php?code=$newVerifyCode'>Verifikasi Akun</a></p>
                ";

                $mail->send();
                $message = "<p style='color:lightgreen;'>Email berhasil diubah. Verifikasi ulang telah dikirim ke email baru kamu.</p>";
                $currentEmail = $newEmail;
                $isVerified = false;
            } catch (Exception $e) {
                $message = "<p style='color:red;'>Email berhasil diubah, tapi gagal kirim verifikasi: {$mail->ErrorInfo}</p>";
            }
        } else {
            $message = "<p style='color:red;'>Gagal mengubah email: " . $update->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Profil</title>
    <style>
        body {
            font-family: sans-serif;
            background: #1e1e1e;
            color: #fff;
            padding: 50px;
            text-align: center;
        }
        .container {
            background: #2c2c2c;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        input {
            padding: 10px;
            width: 300px;
            margin-bottom: 10px;
        }
        button {
            background: #00aaff;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #008ecc;
        }
        .verified {
            color: lightgreen;
        }
        .not-verified {
            color: orange;
        }
        a {
            color: #00aaff;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Pengaturan Profil</h2>

        <p><strong>Username:</strong> <?= htmlspecialchars($username); ?></p>
        <p><strong>Email Sekarang:</strong> <?= htmlspecialchars($currentEmail); ?></p>
        <p><strong>Status Verifikasi:</strong> 
            <span class="<?= $isVerified ? 'verified' : 'not-verified'; ?>">
                <?= $isVerified ? "✅ Terverifikasi" : "⚠️ Belum Terverifikasi" ?>
            </span>
        </p>

        <form method="POST">
            <input type="email" name="email" value="<?= htmlspecialchars($currentEmail); ?>" required><br>
            <button type="submit" name="ubah_email">Ubah Email</button>
        </form>

        <?= $message; ?>

        <br><a href="dashboard.php">⬅️ Kembali ke Dashboard</a>
    </div>
</body>
</html>
