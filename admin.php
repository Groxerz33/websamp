<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';
include 'gta_config.php';

// --- Handle Query Pencarian ---
$where = [];
$params = [];
$types = "";

if (!empty($_GET['q'])) {
    $where[] = "(Username LIKE ? OR Email LIKE ?)";
    $q = "%" . $_GET['q'] . "%";
    $params[] = $q;
    $params[] = $q;
    $types .= "ss";
}

if ($_GET['admin'] !== "" && ($_GET['admin'] === "1" || $_GET['admin'] === "0")) {
    $where[] = "Admin = ?";
    $params[] = $_GET['admin'];
    $types .= "i";
}

if ($_GET['banned'] !== "" && ($_GET['banned'] === "1" || $_GET['banned'] === "0")) {
    $where[] = "Banned = ?";
    $params[] = $_GET['banned'];
    $types .= "i";
}

if ($_GET['verified'] !== "") {
    if ($_GET['verified'] == "1") {
        $where[] = "VerifyCode IS NULL";
    } elseif ($_GET['verified'] == "0") {
        $where[] = "VerifyCode IS NOT NULL";
    }
}

$sql = "SELECT * FROM accounts";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - SA-MP</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #fff; padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background: #222; }
        input, select { padding: 8px; margin: 5px; }
        .btn { padding: 5px 10px; text-decoration: none; background: #007bff; color: white; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>

<h1>Admin Dashboard</h1>
<p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
<a class="btn" href="dashboard.php">Kembali ke Dashboard</a> | 
<a class="btn" href="logout.php">Logout</a>

<h2>Pencarian Akun</h2>
<form method="GET">
    <input type="text" name="q" placeholder="Cari Username / Email" value="<?php echo $_GET['q'] ?? ''; ?>">
    
    <select name="admin">
        <option value="">Admin (Semua)</option>
        <option value="1" <?php if ($_GET['admin'] ?? '' === '1') echo 'selected'; ?>>Admin</option>
        <option value="0" <?php if ($_GET['admin'] ?? '' === '0') echo 'selected'; ?>>Bukan</option>
    </select>
    
    <select name="banned">
        <option value="">Banned (Semua)</option>
        <option value="1" <?php if ($_GET['banned'] ?? '' === '1') echo 'selected'; ?>>Ya</option>
        <option value="0" <?php if ($_GET['banned'] ?? '' === '0') echo 'selected'; ?>>Tidak</option>
    </select>

    <select name="verified">
        <option value="">Verifikasi (Semua)</option>
        <option value="1" <?php if ($_GET['verified'] ?? '' === '1') echo 'selected'; ?>>Terverifikasi</option>
        <option value="0" <?php if ($_GET['verified'] ?? '' === '0') echo 'selected'; ?>>Belum</option>
    </select>

    <button class="btn" type="submit">Cari</button>
</form>

<?php
echo "<h3>Daftar Akun:</h3>";
if ($result->num_rows > 0) {
    echo "<table><tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Admin</th>
        <th>Banned</th>
        <th>Verifikasi</th>
        <th>Aksi</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['ID']}</td>
            <td>{$row['Username']}</td>
            <td>{$row['Email']}</td>
            <td>{$row['Admin']}</td>
            <td>{$row['Banned']}</td>
            <td>" . ($row['VerifyCode'] === null ? "✅" : "❌") . "</td>
            <td>
                <a class='btn' href='edit_user.php?id={$row['ID']}'>Edit</a>
                <a class='btn' href='delete_user.php?id={$row['ID']}' onclick=\"return confirm('Yakin ingin menghapus akun ini?')\">Hapus</a>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada akun ditemukan.</p>";
}
?>

</body>
</html>
