<?php
include 'config.php';
include 'gta_config.php';

$message = "";

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Cek apakah kode verifikasi ada di database
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE VerifyCode = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Set VerifyCode ke NULL → akun jadi terverifikasi
        $update = $conn->prepare("UPDATE accounts SET VerifyCode = NULL WHERE VerifyCode = ?");
        $update->bind_param("s", $code);

        if ($update->execute()) {
            $message = "✅ Akun berhasil diverifikasi. Silakan login dengan Kode: " . htmlspecialchars($code);
        } else {
            $message = "❌ Gagal memverifikasi akun. Silakan coba lagi nanti.";
        }
    } else {
        $message = "❗ Kode verifikasi tidak valid atau sudah pernah digunakan.";
    }
} else {
    $message = "⚠️ Tidak ada kode verifikasi yang dikirim.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Akun</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #1e1e1e;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background-color: #2c2c2c;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            text-align: center;
            max-width: 400px;
        }
        .box h2 {
            margin-bottom: 20px;
        }
        .box p {
            font-size: 16px;
        }
        .box a {
            color: #00aaff;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Verifikasi Akun</h2>
        <p><?= htmlspecialchars($message); ?></p>
        <a href="login.php">⬅️ Kembali ke Login</a>
    </div>
</body>
</html>
