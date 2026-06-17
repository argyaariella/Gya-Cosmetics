<?php
require_once 'config/config.php';
$cari  = bersihkan($_GET['cari']??'');
$fkat  = (int)($_GET['kat']??0);
$sort  = bersihkan($_GET['sort']??'terbaru');
$where = "WHERE p.status='aktif'";
if($cari) $where.=" AND p.nama_produk LIKE '%$cari%'";
if($fkat) $where.=" AND p.kategori_id=$fkat";
$order = match($sort){'murah'=>"ORDER BY p.harga_jual_online ASC",'mahal'=>"ORDER BY p.harga_jual_online DESC",'nama'=>"ORDER BY p.nama_produk ASC",default=>"ORDER BY p.created_at DESC"};
$produk   = $conn->query("SELECT p.*,k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id $where $order");
$kategori = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
$promo_r  = $conn->query("SELECT * FROM promo WHERE status='aktif' AND tanggal_selesai>=CURDATE() LIMIT 1");
$promo    = $promo_r ? $promo_r->fetch_assoc() : null;
$total_p  = $produk ? $produk->num_rows : 0;
$total_kat= $kategori ? $kategori->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GYA Cosmetics — Beauty Store Medan</title>
<meta name="description" content="Toko kecantikan terpercaya di Medan. 100+ produk skincare, makeup, bodycare dari brand lokal & Korea.">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--rose:#f43f88;--rose-deep:#c2185b;--lilac:#e879f9;--blush:#fbb6ce;--cream:#fff8f5;--fd:'Cormorant Garamond',serif;--fb:'DM Sans',sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:var(--fb);-webkit-font-smoothing:antialiased;
  background:radial-gradient(ellipse 80% 50% at 10% 0%,rgba(251,182,206,.55)0%,transparent 55%),
             radial-gradient(ellipse 60% 50% at 90% 100%,rgba(232,121,249,.35)0%,transparent 55%),
             linear-gradient(160deg,#fff8fc 0%,#fce4ec 50%,#f3e8ff 100%);
  min-height:100vh;}

/* ── NAVBAR ── */
.navbar-gya{
  background:rgba(255,255,255,.65);
  backdrop-filter:blur(28px) saturate(180%);
  -webkit-backdrop-filter:blur(28px) saturate(180%);
  border-bottom:1px solid rgba(244,63,136,.1);
  padding:14px 0;position:sticky;top:0;z-index:1000;
  box-shadow:0 2px 24px rgba(244,63,136,.07);
}
.nav-logo{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,var(--rose),var(--rose-deep));display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;box-shadow:0 4px 14px rgba(244,63,136,.4);}
.nav-brand-text{font-family:var(--fd);font-size:1.4rem;font-weight:600;color:var(--rose-deep);letter-spacing:.3px;}
.nav-pill{background:rgba(244,63,136,.08);border:1.5px solid rgba(244,63,136,.18);border-radius:50px;padding:7px 16px;font-size:.8rem;font-weight:600;color:var(--rose-deep);text-decoration:none;transition:all .2s;}
.nav-pill:hover{background:rgba(244,63,136,.15);color:var(--rose-deep);}

/* ── HERO ── */
.hero{padding:80px 0 60px;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(244,63,136,.1)0%,transparent 70%);top:-250px;right:-200px;pointer-events:none;animation:floatBg 12s ease-in-out infinite;}
.hero::after{content:'';position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(232,121,249,.12)0%,transparent 70%);bottom:-120px;left:-100px;pointer-events:none;animation:floatBg 9s ease-in-out infinite reverse;}
@keyframes floatBg{0%,100%{transform:translate(0,0);}50%{transform:translate(20px,-20px);}}
.hero-eyebrow{font-size:.75rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--rose);margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.hero-eyebrow::before{content:'';width:28px;height:2px;background:var(--rose);border-radius:1px;}
.hero-title{font-family:var(--fd);font-size:clamp(2.2rem,5.5vw,3.8rem);font-weight:600;color:#1a0a14;line-height:1.12;margin-bottom:18px;}
.hero-title em{color:var(--rose);font-style:italic;}
.hero-desc{font-size:.96rem;color:#9d7a8a;line-height:1.75;max-width:460px;margin-bottom:32px;}
.search-bar{display:flex;max-width:500px;background:rgba(255,255,255,.72);backdrop-filter:blur(16px);border:1.5px solid rgba(244,63,136,.2);border-radius:50px;overflow:hidden;box-shadow:0 6px 28px rgba(244,63,136,.13);transition:all .25s;}
.search-bar:focus-within{border-color:var(--rose);box-shadow:0 6px 28px rgba(244,63,136,.25);}
.search-bar input{flex:1;border:none;background:transparent;padding:14px 22px;font-family:var(--fb);font-size:.9rem;color:#1a0a14;outline:none;}
.search-bar input::placeholder{color:#c4a0b5;}
.search-bar button{background:linear-gradient(135deg,var(--rose),var(--rose-deep));border:none;color:#fff;padding:0 24px;font-size:1rem;cursor:pointer;transition:opacity .2s;}
.search-bar button:hover{opacity:.88;}
.hero-stats{display:flex;gap:24px;flex-wrap:wrap;margin-top:28px;padding-top:24px;border-top:1px solid rgba(244,63,136,.1);}
.hstat-val{font-family:var(--fd);font-size:1.7rem;font-weight:600;color:#1a0a14;line-height:1;}
.hstat-label{font-size:.72rem;color:#b08fa0;margin-top:3px;}

/* Floating cards hero */
.float-card{background:rgba(255,255,255,.72);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.55);border-radius:18px;padding:16px 20px;box-shadow:0 8px 28px rgba(244,63,136,.12);}

/* ── PROMO BANNER ── */
.promo-wrap{background:linear-gradient(135deg,var(--rose)0%,var(--rose-deep)100%);border-radius:22px;padding:22px 30px;display:flex;align-items:center;justify-content:space-between;margin-bottom:40px;box-shadow:0 10px 36px rgba(244,63,136,.32);position:relative;overflow:hidden;}
.promo-wrap::before{content:'✿';position:absolute;font-size:10rem;color:rgba(255,255,255,.06);right:-15px;top:-30px;line-height:1;pointer-events:none;}
.promo-wrap::after{content:'✦';position:absolute;font-size:6rem;color:rgba(255,255,255,.05);left:20px;bottom:-20px;line-height:1;pointer-events:none;}

/* ── FILTER BAR ── */
.filter-bar{background:rgba(255,255,255,.55);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.42);border-radius:18px;padding:16px 20px;margin-bottom:28px;box-shadow:0 4px 18px rgba(244,63,136,.06);}
.chip{display:inline-flex;align-items:center;padding:7px 16px;border-radius:50px;font-size:.79rem;font-weight:600;text-decoration:none;transition:all .22s;border:1.5px solid rgba(244,63,136,.18);background:rgba(255,255,255,.65);color:#7c3f5e;white-space:nowrap;}
.chip:hover,.chip.active{background:linear-gradient(135deg,var(--rose),var(--rose-deep));color:#fff;border-color:transparent;box-shadow:0 4px 14px rgba(244,63,136,.35);}
.sort-select{border:1.5px solid rgba(244,63,136,.2);border-radius:50px;font-size:.79rem;background:rgba(255,255,255,.7);color:#7c3f5e;padding:7px 14px;outline:none;cursor:pointer;font-family:var(--fb);}
.sort-select:focus{border-color:var(--rose);}

/* ── PRODUK CARD ── */
.produk-card{background:rgba(255,255,255,.62);backdrop-filter:blur(18px) saturate(160%);-webkit-backdrop-filter:blur(18px) saturate(160%);border:1px solid rgba(255,255,255,.52);border-radius:22px;overflow:hidden;box-shadow:0 4px 22px rgba(244,63,136,.07);transition:transform .28s cubic-bezier(.4,0,.2,1),box-shadow .28s;height:100%;}
.produk-card:hover{transform:translateY(-7px);box-shadow:0 18px 48px rgba(244,63,136,.18);}
.produk-img{height:190px;background:linear-gradient(135deg,rgba(251,182,206,.3),rgba(232,121,249,.2));display:flex;align-items:center;justify-content:center;font-size:3.8rem;color:rgba(244,63,136,.28);position:relative;overflow:hidden;}
.produk-img img{width:100%;height:100%;object-fit:cover;}
.produk-img::after{content:'';position:absolute;inset:0;background:linear-gradient(to bottom,transparent 55%,rgba(255,255,255,.08));pointer-events:none;}
.produk-body{padding:17px;}
.produk-kat{font-size:.67rem;font-weight:700;color:var(--rose);text-transform:uppercase;letter-spacing:1.3px;margin-bottom:6px;}
.produk-nama{font-family:var(--fd);font-size:1.05rem;font-weight:600;color:#1a0a14;line-height:1.3;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.produk-brand{font-size:.73rem;color:#b08fa0;margin-bottom:10px;}
.produk-harga{font-family:var(--fd);font-size:1.18rem;font-weight:600;color:var(--rose);}
.stok-chip{font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;}
.btn-wa{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:11px;margin-top:12px;background:#25d366;color:#fff;border:none;border-radius:13px;font-family:var(--fb);font-size:.83rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all .22s;box-shadow:0 4px 14px rgba(37,211,102,.3);}
.btn-wa:hover{background:#1ea952;color:#fff;transform:translateY(-1px);box-shadow:0 6px 20px rgba(37,211,102,.4);}
.btn-wa-dis{background:#e9d5e4;color:#b08fa0;cursor:not-allowed;box-shadow:none;}

/* ── EMPTY ── */
.empty-cs{text-align:center;padding:80px 20px;color:#b08fa0;}
.empty-cs i{font-size:4rem;opacity:.22;display:block;margin-bottom:18px;}

/* ── FOOTER ── */
footer{background:linear-gradient(160deg,#1a0514 0%,#2d0a1f 100%);color:rgba(255,255,255,.6);padding:56px 0 24px;margin-top:80px;}
footer .f-brand{font-family:var(--fd);font-size:1.5rem;color:#fff;font-weight:600;}
footer a{color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;}
footer a:hover{color:var(--blush);}
.f-divider{height:1px;background:rgba(255,255,255,.07);margin:28px 0;}
.wa-float{position:fixed;bottom:24px;right:24px;z-index:999;width:54px;height:54px;border-radius:50%;background:#25d366;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;box-shadow:0 6px 24px rgba(37,211,102,.45);text-decoration:none;transition:all .25s;animation:waPulse 2s ease-in-out infinite;}
.wa-float:hover{transform:scale(1.1);color:#fff;box-shadow:0 8px 32px rgba(37,211,102,.55);}
@keyframes waPulse{0%,100%{box-shadow:0 6px 24px rgba(37,211,102,.45);}50%{box-shadow:0 6px 32px rgba(37,211,102,.7);}}

/* Float animations */
@keyframes f1{0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}
@keyframes f2{0%,100%{transform:translateY(0);}50%{transform:translateY(-9px);}}
@keyframes f3{0%,100%{transform:translateY(0);}50%{transform:translateY(-7px);}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-gya">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="index.php" class="text-decoration-none d-flex align-items-center gap-2">
      <div class="nav-logo"><i class="bi bi-flower1"></i></div>
      <span class="nav-brand-text">GYA Cosmetics</span>
    </a>
    <div class="d-flex gap-2 align-items-center">
      <span class="d-none d-md-flex align-items-center gap-1" style="font-size:.78rem;color:#b08fa0;">
        <i class="bi bi-geo-alt-fill" style="color:var(--rose);"></i> Medan
      </span>
      <a href="https://wa.me/<?= WA_NUMBER ?>" target="_blank" class="nav-pill" style="background:rgba(37,211,102,.1);border-color:rgba(37,211,102,.25);color:#1a7a3e;">
        <i class="bi bi-whatsapp"></i> <span class="d-none d-md-inline">Chat WA</span>
      </a>
      <a href="login.php" class="nav-pill">
        <i class="bi bi-lock"></i> <span class="d-none d-md-inline">Staff Login</span>
      </a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="container" style="position:relative;z-index:1;">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <div class="hero-eyebrow">✨ Beauty Store Medan</div>
        <h1 class="hero-title">
          Cantik Itu<br><em>Investasi</em>,<br>Bukan Pengeluaran
        </h1>
        <p class="hero-desc">
          100+ produk kecantikan pilihan dari brand lokal & Korea terpercaya.
          Skincare, makeup, bodycare & aksesoris — semua ada di GYA!
        </p>
        <form method="GET" class="search-bar">
          <input type="text" name="cari" placeholder="Cari produk, brand, kategori..." value="<?= htmlspecialchars($cari) ?>">
          <button type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="hero-stats">
          <div><div class="hstat-val"><?= $total_p ?>+</div><div class="hstat-label">Produk</div></div>
          <div style="width:1px;background:rgba(244,63,136,.15);height:38px;align-self:center;"></div>
          <div><div class="hstat-val"><?= $total_kat ?></div><div class="hstat-label">Kategori</div></div>
          <div style="width:1px;background:rgba(244,63,136,.15);height:38px;align-self:center;"></div>
          <div><div class="hstat-val">100%</div><div class="hstat-label">Original</div></div>
          <div style="width:1px;background:rgba(244,63,136,.15);height:38px;align-self:center;"></div>
          <div><div class="hstat-val">Fast</div><div class="hstat-label">Respon WA</div></div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div style="position:relative;height:360px;">
          <div class="float-card" style="position:absolute;top:0;left:30px;width:190px;animation:f1 4s ease-in-out infinite;">
            <div style="font-size:2.2rem;margin-bottom:8px;">🌸</div>
            <div style="font-family:var(--fd);font-weight:600;color:#1a0a14;font-size:1rem;">Skincare Korea</div>
            <div style="font-size:.73rem;color:#b08fa0;">COSRX · Skin1004 · Anua</div>
            <div style="margin-top:8px;display:flex;gap:5px;">
              <span style="font-size:.65rem;background:rgba(244,63,136,.1);color:var(--rose);padding:2px 7px;border-radius:10px;font-weight:700;">Bestseller</span>
              <span style="font-size:.65rem;background:rgba(16,185,129,.1);color:#059669;padding:2px 7px;border-radius:10px;font-weight:700;">Tersedia</span>
            </div>
          </div>
          <div class="float-card" style="position:absolute;bottom:30px;right:10%;width:170px;animation:f2 5.5s ease-in-out infinite;">
            <div style="font-size:2rem;margin-bottom:8px;">💄</div>
            <div style="font-family:var(--fd);font-weight:600;color:#1a0a14;font-size:.95rem;">Makeup Viral</div>
            <div style="font-size:.72rem;color:#b08fa0;">Romand · 3CE · BLP</div>
          </div>
          <div style="position:absolute;top:5%;left:45%;transform:translate(-50%,-50%);background:linear-gradient(135deg,var(--rose),var(--rose-deep));border-radius:20px;padding:20px;width:155px;box-shadow:0 10px 36px rgba(244,63,136,.38);animation:f3 7s ease-in-out infinite;text-align:center;">
            <div style="font-size:2rem;margin-bottom:6px;">🎀</div>
            <div style="font-weight:700;color:#fff;font-size:.9rem;">Aksesoris</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.75);margin-top:3px;">Claw clip & more</div>
          </div>
          <div class="float-card" style="position:absolute;bottom:10%;left:25%;width:160px;animation:f1 6s ease-in-out infinite 1s;">
            <div style="font-size:1.8rem;margin-bottom:6px;">☀️</div>
            <div style="font-family:var(--fd);font-weight:600;color:#1a0a14;font-size:.9rem;">Sunscreen</div>
            <div style="font-size:.72rem;color:#b08fa0;">SPF 30–50+ PA++++</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container pb-5">

  <!-- PROMO BANNER -->
  <?php if($promo): ?>
  <div class="promo-wrap">
    <div style="position:relative;z-index:1;">
      <div style="font-size:.7rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,.7);margin-bottom:7px;">🎉 Promo Spesial</div>
      <div style="font-family:var(--fd);font-size:1.5rem;font-weight:600;color:#fff;margin-bottom:5px;"><?= htmlspecialchars($promo['judul']) ?></div>
      <?php if($promo['deskripsi']): ?><div style="font-size:.84rem;color:rgba(255,255,255,.75);max-width:380px;"><?= htmlspecialchars($promo['deskripsi']) ?></div><?php endif; ?>
      <?php if($promo['tanggal_selesai']): ?><div style="margin-top:10px;font-size:.74rem;color:rgba(255,255,255,.6);"><i class="bi bi-clock"></i> Berlaku s/d <?= formatTanggal($promo['tanggal_selesai']) ?></div><?php endif; ?>
    </div>
    <?php if($promo['diskon_persen']>0): ?>
    <div style="position:relative;z-index:1;text-align:center;flex-shrink:0;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:16px;padding:14px 24px;backdrop-filter:blur(8px);">
      <div style="font-family:var(--fd);font-size:3.2rem;font-weight:600;color:#fff;line-height:1;"><?= $promo['diskon_persen'] ?>%</div>
      <div style="font-size:.78rem;color:rgba(255,255,255,.75);font-weight:700;letter-spacing:1px;">DISKON</div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- FILTER BAR -->
  <div class="filter-bar">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="d-flex gap-2 flex-wrap" style="flex:1;">
        <a href="?cari=<?=urlencode($cari)?>&sort=<?=$sort?>" class="chip <?=$fkat==0?'active':''?>">
          <i class="bi bi-grid-3x3-gap me-1"></i> Semua
        </a>
        <?php $kategori->data_seek(0); while($k=$kategori->fetch_assoc()): ?>
        <a href="?cari=<?=urlencode($cari)?>&kat=<?=$k['id']?>&sort=<?=$sort?>" class="chip <?=$fkat==$k['id']?'active':''?>">
          <?= htmlspecialchars($k['nama_kategori']) ?>
        </a>
        <?php endwhile; ?>
      </div>
      <select class="sort-select" onchange="window.location='?cari=<?=urlencode($cari)?>&kat=<?=$fkat?>&sort='+this.value">
        <option value="terbaru" <?=$sort==='terbaru'?'selected':''?>>Terbaru</option>
        <option value="murah"   <?=$sort==='murah'?'selected':''?>>Harga ↑</option>
        <option value="mahal"   <?=$sort==='mahal'?'selected':''?>>Harga ↓</option>
        <option value="nama"    <?=$sort==='nama'?'selected':''?>>A–Z</option>
      </select>
    </div>
  </div>

  <!-- GRID PRODUK -->
  <?php if($produk&&$produk->num_rows>0): ?>
  <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
    <?php while($p=$produk->fetch_assoc()):
      $stok_ok  = $p['stok']>0;
      $stok_red = $stok_ok && $p['stok']<=$p['stok_minimum'];
      $stok_lbl = !$stok_ok?'Habis':($stok_red?'Hampir Habis':'Tersedia');
      $stok_bg  = !$stok_ok?'rgba(239,68,68,.1)':($stok_red?'rgba(245,158,11,.1)':'rgba(16,185,129,.1)');
      $stok_c   = !$stok_ok?'#ef4444':($stok_red?'#f59e0b':'#10b981');
      $wa_msg   = urlencode("Halo GYA Cosmetics 👋\n\nSaya tertarik dengan produk:\n*{$p['nama_produk']}*\n\nApakah masih tersedia? Berapa harganya? 😊");
    ?>
    <div class="col">
      <div class="produk-card" style="cursor: pointer;" onclick="showProductDetail(htmlspecialchars_decode('<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>'))">
        <div class="produk-img">
          <?php if(!empty($p['gambar'])&&file_exists('assets/images/'.$p['gambar'])): ?>
            <img src="assets/images/<?=htmlspecialchars($p['gambar'])?>" alt="<?=htmlspecialchars($p['nama_produk'])?>">
          <?php else: ?>
            <i class="bi bi-bag-heart-fill"></i>
          <?php endif; ?>
        </div>
        <div class="produk-body">
          <div class="produk-kat"><?=htmlspecialchars($p['nama_kategori'])?></div>
          <div class="produk-nama"><?=htmlspecialchars($p['nama_produk'])?></div>
          <?php if($p['brand']): ?><div class="produk-brand"><?=htmlspecialchars($p['brand'])?></div><?php endif; ?>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;margin-bottom:4px;">
            <div class="produk-harga"><?=formatRupiah($p['harga_jual_online'])?></div>
            <span class="stok-chip" style="background:<?=$stok_bg?>;color:<?=$stok_c?>;">
              <i class="bi bi-circle-fill" style="font-size:.4rem;"></i><?=$stok_lbl?>
            </span>
          </div>
          <?php if($stok_ok): ?>
          <a href="https://wa.me/<?=WA_NUMBER?>?text=<?=$wa_msg?>" target="_blank" class="btn-wa" onclick="event.stopPropagation();">
            <i class="bi bi-whatsapp"></i> Beli via WhatsApp
          </a>
          <?php else: ?>
          <div class="btn-wa btn-wa-dis"><i class="bi bi-x-circle"></i> Stok Habis</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <?php else: ?>
  <div class="empty-cs">
    <i class="bi bi-search"></i>
    <h5 style="font-family:var(--fd);font-size:1.5rem;margin-bottom:8px;color:#7c3f5e;">Produk Tidak Ditemukan</h5>
    <p style="font-size:.87rem;margin-bottom:20px;">Coba kata kunci lain atau pilih kategori berbeda</p>
    <a href="index.php" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--rose),var(--rose-deep));color:#fff;padding:11px 24px;border-radius:50px;text-decoration:none;font-size:.86rem;font-weight:600;box-shadow:0 4px 14px rgba(244,63,136,.35);">
      <i class="bi bi-arrow-left"></i> Lihat Semua Produk
    </a>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- MODAL DETAIL PRODUK -->
<!-- ============================================ -->
<div class="modal fade" id="modalDetailProduk" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:24px; overflow:hidden; border:none; box-shadow:0 20px 50px rgba(0,0,0,0.15);">
      <button type="button" class="btn-close" data-bs-dismiss="modal" style="position:absolute; top:20px; right:20px; z-index:10; background-color:white; opacity:1; border-radius:50%; padding:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);"></button>
      
      <div class="row g-0">
        <!-- Kolom Gambar -->
        <div class="col-md-5" style="background:#fdf2f8; display:flex; align-items:center; justify-content:center; padding:30px;">
          <img id="detailImg" src="" alt="" style="width:100%; max-height:400px; object-fit:contain; border-radius:12px; display:none;">
          <div id="detailImgPlaceholder" style="font-size:4rem; color:rgba(244,63,136,.3); display:none;"><i class="bi bi-bag-heart-fill"></i></div>
        </div>
        
        <!-- Kolom Info -->
        <div class="col-md-7">
          <div class="modal-body" style="padding:40px 30px;">
            <div id="detailKategori" style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--rose); margin-bottom:8px;">Kategori</div>
            <h4 id="detailNama" style="font-family:var(--fd); font-weight:600; color:#1a0a14; line-height:1.3; margin-bottom:12px;">Nama Produk</h4>
            <div id="detailBrand" style="font-size:.85rem; color:#b08fa0; margin-bottom:20px;">Brand</div>
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid rgba(244,63,136,.1);">
              <div id="detailHarga" style="font-size:1.6rem; font-weight:700; color:var(--rose-deep);">Rp 0</div>
              <div id="detailStokBadge"></div>
            </div>
            
            <h6 style="font-family:var(--fd); font-weight:600; color:#1a0a14; font-size:.95rem; margin-bottom:8px;">Deskripsi</h6>
            <p id="detailDeskripsi" style="font-size:.87rem; color:#6b4c5d; line-height:1.6; margin-bottom:20px;">-</p>
            
            <h6 style="font-family:var(--fd); font-weight:600; color:#1a0a14; font-size:.95rem; margin-bottom:8px;">Cara Pakai</h6>
            <p id="detailCaraPakai" style="font-size:.87rem; color:#6b4c5d; line-height:1.6; margin-bottom:30px;">-</p>
            
            <a href="#" id="detailBtnWa" target="_blank" class="btn-wa" style="width:100%; text-align:center; padding:14px; font-size:1rem;">
              <i class="bi bi-whatsapp"></i> Beli Sekarang via WhatsApp
            </a>
            <div id="detailBtnWaDis" class="btn-wa btn-wa-dis" style="width:100%; text-align:center; padding:14px; font-size:1rem; display:none;">
              <i class="bi bi-x-circle"></i> Stok Habis
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function htmlspecialchars_decode(string) {
  var map = { '&amp;': '&', '&lt;': '<', '&gt;': '>', '&quot;': '"', '&#039;': "'" };
  return string.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) { return map[m]; });
}

function showProductDetail(dataStr) {
  try {
    const p = JSON.parse(dataStr);
    
    // Set text
    document.getElementById('detailNama').innerText = p.nama_produk;
    document.getElementById('detailKategori').innerText = p.nama_kategori || 'Produk';
    document.getElementById('detailBrand').innerText = p.brand ? 'Brand: ' + p.brand : '';
    document.getElementById('detailDeskripsi').innerText = p.deskripsi || 'Tidak ada deskripsi.';
    document.getElementById('detailCaraPakai').innerText = p.cara_pakai || '-';
    
    // Format Rupiah
    const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
    document.getElementById('detailHarga').innerText = formatter.format(p.harga_jual_online);
    
    // Stok Badge (Parsing ke integer untuk menghindari error komparasi string, misal "20" <= "5")
    let stok = parseInt(p.stok, 10) || 0;
    let stok_min = parseInt(p.stok_minimum, 10) || 0;
    
    let stok_ok = stok > 0;
    let stok_red = stok_ok && stok <= stok_min;
    let stok_lbl = !stok_ok ? 'Habis' : (stok_red ? 'Hampir Habis' : 'Tersedia');
    let stok_bg = !stok_ok ? 'rgba(239,68,68,.1)' : (stok_red ? 'rgba(245,158,11,.1)' : 'rgba(16,185,129,.1)');
    let stok_c = !stok_ok ? '#ef4444' : (stok_red ? '#f59e0b' : '#10b981');
    
    document.getElementById('detailStokBadge').innerHTML = `<span class="stok-chip" style="background:${stok_bg};color:${stok_c};padding:6px 12px;font-size:.8rem;"><i class="bi bi-circle-fill" style="font-size:.5rem;margin-right:6px;"></i>${stok_lbl}</span>`;
    
    // Gambar
    const imgEl = document.getElementById('detailImg');
    const phEl = document.getElementById('detailImgPlaceholder');
    if(p.gambar) {
      imgEl.src = 'assets/images/' + p.gambar;
      imgEl.style.display = 'block';
      phEl.style.display = 'none';
    } else {
      imgEl.style.display = 'none';
      phEl.style.display = 'block';
    }
    
    // Tombol WA
    const btnWa = document.getElementById('detailBtnWa');
    const btnWaDis = document.getElementById('detailBtnWaDis');
    if(stok_ok) {
      let wa_msg = `Halo GYA Cosmetics 👋\n\nSaya tertarik dengan produk:\n*${p.nama_produk}*\n\nApakah masih tersedia? Berapa harganya? 😊`;
      btnWa.href = `https://wa.me/<?=WA_NUMBER?>?text=${encodeURIComponent(wa_msg)}`;
      btnWa.style.display = 'inline-block';
      btnWaDis.style.display = 'none';
    } else {
      btnWa.style.display = 'none';
      btnWaDis.style.display = 'inline-block';
    }
    
    // Tampilkan Modal
    var myModal = new bootstrap.Modal(document.getElementById('modalDetailProduk'));
    myModal.show();
  } catch (e) {
    console.error("Gagal parse JSON produk", e);
  }
}
</script>

<!-- CONTACT & LOCATION SECTION -->
<div class="container" style="margin-top:70px; margin-bottom:70px;" id="lokasi-toko">
  <div style="background:rgba(255,255,255,.7);backdrop-filter:blur(15px);border:1px solid rgba(255,255,255,.8);border-radius:24px;padding:40px;box-shadow:0 15px 35px rgba(244,63,136,.06);">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <h3 style="font-family:var(--fd);font-weight:700;font-size:2rem;color:#1a0a14;margin-bottom:12px;">Kunjungi GYA Cosmetics</h3>
        <p style="color:#7c3f5e;font-size:.95rem;line-height:1.7;margin-bottom:28px;">Belanja langsung ke toko kami untuk mencoba *tester* makeup & skincare incaranmu! Admin kami siap bantu pilih *shade* yang pas buat kulitmu.</p>
        
        <div style="display:flex;flex-direction:column;gap:18px;">
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(244,63,136,.1);display:flex;align-items:center;justify-content:center;color:var(--rose);font-size:1.15rem;flex-shrink:0;"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <div style="font-weight:700;color:#1a0a14;font-size:.92rem;margin-bottom:4px;">Lokasi Toko</div>
              <div style="font-size:.85rem;color:#7c3f5e;line-height:1.5;">Jl. Pancing I Martubung<br>Kec. Medan Labuhan, Kota Medan</div>
            </div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(244,63,136,.1);display:flex;align-items:center;justify-content:center;color:var(--rose);font-size:1.15rem;flex-shrink:0;"><i class="bi bi-clock-fill"></i></div>
            <div>
              <div style="font-weight:700;color:#1a0a14;font-size:.92rem;margin-bottom:4px;">Jam Operasional</div>
              <div style="font-size:.85rem;color:#7c3f5e;line-height:1.5;">Senin - Sabtu: 08.00 - 20.00 WIB<br>Minggu & Hari Libur: Tutup</div>
            </div>
          </div>
        </div>
        
        <div style="margin-top:35px;">
          <a href="https://wa.me/<?=WA_NUMBER?>" target="_blank" class="btn-primary-gya" style="display:inline-flex;align-items:center;gap:10px;padding:12px 28px;text-decoration:none;">
            <i class="bi bi-whatsapp"></i> Chat Admin Toko
          </a>
        </div>
      </div>
      
      <div class="col-lg-7">
        <div style="border-radius:20px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,.08);height:380px;">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15926.685934177265!2d98.66531383794353!3d3.6603816155998186!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303132e49c71ea45%3A0xa5a507cb0ce40c31!2sMartubung%2C%20Medan%20Labuhan%2C%20Medan%20City%2C%20North%20Sumatra!5e0!3m2!1sen!2sid!4v1707576594321!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <div style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,var(--rose),var(--rose-deep));display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.05rem;"><i class="bi bi-flower1"></i></div>
          <span class="f-brand">GYA Cosmetics</span>
        </div>
        <p style="font-size:.84rem;line-height:1.75;">Toko kecantikan terpercaya di Medan. Produk 100% original dari brand lokal & Korea pilihan.</p>
        <div style="margin-top:16px;display:flex;gap:8px;">
          <a href="https://wa.me/<?=WA_NUMBER?>" target="_blank" style="width:36px;height:36px;border-radius:10px;background:rgba(37,211,102,.15);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#25d366;border:1px solid rgba(37,211,102,.2);"><i class="bi bi-whatsapp"></i></a>
          <a href="#" style="width:36px;height:36px;border-radius:10px;background:rgba(244,63,136,.15);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--rose);border:1px solid rgba(244,63,136,.2);"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
      <div class="col-md-4">
        <h6 style="color:#fff;margin-bottom:16px;font-family:var(--fd);font-size:1.05rem;">Lokasi & Kontak</h6>
        <div style="font-size:.84rem;line-height:2.3;">
          <div><i class="bi bi-geo-alt-fill" style="color:var(--rose);margin-right:7px;"></i>Jl. Pancing I Martubung, Medan Labuhan</div>
          <div><i class="bi bi-whatsapp" style="color:#25d366;margin-right:7px;"></i><a href="https://wa.me/<?=WA_NUMBER?>" target="_blank">Chat WhatsApp Kami</a></div>
          <div><i class="bi bi-clock" style="color:var(--rose);margin-right:7px;"></i>Senin–Sabtu: 08.00–20.00 WIB</div>
        </div>
      </div>
      <div class="col-md-4">
        <h6 style="color:#fff;margin-bottom:16px;font-family:var(--fd);font-size:1.05rem;">Kategori Produk</h6>
        <div style="display:flex;flex-wrap:wrap;gap:7px;">
          <?php
          $kategori->data_seek(0);
          while($k=$kategori->fetch_assoc()):?>
          <a href="?kat=<?=$k['id']?>" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:5px 12px;font-size:.75rem;color:rgba(255,255,255,.6);"><?=htmlspecialchars($k['nama_kategori'])?></a>
          <?php endwhile;?>
        </div>
      </div>
    </div>
    <div class="f-divider"></div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <p style="font-size:.77rem;margin:0;">© 2026 GYA Cosmetics — Sistem Informasi by Kelompok 5 · Universitas Sumatera Utara</p>
      <a href="login.php" style="font-size:.77rem;"><i class="bi bi-shield-lock"></i> Staff Login</a>
    </div>
  </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/<?=WA_NUMBER?>?text=<?=urlencode('Halo GYA Cosmetics 👋 Saya mau tanya-tanya produk kecantikan!')?>" target="_blank" class="wa-float" title="Chat WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>