<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Dashboard Owner';

$total_produk    = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE status='aktif'")->fetch_assoc()['n'];
$total_pend_bln  = (float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE()) AND status_transaksi!='kredit'")->fetch_assoc()['n'];
$total_pend_hari = (float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at)=CURDATE() AND status_transaksi!='kredit'")->fetch_assoc()['n'];
$total_piutang   = (float)$conn->query("SELECT COALESCE(SUM(sisa_hutang),0) as n FROM kredit WHERE status='belum_lunas'")->fetch_assoc()['n'];
$total_admin     = (int)$conn->query("SELECT COUNT(*) as n FROM users WHERE role='admin' AND status='aktif'")->fetch_assoc()['n'];
$total_trx_bln   = (int)$conn->query("SELECT COUNT(*) as n FROM transaksi WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetch_assoc()['n'];
$total_pelanggan = (int)$conn->query("SELECT COUNT(*) as n FROM pelanggan")->fetch_assoc()['n'];
$stok_menipis    = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE stok<=stok_minimum AND status='aktif'")->fetch_assoc()['n'];

// Grafik 30 hari
$g_label=[];$g_data=[];
for($i=29;$i>=0;$i--){
    $d=date('Y-m-d',strtotime("-{$i} days"));
    $g_label[]=($i%5===0||$i===0)?date('d/m',strtotime("-{$i} days")):'';
    $g_data[]=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE DATE(created_at)='$d' AND status_transaksi!='kredit'")->fetch_assoc()['n'];
}

// Produk terlaris bulan ini
$terlaris=$conn->query("
    SELECT p.nama_produk,p.brand,SUM(dt.jumlah) as terjual,SUM(dt.subtotal) as omzet
    FROM detail_transaksi dt
    JOIN produk p ON dt.produk_id=p.id
    JOIN transaksi t ON dt.transaksi_id=t.id
    WHERE MONTH(t.created_at)=MONTH(CURDATE()) AND YEAR(t.created_at)=YEAR(CURDATE())
    GROUP BY dt.produk_id ORDER BY terjual DESC LIMIT 5
");

// Activity log terbaru
$logs=$conn->query("
    SELECT al.*,u.nama,u.role FROM activity_log al
    LEFT JOIN users u ON al.user_id=u.id
    ORDER BY al.created_at DESC LIMIT 8
");

// Perbandingan bulan ini vs bulan lalu
$pend_lalu=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM transaksi WHERE MONTH(created_at)=MONTH(CURDATE()-INTERVAL 1 MONTH) AND YEAR(created_at)=YEAR(CURDATE()-INTERVAL 1 MONTH) AND status_transaksi!='kredit'")->fetch_assoc()['n'];
$growth = $pend_lalu>0 ? round((($total_pend_bln-$pend_lalu)/$pend_lalu)*100,1) : 0;

require_once '../views/owner_header.php';
?>

<!-- GREETING -->
<div style="margin-bottom:28px;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:600;color:#1a0a14;margin-bottom:6px;">
      Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👑
    </h2>
    <p style="font-size:.86rem;color:#b08fa0;">Panel kontrol owner GYA Cosmetics — <?= date('d F Y') ?></p>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="laporan.php" class="btn-gya btn-glass-gya"><i class="bi bi-bar-chart-line"></i> Laporan</a>
    <a href="backup.php" class="btn-gya btn-primary-gya"><i class="bi bi-cloud-download"></i> Backup</a>
  </div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(139,92,246,.5);">
      <div class="stat-icon-wrap" style="background:rgba(139,92,246,.15);">
        <i class="bi bi-graph-up-arrow" style="color:#7c3aed;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($total_pend_bln) ?></div>
      <div class="stat-label">Pendapatan Bulan Ini</div>
      <div class="stat-change" style="color:<?= $growth>=0?'#059669':'#ef4444' ?>;">
        <i class="bi bi-arrow-<?= $growth>=0?'up':'down' ?>-short"></i>
        <?= abs($growth) ?>% vs bulan lalu
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.5);">
      <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);">
        <i class="bi bi-cash-coin" style="color:#f43f88;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($total_pend_hari) ?></div>
      <div class="stat-label">Pendapatan Hari Ini</div>
      <div class="stat-change" style="color:#7c3aed;">
        <i class="bi bi-receipt"></i> <?= $total_trx_bln ?> trx bulan ini
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(245,158,11,.5);">
      <div class="stat-icon-wrap" style="background:rgba(245,158,11,.15);">
        <i class="bi bi-hourglass-split" style="color:#f59e0b;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= formatRupiah($total_piutang) ?></div>
      <div class="stat-label">Total Piutang</div>
      <div class="stat-change" style="color:<?= $stok_menipis>0?'#d97706':'#059669' ?>;">
        <i class="bi bi-<?= $stok_menipis>0?'exclamation-triangle':'check-circle' ?>-fill"></i>
        <?= $stok_menipis>0?"$stok_menipis produk stok menipis":'Semua stok aman' ?>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="stat-card" style="border-top:3px solid rgba(52,211,153,.5);">
      <div class="stat-icon-wrap" style="background:rgba(52,211,153,.15);">
        <i class="bi bi-box-seam" style="color:#10b981;font-size:1.3rem;"></i>
      </div>
      <div class="stat-value"><?= $total_produk ?></div>
      <div class="stat-label">Total Produk Aktif</div>
      <div class="stat-change" style="color:#b08fa0;">
        <i class="bi bi-people-fill"></i> <?= $total_pelanggan ?> pelanggan • <?= $total_admin ?> admin
      </div>
    </div>
  </div>
</div>

<!-- GRAFIK + TERLARIS -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="content-card">
      <div class="content-card-header">
        <div class="content-card-title">
          <i class="bi bi-graph-up" style="color:#7c3aed;"></i> Tren Penjualan 30 Hari
        </div>
        <a href="laporan.php" class="btn-gya btn-glass-gya btn-sm-gya">Detail <i class="bi bi-arrow-right"></i></a>
      </div>
      <div style="padding:20px;"><canvas id="chart30" style="max-height:230px;"></canvas></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="content-card" style="height:100%;">
      <div class="content-card-header">
        <div class="content-card-title">
          <i class="bi bi-trophy" style="color:#f59e0b;"></i> Produk Terlaris
        </div>
        <span style="font-size:.74rem;color:#b08fa0;"><?= date('F Y') ?></span>
      </div>
      <div>
        <?php if($terlaris&&$terlaris->num_rows>0): $rank=1; while($p=$terlaris->fetch_assoc()):
          $medal=['','🥇','🥈','🥉','4️⃣','5️⃣'];
        ?>
        <div style="padding:12px 18px;display:flex;align-items:center;gap:12px;border-bottom:1px solid rgba(139,92,246,.06);">
          <span style="font-size:1.2rem;flex-shrink:0;"><?= $medal[$rank] ?></span>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.82rem;font-weight:600;color:#1a0a14;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($p['nama_produk']) ?></div>
            <div style="font-size:.72rem;color:#b08fa0;"><?= formatRupiah($p['omzet']) ?></div>
          </div>
          <span class="badge-gya badge-success" style="flex-shrink:0;"><?= $p['terjual'] ?>x</span>
        </div>
        <?php $rank++; endwhile; else: ?>
        <div class="empty-state" style="padding:40px;">
          <span class="empty-icon"><i class="bi bi-trophy"></i></span>
          <div>Belum ada data bulan ini</div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- ACTIVITY LOG -->
<div class="content-card">
  <div class="content-card-header">
    <div class="content-card-title">
      <i class="bi bi-clock-history" style="color:#7c3aed;"></i> Aktivitas Terbaru
    </div>
    <a href="activity_log.php" class="btn-gya btn-glass-gya btn-sm-gya">Lihat Semua <i class="bi bi-arrow-right"></i></a>
  </div>
  <div style="overflow-x:auto;">
    <table class="table-gya">
      <thead><tr><th>User</th><th>Aktivitas</th><th>Keterangan</th><th>Waktu</th></tr></thead>
      <tbody>
        <?php if($logs&&$logs->num_rows>0): while($l=$logs->fetch_assoc()):
          $icmap=['login'=>['bi-box-arrow-in-right','#10b981'],'logout'=>['bi-box-arrow-left','#94a3b8'],'tambah_transaksi'=>['bi-cart-plus','#f43f88'],'lunas_kredit'=>['bi-check2-circle','#059669'],'tambah_produk'=>['bi-plus-circle','#7c3aed'],'edit_produk'=>['bi-pencil','#3b82f6'],'hapus_produk'=>['bi-trash','#ef4444'],'barang_masuk'=>['bi-box-arrow-in-down','#8b5cf6']];
          [$ic,$cl]=$icmap[$l['aktivitas']]??['bi-activity','#b08fa0'];
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:30px;height:30px;border-radius:8px;background:<?= $l['role']==='owner'?'rgba(139,92,246,.15)':'rgba(244,63,136,.15)' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;color:<?= $l['role']==='owner'?'#7c3aed':'#f43f88' ?>;font-size:.8rem;flex-shrink:0;">
                <?= strtoupper(substr($l['nama']??'?',0,1)) ?>
              </div>
              <div>
                <div style="font-size:.83rem;font-weight:600;"><?= htmlspecialchars($l['nama']??'?') ?></div>
                <div style="font-size:.7rem;color:#b08fa0;"><?= ucfirst($l['role']??'') ?></div>
              </div>
            </div>
          </td>
          <td><span style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:<?=$cl?>;"><i class="bi <?=$ic?>"></i><?= htmlspecialchars($l['aktivitas']) ?></span></td>
          <td style="font-size:.81rem;color:#7c3f5e;max-width:200px;"><?= htmlspecialchars($l['keterangan']??'—') ?></td>
          <td style="font-size:.79rem;color:#b08fa0;white-space:nowrap;"><?= date('d/m/Y H:i',strtotime($l['created_at'])) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4" class="empty-state"><span class="empty-icon"><i class="bi bi-clock-history"></i></span><div>Belum ada log</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php $extra_js = "<script>
(function(){
  const ctx=document.getElementById('chart30').getContext('2d');
  const grad=ctx.createLinearGradient(0,0,0,230);
  grad.addColorStop(0,'rgba(139,92,246,.3)');
  grad.addColorStop(1,'rgba(139,92,246,.01)');
  new Chart(ctx,{
    type:'line',
    data:{
      labels:".json_encode($g_label).",
      datasets:[{
        label:'Penjualan',data:".json_encode($g_data).",
        backgroundColor:grad,borderColor:'#7c3aed',
        borderWidth:2.5,fill:true,tension:.45,
        pointRadius:0,pointHoverRadius:5,
        pointBackgroundColor:'#7c3aed',pointBorderColor:'#fff',pointBorderWidth:2
      }]
    },
    options:{
      responsive:true,maintainAspectRatio:true,
      plugins:{
        legend:{display:false},
        tooltip:{backgroundColor:'rgba(26,5,18,.88)',titleColor:'#c4b5fd',bodyColor:'#fff',padding:12,cornerRadius:10,
          callbacks:{label:function(c){return' Rp '+c.raw.toLocaleString('id-ID');}}}
      },
      scales:{
        y:{beginAtZero:true,grid:{color:'rgba(139,92,246,.07)'},border:{display:false},
          ticks:{color:'#b08fa0',font:{size:11},callback:function(v){if(v>=1e6)return'Rp '+(v/1e6).toFixed(1)+'jt';if(v>=1e3)return'Rp '+(v/1e3).toFixed(0)+'rb';return'Rp '+v;}}},
        x:{grid:{display:false},border:{display:false},ticks:{color:'#b08fa0',font:{size:10}}}
      }
    }
  });
})();
</script>"; ?>
<?php require_once '../views/owner_footer.php'; ?>