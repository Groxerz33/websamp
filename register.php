<?php
session_start();
include 'config.php';
include 'gta_config.php'; // Pastikan ini adalah koneksi ke database GTA

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/PHPMailer.php';
require 'libs/PHPMailer/SMTP.php';
require 'libs/PHPMailer/Exception.php';

function generateVerifyCode() {
    // Generate kode dengan format OSRP-xxxx
    $randomNumber = rand(1000, 9999); // Generate angka acak 4 digit
    return 'OSRP-' . $randomNumber;
}

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email    = trim($_POST['email']);

    $verifyCode = generateVerifyCode();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $registerDate = date('Y-m-d H:i:s');
    $role = 'user'; // default role

    // Cek apakah username sudah ada di database website
    $check = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color:orange;'>Username sudah digunakan, silakan coba yang lain.</p>";
    } else {
        // Masukkan akun ke database website
        $stmt = $conn->prepare("INSERT INTO accounts (Username, Password, Email, RegisterDate, VerifyCode, Role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashedPassword, $email, $registerDate, $verifyCode, $role);

        if ($stmt->execute()) {
            // Masukkan data ke database GTA (misalnya tabel 'accounts')
            $stmtGTA = $gtaConn->prepare("INSERT INTO accounts (Username, Email, VerifyCode, RegisterDate, WhiteList) VALUES (?, ?, ?, ?, 1)");
            $stmtGTA->bind_param("ssss", $username, $email, $verifyCode, $registerDate);
            
            if ($stmtGTA->execute()) {
                // Kirim email verifikasi
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'nitasaswi1602@gmail.com'; // Ganti sesuai
                    $mail->Password   = 'jvlqpzvmnrorvfdq'; // App Password Gmail
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('nitasaswi1602@gmail.com', 'SA-MP Website');
                    $mail->addAddress($email, $username);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verifikasi Akun SA-MP Kamu';
                    $mail->Body = "
                        <p>Hai <strong>$username</strong>,</p>
                        <p>Klik link di bawah ini untuk verifikasi akun kamu:</p>
                        <p><a href='http://localhost/WebSAmp/verify.php?code=$verifyCode'>Verifikasi Akun</a></p>
                        <p>Abaikan jika kamu tidak merasa mendaftar.</p>
                    ";

                    $mail->SMTPDebug = 0;

                    $mail->send();
                    echo "<p style='color:lightgreen;'>Akun berhasil dibuat! Silakan cek email untuk verifikasi.</p>";
                } catch (Exception $e) {
                    echo "<p style='color:orange;'>Akun dibuat, tapi gagal kirim email: {$mail->ErrorInfo}</p>";
                }
            } else {
                echo "<p style='color:red;'>Gagal memasukkan data ke database GTA: " . $stmtGTA->error . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Gagal membuat akun: " . $stmt->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - SA-MP</title>
    <style>
        body { font-family: sans-serif; background: #1e1e1e; color: #fff; text-align: center; padding-top: 50px; }
        input { padding: 10px; margin: 10px; width: 300px; border: none; border-radius: 4px; }
        .btn { background: #00aaff; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        .btn:hover { background: #008dd1; }
    </style>
</head>
<body>
    <h1>Register Akun</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <button class="btn" type="submit" name="register">Daftar</button>
    </form>
</body>
</html>
