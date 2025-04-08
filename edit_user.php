<?php
include 'config.php';
include 'gta_config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $admin = $_POST['admin'];
    $banned = $_POST['banned'];
    $whitelist = $_POST['whitelist'];
    $volunteer = $_POST['volunteer'];

    $stmt = $conn->prepare("UPDATE accounts SET Username=?, Email=?, Admin=?, Banned=?, WhiteList=?, Volunteer=? WHERE ID=?");
    $stmt->bind_param("ssiiiii", $username, $email, $admin, $banned, $whitelist, $volunteer, $id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM accounts WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Akun</title>
    <style>
        body { font-family: sans-serif; background: #1d1d1d; color: white; text-align: center; padding-top: 40px; }
        input, select { padding: 10px; margin: 10px; width: 300px; }
        .btn { padding: 10px 20px; background: #ffaa00; border: none; color: white; cursor: pointer; }
    </style>
</head>
<body>
<h1>Edit Akun ID: <?php echo $user['ID']; ?></h1>
<form method="POST">
    <input type="text" name="username" value="<?= htmlspecialchars($user['Username']) ?>" required><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>"><br>
    <select name="admin">
        <option value="0" <?= $user['Admin'] == 0 ? 'selected' : '' ?>>User</option>
        <option value="1" <?= $user['Admin'] == 1 ? 'selected' : '' ?>>Admin</option>
    </select><br>
    <select name="banned">
        <option value="0" <?= $user['Banned'] == 0 ? 'selected' : '' ?>>Aktif</option>
        <option value="1" <?= $user['Banned'] == 1 ? 'selected' : '' ?>>Banned</option>
    </select><br>
    <select name="whitelist">
        <option value="0" <?= $user['WhiteList'] == 0 ? 'selected' : '' ?>>Tidak</option>
        <option value="1" <?= $user['WhiteList'] == 1 ? 'selected' : '' ?>>Ya</option>
    </select><br>
    <select name="volunteer">
        <option value="0" <?= $user['Volunteer'] == 0 ? 'selected' : '' ?>>Tidak</option>
        <option value="1" <?= $user['Volunteer'] == 1 ? 'selected' : '' ?>>Ya</option>
    </select><br>
    <button class="btn" type="submit" name="update">Simpan Perubahan</button>
</form>
<a href="admin.php">Kembali ke Admin Panel</a>
</body>
</html>