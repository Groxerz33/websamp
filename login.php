<?php
include 'config.php';
include 'gta_config.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - SA-MP</title>
    <style>
        body {
            font-family: sans-serif;
            background: #2b2b2b;
            color: #fff;
            text-align: center;
            padding-top: 50px;
        }
        input {
            padding: 10px;
            margin: 10px;
            width: 300px;
        }
        .btn {
            background: #ff5555;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .msg {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<h1>Login Akun</h1>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button class="btn" type="submit" name="login">Masuk</button>
</form>

<?php
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data akun dari database website
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Cek verifikasi akun
        if (!is_null($user['VerifyCode'])) {
            echo "<p style='color:orange;'>Akun belum diverifikasi. <a href='resend_verifikasi.php?email=" . urlencode($user['Email']) . "'>Kirim ulang email verifikasi</a></p>";
        }
        // Cek password
        elseif (password_verify($password, $user['Password'])) {
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role']; // jika ada Role
            header("Location: dashboard.php");
            exit;
        }
        else {
            echo "<p class='msg' style='color:red;'>❌ Password salah!</p>";
        }
    } else {
        echo "<p class='msg' style='color:red;'>❌ Akun tidak ditemukan!</p>";
    }
}
?>

</body>
</html>
