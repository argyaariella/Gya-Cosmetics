<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Dashboard';

$total_produk    = $conn->query("SELECT COUNT(*) as n FROM produk WHERE status='aktif'")->fetch_assoc()['n'];
$trx_hari        = $conn->query("SELECT COUNT(*) as n FROM transaksi WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['n'];
$pendapatan_hari = (float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at)=CURDATE() AND status_transaksi!='kredit'")->fetch_assoc()['n'];
$total_piutang   = (float)$conn->query("SELECT COALESCE(SUM(sisa_hutang),0) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$stok_menipis    = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE stok<=stok_minimum AND status='aktif'")->fetch_assoc()['n'];
$total_pelanggan = $conn->query("SELECT COUNT(*) as n FROM pelanggan")->fetch_assoc()['n'];
$pend_bulan      = (float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE()) AND status_transaksi!='kredit'")->fetch_assoc()['n'];

$g_label=[]; $g_data=[];
for($i=6;$i>=0;$i--){
  $tgl=$date=date('Y-m-d',strtotime("-{$i} days"));
  $v=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at)='$tgl'")->fetch_assoc()['n'];
  $g_label[]=date('D d/m',strtotime("-{$i} days")); $g_data[]=$v;
}

$trx_list  = $conn->query("SELECT t.*,p.nama nm FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id ORDER BY t.created_at DESC LIMIT 8");
$stok_list = $conn->query("SELECT nama_produk,stok,stok_minimum FROM produk WHERE stok<=stok_minimum AND status='aktif' ORDER BY stok ASC LIMIT 7");

require_once '../views/admin_header.php';
?>

<div style="margin-bottom:28px;">
  <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">
    Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👋
  </h2>
  <p style="font-size:.85rem;color:#b08fa0;">Ringkasan operasional GYA Cosmetics — <?= date('l, d F Y') ?></p>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.5);">
      <div class="stat-icon-wrap" style="background:linear-gradient(135deg,rgba(244,63,136,.18),rgba(232,121,249,.1));">
        <i class="bi bi-cash-coin" style="color:#f43f88;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($pendapatan_hari) ?></div>
      <div class="stat-label">Pendapatan Hari Ini</div>
      <div class="stat-change" style="color:#059669;"><i class="bi bi-receipt"></i> <?= $trx_hari ?> transaksi</div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(124,58,237,.5);">
      <div class="stat-icon-wrap" style="background:linear-gradient(135deg,rgba(124,58,237,.15),rgba(232,121,249,.1));">
        <i class="bi bi-graph-up-arrow" style="color:#7c3aed;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($pend_bulan) ?></div>
      <div class="stat-label">Pendapatan Bulan Ini</div>
      <div class="stat-change" style="color:#7c3aed;"><i class="bi bi-calendar3"></i> <?= date('F Y') ?></div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(16,185,129,.5);">
      <div class="stat-icon-wrap" style="background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(52,211,153,.1));">
        <i class="bi bi-box-seam" style="color:#10b981;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= $total_produk ?></div>
      <div class="stat-label">Produk Aktif</div>
      <?php if($stok_menipis>0):?>
      <div class="stat-change" style="color:#d97706;"><i class="bi bi-exclamation-triangle-fill"></i> <?= $stok_menipis ?> stok menipis</div>
      <?php else:?>
      <div class="stat-change" style="color:#059669;"><i class="bi bi-check-circle-fill"></i> Semua stok aman</div>
      <?php endif;?>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(245,158,11,.5);">
      <div class="stat-icon-wrap" style="background:linear-gradient(135deg,rgba(245,158,11,.15),rgba(251,146,60,.1));">
        <i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($total_piutang) ?></div>
      <div class="stat-label">Total Piutang</div>
      <div class="stat-change" style="color:#9d5a78;"><i class="bi bi-people-fill"></i> <?= $total_pelanggan ?> pelanggan</div>
    </div>
  </div>
</div>

<!-- CHART + STOK -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="content-card" style="height:100%;">
      <div class="content-card-header">
        <div class="content-card-title"><i class="bi bi-graph-up" style="color:#f43f88;"></i> Penjualan 7 Hari Terakhir</div>
        <a href="laporan.php" class="btn-gya btn-glass-gya btn-sm-gya">Laporan Lengkap <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="content-card-body" style="padding:24px;"><canvas id="chartSales" style="max-height:230px;"></canvas></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="content-card" style="height:100%;">
      <div class="content-card-header">
        <div class="content-card-title"><i class="bi bi-exclamation-triangle" style="color:#f59e0b;"></i> Stok Menipis</div>
        <a href="produk.php" class="btn-gya btn-glass-gya btn-sm-gya">Kelola <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="content-card-body">
        <?php if($stok_list->num_rows>0): while($p=$stok_list->fetch_assoc()):
          $pct=min(100,$p['stok_minimum']>0?round($p['stok']/$p['stok_minimum']*100):100);
          $c=$p['stok']==0?'#ef4444':($pct<=50?'#f59e0b':'#10b981');
        ?>
        <div style="padding:12px 20px;border-bottom:1px solid rgba(244,63,136,.06);">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:.82rem;font-weight:600;color:#1a0a14;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($p['nama_produk']) ?></span>
            <span style="font-size:.72rem;font-weight:700;color:<?=$c?>;background:<?=$c?>18;padding:2px 8px;border-radius:12px;border:1px solid <?=$c?>33;flex-shrink:0;margin-left:8px;"><?= $p['stok'] ?> pcs</span>
          </div>
          <div style="height:4px;background:rgba(0,0,0,.06);border-radius:4px;"><div style="height:100%;width:<?=$pct?>%;background:<?=$c?>;border-radius:4px;"></div></div>
        </div>
        <?php endwhile; else:?>
        <div style="text-align:center;padding:40px 20px;color:#b08fa0;">
          <i class="bi bi-check-circle" style="font-size:2.5rem;color:#10b981;opacity:.6;display:block;margin-bottom:12px;"></i>
          Semua stok aman!
        </div>
        <?php endif;?>
      </div>
    </div>
  </div>
</div>

<!-- TRANSAKSI TERBARU -->
<div class="content-card">
  <div class="content-card-header">
    <div class="content-card-title"><i class="bi bi-clock-history" style="color:#f43f88;"></i> Transaksi Terbaru</div>
    <a href="transaksi.php" class="btn-gya btn-primary-gya btn-sm-gya">+ Transaksi Baru</a>
  </div>
  <div class="content-card-body" style="overflow-x:auto;">
    <table class="table-gya" style="min-width:700px;">
      <thead><tr><th>Kode</th><th>Pelanggan</th><th>Channel</th><th>Total</th><th>Metode</th><th>Status</th><th>Waktu</th></tr></thead>
      <tbody>
        <?php if($trx_list&&$trx_list->num_rows>0): while($t=$trx_list->fetch_assoc()):?>
        <tr>
          <td><code style="background:rgba(244,63,136,.08);color:#c2185b;padding:3px 8px;border-radius:6px;font-size:.79rem;"><?= $t['kode_transaksi'] ?></code></td>
          <td style="font-weight:500;font-size:.86rem;"><?= htmlspecialchars($t['nm']??'Umum') ?></td>
          <td><?= $t['tipe_penjualan']==='online'?"<span class='badge-gya badge-purple'>Online</span>":"<span class='badge-gya' style='background:rgba(100,100,100,.09);color:#666;border:1px solid rgba(0,0,0,.09);'>Offline</span>" ?></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1rem;"><?= formatRupiah($t['total_harga']) ?></td>
          <td><?= $t['metode_bayar']==='kredit'?"<span class='badge-gya badge-warning'>Kredit</span>":"<span class='badge-gya badge-success'>Tunai</span>" ?></td>
          <td><?php
            $m=['selesai'=>['badge-info','Selesai'],'kredit'=>['badge-warning','Kredit'],'lunas'=>['badge-success','Lunas']];
            $x=$m[$t['status_transaksi']]??['badge-info','—'];
            echo "<span class='badge-gya {$x[0]}'>{$x[1]}</span>";
          ?></td>
          <td style="font-size:.79rem;color:#b08fa0;"><?= date('d/m H:i',strtotime($t['created_at'])) ?></td>
        </tr>
        <?php endwhile; else:?>
        <tr><td colspan="7" style="text-align:center;padding:48px;color:#b08fa0;"><i class="bi bi-inbox" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px;"></i>Belum ada transaksi</td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php $extra_js="<script>
(function(){
  const ctx=document.getElementById('chartSales').getContext('2d');
  const g=ctx.createLinearGradient(0,0,0,230);
  g.addColorStop(0,'rgba(244,63,136,.28)');g.addColorStop(1,'rgba(244,63,136,.01)');
  new Chart(ctx,{type:'bar',data:{
    labels:".json_encode($g_label).",
    datasets:[{
      label:'Penjualan',data:".json_encode($g_data).",
      backgroundColor:g,borderColor:'#f43f88',borderWidth:0,
      borderRadius:8,borderSkipped:false,
      hoverBackgroundColor:'rgba(244,63,136,.45)'
    },{
      type:'line',label:'Tren',data:".json_encode($g_data).",
      borderColor:'rgba(232,121,249,.7)',borderWidth:2,pointRadius:0,
      fill:false,tension:.4
    }]
  },options:{responsive:true,maintainAspectRatio:true,
    plugins:{legend:{display:false},tooltip:{backgroundColor:'rgba(26,5,18,.9)',titleColor:'#fbb6ce',bodyColor:'#fff',padding:12,cornerRadius:10,callbacks:{label:function(c){return' Rp '+c.raw.toLocaleString('id-ID');}}}},
    scales:{y:{beginAtZero:true,grid:{color:'rgba(244,63,136,.06)'},border:{display:false},ticks:{color:'#b08fa0',font:{size:11},callback:function(v){if(v>=1e6)return'Rp '+(v/1e6).toFixed(1)+'jt';if(v>=1e3)return'Rp '+(v/1e3).toFixed(0)+'rb';return v;}}},
    x:{grid:{display:false},border:{display:false},ticks:{color:'#b08fa0',font:{size:11}}}}
  }});
})();
</script>"; ?>
<?php require_once '../views/admin_footer.php'; ?>