<?php
// views/admin_header.php — Premium Glassmorphism Sidebar
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Admin' ?> — GYA Cosmetics</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
body{background:
  radial-gradient(ellipse 70% 50% at 80% 10%,  rgba(251,182,206,.35) 0%,transparent 55%),
  radial-gradient(ellipse 50% 60% at 10% 80%,  rgba(232,121,249,.20) 0%,transparent 55%),
  linear-gradient(145deg,#fff8fc 0%,#fce4ec 60%,#f3e8ff 100%);
  min-height:100vh;}
</style>
</head>
<body>

<!-- ══════════════════════════════
     SIDEBAR
══════════════════════════════ -->
<nav class="sidebar" id="sidebar">

  <!-- Brand -->
  <div class="sidebar-brand">
    <div class="sidebar-logo"><i class="bi bi-flower1"></i></div>
    <div class="sidebar-brand-name">GYA Cosmetics</div>
    <div class="sidebar-brand-sub">Admin Panel</div>
  </div>

  <!-- Nav scroll area -->
  <div style="flex:1;overflow-y:auto;overflow-x:hidden;padding-bottom:8px;">

    <!-- Dashboard -->
    <div class="sidebar-section">
      <span class="sidebar-label">Overview</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link-gya <?= $current==='dashboard.php'?'active':'' ?>">
            <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
          </a>
        </li>
      </ul>
    </div>

    <!-- Produk & Stok -->
    <div class="sidebar-section">
      <span class="sidebar-label">Produk & Inventori</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li class="nav-item">
          <a href="produk.php" class="nav-link-gya <?= $current==='produk.php'?'active':'' ?>">
            <i class="bi bi-box-seam nav-icon"></i> Produk
          </a>
        </li>
        <li class="nav-item">
          <a href="kategori.php" class="nav-link-gya <?= $current==='kategori.php'?'active':'' ?>">
            <i class="bi bi-tags nav-icon"></i> Kategori
          </a>
        </li>
        <li class="nav-item">
          <a href="supplier.php" class="nav-link-gya <?= $current==='supplier.php'?'active':'' ?>">
            <i class="bi bi-truck nav-icon"></i> Supplier
          </a>
        </li>
        <li class="nav-item">
          <a href="barang_masuk.php" class="nav-link-gya <?= $current==='barang_masuk.php'?'active':'' ?>">
            <i class="bi bi-box-arrow-in-down nav-icon"></i> Barang Masuk
          </a>
        </li>
        <li class="nav-item">
          <a href="retur.php" class="nav-link-gya <?= $current==='retur.php'?'active':'' ?>">
            <i class="bi bi-arrow-return-left nav-icon"></i> Retur & Rusak
          </a>
        </li>
      </ul>
    </div>

    <!-- Transaksi -->
    <div class="sidebar-section">
      <span class="sidebar-label">Penjualan</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li class="nav-item">
          <a href="transaksi.php" class="nav-link-gya <?= $current==='transaksi.php'?'active':'' ?>">
            <i class="bi bi-cart3 nav-icon"></i> Transaksi
          </a>
        </li>
        <li class="nav-item">
          <a href="kredit.php" class="nav-link-gya <?= $current==='kredit.php'?'active':'' ?>">
            <i class="bi bi-credit-card nav-icon"></i> Kredit / Piutang
            <?php
            // Badge jumlah kredit belum lunas
            global $conn;
            $kr = $conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas'");
            $kn = $kr ? (int)$kr->fetch_assoc()['n'] : 0;
            if($kn > 0) echo "<span class='nav-badge'>$kn</span>";
            ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="pelanggan.php" class="nav-link-gya <?= $current==='pelanggan.php'?'active':'' ?>">
            <i class="bi bi-people nav-icon"></i> Pelanggan
          </a>
        </li>
      </ul>
    </div>

    <!-- Laporan & Lainnya -->
    <div class="sidebar-section">
      <span class="sidebar-label">Laporan</span>
      <ul style="list-style:none;padding:0;margin:0;">
        <li class="nav-item">
          <a href="laporan.php" class="nav-link-gya <?= $current==='laporan.php'?'active':'' ?>">
            <i class="bi bi-bar-chart-line nav-icon"></i> Laporan
          </a>
        </li>
        <li class="nav-item">
          <a href="promo.php" class="nav-link-gya <?= $current==='promo.php'?'active':'' ?>">
            <i class="bi bi-megaphone nav-icon"></i> Promo
          </a>
        </li>
      </ul>
    </div>

  </div><!-- end scroll -->

  <!-- Footer -->
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama']??'A',0,1)) ?></div>
      <div>
        <div class="user-name-text"><?= htmlspecialchars($_SESSION['nama']??'') ?></div>
        <div class="user-role-text">Admin</div>
      </div>
    </div>
    <a href="../logout.php" class="btn-logout-gya">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>

</nav>

<!-- ══════════════════════════════
     MAIN CONTENT
══════════════════════════════ -->
<div class="main-content" id="mainContent">

  <!-- TOPBAR -->
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px;">
      <button class="btn-toggle-sidebar" onclick="toggleSidebar()" id="sidebarToggle">
        <i class="bi bi-list"></i>
      </button>
      <div>
        <div class="topbar-title"><?= $page_title ?? 'Dashboard' ?></div>
        <div class="topbar-sub"><?= date('l, d F Y') ?></div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <div class="topbar-badge">
        <i class="bi bi-shield-fill-check"></i> Admin
      </div>
      <a href="../index.php" class="topbar-badge" style="text-decoration:none;color:inherit;" title="Lihat Toko">
        <i class="bi bi-shop"></i> Toko
      </a>
    </div>
  </div>

  <!-- ALERT FLASH -->
  <div id="alertArea" style="padding:16px 32px 0; display:none;">
    <?php tampilAlert(); ?>
  </div>

  <!-- PAGE CONTENT START -->
  <div class="page-content">
    <div class="page-inner">