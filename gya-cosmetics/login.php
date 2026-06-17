<?php
require_once 'config/config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'owner' ? 'owner/dashboard.php' : 'admin/dashboard.php'));
    exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = bersihkan($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $stmt = $conn->prepare("SELECT id, nama, username, password, role, status FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$user || $user['status'] === 'nonaktif') {
            $error = $user ? 'Akun Anda dinonaktifkan. Hubungi owner.' : 'Username atau password salah!';
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            simpanLog($user['id'], 'login', 'Login berhasil');
            header('Location: ' . ($user['role'] === 'owner' ? 'owner/dashboard.php' : 'admin/dashboard.php'));
            exit();
        } else { $error = 'Username atau password salah!'; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GYA Cosmetics — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{--rose:#f43f88;--rose-deep:#c2185b;--lilac:#e879f9;--blush:#fbb6ce;--fd:'Cormorant Garamond',serif;--fb:'DM Sans',sans-serif;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:var(--fb);-webkit-font-smoothing:antialiased;}
body{
  min-height:100vh;
  background:
    radial-gradient(ellipse 80% 70% at 15%  5%, rgba(251,182,206,.70) 0%,transparent 55%),
    radial-gradient(ellipse 60% 55% at 85% 95%, rgba(232,121,249,.50) 0%,transparent 55%),
    radial-gradient(ellipse 50% 60% at 80% 10%, rgba(244, 63,136,.30) 0%,transparent 55%),
    radial-gradient(ellipse 40% 50% at  5% 90%, rgba(251,146, 60,.20) 0%,transparent 55%),
    linear-gradient(145deg,#fff0f8 0%,#fce4ec 45%,#f3e8ff 100%);
  display:flex;align-items:center;justify-content:center;padding:24px;overflow:hidden;position:relative;
}
.blob{position:fixed;border-radius:50%;pointer-events:none;z-index:0;}
.b1{width:700px;height:700px;background:radial-gradient(circle,rgba(244,63,136,.15) 0%,transparent 65%);top:-200px;right:-200px;animation:bf 9s ease-in-out infinite;}
.b2{width:500px;height:500px;background:radial-gradient(circle,rgba(232,121,249,.18) 0%,transparent 65%);bottom:-150px;left:-120px;animation:bf 12s ease-in-out infinite reverse;}
.b3{width:300px;height:300px;background:radial-gradient(circle,rgba(251,146,60,.12) 0%,transparent 65%);top:50%;left:50%;animation:bf 7s ease-in-out infinite 2s;}
@keyframes bf{0%,100%{transform:translate(0,0) scale(1);}33%{transform:translate(20px,-30px) scale(1.04);}66%{transform:translate(-15px,20px) scale(.96);}}

.wrap{
  position:relative;z-index:1;
  display:grid;grid-template-columns:1fr 1fr;
  width:100%;max-width:960px;
  border-radius:28px;overflow:hidden;
  box-shadow:0 32px 100px rgba(100,10,60,.22),0 0 0 1px rgba(255,255,255,.5);
  animation:fu .6s cubic-bezier(.4,0,.2,1) both;
}
@keyframes fu{from{opacity:0;transform:translateY(32px) scale(.97);}to{opacity:1;transform:translateY(0) scale(1);}}

/* LEFT */
.pl{
  background:linear-gradient(145deg,rgba(26,5,18,.93) 0%,rgba(50,8,36,.96) 100%);
  backdrop-filter:blur(20px);padding:52px 44px;
  display:flex;flex-direction:column;justify-content:space-between;
  position:relative;overflow:hidden;
}
.pl::before{content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 80% 50% at 50% 0%,rgba(244,63,136,.25) 0%,transparent 60%),
             radial-gradient(ellipse 60% 60% at 100% 100%,rgba(232,121,249,.2) 0%,transparent 60%);
  pointer-events:none;}
.plc{position:relative;z-index:1;}
.logo{width:54px;height:54px;border-radius:16px;background:linear-gradient(135deg,var(--rose),var(--rose-deep));
  display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;
  box-shadow:0 8px 24px rgba(244,63,136,.5);margin-bottom:28px;position:relative;}
.logo::after{content:'';position:absolute;inset:-1px;border-radius:17px;
  background:linear-gradient(135deg,rgba(255,255,255,.25),transparent 60%);pointer-events:none;}
.bh{font-family:var(--fd);font-size:2.2rem;font-weight:600;color:#fff;line-height:1.2;margin-bottom:8px;}
.bh em{color:var(--blush);font-style:italic;}
.bs{font-size:.85rem;color:rgba(255,255,255,.45);line-height:1.7;max-width:260px;}
.feats{margin-top:40px;display:flex;flex-direction:column;gap:12px;}
.feat{display:flex;align-items:center;gap:11px;background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:11px 14px;transition:background .2s;}
.feat:hover{background:rgba(255,255,255,.09);}
.fi{width:32px;height:32px;flex-shrink:0;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.95rem;}
.ft{font-size:.8rem;color:rgba(255,255,255,.65);}
.ft strong{color:rgba(255,255,255,.85);font-weight:600;display:block;font-size:.83rem;margin-bottom:1px;}
.pf{position:relative;z-index:1;}
.tag{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:7px 13px;
  font-size:.72rem;color:rgba(255,255,255,.45);}
.tag i{color:var(--rose);}

/* RIGHT */
.pr{
  background:rgba(255,255,255,.72);
  backdrop-filter:blur(32px) saturate(200%);-webkit-backdrop-filter:blur(32px) saturate(200%);
  padding:52px 44px;display:flex;flex-direction:column;justify-content:center;position:relative;
}
.pr::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,rgba(244,63,136,.5),transparent);}
.fh{font-family:var(--fd);font-size:1.9rem;font-weight:600;color:#1a0a14;margin-bottom:6px;}
.fsub{font-size:.82rem;color:#b08fa0;margin-bottom:32px;}

.err{background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.22);border-radius:12px;
  padding:12px 16px;font-size:.83rem;color:#9f1239;display:flex;align-items:center;gap:9px;
  margin-bottom:20px;animation:shake .4s cubic-bezier(.36,.07,.19,.97) both;}
@keyframes shake{0%,100%{transform:translateX(0);}20%{transform:translateX(-6px);}40%{transform:translateX(6px);}60%{transform:translateX(-4px);}80%{transform:translateX(4px);}}

.fw{margin-bottom:18px;}
.fl{display:block;font-size:.78rem;font-weight:600;color:#7c3f5e;margin-bottom:7px;letter-spacing:.3px;}
.iw{position:relative;display:flex;align-items:center;}
.ii{position:absolute;left:14px;color:#b08fa0;font-size:.95rem;pointer-events:none;transition:color .2s;}
.fi2{position:absolute;right:14px;background:none;border:none;cursor:pointer;color:#b08fa0;font-size:.9rem;padding:4px;transition:color .2s;}
.fi2:hover{color:var(--rose);}
.inp{
  width:100%;padding:12px 44px 12px 42px;
  background:rgba(255,255,255,.6);border:1.5px solid rgba(244,63,136,.18);border-radius:12px;
  font-family:var(--fb);font-size:.88rem;color:#1a0a14;outline:none;transition:all .22s;
}
.inp::placeholder{color:#c4a0b5;}
.inp:focus{border-color:var(--rose);background:rgba(255,255,255,.9);
  box-shadow:0 0 0 4px rgba(244,63,136,.1),0 2px 12px rgba(244,63,136,.1);}
.iw:focus-within .ii{color:var(--rose);}

.sbtn{
  width:100%;padding:13px;
  background:linear-gradient(135deg,var(--rose) 0%,var(--rose-deep) 100%);
  color:#fff;border:none;border-radius:12px;
  font-family:var(--fb);font-size:.9rem;font-weight:600;cursor:pointer;letter-spacing:.3px;
  transition:all .25s cubic-bezier(.4,0,.2,1);
  box-shadow:0 4px 16px rgba(244,63,136,.4);
  display:flex;align-items:center;justify-content:center;gap:9px;
  position:relative;overflow:hidden;margin-top:8px;
}
.sbtn::before{content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,.15),transparent);opacity:0;transition:opacity .2s;}
.sbtn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(244,63,136,.5);}
.sbtn:hover::before{opacity:1;}
.sbtn:active{transform:translateY(0);}

.demo{margin-top:24px;background:rgba(244,63,136,.05);border:1px dashed rgba(244,63,136,.25);
  border-radius:12px;padding:14px 16px;font-size:.76rem;color:#9d5a78;line-height:1.9;}
.demo strong{color:var(--rose-deep);font-weight:700;}
.slink{text-align:center;margin-top:20px;font-size:.79rem;color:#b08fa0;}
.slink a{color:var(--rose-deep);text-decoration:none;font-weight:600;transition:color .2s;}
.slink a:hover{color:var(--rose);}

@media(max-width:768px){.wrap{grid-template-columns:1fr;}.pl{display:none;}.pr{padding:40px 28px;}}
</style>
</head>
<body>
<div class="blob b1"></div>
<div class="blob b2"></div>
<div class="blob b3"></div>

<div class="wrap">
  <!-- LEFT -->
  <div class="pl">
    <div class="plc">
      <div class="logo"><i class="bi bi-flower1"></i></div>
      <div class="bh">GYA<br><em>Cosmetics</em></div>
      <p class="bs">Sistem manajemen toko kecantikan — kelola produk, stok, transaksi & laporan dalam satu platform.</p>
      <div class="feats">
        <div class="feat">
          <div class="fi" style="background:rgba(244,63,136,.2);"><i class="bi bi-box-seam" style="color:#fbb6ce;"></i></div>
          <div class="ft"><strong>Manajemen Stok Real-time</strong>Update otomatis setiap transaksi</div>
        </div>
        <div class="feat">
          <div class="fi" style="background:rgba(232,121,249,.2);"><i class="bi bi-receipt" style="color:#e879f9;"></i></div>
          <div class="ft"><strong>Transaksi Tunai & Kredit</strong>Kelola piutang dengan mudah</div>
        </div>
        <div class="feat">
          <div class="fi" style="background:rgba(251,146,60,.2);"><i class="bi bi-bar-chart" style="color:#fb923c;"></i></div>
          <div class="ft"><strong>Laporan & Analitik</strong>Grafik penjualan & keuntungan</div>
        </div>
        <div class="feat">
          <div class="fi" style="background:rgba(52,211,153,.2);"><i class="bi bi-globe" style="color:#34d399;"></i></div>
          <div class="ft"><strong>Online & Offline Terintegrasi</strong>Satu database semua channel</div>
        </div>
      </div>
    </div>
    <div class="pf">
      <div class="tag"><i class="bi bi-geo-alt-fill"></i>Jl. Pancing I Martubung, Medan Labuhan</div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="pr">
    <div class="fh">Selamat Datang 👋</div>
    <p class="fsub">Masuk ke panel manajemen GYA Cosmetics</p>

    <?php if(!empty($error)):?>
    <div class="err"><i class="bi bi-exclamation-circle-fill"></i><?=htmlspecialchars($error)?></div>
    <?php endif;?>

    <form method="POST" id="lf">
      <div class="fw">
        <label class="fl" for="un">Username</label>
        <div class="iw">
          <i class="bi bi-person ii"></i>
          <input type="text" name="username" id="un" class="inp" placeholder="Masukkan username"
            value="<?=htmlspecialchars($_POST['username']??'')?>" autocomplete="username" required autofocus>
        </div>
      </div>
      <div class="fw">
        <label class="fl" for="pw">Password</label>
        <div class="iw">
          <i class="bi bi-lock ii"></i>
          <input type="password" name="password" id="pw" class="inp" placeholder="Masukkan password" required>
          <button type="button" class="fi2" onclick="tp()"><i class="bi bi-eye-fill" id="ei"></i></button>
        </div>
      </div>
      <button type="submit" class="sbtn" id="sb">
        <i class="bi bi-box-arrow-in-right"></i>
        <span id="st">Masuk ke Sistem</span>
      </button>
    </form>

    <div class="demo">
      <div style="font-weight:700;color:#c2185b;margin-bottom:6px;"><i class="bi bi-info-circle"></i> Akun Demo</div>
      👑 Owner &nbsp;→&nbsp; user: <strong>owner</strong> &nbsp;|&nbsp; pass: <strong>password</strong><br>
      🛠️ Admin &nbsp;→&nbsp; user: <strong>admin</strong> &nbsp;|&nbsp; pass: <strong>password</strong>
    </div>
    <div class="slink"><a href="index.php"><i class="bi bi-shop"></i> Lihat Katalog Toko</a></div>
  </div>
</div>

<script>
function tp(){const i=document.getElementById('pw'),e=document.getElementById('ei');i.type=i.type==='password'?'text':'password';e.className=i.type==='text'?'bi bi-eye-slash-fill':'bi bi-eye-fill';}
document.getElementById('lf').addEventListener('submit',function(){const b=document.getElementById('sb'),t=document.getElementById('st');b.disabled=true;b.style.opacity='.75';t.textContent='Memproses...';});
document.addEventListener('mousemove',function(e){const x=(e.clientX/window.innerWidth-.5)*20,y=(e.clientY/window.innerHeight-.5)*20;document.querySelector('.b1').style.transform=`translate(${x*.5}px,${y*.5}px)`;document.querySelector('.b2').style.transform=`translate(${-x*.3}px,${-y*.3}px)`;});
</script>
</body>
</html>