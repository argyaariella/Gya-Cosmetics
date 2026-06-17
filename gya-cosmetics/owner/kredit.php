<?php
// owner/kredit.php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Kredit & Piutang';

$total_piutang = (float)$conn->query("SELECT COALESCE(SUM(sisa_hutang),0) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$total_kredit  = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$total_lunas   = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='lunas'")->fetch_assoc()['n'];
$jt_lewat      = (int)$conn->query("SELECT COUNT(*) as n FROM kredit WHERE status='belum_lunas' AND jatuh_tempo<=CURDATE()")->fetch_assoc()['n'];

$fsts='belum_lunas'; if(isset($_GET['sts'])) $fsts=bersihkan($_GET['sts']);
$per=20;$hal=max(1,(int)($_GET['hal']??1));$off=($hal-1)*$per;
$where=$fsts!=='semua'?"WHERE k.status='$fsts'":'WHERE 1=1';
$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM kredit k $where")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("SELECT k.*,t.kode_transaksi,t.created_at as tgl_trx,p.nama as nm_pel,p.no_hp FROM kredit k LEFT JOIN transaksi t ON k.transaksi_id=t.id LEFT JOIN pelanggan p ON k.pelanggan_id=p.id $where ORDER BY k.created_at DESC LIMIT $per OFFSET $off");

require_once '../views/owner_header.php';
?>
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div><h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Kredit & Piutang</h2><p style="font-size:.84rem;color:#b08fa0;">Pantau tagihan kredit pelanggan</p></div>
</div>
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6"><div class="stat-card" style="border-top:3px solid rgba(245,158,11,.5);"><div class="stat-icon-wrap" style="background:rgba(245,158,11,.15);"><i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.3rem;"></i></div><div class="stat-value"><?=formatRupiah($total_piutang)?></div><div class="stat-label">Total Piutang</div></div></div>
  <div class="col-xl-3 col-md-6"><div class="stat-card" style="border-top:3px solid rgba(244,63,136,.5);"><div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);"><i class="bi bi-credit-card" style="color:#f43f88;font-size:1.3rem;"></i></div><div class="stat-value"><?=$total_kredit?></div><div class="stat-label">Belum Lunas</div></div></div>
  <div class="col-xl-3 col-md-6"><div class="stat-card" style="border-top:3px solid rgba(239,68,68,.5);"><div class="stat-icon-wrap" style="background:rgba(239,68,68,.15);"><i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.3rem;"></i></div><div class="stat-value"><?=$jt_lewat?></div><div class="stat-label">Jatuh Tempo Lewat</div></div></div>
  <div class="col-xl-3 col-md-6"><div class="stat-card" style="border-top:3px solid rgba(16,185,129,.5);"><div class="stat-icon-wrap" style="background:rgba(16,185,129,.15);"><i class="bi bi-check-circle-fill" style="color:#10b981;font-size:1.3rem;"></i></div><div class="stat-value"><?=$total_lunas?></div><div class="stat-label">Sudah Lunas</div></div></div>
</div>
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
  <a href="?sts=belum_lunas" class="btn-gya <?=$fsts==='belum_lunas'?'btn-primary-gya':'btn-glass-gya'?>"><i class="bi bi-hourglass-split"></i> Belum Lunas</a>
  <a href="?sts=lunas" class="btn-gya <?=$fsts==='lunas'?'btn-primary-gya':'btn-glass-gya'?>"><i class="bi bi-check-circle"></i> Lunas</a>
  <a href="?sts=semua" class="btn-gya <?=$fsts==='semua'?'btn-primary-gya':'btn-glass-gya'?>"><i class="bi bi-list"></i> Semua</a>
</div>
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:780px;">
      <thead><tr><th>#</th><th>Kode</th><th>Pelanggan</th><th>Total Hutang</th><th>Sisa Hutang</th><th>Jatuh Tempo</th><th>Status</th><th>Tgl Trx</th></tr></thead>
      <tbody>
        <?php if($rows&&$rows->num_rows>0):$no=$off+1;while($k=$rows->fetch_assoc()):$lewat=$k['status']==='belum_lunas'&&$k['jatuh_tempo']&&$k['jatuh_tempo']<=date('Y-m-d');?>
        <tr style="<?=$lewat?'background:rgba(239,68,68,.03);':''?>">
          <td style="color:#b08fa0;font-size:.79rem;"><?=$no++?></td>
          <td><code style="background:rgba(139,92,246,.08);color:#7c3aed;padding:3px 9px;border-radius:7px;font-size:.78rem;"><?=$k['kode_transaksi']?></code></td>
          <td>
            <div style="font-weight:600;font-size:.85rem;"><?=htmlspecialchars($k['nm_pel']??'Umum')?></div>
            <?php if($k['no_hp']):?><a href="https://wa.me/<?=preg_replace('/[^0-9]/','', $k['no_hp'])?>" target="_blank" style="font-size:.74rem;color:#25d366;text-decoration:none;"><i class="bi bi-whatsapp"></i> <?=$k['no_hp']?></a><?php endif;?>
          </td>
          <td style="font-weight:600;"><?=formatRupiah($k['total_hutang'])?></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;color:<?=$k['sisa_hutang']>0?'#ef4444':'#10b981'?>;"><?=formatRupiah($k['sisa_hutang'])?></td>
          <td style="font-size:.82rem;<?=$lewat?'color:#ef4444;font-weight:700;':''?>"><?=$k['jatuh_tempo']?($lewat?'⚠ ':'').formatTanggal($k['jatuh_tempo']):'—'?></td>
          <td><?=$k['status']==='belum_lunas'?'<span class="badge-gya badge-warning">Belum Lunas</span>':'<span class="badge-gya badge-success">Lunas</span>'?></td>
          <td style="font-size:.79rem;color:#b08fa0;"><?=date('d/m/Y',strtotime($k['tgl_trx']))?></td>
        </tr>
        <?php endwhile;else:?>
        <tr><td colspan="8" class="empty-state"><span class="empty-icon"><i class="bi bi-credit-card"></i></span><div>Tidak ada data kredit</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1):?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(139,92,246,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?=$total_row?> data</span>
    <div class="pagination-gya"><?php for($i=1;$i<=$pages;$i++):?><a href="?hal=<?=$i?>&sts=<?=$fsts?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor;?></div>
  </div>
  <?php endif;?>
</div>
<?php require_once '../views/owner_footer.php'; ?>