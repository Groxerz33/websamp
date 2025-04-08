<?php
session_start();

// Data dummy, nanti bisa diambil dari server real atau API
$serverIP = "127.0.0.1:7777";
$onlinePlayers = 105;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OLD SCHOOL ROLEPLAY - SA-MP Community</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      color: white;
      overflow-x: hidden;
    }

    /* GIF Background */
    .gif-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      object-fit: cover;
      opacity: 0.7;
    }

    /* Overlay untuk meningkatkan keterbacaan teks */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }

    .container {
      max-width: 960px;
      margin: auto;
      padding: 40px 20px;
      text-align: center;
      position: relative;
    }

    .header {
      background: rgba(15, 17, 23, 0.7);
      border-radius: 20px;
      padding: 80px 20px;
      position: relative;
      backdrop-filter: blur(5px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      margin-bottom: 40px;
    }

    .logo-title {
      font-size: 40px;
      font-weight: bold;
      color: #ffffff;
      text-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
    }

    .logo-title span {
      color: #4dc3ff;
      text-shadow: 0 0 10px #4dc3ff;
    }

    .slogan {
      font-size: 18px;
      margin-top: 12px;
      color: #ddd;
    }

    .buttons {
      margin-top: 30px;
    }

    .buttons a {
      text-decoration: none;
      color: white;
      background-color: #2c88ff;
      padding: 12px 24px;
      margin: 6px;
      border-radius: 8px;
      display: inline-block;
      font-size: 16px;
      transition: 0.3s;
    }

    .buttons a:hover {
      background-color: #1f6dd9;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .info-boxes {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .box {
      background-color: rgba(29, 31, 43, 0.8);
      padding: 20px;
      border-radius: 10px;
      width: 220px;
      box-shadow: 0 0 8px rgba(0,0,0,0.4);
      backdrop-filter: blur(5px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: transform 0.3s;
    }

    .box:hover {
      transform: translateY(-5px);
    }

    .box h3 {
      margin: 10px 0;
      font-size: 26px;
      color: #4dc3ff;
    }

    .status-dot {
      display: inline-block;
      width: 10px;
      height: 10px;
      background-color: #00ff75;
      border-radius: 50%;
      margin-right: 6px;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }

    .footer {
      margin-top: 60px;
      text-align: center;
      color: #aaa;
      font-size: 14px;
      background: rgba(15, 17, 23, 0.7);
      padding: 20px;
      border-radius: 10px;
      backdrop-filter: blur(5px);
    }

    .footer-icons {
      margin-top: 12px;
    }

    .footer-icons img {
      width: 24px;
      height: 24px;
      margin: 0 8px;
      filter: brightness(0) invert(1);
      transition: transform 0.3s;
      cursor: pointer; /* Menambahkan cursor pointer untuk interaksi */
    }

    .footer-icons img:hover {
      transform: scale(1.2);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .header {
        padding: 50px 15px;
      }
      
      .logo-title {
        font-size: 32px;
      }
      
      .info-boxes {
        flex-direction: column;
        align-items: center;
      }
      
      .box {
        width: 80%;
      }
    }
  </style>
</head>
<body>
  <!-- GIF Background -->
  <img src="https://cdn.discordapp.com/attachments/1185851977624727613/1359052937871101962/giphy.gif?ex=67f6142e&is=67f4c2ae&hm=8c85c8eea6bbcbe07370d570369dd2116b4347d08e9a19852aba27e726a6d1e3&" class="gif-background" alt="Background Animation">
  
  <!-- Overlay untuk meningkatkan keterbacaan -->
  <div class="overlay"></div>

  <div class="container">
    <div class="header">
      <div class="logo-title">OLD SCHOOL <span>ROLEPLAY</span></div>
      <div class="slogan">Bangun Hidupmu, Tentukan Jalanmu.</div>
      <div class="buttons">
        <?php if (!isset($_SESSION['username'])): ?>
          <a href="register.php">Daftar</a>
          <a href="login.php">Login</a>
        <?php else: ?>
          <a href="dashboard.php">Dashboard</a>
          <a href="logout.php">Logout</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="info-boxes">
      <div class="box">
        <div><strong>Alamat Server</strong></div>
        <h3><?= $serverIP ?></h3>
        <div><span class="status-dot"></span> Online</div>
      </div>
      <div class="box">
        <div><strong>Pemain Online</strong></div>
        <h3><?= $onlinePlayers ?></h3>
      </div>
    </div>

    <div class="footer">
      &copy; <?= date('Y') ?> OLD SCHOOL. All rights reserved.
      <div class="footer-icons">
        <a href="https://discord.gg/yPgyxmUfAm" target="_blank">
          <img src="https://cdn-icons-png.flaticon.com/512/2111/2111370.png" alt="Discord">
        </a>
      </div>
    </div>
  </div>

  <script>
    // Fallback jika GIF tidak bisa dimuat
    document.querySelector('.gif-background').addEventListener('error', function() {
      document.body.style.background = "linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d)";
      document.querySelector('.overlay').style.display = 'none';
    });
  </script>
</body>
</html>