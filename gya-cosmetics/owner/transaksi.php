<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Data Transaksi';

$per=20; $hal=max(1,(int)($_GET['hal']??1)); $off=($hal-1)*$per;
$tgl1=bersihkan($_GET['tgl1']??date('Y-m-01'));
$tgl2=bersihkan($_GET['tgl2']??date('Y-m-d'));
$fsts=bersihkan($_GET['sts']??'');
$ftipe=bersihkan($_GET['tipe']??'');

$where="WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'";
if($fsts)  $where.=" AND t.status_transaksi='$fsts'";
if($ftipe) $where.=" AND t.tipe_penjualan='$ftipe'";

$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM transaksi t $where")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("SELECT t.*,p.nama as nm_pel,u.nama as nm_usr FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id LEFT JOIN users u ON t.user_id=u.id $where ORDER BY t.created_at DESC LIMIT $per OFFSET $off");
$total_pend=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi t $where AND t.status_transaksi!='kredit'")->fetch_assoc()['n'];
$total_kredit_blm=(float)$conn->query("SELECT COALESCE(SUM(k.sisa_hutang),0) as n FROM kredit k JOIN transaksi t ON k.transaksi_id=t.id $where AND k.status='belum_lunas'")->fetch_assoc()['n'];

require_once '../views/owner_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Data Transaksi</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Pantau semua transaksi penjualan toko</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <div class="stat-card" style="padding:12px 16px;min-width:170px;">
      <div class="stat-label">Pendapatan Periode</div>
      <div class="stat-value" style="font-size:1.1rem;"><?=formatRupiah($total_pend)?></div>
    </div>
    <div class="stat-card" style="padding:12px 16px;min-width:150px;">
      <div class="stat-label">Piutang Belum Lunas</div>
      <div class="stat-value" style="font-size:1.05rem;color:#f59e0b;"><?=formatRupiah($total_kredit_blm)?></div>
    </div>
  </div>
</div>

<!-- FILTER -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      <div><label class="form-label-gya">Dari</label><input type="date" name="tgl1" class="form-control-gya" value="<?=$tgl1?>"></div>
      <div><label class="form-label-gya">Sampai</label><input type="date" name="tgl2" class="form-control-gya" value="<?=$tgl2?>"></div>
      <div style="min-width:130px;"><label class="form-label-gya">Status</label>
        <select name="sts" class="form-select-gya">
          <option value="">Semua</option>
          <option value="selesai" <?=$fsts==='selesai'?'selected':''?>>Selesai</option>
          <option value="kredit"  <?=$fsts==='kredit'?'selected':''?>>Kredit</option>
          <option value="lunas"   <?=$fsts==='lunas'?'selected':''?>>Lunas</option>
        </select>
      </div>
      <div style="min-width:130px;"><label class="form-label-gya">Channel</label>
        <select name="tipe" class="form-select-gya">
          <option value="">Semua</option>
          <option value="offline" <?=$ftipe==='offline'?'selected':''?>>Offline</option>
          <option value="online"  <?=$ftipe==='online'?'selected':''?>>Online</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-funnel"></i> Filter</button>
        <a href="transaksi.php" class="btn-gya btn-glass-gya">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:800px;">
      <thead><tr><th>#</th><th>Kode</th><th>Pelanggan</th><th>Admin</th><th>Channel</th><th>Total</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr></thead>
      <tbody>
        <?php if($rows&&$rows->num_rows>0):$no=$off+1;while($t=$rows->fetch_assoc()):?>
        <tr>
          <td style="color:#b08fa0;font-size:.79rem;"><?=$no++?></td>
          <td><code style="background:rgba(139,92,246,.08);color:#7c3aed;padding:3px 9px;border-radius:7px;font-size:.78rem;"><?=$t['kode_transaksi']?></code></td>
          <td style="font-weight:500;font-size:.85rem;"><?=htmlspecialchars($t['nm_pel']??'Umum')?></td>
          <td style="font-size:.82rem;color:#b08fa0;"><?=htmlspecialchars($t['nm_usr']??'')?></td>
          <td><?=$t['tipe_penjualan']==='online'?'<span class="badge-gya badge-purple"><i class="bi bi-globe"></i> Online</span>':'<span class="badge-gya" style="background:rgba(100,100,100,.1);color:#555;border:1px solid rgba(0,0,0,.1);"><i class="bi bi-shop"></i> Offline</span>'?></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;"><?=formatRupiah($t['total_harga'])?></td>
          <td><?=$t['metode_bayar']==='kredit'?'<span class="badge-gya badge-warning">Kredit</span>':'<span class="badge-gya badge-success">Tunai</span>'?></td>
          <td><?php $sc=['selesai'=>'badge-info','kredit'=>'badge-warning','lunas'=>'badge-success'];$sl=['selesai'=>'Selesai','kredit'=>'Kredit','lunas'=>'Lunas'];$st=$t['status_transaksi'];echo "<span class='badge-gya ".($sc[$st]??'badge-info')."'>".$sl[$st]."</span>";?></td>
          <td style="font-size:.79rem;color:#b08fa0;white-space:nowrap;"><?=date('d/m/Y H:i',strtotime($t['created_at']))?></td>
        </tr>
        <?php endwhile;else:?>
        <tr><td colspan="9" class="empty-state"><span class="empty-icon"><i class="bi bi-receipt"></i></span><div>Tidak ada transaksi</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1):?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(139,92,246,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?=$total_row?> transaksi</span>
    <div class="pagination-gya"><?php for($i=1;$i<=$pages;$i++):?><a href="?hal=<?=$i?>&tgl1=<?=$tgl1?>&tgl2=<?=$tgl2?>&sts=<?=$fsts?>&tipe=<?=$ftipe?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor;?></div>
  </div>
  <?php endif;?>
</div>

<?php require_once '../views/owner_footer.php'; ?>