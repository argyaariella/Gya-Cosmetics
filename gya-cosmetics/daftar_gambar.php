<?php
require 'config/config.php';

$result = $conn->query("SELECT p.id, p.nama_produk, p.brand, p.gambar, k.nama_kategori 
                         FROM produk p 
                         LEFT JOIN kategori k ON p.kategori_id = k.id 
                         ORDER BY k.nama_kategori, p.nama_produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Gambar Produk — GYA Cosmetics</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--rose:#f43f88;--rose-deep:#c2185b;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:#fdf2f8;min-height:100vh;padding:24px;}

.container{max-width:1100px;margin:0 auto;}

h1{font-size:1.8rem;color:var(--rose-deep);margin-bottom:6px;}
.subtitle{color:#9d7a8a;font-size:.9rem;margin-bottom:8px;}
.info-box{background:linear-gradient(135deg,var(--rose),var(--rose-deep));color:#fff;border-radius:14px;padding:18px 24px;margin-bottom:24px;font-size:.88rem;line-height:1.7;}
.info-box b{font-size:.95rem;}
.info-box code{background:rgba(255,255,255,.2);padding:2px 8px;border-radius:6px;font-size:.82rem;}

.kategori-title{font-size:1.1rem;font-weight:700;color:var(--rose-deep);margin:28px 0 12px;padding:8px 16px;background:rgba(244,63,136,.08);border-radius:10px;border-left:4px solid var(--rose);}

table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 16px rgba(244,63,136,.08);margin-bottom:20px;}
thead th{background:rgba(244,63,136,.06);color:var(--rose-deep);font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;padding:12px 16px;text-align:left;border-bottom:2px solid rgba(244,63,136,.12);}
tbody td{padding:10px 16px;font-size:.85rem;color:#4a2040;border-bottom:1px solid rgba(244,63,136,.06);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:rgba(244,63,136,.03);}

.filename{font-family:'Courier New',monospace;font-weight:700;color:var(--rose);background:rgba(244,63,136,.08);padding:3px 10px;border-radius:6px;font-size:.82rem;}
.preview-img{width:50px;height:50px;border-radius:8px;object-fit:cover;border:2px solid rgba(244,63,136,.15);}
.placeholder-img{width:50px;height:50px;border-radius:8px;background:rgba(244,63,136,.08);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:rgba(244,63,136,.3);border:2px dashed rgba(244,63,136,.2);}
.status-ok{color:#10b981;font-weight:700;font-size:.8rem;}
.status-no{color:#ef4444;font-weight:700;font-size:.8rem;}
.brand-tag{font-size:.72rem;color:#b08fa0;font-weight:500;}
.total-bar{background:#fff;border-radius:14px;padding:16px 24px;box-shadow:0 2px 12px rgba(244,63,136,.06);margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.total-item{text-align:center;}
.total-val{font-size:1.4rem;font-weight:700;color:var(--rose-deep);}
.total-label{font-size:.72rem;color:#b08fa0;}
</style>
</head>
<body>
<div class="container">
    <h1>📸 Daftar Gambar Produk</h1>
    <p class="subtitle">Referensi nama file untuk upload gambar tiap produk</p>

    <div class="info-box">
        <b>📁 Cara Upload Gambar Produk:</b><br>
        1. Siapkan foto produk dalam format <code>.jpg</code> / <code>.png</code> / <code>.webp</code><br>
        2. Rename file sesuai nama di kolom <b>"Nama File"</b> di tabel bawah<br>
        3. Copy/upload ke folder: <code>gya-cosmetics/assets/images/</code><br>
        4. Refresh halaman web, gambar otomatis muncul! ✨
    </div>

    <?php
    $total = 0;
    $uploaded = 0;
    $current_kat = '';
    
    // Kumpulkan data per kategori
    $data = [];
    while ($p = $result->fetch_assoc()) {
        $kat = $p['nama_kategori'] ?? 'Tanpa Kategori';
        if (!isset($data[$kat])) $data[$kat] = [];
        $data[$kat][] = $p;
        $total++;
        if (!empty($p['gambar']) && file_exists('assets/images/' . $p['gambar'])) {
            $uploaded++;
        }
    }
    ?>

    <div class="total-bar">
        <div class="total-item">
            <div class="total-val"><?= $total ?></div>
            <div class="total-label">Total Produk</div>
        </div>
        <div class="total-item">
            <div class="total-val"><?= $uploaded ?></div>
            <div class="total-label">Sudah Ada Gambar</div>
        </div>
        <div class="total-item">
            <div class="total-val"><?= $total - $uploaded ?></div>
            <div class="total-label">Belum Upload</div>
        </div>
    </div>

    <?php foreach ($data as $kat => $produk_list): ?>
    <div class="kategori-title"><?= htmlspecialchars($kat) ?> (<?= count($produk_list) ?> produk)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Preview</th>
                <th>Nama Produk</th>
                <th>Nama File</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produk_list as $i => $p): 
            $file_exists = !empty($p['gambar']) && file_exists('assets/images/' . $p['gambar']);
        ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <?php if ($file_exists): ?>
                        <img src="assets/images/<?= htmlspecialchars($p['gambar']) ?>" class="preview-img" alt="">
                    <?php else: ?>
                        <div class="placeholder-img">📷</div>
                    <?php endif; ?>
                </td>
                <td>
                    <?= htmlspecialchars($p['nama_produk']) ?>
                    <br><span class="brand-tag"><?= htmlspecialchars($p['brand'] ?? '') ?></span>
                </td>
                <td><span class="filename"><?= htmlspecialchars($p['gambar'] ?? '-') ?></span></td>
                <td>
                    <?php if ($file_exists): ?>
                        <span class="status-ok">✅ Ada</span>
                    <?php else: ?>
                        <span class="status-no">❌ Belum</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>
</div>
</body>
</html>
