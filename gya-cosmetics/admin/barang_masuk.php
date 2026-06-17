<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Barang Masuk';

if ($_SERVER['REQUEST_METHOD']==='POST'&&$_POST['aksi']==='tambah') {
    $pid=(int)$_POST['produk_id']; $sid=(int)$_POST['supplier_id'];
    $jml=(int)$_POST['jumlah']; $hb=(float)$_POST['harga_beli'];
    $tgl=bersihkan($_POST['tanggal']); $ket=bersihkan($_POST['keterangan']??'');
    if($pid<=0||$sid<=0||$jml<=0||$hb<=0||empty($tgl)){setAlert('Semua field wajib diisi!','danger');}
    else{
        $total=$jml*$hb;
        $st=$conn->prepare("INSERT INTO barang_masuk(produk_id,supplier_id,user_id,jumlah,harga_beli,total_harga,tanggal,keterangan)VALUES(?,?,?,?,?,?,?,?)");
        $st->bind_param("iiiiddss",$pid,$sid,$_SESSION['user_id'],$jml,$hb,$total,$tgl,$ket);
        if($st->execute()){
            // Update stok & harga beli produk
            $conn->query("UPDATE produk SET stok=stok+$jml, harga_beli=$hb WHERE id=$pid");
            simpanLog($_SESSION['user_id'],'barang_masuk',"Produk ID:$pid, Jml:$jml");
            setAlert("Barang masuk berhasil dicatat! Stok bertambah <b>$jml</b> pcs.",'success');
        } else setAlert('Gagal menyimpan!','danger');
        $st->close();
    }
    header('Location: barang_masuk.php'); exit();
}

// Data untuk filter & tabel
$per=15; $hal=max(1,(int)($_GET['hal']??1)); $off=($hal-1)*$per;
$ftgl1=bersihkan($_GET['tgl1']??date('Y-m-01')); $ftgl2=bersihkan($_GET['tgl2']??date('Y-m-d'));
$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM barang_masuk WHERE tanggal BETWEEN '$ftgl1' AND '$ftgl2'")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("SELECT bm.*,p.nama_produk,s.nama as nm_sup,u.nama as nm_user FROM barang_masuk bm LEFT JOIN produk p ON bm.produk_id=p.id LEFT JOIN supplier s ON bm.supplier_id=s.id LEFT JOIN users u ON bm.user_id=u.id WHERE bm.tanggal BETWEEN '$ftgl1' AND '$ftgl2' ORDER BY bm.created_at DESC LIMIT $per OFFSET $off");
$total_modal=(float)$conn->query("SELECT COALESCE(SUM(total_harga),0) as n FROM barang_masuk WHERE tanggal BETWEEN '$ftgl1' AND '$ftgl2'")->fetch_assoc()['n'];
$produk_list=$conn->query("SELECT id,nama_produk,harga_beli,stok FROM produk WHERE status='aktif' ORDER BY nama_produk");
$sup_list=$conn->query("SELECT id,nama FROM supplier ORDER BY nama");

require_once '../views/admin_header.php';
?>
<?php tampilAlert();?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Barang Masuk</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Catat penerimaan barang dari supplier</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya"><i class="bi bi-box-arrow-in-down"></i> Catat Barang Masuk</button>
</div>

<!-- STAT + FILTER -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(52,211,153,.4);">
      <div class="stat-icon-wrap" style="background:rgba(52,211,153,.15);"><i class="bi bi-box-arrow-in-down" style="color:#10b981;"></i></div>
      <div class="stat-value"><?=$total_row?></div>
      <div class="stat-label">Total Entri Periode Ini</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.4);">
      <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);"><i class="bi bi-cash-stack" style="color:#f43f88;"></i></div>
      <div class="stat-value"><?=formatRupiah($total_modal)?></div>
      <div class="stat-label">Total Modal Periode Ini</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="content-card" style="padding:0;">
      <div style="padding:18px 20px;">
        <form method="GET" style="display:flex;flex-direction:column;gap:10px;">
          <div style="display:flex;gap:8px;">
            <div style="flex:1;"><label class="form-label-gya">Dari</label><input type="date" name="tgl1" class="form-control-gya" value="<?=$ftgl1?>"></div>
            <div style="flex:1;"><label class="form-label-gya">Sampai</label><input type="date" name="tgl2" class="form-control-gya" value="<?=$ftgl2?>"></div>
          </div>
          <button type="submit" class="btn-gya btn-primary-gya" style="width:100%;justify-content:center;"><i class="bi bi-funnel"></i> Filter</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:750px;">
      <thead><tr><th>#</th><th>Tanggal</th><th>Produk</th><th>Supplier</th><th>Jumlah</th><th>Harga Beli</th><th>Total</th><th>Dicatat oleh</th></tr></thead>
      <tbody>
        <?php if($rows&&$rows->num_rows>0):$no=$off+1;while($b=$rows->fetch_assoc()):?>
        <tr>
          <td style="color:#b08fa0;font-size:.8rem;"><?=$no++?></td>
          <td style="font-size:.84rem;"><?=formatTanggal($b['tanggal'])?></td>
          <td style="font-weight:600;font-size:.86rem;"><?=htmlspecialchars($b['nama_produk'])?></td>
          <td><span class="badge-gya badge-info"><?=htmlspecialchars($b['nm_sup'])?></span></td>
          <td><span class="badge-gya badge-success">+<?=$b['jumlah']?> pcs</span></td>
          <td style="color:#7c3f5e;"><?=formatRupiah($b['harga_beli'])?>/pcs</td>
          <td style="font-weight:600;font-family:'Cormorant Garamond',serif;font-size:1rem;"><?=formatRupiah($b['total_harga'])?></td>
          <td style="font-size:.8rem;color:#b08fa0;"><?=htmlspecialchars($b['nm_user'])?></td>
        </tr>
        <?php endwhile;else:?>
        <tr><td colspan="8" class="empty-state"><span class="empty-icon"><i class="bi bi-inbox"></i></span><div>Belum ada data barang masuk periode ini</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1):?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(244,63,136,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?=$total_row?> entri</span>
    <div class="pagination-gya">
      <?php for($i=1;$i<=$pages;$i++):?><a href="?hal=<?=$i?>&tgl1=<?=$ftgl1?>&tgl2=<?=$ftgl2?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor;?>
    </div>
  </div>
  <?php endif;?>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-box-arrow-in-down" style="color:#10b981;margin-right:8px;"></i>Catat Barang Masuk</div>
      <button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group">
          <label class="form-label-gya">Produk <span style="color:#f43f88;">*</span></label>
          <select name="produk_id" id="selProduk" class="form-select-gya" required onchange="isiHarga(this)">
            <option value="">-- Pilih Produk --</option>
            <?php while($p=$produk_list->fetch_assoc()):?>
            <option value="<?=$p['id']?>" data-hb="<?=$p['harga_beli']?>" data-stok="<?=$p['stok']?>">
              <?=htmlspecialchars($p['nama_produk'])?> (stok: <?=$p['stok']?>)
            </option>
            <?php endwhile;?>
          </select>
        </div>
        <div id="infoProduk" style="display:none;background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:.82rem;color:#065f46;">
          <i class="bi bi-info-circle"></i> Stok saat ini: <b id="infoStok">-</b> pcs | Harga beli terakhir: <b id="infoHb">-</b>
        </div>
        <div class="form-group">
          <label class="form-label-gya">Supplier <span style="color:#f43f88;">*</span></label>
          <select name="supplier_id" class="form-select-gya" required>
            <option value="">-- Pilih Supplier --</option>
            <?php while($s=$sup_list->fetch_assoc()):?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['nama'])?></option>
            <?php endwhile;?>
          </select>
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Jumlah <span style="color:#f43f88;">*</span></label>
              <input type="number" name="jumlah" id="inpJml" class="form-control-gya" min="1" placeholder="0" required oninput="hitungTotal()">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Harga Beli/pcs <span style="color:#f43f88;">*</span></label>
              <div class="input-group-gya"><span class="input-addon">Rp</span><input type="number" name="harga_beli" id="inpHb" min="0" required oninput="hitungTotal()"></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Total Modal</label>
              <div style="background:rgba(244,63,136,.08);border:1.5px solid rgba(244,63,136,.2);border-radius:12px;padding:11px 14px;font-weight:700;color:#c2185b;font-family:'Cormorant Garamond',serif;font-size:1.05rem;" id="dispTotal">Rp 0</div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label-gya">Tanggal <span style="color:#f43f88;">*</span></label>
          <input type="date" name="tanggal" class="form-control-gya" value="<?=date('Y-m-d')?>" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label-gya">Keterangan</label>
          <input type="text" name="keterangan" class="form-control-gya" placeholder="Catatan opsional...">
        </div>
      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan & Update Stok</button>
      </div>
    </form>
  </div>
</div>

<script>
function isiHarga(sel){
  const opt=sel.options[sel.selectedIndex];
  const hb=opt.dataset.hb||0; const stok=opt.dataset.stok||0;
  if(sel.value){
    document.getElementById('infoProduk').style.display='block';
    document.getElementById('infoStok').textContent=stok;
    document.getElementById('infoHb').textContent='Rp '+parseInt(hb).toLocaleString('id-ID');
    document.getElementById('inpHb').value=hb;
  } else { document.getElementById('infoProduk').style.display='none'; }
  hitungTotal();
}
function hitungTotal(){
  const j=parseInt(document.getElementById('inpJml').value)||0;
  const h=parseFloat(document.getElementById('inpHb').value)||0;
  document.getElementById('dispTotal').textContent='Rp '+(j*h).toLocaleString('id-ID');
}
</script>
<?php require_once '../views/admin_footer.php'; ?>