<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Laporan';

$tgl1 = bersihkan($_GET['tgl1'] ?? date('Y-m-01'));
$tgl2 = bersihkan($_GET['tgl2'] ?? date('Y-m-d'));

$total_pend  = (float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at) BETWEEN '$tgl1' AND '$tgl2' AND status_transaksi!='kredit'")->fetch_assoc()['n'];
$total_trx   = (int)$conn->query("SELECT COUNT(*) as n FROM transaksi WHERE DATE(created_at) BETWEEN '$tgl1' AND '$tgl2'")->fetch_assoc()['n'];
$total_kredit= (float)$conn->query("SELECT COALESCE(SUM(k.total_hutang),0) as n FROM kredit k JOIN transaksi t ON k.transaksi_id=t.id WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'")->fetch_assoc()['n'];
$total_modal = (float)$conn->query("SELECT COALESCE(SUM(dt.jumlah*p.harga_beli),0) as n FROM detail_transaksi dt JOIN produk p ON dt.produk_id=p.id JOIN transaksi t ON dt.transaksi_id=t.id WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2' AND t.status_transaksi!='kredit'")->fetch_assoc()['n'];
$keuntungan  = max(0, $total_pend - $total_modal);

$harian = $conn->query("
    SELECT DATE(t.created_at) as tgl,
           COUNT(*) as jml,
           SUM(t.total_harga) as pend,
           COALESCE(SUM(dt.jumlah*p.harga_beli),0) as modal
    FROM transaksi t
    LEFT JOIN detail_transaksi dt ON t.id=dt.transaksi_id
    LEFT JOIN produk p ON dt.produk_id=p.id
    WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'
    AND t.status_transaksi!='kredit'
    GROUP BY DATE(t.created_at)
    ORDER BY tgl DESC
");

$terlaris = $conn->query("
    SELECT p.nama_produk, p.brand, k.nama_kategori,
           SUM(dt.jumlah) as terjual, SUM(dt.subtotal) as omzet
    FROM detail_transaksi dt
    JOIN produk p ON dt.produk_id=p.id
    LEFT JOIN kategori k ON p.kategori_id=k.id
    JOIN transaksi t ON dt.transaksi_id=t.id
    WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'
    GROUP BY dt.produk_id
    ORDER BY terjual DESC LIMIT 10
");

$per_kat = $conn->query("
    SELECT k.nama_kategori, COUNT(DISTINCT t.id) as jml_trx, SUM(dt.subtotal) as omzet
    FROM detail_transaksi dt
    JOIN produk p ON dt.produk_id=p.id
    JOIN kategori k ON p.kategori_id=k.id
    JOIN transaksi t ON dt.transaksi_id=t.id
    WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'
    GROUP BY k.id ORDER BY omzet DESC
");

// Grafik 7 hari
$g_label=[]; $g_pend=[];
for($i=6;$i>=0;$i--){
    $d=date('Y-m-d',strtotime("-{$i} days"));
    $g_label[]=date('d/m',strtotime("-{$i} days"));
    $g_pend[]=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at)='$d' AND status_transaksi!='kredit'")->fetch_assoc()['n'];
}

require_once '../views/admin_header.php';
?>
<style>
@media print {
  /* Sembunyikan elemen yang tidak perlu dicetak */
  .sidebar, .top-header, .btn-gya, form, .modal-overlay, #sidebarOverlay { display: none !important; }
  /* Full width untuk area utama */
  .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
  .page-content { padding: 0 !important; }
  /* Atur background card menjadi putih agar rapi di kertas */
  .content-card, .stat-card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
  body { background: white !important; }
}
</style>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Laporan & Analitik</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Data penjualan & keuntungan GYA Cosmetics</p>
  </div>
  <div>
    <button onclick="window.print()" class="btn-gya btn-primary-gya"><i class="bi bi-printer"></i> Cetak / Backup PDF</button>
  </div>
</div>

<!-- FILTER -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:18px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      <div><label class="form-label-gya">Dari Tanggal</label><input type="date" name="tgl1" class="form-control-gya" value="<?=$tgl1?>"></div>
      <div><label class="form-label-gya">Sampai Tanggal</label><input type="date" name="tgl2" class="form-control-gya" value="<?=$tgl2?>"></div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-funnel"></i> Tampilkan</button>
        <a href="?tgl1=<?=date('Y-m-01')?>&tgl2=<?=date('Y-m-d')?>" class="btn-gya btn-glass-gya">Bulan Ini</a>
        <a href="?tgl1=<?=date('Y-m-d')?>&tgl2=<?=date('Y-m-d')?>" class="btn-gya btn-glass-gya">Hari Ini</a>
      </div>
    </form>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.4);">
      <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);"><i class="bi bi-cash-coin" style="color:#f43f88;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?=formatRupiah($total_pend)?></div>
      <div class="stat-label">Total Pendapatan</div>
      <div class="stat-change" style="color:#059669;"><i class="bi bi-receipt"></i> <?=$total_trx?> transaksi</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(59,130,246,.4);">
      <div class="stat-icon-wrap" style="background:rgba(59,130,246,.15);"><i class="bi bi-box-seam" style="color:#3b82f6;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?=formatRupiah($total_modal)?></div>
      <div class="stat-label">Total Modal</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(16,185,129,.4);">
      <div class="stat-icon-wrap" style="background:rgba(16,185,129,.15);"><i class="bi bi-graph-up-arrow" style="color:#10b981;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?=formatRupiah($keuntungan)?></div>
      <div class="stat-label">Estimasi Keuntungan</div>
      <div class="stat-change" style="color:#10b981;"><?=$total_pend>0?round($keuntungan/$total_pend*100,1):0?>% margin</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(245,158,11,.4);">
      <div class="stat-icon-wrap" style="background:rgba(245,158,11,.15);"><i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.3rem;"></i></div>
      <div class="stat-value"><?=formatRupiah($total_kredit)?></div>
      <div class="stat-label">Piutang Periode Ini</div>
    </div>
  </div>
</div>

<!-- GRAFIK + TERLARIS -->
<div class="row g-3 mb-4">
  <div class="col-lg-7">
    <div class="content-card">
      <div class="content-card-header"><div class="content-card-title"><i class="bi bi-bar-chart-line" style="color:#f43f88;"></i> Grafik 7 Hari Terakhir</div></div>
      <div style="padding:20px;"><canvas id="chartLap" style="max-height:220px;"></canvas></div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="content-card" style="height:100%;">
      <div class="content-card-header"><div class="content-card-title"><i class="bi bi-trophy" style="color:#f59e0b;"></i> Produk Terlaris</div></div>
      <div style="max-height:290px;overflow-y:auto;">
        <?php $medal=['','🥇','🥈','🥉']; $rank=1;
        if($terlaris&&$terlaris->num_rows>0): while($p=$terlaris->fetch_assoc()): ?>
        <div style="padding:12px 18px;display:flex;align-items:center;gap:11px;border-bottom:1px solid rgba(244,63,136,.05);">
          <span style="font-size:1.1rem;flex-shrink:0;"><?=$rank<=3?$medal[$rank]:"<span style='font-size:.77rem;color:#b08fa0;font-weight:700;min-width:20px;text-align:center;display:inline-block;'>$rank</span>"?></span>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.82rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#1a0a14;"><?=htmlspecialchars($p['nama_produk'])?></div>
            <div style="font-size:.71rem;color:#b08fa0;"><?=htmlspecialchars($p['brand']??'')?> · <?=formatRupiah($p['omzet'])?></div>
          </div>
          <span class="badge-gya badge-success" style="flex-shrink:0;"><?=$p['terjual']?>x</span>
        </div>
        <?php $rank++; endwhile;
        else: ?><div class="empty-state" style="padding:40px;"><span class="empty-icon"><i class="bi bi-trophy"></i></span><div>Belum ada data</div></div><?php endif;?>
      </div>
    </div>
  </div>
</div>

<!-- PER KATEGORI -->
<div class="content-card mb-4">
  <div class="content-card-header"><div class="content-card-title"><i class="bi bi-tags" style="color:#f43f88;"></i> Penjualan per Kategori</div></div>
  <div style="overflow-x:auto;">
    <table class="table-gya">
      <thead><tr><th>Kategori</th><th>Jumlah Transaksi</th><th>Total Omzet</th><th>Proporsi</th></tr></thead>
      <tbody>
        <?php if($per_kat&&$per_kat->num_rows>0): while($r=$per_kat->fetch_assoc()):
          $pct=$total_pend>0?round($r['omzet']/$total_pend*100,1):0;
        ?>
        <tr>
          <td><span class="badge-gya badge-pink"><?=htmlspecialchars($r['nama_kategori'])?></span></td>
          <td><span class="badge-gya badge-info"><?=$r['jml_trx']?> trx</span></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;"><?=formatRupiah($r['omzet'])?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="flex:1;height:6px;background:rgba(244,63,136,.1);border-radius:3px;overflow:hidden;">
                <div style="height:100%;width:<?=$pct?>%;background:linear-gradient(90deg,#f43f88,#c2185b);border-radius:3px;transition:width .5s;"></div>
              </div>
              <span style="font-size:.78rem;font-weight:600;color:#f43f88;white-space:nowrap;"><?=$pct?>%</span>
            </div>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4" class="empty-state"><span class="empty-icon"><i class="bi bi-tags"></i></span><div>Belum ada data</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</div>

<!-- TABEL HARIAN -->
<div class="content-card">
  <div class="content-card-header"><div class="content-card-title"><i class="bi bi-table" style="color:#f43f88;"></i> Detail Harian</div></div>
  <div style="overflow-x:auto;">
    <table class="table-gya">
      <thead><tr><th>Tanggal</th><th>Transaksi</th><th>Pendapatan</th><th>Modal</th><th>Keuntungan</th><th>Margin</th></tr></thead>
      <tbody>
        <?php if($harian&&$harian->num_rows>0): while($r=$harian->fetch_assoc()):
          $unt=max(0,$r['pend']-$r['modal']);
          $margin=$r['pend']>0?round($unt/$r['pend']*100,1):0;
        ?>
        <tr>
          <td style="font-weight:600;"><?=formatTanggal($r['tgl'])?></td>
          <td><span class="badge-gya badge-info"><?=$r['jml']?>x</span></td>
          <td style="font-weight:600;"><?=formatRupiah($r['pend'])?></td>
          <td style="color:#7c3f5e;"><?=formatRupiah($r['modal'])?></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;color:#059669;"><?=formatRupiah($unt)?></td>
          <td><span class="badge-gya badge-success"><?=$margin?>%</span></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6" class="empty-state"><span class="empty-icon"><i class="bi bi-graph-up"></i></span><div>Tidak ada data periode ini</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php $extra_js="<script>
(function(){
  const ctx=document.getElementById('chartLap').getContext('2d');
  const grad=ctx.createLinearGradient(0,0,0,220);
  grad.addColorStop(0,'rgba(244,63,136,.28)');
  grad.addColorStop(1,'rgba(244,63,136,.01)');
  new Chart(ctx,{type:'bar',data:{labels:".json_encode($g_label).",datasets:[{label:'Penjualan',data:".json_encode($g_pend).",backgroundColor:grad,borderColor:'#f43f88',borderWidth:2,borderRadius:8,borderSkipped:false}]},options:{responsive:true,plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(26,5,18,.88)',titleColor:'#fbb6ce',bodyColor:'#fff',padding:12,cornerRadius:10,callbacks:{label:function(c){return' Rp '+c.raw.toLocaleString('id-ID');}}}},scales:{y:{beginAtZero:true,grid:{color:'rgba(244,63,136,.07)'},border:{display:false},ticks:{color:'#b08fa0',font:{size:11},callback:function(v){if(v>=1e6)return'Rp '+(v/1e6).toFixed(1)+'jt';if(v>=1e3)return'Rp '+(v/1e3).toFixed(0)+'rb';return'Rp '+v;}}},x:{grid:{display:false},border:{display:false},ticks:{color:'#b08fa0',font:{size:11}}}}}});
})();
</script>";?>
<?php require_once '../views/admin_footer.php'; ?>