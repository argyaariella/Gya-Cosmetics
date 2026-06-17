<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Transaksi';

if ($_SERVER['REQUEST_METHOD']==='POST'&&($_POST['aksi']??'')==='tambah') {
    $pel_id   = !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : null;
    $tipe     = bersihkan($_POST['tipe_penjualan']);
    $metode   = bersihkan($_POST['metode_bayar']);
    $promo_id = !empty($_POST['promo_id']) ? (int)$_POST['promo_id'] : null;
    $catatan  = bersihkan($_POST['catatan']??'');
    $jt       = !empty($_POST['jatuh_tempo']) ? bersihkan($_POST['jatuh_tempo']) : null;
    $pid_arr  = $_POST['produk_id']??[];
    $qty_arr  = $_POST['jumlah']??[];

    if (empty($pid_arr)) { setAlert('Minimal 1 produk harus dipilih!','danger'); header('Location: transaksi.php'); exit(); }

    $total=0; $items=[]; $err=false;
    for($i=0;$i<count($pid_arr);$i++){
        $pid=(int)$pid_arr[$i]; $qty=(int)$qty_arr[$i];
        if($pid<=0||$qty<=0) continue;
        $pr=$conn->query("SELECT nama_produk,harga_jual_offline,harga_jual_online,stok FROM produk WHERE id=$pid")->fetch_assoc();
        if(!$pr) continue;
        if($qty>$pr['stok']){ setAlert("Stok <b>{$pr['nama_produk']}</b> tidak cukup! Tersedia: {$pr['stok']} pcs",'danger'); $err=true; break; }
        $hrg = $tipe==='online' ? $pr['harga_jual_online'] : $pr['harga_jual_offline'];
        $sub = $hrg*$qty; $total+=$sub;
        $items[]=['id'=>$pid,'qty'=>$qty,'hrg'=>$hrg,'sub'=>$sub,'nama'=>$pr['nama_produk']];
    }
    if(!$err&&!empty($items)){
        $sts  = $metode==='kredit'?'kredit':'selesai';
        $kode = generateKodeTransaksi();
        
        $diskon = 0;
        if($promo_id) {
            $prm = $conn->query("SELECT diskon_persen FROM promo WHERE id=$promo_id AND status='aktif'")->fetch_assoc();
            if($prm) $diskon = ($total * $prm['diskon_persen']) / 100;
        }
        $total_bersih = $total - $diskon;
        $tbyr = $metode==='kredit'?0:$total_bersih;
        
        $st=$conn->prepare("INSERT INTO transaksi(kode_transaksi,pelanggan_id,user_id,promo_id,tipe_penjualan,metode_bayar,status_transaksi,total_harga,diskon,total_bayar,jatuh_tempo,catatan)VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $st->bind_param("siiisssdddss",$kode,$pel_id,$_SESSION['user_id'],$promo_id,$tipe,$metode,$sts,$total,$diskon,$tbyr,$jt,$catatan);
        $st->execute(); $tid=$conn->insert_id; $st->close();
        foreach($items as $it){
            $st=$conn->prepare("INSERT INTO detail_transaksi(transaksi_id,produk_id,jumlah,harga_satuan,subtotal)VALUES(?,?,?,?,?)");
            $st->bind_param("iiidd",$tid,$it['id'],$it['qty'],$it['hrg'],$it['sub']); $st->execute(); $st->close();
            $conn->query("UPDATE produk SET stok=stok-{$it['qty']} WHERE id={$it['id']}");
        }
        if($metode==='kredit'){
            $st=$conn->prepare("INSERT INTO kredit(transaksi_id,pelanggan_id,total_hutang,total_bayar,sisa_hutang,jatuh_tempo,status)VALUES(?,?,?,0,?,?,'belum_lunas')");
            $st->bind_param("iidds",$tid,$pel_id,$total_bersih,$total_bersih,$jt); $st->execute(); $st->close();
        }
        simpanLog($_SESSION['user_id'],'tambah_transaksi',"Kode:$kode Total:$total_bersih");
        setAlert("Transaksi <b>$kode</b> berhasil! Total: ".formatRupiah($total_bersih),'success');
    }
    header('Location: transaksi.php'); exit();
}

// QUERY LIST
$per=15; $hal=max(1,(int)($_GET['hal']??1)); $off=($hal-1)*$per;
$tgl1=bersihkan($_GET['tgl1']??date('Y-m-01')); $tgl2=bersihkan($_GET['tgl2']??date('Y-m-d'));
$fsts=bersihkan($_GET['sts']??''); $ftipe=bersihkan($_GET['tipe']??'');
$where="WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2'";
if($fsts) $where.=" AND t.status_transaksi='$fsts'";
if($ftipe) $where.=" AND t.tipe_penjualan='$ftipe'";
$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM transaksi t $where")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("SELECT t.*,p.nama as nm_pel,u.nama as nm_usr FROM transaksi t LEFT JOIN pelanggan p ON t.pelanggan_id=p.id LEFT JOIN users u ON t.user_id=u.id $where ORDER BY t.created_at DESC LIMIT $per OFFSET $off");
$where2 = "WHERE DATE(t.created_at) BETWEEN '$tgl1' AND '$tgl2' AND t.status_transaksi!='kredit'";
if($ftipe) $where2 .= " AND t.tipe_penjualan='$ftipe'";
$total_pend=(float)$conn->query("SELECT COALESCE(SUM(total_harga - diskon),0) as n FROM transaksi t $where2")->fetch_assoc()['n'];
$produk_list=$conn->query("SELECT id,nama_produk,harga_jual_offline,harga_jual_online,stok FROM produk WHERE status='aktif' ORDER BY nama_produk");
$pel_list=$conn->query("SELECT id,nama,no_hp FROM pelanggan ORDER BY nama");
$promo_list=$conn->query("SELECT id,judul,diskon_persen FROM promo WHERE status='aktif' AND (tanggal_mulai IS NULL OR tanggal_mulai<=CURDATE()) AND (tanggal_selesai IS NULL OR tanggal_selesai>=CURDATE())");
$produk_arr=[];
$produk_list->data_seek(0);
while($p=$produk_list->fetch_assoc()) $produk_arr[]=$p;

require_once '../views/admin_header.php';
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.trx-row-items{background:rgba(244,63,136,.03);padding:14px 20px;border-top:1px solid rgba(244,63,136,.06);}
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid rgba(244,63,136,.2);
    border-radius: 12px;
    background: rgba(244,63,136,.03);
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #4a2040;
    font-size: .86rem;
    font-weight: 500;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
.select2-dropdown {
    border: 1px solid rgba(244,63,136,.2);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(244,63,136,.1);
}
</style>
<?php tampilAlert();?>

<!-- HEADER -->
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Transaksi Penjualan</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Catat & kelola semua transaksi toko</p>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <div class="stat-card" style="padding:14px 18px;min-width:180px;">
      <div class="stat-label">Total Pendapatan Periode</div>
      <div class="stat-value" style="font-size:1.15rem;"><?=formatRupiah($total_pend)?></div>
    </div>
    <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya"><i class="bi bi-cart-plus"></i> Transaksi Baru</button>
  </div>
</div>

<!-- FILTER -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:18px 20px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
      <div style="min-width:140px;"><label class="form-label-gya">Dari</label><input type="date" name="tgl1" class="form-control-gya" value="<?=$tgl1?>"></div>
      <div style="min-width:140px;"><label class="form-label-gya">Sampai</label><input type="date" name="tgl2" class="form-control-gya" value="<?=$tgl2?>"></div>
      <div style="min-width:130px;">
        <label class="form-label-gya">Status</label>
        <select name="sts" class="form-select-gya">
          <option value="">Semua</option>
          <option value="selesai" <?=$fsts==='selesai'?'selected':''?>>Selesai</option>
          <option value="kredit" <?=$fsts==='kredit'?'selected':''?>>Kredit</option>
          <option value="lunas" <?=$fsts==='lunas'?'selected':''?>>Lunas</option>
        </select>
      </div>
      <div style="min-width:130px;">
        <label class="form-label-gya">Channel</label>
        <select name="tipe" class="form-select-gya">
          <option value="">Semua</option>
          <option value="offline" <?=$ftipe==='offline'?'selected':''?>>Offline</option>
          <option value="online" <?=$ftipe==='online'?'selected':''?>>Online</option>
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
      <thead><tr><th>#</th><th>Kode</th><th>Pelanggan</th><th>Channel</th><th>Total</th><th>Metode</th><th>Status</th><th>Tanggal</th><th style="text-align:center;">Detail</th></tr></thead>
      <tbody>
        <?php if($rows&&$rows->num_rows>0):$no=$off+1;while($t=$rows->fetch_assoc()):?>
        <tr>
          <td style="color:#b08fa0;font-size:.8rem;"><?=$no++?></td>
          <td><code style="background:rgba(244,63,136,.08);color:#c2185b;padding:3px 9px;border-radius:7px;font-size:.78rem;"><?=$t['kode_transaksi']?></code></td>
          <td style="font-weight:500;font-size:.86rem;"><?=htmlspecialchars($t['nm_pel']??'Pelanggan Umum')?></td>
          <td>
            <?php if($t['tipe_penjualan']==='online'):?><span class="badge-gya badge-purple"><i class="bi bi-globe"></i> Online</span>
            <?php else:?><span class="badge-gya" style="background:rgba(100,100,100,.1);color:#666;border:1px solid rgba(0,0,0,.1);"><i class="bi bi-shop"></i> Offline</span><?php endif;?>
          </td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1.05rem;color:#1a0a14;">
            <?=formatRupiah($t['total_harga'] - $t['diskon'])?>
            <?php if($t['diskon']>0):?><br><small style="font-size:.7rem;color:#f59e0b;font-weight:600;"><i class="bi bi-tag-fill"></i> Promo</small><?php endif;?>
          </td>
          <td>
            <?php if($t['metode_bayar']==='kredit'):?><span class="badge-gya badge-warning"><i class="bi bi-clock"></i> Kredit</span>
            <?php else:?><span class="badge-gya badge-success"><i class="bi bi-cash"></i> Tunai</span><?php endif;?>
          </td>
          <td>
            <?php $sc=['selesai'=>'badge-info','kredit'=>'badge-warning','lunas'=>'badge-success']; $sl=['selesai'=>'Selesai','kredit'=>'Kredit','lunas'=>'Lunas']; $st=$t['status_transaksi'];?>
            <span class="badge-gya <?=$sc[$st]??'badge-info'?>"><?=$sl[$st]??ucfirst($st)?></span>
          </td>
          <td style="font-size:.8rem;color:#b08fa0;"><?=date('d/m/Y H:i',strtotime($t['created_at']))?></td>
          <td style="text-align:center;">
            <button class="btn-gya btn-glass-gya btn-sm-gya" onclick="lihatDetail(<?=$t['id']?>)"><i class="bi bi-eye"></i></button>
          </td>
        </tr>
        <?php endwhile;else:?>
        <tr><td colspan="9" class="empty-state"><span class="empty-icon"><i class="bi bi-receipt"></i></span><div>Tidak ada transaksi pada periode ini</div></td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
  <?php if($pages>1):?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(244,63,136,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?=$total_row?> transaksi</span>
    <div class="pagination-gya">
      <?php for($i=1;$i<=$pages;$i++):?><a href="?hal=<?=$i?>&tgl1=<?=$tgl1?>&tgl2=<?=$tgl2?>&sts=<?=$fsts?>&tipe=<?=$ftipe?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor;?>
    </div>
  </div>
  <?php endif;?>
</div>

<!-- ══ MODAL TRANSAKSI BARU ══ -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box modal-box-lg" style="max-width:900px;">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-cart-plus" style="color:#f43f88;margin-right:8px;"></i>Transaksi Baru</div>
      <button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" id="fTrx"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">

        <!-- INFO TRANSAKSI -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <label class="form-label-gya">Pelanggan</label>
            <select name="pelanggan_id" class="form-select-gya">
              <option value="">— Umum —</option>
              <?php $pel_list->data_seek(0); while($pl=$pel_list->fetch_assoc()):?>
              <option value="<?=$pl['id']?>"><?=htmlspecialchars($pl['nama'])?></option>
              <?php endwhile;?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label-gya">Promo (Opsional)</label>
            <select name="promo_id" id="selPromo" class="form-select-gya" onchange="hitungTotal()">
              <option value="" data-diskon="0">— Tidak Ada Promo —</option>
              <?php if($promo_list) { $promo_list->data_seek(0); while($prl=$promo_list->fetch_assoc()):?>
              <option value="<?=$prl['id']?>" data-diskon="<?=$prl['diskon_persen']?>"><?=htmlspecialchars($prl['judul'])?> (Diskon <?=$prl['diskon_persen']?>%)</option>
              <?php endwhile; } ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label-gya">Channel</label>
            <select name="tipe_penjualan" id="selTipe" class="form-select-gya" onchange="updateHargaSemua()">
              <option value="offline">Offline</option>
              <option value="online">Online</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label-gya">Metode Bayar</label>
            <select name="metode_bayar" id="selMetode" class="form-select-gya" onchange="toggleJT()">
              <option value="tunai">Tunai</option>
              <option value="kredit">Kredit</option>
            </select>
          </div>
          <div class="col-md-2" id="divJT" style="display:none;">
            <label class="form-label-gya">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" id="inpJT" class="form-control-gya" min="<?=date('Y-m-d')?>">
          </div>
          <div class="col-md-2">
            <label class="form-label-gya">Catatan</label>
            <input type="text" name="catatan" class="form-control-gya" placeholder="Opsional...">
          </div>
        </div>

        <!-- PRODUK ITEMS -->
        <div style="background:rgba(244,63,136,.04);border:1.5px dashed rgba(244,63,136,.2);border-radius:16px;overflow:hidden;">
          <div style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;background:rgba(244,63,136,.06);">
            <span style="font-weight:700;font-size:.88rem;color:#c2185b;"><i class="bi bi-bag me-2"></i>Item Produk</span>
            <button type="button" class="btn-gya btn-primary-gya btn-sm-gya" onclick="tambahBaris()"><i class="bi bi-plus"></i> Tambah Produk</button>
          </div>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:600px;">
              <thead><tr style="background:rgba(244,63,136,.06);">
                <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#c2185b;text-align:left;">Produk</th>
                <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#c2185b;width:100px;">Jumlah</th>
                <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#c2185b;width:140px;">Harga</th>
                <th style="padding:10px 14px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#c2185b;width:140px;">Subtotal</th>
                <th style="width:44px;"></th>
              </tr></thead>
              <tbody id="tbodyProduk"></tbody>
            </table>
          </div>
          <div style="padding:14px 18px;display:flex;justify-content:flex-end;align-items:center;gap:14px;border-top:1px solid rgba(244,63,136,.1);">
            <div style="text-align:right;">
              <div style="font-size:.8rem;color:#7c3f5e;margin-bottom:2px;" id="divDiskon">Diskon Promo: <span id="diskonDisplay">Rp 0</span></div>
              <div><span style="font-size:.86rem;font-weight:600;color:#7c3f5e;margin-right:10px;">TOTAL PEMBAYARAN:</span>
              <span id="totalDisplay" style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:700;color:#f43f88;">Rp 0</span></div>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan Transaksi</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal-gya" id="mDetail"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-receipt" style="color:#f43f88;margin-right:8px;"></i>Detail Transaksi</div>
      <button class="modal-close" onclick="closeModal('mDetail')"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body-gya" id="detailContent">
      <div style="text-align:center;padding:40px;"><div class="spinner-gya" style="margin:0 auto;"></div></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const produkData = <?=json_encode($produk_arr)?>;
let rowIdx = 0;

function tambahBaris(){
  const tbody = document.getElementById('tbodyProduk');
  const idx   = rowIdx++;
  const opts  = produkData.map(p=>`<option value="${p.id}" data-off="${p.harga_jual_offline}" data-on="${p.harga_jual_online}" data-stok="${p.stok}">${p.nama_produk} (stok:${p.stok})</option>`).join('');
  const tr = document.createElement('tr');
  tr.id = `row_${idx}`;
  tr.style.borderBottom = '1px solid rgba(244,63,136,.06)';
  tr.innerHTML = `
    <td style="padding:10px 14px;">
      <select name="produk_id[]" id="sel_produk_${idx}" class="form-select-gya select2-produk" style="font-size:.83rem;" onchange="updateHarga(this,${idx})" required>
        <option value="">-- Pilih --</option>${opts}
      </select>
    </td>
    <td style="padding:10px 14px;">
      <input type="number" name="jumlah[]" id="qty_${idx}" class="form-control-gya" value="1" min="1" style="font-size:.86rem;" oninput="hitungSub(${idx})">
    </td>
    <td style="padding:10px 14px;font-size:.86rem;color:#7c3f5e;font-weight:600;" id="hrg_${idx}">Rp 0</td>
    <td style="padding:10px 14px;font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-weight:700;color:#f43f88;" id="sub_${idx}">Rp 0</td>
    <td style="padding:10px 8px;text-align:center;">
      <button type="button" class="btn-gya btn-danger-gya btn-sm-gya" onclick="hapusBaris(${idx})"><i class="bi bi-trash3"></i></button>
    </td>`;
  tbody.appendChild(tr);
  
  // Initialize Select2 on the new select element
  if(typeof jQuery !== 'undefined'){
    $(`#sel_produk_${idx}`).select2({
      dropdownParent: $('#mTambah'),
      width: '100%',
      placeholder: "-- Cari Produk --"
    }).on('change', function() {
      updateHarga(this, idx);
    });
  }
}

function updateHarga(sel,idx){
  const opt = sel.options[sel.selectedIndex];
  const tipe= document.getElementById('selTipe').value;
  const hrg = tipe==='online' ? parseFloat(opt.dataset.on||0) : parseFloat(opt.dataset.off||0);
  document.getElementById(`hrg_${idx}`).textContent = 'Rp '+hrg.toLocaleString('id-ID');
  document.getElementById(`hrg_${idx}`).dataset.val = hrg;
  document.getElementById(`qty_${idx}`).max = opt.dataset.stok||999;
  hitungSub(idx);
}
function hitungSub(idx){
  const hrg = parseFloat(document.getElementById(`hrg_${idx}`)?.dataset.val||0);
  const qty = parseInt(document.getElementById(`qty_${idx}`)?.value||0);
  const sub = hrg*qty;
  if(document.getElementById(`sub_${idx}`)) document.getElementById(`sub_${idx}`).textContent='Rp '+sub.toLocaleString('id-ID');
  hitungTotal();
}
function hitungTotal(){
  let total=0;
  document.querySelectorAll('[id^="sub_"]').forEach(el=>{
    total += parseInt(el.textContent.replace(/[^0-9]/g,''))||0;
  });
  
  const selPromo = document.getElementById('selPromo');
  const diskonPersen = selPromo && selPromo.selectedIndex >= 0 ? parseFloat(selPromo.options[selPromo.selectedIndex].dataset.diskon||0) : 0;
  const diskonRp = (total * diskonPersen) / 100;
  const totalBersih = total - diskonRp;
  
  if(diskonRp > 0) {
    document.getElementById('divDiskon').style.display = 'block';
    document.getElementById('diskonDisplay').textContent = '-Rp ' + diskonRp.toLocaleString('id-ID');
  } else {
    document.getElementById('divDiskon').style.display = 'none';
  }
  
  document.getElementById('totalDisplay').textContent='Rp '+totalBersih.toLocaleString('id-ID');
}
function hapusBaris(idx){ const r=document.getElementById(`row_${idx}`); if(r)r.remove(); hitungTotal(); }
function updateHargaSemua(){
  document.querySelectorAll('[name="produk_id[]"]').forEach((sel,i)=>{ if(sel.value) updateHarga(sel,parseInt(sel.closest('tr').id.split('_')[1])); });
}
function toggleJT(){
  const m=document.getElementById('selMetode').value;
  const d=document.getElementById('divJT');
  d.style.display=m==='kredit'?'block':'none';
  document.getElementById('inpJT').required=m==='kredit';
}

// Auto tambah 1 baris saat modal buka
document.getElementById('mTambah').addEventListener('transitionend',function(e){
  if(this.classList.contains('show')&&document.getElementById('tbodyProduk').children.length===0) tambahBaris();
});

// Lihat detail via fetch
function lihatDetail(id){
  document.getElementById('detailContent').innerHTML='<div style="text-align:center;padding:40px;"><div class="spinner-gya" style="margin:0 auto;"></div></div>';
  openModal('mDetail');
  fetch(`get_detail_transaksi.php?id=${id}`)
    .then(r=>r.text())
    .then(html=>document.getElementById('detailContent').innerHTML=html)
    .catch(()=>document.getElementById('detailContent').innerHTML='<p style="color:#ef4444;text-align:center;">Gagal memuat data</p>');
}
</script>
<?php require_once '../views/admin_footer.php'; ?>