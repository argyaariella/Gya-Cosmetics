<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Activity Log';

$per=20;$hal=max(1,(int)($_GET['hal']??1));$off=($hal-1)*$per;
$fuid=(int)($_GET['uid']??0);
$fakt=bersihkan($_GET['akt']??'');
$where="WHERE 1=1";
if($fuid) $where.=" AND al.user_id=$fuid";
if($fakt) $where.=" AND al.aktivitas='$fakt'";
$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM activity_log al $where")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("SELECT al.*,u.nama,u.role FROM activity_log al LEFT JOIN users u ON al.user_id=u.id $where ORDER BY al.created_at DESC LIMIT $per OFFSET $off");
$users=$conn->query("SELECT id,nama,role FROM users ORDER BY nama");

// Stats
$total_login=$conn->query("SELECT COUNT(*) as n FROM activity_log WHERE aktivitas='login'")->fetch_assoc()['n'];
$total_trx=$conn->query("SELECT COUNT(*) as n FROM activity_log WHERE aktivitas='tambah_transaksi'")->fetch_assoc()['n'];
$total_log=$conn->query("SELECT COUNT(*) as n FROM activity_log")->fetch_assoc()['n'];

require_once '../views/owner_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Activity Log</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Riwayat lengkap semua aktivitas user di sistem</p>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(139,92,246,.5);">
      <div class="stat-icon-wrap" style="background:rgba(139,92,246,.15);"><i class="bi bi-activity" style="color:#7c3aed;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= number_format($total_log) ?></div>
      <div class="stat-label">Total Log Tercatat</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(16,185,129,.5);">
      <div class="stat-icon-wrap" style="background:rgba(16,185,129,.15);"><i class="bi bi-box-arrow-in-right" style="color:#10b981;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= number_format($total_login) ?></div>
      <div class="stat-label">Total Login</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.5);">
      <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);"><i class="bi bi-cart-plus" style="color:#f43f88;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?= number_format($total_trx) ?></div>
      <div class="stat-label">Total Input Transaksi</div>
    </div>
  </div>
</div>

<!-- FILTER -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      <div style="min-width:200px;">
        <label class="form-label-gya">Filter User</label>
        <select name="uid" class="form-select-gya">
          <option value="0">Semua User</option>
          <?php while($u=$users->fetch_assoc()): ?>
          <option value="<?=$u['id']?>" <?=$fuid==$u['id']?'selected':''?>><?=htmlspecialchars($u['nama'])?> (<?=$u['role']?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div style="min-width:180px;">
        <label class="form-label-gya">Filter Aktivitas</label>
        <select name="akt" class="form-select-gya">
          <option value="">Semua Aktivitas</option>
          <?php
          $akts=['login','logout','tambah_transaksi','tambah_produk','edit_produk','hapus_produk','barang_masuk','lunas_kredit','tambah_user','edit_user','backup_database'];
          foreach($akts as $a): ?>
          <option value="<?=$a?>" <?=$fakt===$a?'selected':''?>><?=ucfirst(str_replace('_',' ',$a))?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-funnel"></i> Filter</button>
        <a href="activity_log.php" class="btn-gya btn-glass-gya">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:700px;">
      <thead><tr><th>#</th><th>User</th><th>Aktivitas</th><th>Keterangan</th><th>IP Address</th><th>Waktu</th></tr></thead>
      <tbody>
        <?php if($rows&&$rows->num_rows>0):$no=$off+1;while($l=$rows->fetch_assoc()):
          $icmap=['login'=>['bi-box-arrow-in-right','#10b981'],'logout'=>['bi-box-arrow-left','#94a3b8'],'tambah_transaksi'=>['bi-cart-plus','#f43f88'],'lunas_kredit'=>['bi-check2-circle','#059669'],'tambah_produk'=>['bi-plus-circle','#7c3aed'],'edit_produk'=>['bi-pencil','#3b82f6'],'hapus_produk'=>['bi-trash','#ef4444'],'barang_masuk'=>['bi-box-arrow-in-down','#8b5cf6'],'tambah_user'=>['bi-person-plus','#f43f88'],'backup_database'=>['bi-cloud-download','#10b981']];
          [$ic,$cl]=$icmap[$l['aktivitas']]??['bi-activity','#b08fa0'];
        ?>
        <tr>
          <td style="color:#b08fa0;font-size:.79rem;"><?=$no++?></td>
          <td>
            <div style="display:flex;align-items:center;gap:9px;">
              <div style="width:32px;height:32px;border-radius:9px;background:<?=$l['role']==='owner'?'rgba(139,92,246,.15)':'rgba(244,63,136,.15)'?>;display:flex;align-items:center;justify-content:center;font-weight:700;color:<?=$l['role']==='owner'?'#7c3aed':'#f43f88'?>;font-size:.8rem;flex-shrink:0;"><?=strtoupper(substr($l['nama']??'?',0,1))?></div>
              <div>
                <div style="font-weight:600;font-size:.83rem;"><?=htmlspecialchars($l['nama']??'Deleted')?></div>
                <div style="font-size:.7rem;color:#b08fa0;"><?=ucfirst($l['role']??'')?></div>
              </div>
            </div>
          </td>
          <td><span style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:<?=$cl?>;"><i class="bi <?=$ic?>"></i><?=htmlspecialchars($l['aktivitas'])?></span></td>
          <td style="font-size:.81rem;color:#7c3f5e;max-width:220px;"><?=htmlspecialchars($l['keterangan']??'—')?></td>
          <td><code style="font-size:.74rem;color:#b08fa0;background:rgba(0,0,0,.04);padding:2px 7px;border-radius:5px;"><?=htmlspecialchars($l['ip_address']??'—')?></code></td>
          <td style="font-size:.79rem;color:#b08fa0;white-space:nowrap;"><?=date('d/m/Y H:i:s',strtotime($l['created_at']))?></td>
        </tr>
        <?php endwhile;else: ?>
        <tr><td colspan="6" class="empty-state"><span class="empty-icon"><i class="bi bi-clock-history"></i></span><div>Belum ada log aktivitas</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1): ?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(139,92,246,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?=$total_row?> log aktivitas</span>
    <div class="pagination-gya">
      <?php for($i=1;$i<=$pages;$i++): ?><a href="?hal=<?=$i?>&uid=<?=$fuid?>&akt=<?=urlencode($fakt)?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once '../views/owner_footer.php'; ?>