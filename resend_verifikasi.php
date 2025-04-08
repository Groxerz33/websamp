<?php
session_start();
include 'config.php';
include 'gta_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/PHPMailer.php';
require 'libs/PHPMailer/SMTP.php';
require 'libs/PHPMailer/Exception.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.php';</script>";
    exit;
}

$username = $_SESSION['username'];

// Ambil data user
$stmt = $conn->prepare("SELECT Email, VerifyCode FROM accounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (is_null($user['VerifyCode'])) {
        echo "<script>alert('Akun sudah terverifikasi.'); window.location='dashboard.php';</script>";
        exit;
    }

    $email = $user['Email'];
    $code  = $user['VerifyCode'];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nitasaswi1602@gmail.com'; // ganti dengan emailmu
        $mail->Password   = 'nrkboccelcppesf';         // ganti dengan App Password Gmail kamu
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('nitasaswi1602@gmail.com', 'SA-MP Website');
        $mail->addAddress($email, $username);

        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Akun SA-MP Kamu';
        $mail->Body = "
            <p>Hai <strong>$username</strong>,</p>
            <p>Klik link di bawah ini untuk verifikasi akun kamu:</p>
            <p><a href='http://localhost/WebSAmp/verify.php?code=$code'>Verifikasi Akun</a></p>
            <p>Abaikan jika kamu tidak merasa mendaftar.</p>
        ";

        $mail->send();
        echo "<script>alert('Email verifikasi berhasil dikirim ulang!'); window.location='dashboard.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Gagal mengirim email: {$mail->ErrorInfo}'); window.location='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('User tidak ditemukan.'); window.location='login.php';</script>";
}
?>
