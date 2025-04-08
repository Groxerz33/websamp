<?php
session_start();
include 'config.php';
include 'gta_config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Ambil data user
    $stmt = $conn->prepare("SELECT Password, Salt FROM accounts WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $verifyOld = hash('whirlpool', $user['Salt'] . $old);

    if ($verifyOld !== $user['Password']) {
        $message = "❌ Password lama salah!";
    } elseif ($new !== $confirm) {
        $message = "❌ Password baru dan konfirmasi tidak cocok!";
    } else {
        $newHash = hash('whirlpool', $user['Salt'] . $new);
        $update = $conn->prepare("UPDATE accounts SET Password = ? WHERE Username = ?");
        $update->bind_param("ss", $newHash, $username);

        if ($update->execute()) {
            $message = "✅ Password berhasil diganti!";
        } else {
            $message = "❌ Gagal mengganti password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ganti Password</title>
    <style>
        body {
            font-family: sans-serif;
            background: #1e1e1e;
            color: #fff;
            text-align: center;
            padding-top: 50px;
        }
        .form-box {
            background: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
        }
        input {
            padding: 10px;
            margin: 10px;
            width: 300px;
        }
        .btn {
            background: #00aaff;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
        }
        .msg {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Ganti Password</h1>
    <div class="form-box">
        <form method="POST">
            <input type="password" name="old_password" placeholder="Password Lama" required><br>
            <input type="password" name="new_password" placeholder="Password Baru" required><br>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required><br>
            <button type="submit" class="btn">Ganti Password</button>
        </form>
        <?php if ($message): ?>
            <p class="msg"><?= $message; ?></p>
        <?php endif; ?>
    </div>
    <br><br>
    <a href="dashboard.php" style="color:#00aaff;">⬅️ Kembali ke Dashboard</a>
</body>
</html>
