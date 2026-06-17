<?php
// views/owner_header.php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Owner' ?> — GYA Cosmetics</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
body {
  background:
    radial-gradient(ellipse 70% 50% at 80% 10%,  rgba(139,92,246,.22) 0%,transparent 55%),
    radial-gradient(ellipse 50% 60% at 10% 80%,  rgba(244,63,136,.15) 0%,transparent 55%),
    linear-gradient(145deg, #fdf8ff 0%, #f3e8ff 55%, #fce4ec 100%);
  min-height: 100vh;
}
</style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<nav class="sidebar" id="sidebar"
     style="background:linear-gradient(160deg,rgba(30,5,50,.94)0%,rgba(50,8,36,.96)100%);border-right:1px solid rgba(139,92,246,.18);">

  <!-- Sidebar orb override purple -->
  <style>
    #sidebar::before{background:radial-gradient(circle,rgba(139,92,246,.22)0%,transparent 70%)!important;}
    #sidebar::after{background:radial-gradient(circle,rgba(244,63,136,.12)0%,transparent 70%)!important;}
    .nav-link-gya.active{background:linear-gradient(135deg,rgba(139,92,246,.3),rgba(124,58,237,.2))!important;border:1px solid rgba(139,92,246,.35)!important;box-shadow:0 4px 16px rgba(139,92,246,.25)!important;}
  </style>

  <!-- Brand -->
  <div class="sidebar-brand">
    <div class="sidebar-logo" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);box-shadow:0 4px 16px rgba(139,92,246,.5);">
      <i class="bi bi-crown-fill"></i>
    </div>
    <div class="sidebar-brand-name">GYA Cosmetics</div>
    <div class="sidebar-brand-sub">Owner Panel</div>
  </div>

  <!-- Nav -->
  <div style="flex:1;overflow-y:auto;overflow-x:hidden;padding-bottom:8px;">

    <div class="sidebar-section">
      <span class="sidebar-label">Overview</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li><a href="dashboard.php" class="nav-link-gya <?= $current==='dashboard.php'?'active':''?>">
          <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
        </a></li>
      </ul>
    </div>

    <div class="sidebar-section">
      <span class="sidebar-label">Bisnis</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li><a href="laporan.php" class="nav-link-gya <?= $current==='laporan.php'?'active':''?>">
          <i class="bi bi-bar-chart-line nav-icon"></i> Laporan
        </a></li>
        <li><a href="transaksi.php" class="nav-link-gya <?= $current==='transaksi.php'?'active':''?>">
          <i class="bi bi-receipt nav-icon"></i> Transaksi
        </a></li>
        <li><a href="kredit.php" class="nav-link-gya <?= $current==='kredit.php'?'active':''?>">
          <i class="bi bi-credit-card nav-icon"></i> Kredit
          <?php
          global $conn;
          $kn = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
          if($kn>0) echo "<span class='nav-badge'>$kn</span>";
          ?>
        </a></li>
        <li><a href="produk.php" class="nav-link-gya <?= $current==='produk.php'?'active':''?>">
          <i class="bi bi-box-seam nav-icon"></i> Produk
        </a></li>
        <li><a href="supplier.php" class="nav-link-gya <?= $current==='supplier.php'?'active':''?>">
          <i class="bi bi-truck nav-icon"></i> Supplier
        </a></li>
        <li><a href="promo.php" class="nav-link-gya <?= $current==='promo.php'?'active':''?>">
          <i class="bi bi-megaphone nav-icon"></i> Promo
        </a></li>
      </ul>
    </div>

    <div class="sidebar-section">
      <span class="sidebar-label">Sistem</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li><a href="user.php" class="nav-link-gya <?= $current==='user.php'?'active':''?>">
          <i class="bi bi-people nav-icon"></i> Manajemen User
        </a></li>
        <li><a href="activity_log.php" class="nav-link-gya <?= $current==='activity_log.php'?'active':''?>">
          <i class="bi bi-clock-history nav-icon"></i> Activity Log
        </a></li>
        <li><a href="backup.php" class="nav-link-gya <?= $current==='backup.php'?'active':''?>">
          <i class="bi bi-cloud-download nav-icon"></i> Backup Data
        </a></li>
      </ul>
    </div>

  </div>

  <!-- Footer -->
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);">
        <?= strtoupper(substr($_SESSION['nama']??'O',0,1)) ?>
      </div>
      <div>
        <div class="user-name-text"><?= htmlspecialchars($_SESSION['nama']??'') ?></div>
        <div class="user-role-text">👑 Owner</div>
      </div>
    </div>
    <a href="../logout.php" class="btn-logout-gya">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>
</nav>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content" id="mainContent">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px;">
      <button class="btn-toggle-sidebar" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
      <div>
        <div class="topbar-title"><?= $page_title ?? 'Dashboard' ?></div>
        <div class="topbar-sub"><?= date('l, d F Y') ?></div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <div class="topbar-badge" style="background:linear-gradient(135deg,rgba(139,92,246,.12),rgba(124,58,237,.1));border-color:rgba(139,92,246,.25);color:#7c3aed;">
        <i class="bi bi-crown-fill"></i> Owner
      </div>
      <a href="../index.php" class="topbar-badge" style="text-decoration:none;color:inherit;">
        <i class="bi bi-shop"></i> Toko
      </a>
    </div>
  </div>

  <div id="alertArea" style="padding:16px 32px 0;display:none;">
    <?php tampilAlert(); ?>
  </div>

  <div class="page-content">
    <div class="page-inner">