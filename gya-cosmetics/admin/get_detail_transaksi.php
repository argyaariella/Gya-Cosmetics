<?php
require_once '../config/config.php';
cekLogin();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { echo '<p style="color:#ef4444;text-align:center;padding:20px;">ID tidak valid</p>'; exit(); }

$t = $conn->query("
    SELECT t.*, p.nama as nm_pel, p.no_hp, u.nama as nm_usr, pm.judul as promo_judul
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.pelanggan_id=p.id
    LEFT JOIN users u ON t.user_id=u.id
    LEFT JOIN promo pm ON t.promo_id=pm.id
    WHERE t.id=$id
")->fetch_assoc();

if (!$t) { echo '<p style="color:#ef4444;text-align:center;padding:20px;">Transaksi tidak ditemukan</p>'; exit(); }

$detail = $conn->query("
    SELECT dt.*, pr.nama_produk, pr.brand
    FROM detail_transaksi dt
    LEFT JOIN produk pr ON dt.produk_id=pr.id
    WHERE dt.transaksi_id=$id
");
?>
<style>
.dtl-row{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;}
.dtl-item{flex:1;min-width:120px;}
.dtl-label{font-size:.72rem;font-weight:600;color:#b08fa0;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;}
.dtl-val{font-size:.88rem;font-weight:600;color:#1a0a14;}
</style>

<div class="dtl-row">
  <div class="dtl-item">
    <div class="dtl-label">Kode Transaksi</div>
    <code style="background:rgba(244,63,136,.08);color:#c2185b;padding:4px 10px;border-radius:8px;font-size:.82rem;"><?= htmlspecialchars($t['kode_transaksi']) ?></code>
  </div>
  <div class="dtl-item">
    <div class="dtl-label">Tanggal</div>
    <div class="dtl-val"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></div>
  </div>
  <div class="dtl-item">
    <div class="dtl-label">Admin</div>
    <div class="dtl-val"><?= htmlspecialchars($t['nm_usr'] ?? '—') ?></div>
  </div>
</div>

<div style="margin-top:15px; margin-bottom: 20px; display:flex; justify-content:flex-end;">
    <a href="cetak_struk.php?id=<?=$id?>" target="_blank" class="btn-gya btn-primary-gya" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
        <i class="bi bi-printer"></i> Cetak Struk Kasir
    </a>
</div>

<div class="dtl-row">
  <div class="dtl-item">
    <div class="dtl-label">Pelanggan</div>
    <div class="dtl-val"><?= htmlspecialchars($t['nm_pel'] ?? 'Pelanggan Umum') ?></div>
    <?php if($t['no_hp']):?>
    <a href="https://wa.me/<?=preg_replace('/[^0-9]/','', $t['no_hp'])?>" target="_blank" style="font-size:.74rem;color:#25d366;text-decoration:none;"><i class="bi bi-whatsapp"></i> <?=$t['no_hp']?></a>
    <?php endif;?>
  </div>
  <div class="dtl-item">
    <div class="dtl-label">Channel</div>
    <div><?= $t['tipe_penjualan']==='online' ? '<span class="badge-gya badge-purple"><i class="bi bi-globe"></i> Online</span>' : '<span class="badge-gya" style="background:rgba(100,100,100,.1);color:#555;border:1px solid rgba(0,0,0,.1);">Offline</span>' ?></div>
  </div>
  <div class="dtl-item">
    <div class="dtl-label">Metode Bayar</div>
    <div><?= $t['metode_bayar']==='kredit' ? '<span class="badge-gya badge-warning">Kredit</span>' : '<span class="badge-gya badge-success">Tunai</span>' ?></div>
  </div>
  <div class="dtl-item">
    <div class="dtl-label">Status</div>
    <?php $sc=['selesai'=>'badge-info','kredit'=>'badge-warning','lunas'=>'badge-success'];$sl=['selesai'=>'Selesai','kredit'=>'Kredit','lunas'=>'Lunas'];$st=$t['status_transaksi'];?>
    <div><span class="badge-gya <?=$sc[$st]??'badge-info'?>"><?=$sl[$st]??ucfirst($st)?></span></div>
  </div>
</div>

<?php if($t['jatuh_tempo']||$t['catatan']):?>
<div class="dtl-row">
  <?php if($t['jatuh_tempo']):?>
  <div class="dtl-item">
    <div class="dtl-label">Jatuh Tempo</div>
    <div class="dtl-val" style="color:#f59e0b;"><?=formatTanggal($t['jatuh_tempo'])?></div>
  </div>
  <?php endif;?>
  <?php if($t['catatan']):?>
  <div class="dtl-item">
    <div class="dtl-label">Catatan</div>
    <div class="dtl-val"><?=htmlspecialchars($t['catatan'])?></div>
  </div>
  <?php endif;?>
</div>
<?php endif;?>

<div style="height:1px;background:rgba(244,63,136,.1);margin:16px 0;"></div>

<div style="font-size:.78rem;font-weight:700;color:#c2185b;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">
  <i class="bi bi-bag"></i> Rincian Produk
</div>
<div style="overflow-x:auto;">
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr style="background:rgba(244,63,136,.06);">
        <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#c2185b;text-align:left;border-radius:8px 0 0 0;">Produk</th>
        <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#c2185b;text-align:center;width:80px;">Qty</th>
        <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#c2185b;text-align:right;width:130px;">Harga</th>
        <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:#c2185b;text-align:right;width:130px;border-radius:0 8px 0 0;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php while($d=$detail->fetch_assoc()):?>
      <tr style="border-bottom:1px solid rgba(244,63,136,.05);">
        <td style="padding:11px 14px;">
          <div style="font-size:.85rem;font-weight:600;color:#1a0a14;"><?=htmlspecialchars($d['nama_produk'])?></div>
          <?php if($d['brand']):?><div style="font-size:.72rem;color:#b08fa0;"><?=htmlspecialchars($d['brand'])?></div><?php endif;?>
        </td>
        <td style="padding:11px 14px;text-align:center;font-size:.85rem;color:#7c3f5e;font-weight:600;"><?=$d['jumlah']?> pcs</td>
        <td style="padding:11px 14px;text-align:right;font-size:.85rem;color:#7c3f5e;"><?=formatRupiah($d['harga_satuan'])?></td>
        <td style="padding:11px 14px;text-align:right;font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1rem;color:#1a0a14;"><?=formatRupiah($d['subtotal'])?></td>
      </tr>
      <?php endwhile;?>
    </tbody>
    <tfoot>
      <tr style="background:rgba(244,63,136,.06);">
        <td colspan="3" style="padding:13px 14px;font-weight:700;font-size:.86rem;color:#7c3f5e;text-align:right; border-bottom: none;">TOTAL HARGA</td>
        <td style="padding:13px 14px;text-align:right;font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.1rem;color:#1a0a14; border-bottom: none;"><?=formatRupiah($t['total_harga'])?></td>
      </tr>
      <?php if(isset($t['diskon']) && $t['diskon'] > 0): ?>
      <tr style="background:rgba(244,63,136,.06);">
        <td colspan="3" style="padding:4px 14px;font-weight:600;font-size:.8rem;color:#f59e0b;text-align:right; border-bottom: none;"><i class="bi bi-tag-fill"></i> Diskon (<?=htmlspecialchars($t['promo_judul']??'Promo')?>)</td>
        <td style="padding:4px 14px;text-align:right;font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.1rem;color:#f59e0b; border-bottom: none;">-<?=formatRupiah($t['diskon'])?></td>
      </tr>
      <?php endif; ?>
      <tr style="background:rgba(244,63,136,.06); border-top:1px solid rgba(244,63,136,.1);">
        <td colspan="3" style="padding:13px 14px;font-weight:700;font-size:.86rem;color:#7c3f5e;text-align:right;">TOTAL PEMBAYARAN</td>
        <td style="padding:13px 14px;text-align:right;font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.25rem;color:#f43f88;"><?=formatRupiah($t['total_harga'] - ($t['diskon'] ?? 0))?></td>
      </tr>
    </tfoot>
  </table>
</div>