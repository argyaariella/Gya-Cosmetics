<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Kredit & Piutang';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'lunas') {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE kredit SET status='lunas', tanggal_lunas=CURDATE(), total_bayar=total_hutang, sisa_hutang=0 WHERE id=$id");
    $conn->query("UPDATE transaksi SET status_transaksi='lunas', total_bayar=total_harga WHERE id=(SELECT transaksi_id FROM kredit WHERE id=$id)");
    simpanLog($_SESSION['user_id'], 'lunas_kredit', "Kredit ID:$id dilunasi");
    setAlert('Kredit berhasil ditandai LUNAS! 🎉', 'success');
    header('Location: kredit.php'); exit();
}

$total_piutang = (float)$conn->query("SELECT COALESCE(SUM(sisa_hutang),0) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$total_kredit  = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$total_lunas   = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='lunas'")->fetch_assoc()['n'];
$jt_lewat      = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas' AND jatuh_tempo<=CURDATE()")->fetch_assoc()['n'];

$fsts = bersihkan($_GET['sts'] ?? 'belum_lunas');
$per  = 15; $hal = max(1,(int)($_GET['hal']??1)); $off = ($hal-1)*$per;
$where = $fsts !== 'semua' ? "WHERE k.status='$fsts'" : "WHERE 1=1";
$total_row = (int)$conn->query("SELECT COUNT(*) as n FROM kredit k $where")->fetch_assoc()['n'];
$pages     = max(1, ceil($total_row/$per));
$rows      = $conn->query("
    SELECT k.*, t.kode_transaksi, t.tipe_penjualan, t.created_at as tgl_trx,
           p.nama as nm_pel, p.no_hp
    FROM kredit k
    LEFT JOIN transaksi t ON k.transaksi_id=t.id
    LEFT JOIN pelanggan p ON k.pelanggan_id=p.id
    $where ORDER BY k.created_at DESC LIMIT $per OFFSET $off
");

require_once '../views/admin_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Kredit & Piutang</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Kelola tagihan kredit pelanggan GYA Cosmetics</p>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(245,158,11,.4);">
      <div class="stat-icon-wrap" style="background:rgba(245,158,11,.15);"><i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= formatRupiah($total_piutang) ?></div>
      <div class="stat-label">Total Piutang</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.4);">
      <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);"><i class="bi bi-credit-card" style="color:#f43f88;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= $total_kredit ?></div>
      <div class="stat-label">Tagihan Belum Lunas</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(239,68,68,.4);">
      <div class="stat-icon-wrap" style="background:rgba(239,68,68,.15);"><i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= $jt_lewat ?></div>
      <div class="stat-label">Jatuh Tempo Lewat</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(16,185,129,.4);">
      <div class="stat-icon-wrap" style="background:rgba(16,185,129,.15);"><i class="bi bi-check-circle-fill" style="color:#10b981;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= $total_lunas ?></div>
      <div class="stat-label">Sudah Lunas</div>
    </div>
  </div>
</div>

<!-- TAB FILTER -->
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
  <a href="?sts=belum_lunas" class="btn-gya <?= $fsts==='belum_lunas'?'btn-primary-gya':'btn-glass-gya' ?>"><i class="bi bi-hourglass-split"></i> Belum Lunas <?= $total_kredit>0?"<span style='background:rgba(255,255,255,.3);padding:1px 7px;border-radius:10px;font-size:.72rem;margin-left:4px;'>$total_kredit</span>":'' ?></a>
  <a href="?sts=lunas"       class="btn-gya <?= $fsts==='lunas'?'btn-primary-gya':'btn-glass-gya' ?>"><i class="bi bi-check-circle"></i> Sudah Lunas</a>
  <a href="?sts=semua"       class="btn-gya <?= $fsts==='semua'?'btn-primary-gya':'btn-glass-gya' ?>"><i class="bi bi-list-ul"></i> Semua</a>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:820px;">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode Transaksi</th>
          <th>Pelanggan</th>
          <th>Total Hutang</th>
          <th>Sisa Hutang</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
          <th>Tgl Transaksi</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows && $rows->num_rows > 0): $no = $off+1; while ($k = $rows->fetch_assoc()):
          $lewat = $k['status']==='belum_lunas' && $k['jatuh_tempo'] && $k['jatuh_tempo'] <= date('Y-m-d');
        ?>
        <tr style="<?= $lewat ? 'background:rgba(239,68,68,.025);' : '' ?>">
          <td style="color:#b08fa0;font-size:.79rem;"><?= $no++ ?></td>
          <td><code style="background:rgba(244,63,136,.08);color:#c2185b;padding:3px 9px;border-radius:7px;font-size:.78rem;"><?= htmlspecialchars($k['kode_transaksi']) ?></code></td>
          <td>
            <div style="font-weight:600;font-size:.86rem;"><?= htmlspecialchars($k['nm_pel'] ?? 'Pelanggan Umum') ?></div>
            <?php if ($k['no_hp']): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/','', $k['no_hp']) ?>" target="_blank" style="font-size:.73rem;color:#25d366;text-decoration:none;display:flex;align-items:center;gap:4px;">
              <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($k['no_hp']) ?>
            </a>
            <?php endif; ?>
          </td>
          <td style="font-weight:600;"><?= formatRupiah($k['total_hutang']) ?></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;color:<?= $k['sisa_hutang']>0?'#ef4444':'#10b981' ?>;"><?= formatRupiah($k['sisa_hutang']) ?></td>
          <td style="font-size:.83rem;<?= $lewat?'color:#ef4444;font-weight:700;':'' ?>">
            <?= $k['jatuh_tempo'] ? ($lewat?'⚠️ ':'').formatTanggal($k['jatuh_tempo']) : '—' ?>
          </td>
          <td>
            <?php if ($k['status']==='belum_lunas'): ?>
              <span class="badge-gya badge-warning"><i class="bi bi-clock"></i> Belum Lunas</span>
            <?php else: ?>
              <span class="badge-gya badge-success"><i class="bi bi-check-circle"></i> Lunas</span>
            <?php endif; ?>
          </td>
          <td style="font-size:.79rem;color:#b08fa0;white-space:nowrap;"><?= date('d/m/Y', strtotime($k['tgl_trx'])) ?></td>
          <td style="text-align:center;">
            <?php if ($k['status']==='belum_lunas'): ?>
            <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Tandai kredit ini sebagai LUNAS?')">
              <input type="hidden" name="aksi" value="lunas">
              <input type="hidden" name="id" value="<?= $k['id'] ?>">
              <button type="submit" class="btn-gya btn-primary-gya btn-sm-gya">
                <i class="bi bi-check2-circle"></i> Lunas
              </button>
            </form>
            <?php else: ?>
              <span style="font-size:.77rem;color:#10b981;font-weight:600;">
                <i class="bi bi-check-circle-fill"></i>
                <?= $k['tanggal_lunas'] ? date('d/m/Y', strtotime($k['tanggal_lunas'])) : '—' ?>
              </span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="9" class="empty-state"><span class="empty-icon"><i class="bi bi-credit-card"></i></span><div>Tidak ada data kredit</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(244,63,136,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?= $total_row ?> data kredit</span>
    <div class="pagination-gya">
      <?php for ($i=1;$i<=$pages;$i++): ?><a href="?hal=<?=$i?>&sts=<?=$fsts?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once '../views/admin_footer.php'; ?>