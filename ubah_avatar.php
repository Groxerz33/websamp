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

// Ambil data user
$stmt = $conn->prepare("SELECT Avatar FROM accounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$currentAvatar = $user['Avatar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    
    if ($file['error'] === 0 && in_array($file['type'], $allowed)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid("avatar_") . '.' . $ext;
        $targetPath = 'avatars/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update DB
            $update = $conn->prepare("UPDATE accounts SET Avatar = ? WHERE Username = ?");
            $update->bind_param("ss", $newName, $username);
            $update->execute();

            $message = "✅ Avatar berhasil diubah!";
            $currentAvatar = $newName;
        } else {
            $message = "❌ Gagal mengupload file.";
        }
    } else {
        $message = "❌ Format tidak didukung (hanya .jpg/.jpeg/.png)";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ubah Avatar</title>
    <style>
        body { background: #1e1e1e; color: white; font-family: sans-serif; text-align: center; padding-top: 50px; }
        .form-box { background: #2a2a2a; padding: 30px; border-radius: 10px; display: inline-block; }
        input { margin: 10px; }
        .btn { background: #00aaff; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        .avatar-preview { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Ubah Avatar</h1>
    <div class="form-box">
        <?php if ($currentAvatar): ?>
            <div class="avatar-preview">
                <img src="avatars/<?= htmlspecialchars($currentAvatar); ?>" alt="Avatar" width="120" height="120" style="border-radius: 50%;">
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="avatar" accept="image/png, image/jpeg" required><br>
            <button type="submit" class="btn">Upload Avatar</button>
        </form>
        <?php if ($message): ?>
            <p><?= $message; ?></p>
        <?php endif; ?>
    </div>
    <br><br>
    <a href="dashboard.php" style="color:#00aaff;">⬅️ Kembali ke Dashboard</a>
</body>
</html>
