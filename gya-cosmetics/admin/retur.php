<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Retur / Barang Rusak';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    
    if ($aksi === 'tambah') {
        $produk_id = (int)$_POST['produk_id'];
        $jumlah = (int)$_POST['jumlah'];
        $jenis = bersihkan($_POST['jenis']);
        $keterangan = bersihkan($_POST['keterangan'] ?? '');
        
        if ($produk_id > 0 && $jumlah > 0) {
            $conn->autocommit(FALSE);
            try {
                $st = $conn->prepare("INSERT INTO retur_produk(produk_id, user_id, jumlah, jenis, keterangan) VALUES(?, ?, ?, ?, ?)");
                $st->bind_param("iiiss", $produk_id, $_SESSION['user_id'], $jumlah, $jenis, $keterangan);
                $st->execute();
                $st->close();
                
                $conn->query("UPDATE produk SET stok = stok - $jumlah WHERE id = $produk_id");
                
                simpanLog($_SESSION['user_id'], 'retur_produk', "Retur ID:$produk_id Jml:$jumlah ($jenis)");
                $conn->commit();
                setAlert('Data retur/barang rusak berhasil disimpan dan stok dikurangi!', 'success');
            } catch (Exception $e) {
                $conn->rollback();
                setAlert('Gagal menyimpan data!', 'danger');
            }
        }
    } elseif ($aksi === 'hapus') {
        // Hapus retur = kembalikan stok
        $id = (int)$_POST['id'];
        $r = $conn->query("SELECT produk_id, jumlah FROM retur_produk WHERE id=$id")->fetch_assoc();
        if ($r) {
            $conn->autocommit(FALSE);
            try {
                $conn->query("DELETE FROM retur_produk WHERE id=$id");
                $conn->query("UPDATE produk SET stok = stok + {$r['jumlah']} WHERE id = {$r['produk_id']}");
                simpanLog($_SESSION['user_id'], 'hapus_retur', "Hapus Retur ID:$id");
                $conn->commit();
                setAlert('Data retur dibatalkan, stok dikembalikan!', 'success');
            } catch (Exception $e) {
                $conn->rollback();
                setAlert('Gagal membatalkan!', 'danger');
            }
        }
    }
    header('Location: retur.php'); exit();
}

$per_page = 15;
$page = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$offset = ($page - 1) * $per_page;

$where = "1=1";
if (!empty($_GET['cari'])) {
    $c = $conn->real_escape_string($_GET['cari']);
    $where .= " AND p.nama_produk LIKE '%$c%'";
}

$total_row = (int)$conn->query("SELECT COUNT(*) as n FROM retur_produk rp JOIN produk p ON rp.produk_id=p.id WHERE $where")->fetch_assoc()['n'];
$pages = max(1, ceil($total_row / $per_page));

$rows = $conn->query("
    SELECT rp.*, p.nama_produk, p.brand, u.nama as nm_usr 
    FROM retur_produk rp 
    JOIN produk p ON rp.produk_id=p.id 
    JOIN users u ON rp.user_id=u.id 
    WHERE $where 
    ORDER BY rp.created_at DESC 
    LIMIT $per_page OFFSET $offset
");

$produk_list = $conn->query("SELECT id, nama_produk, stok FROM produk WHERE status='aktif' ORDER BY nama_produk");

require_once '../views/admin_header.php';
?>
<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Retur & Barang Rusak</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Catat barang expired, pecah, atau retur pelanggan (mengurangi stok)</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya"><i class="bi bi-plus-lg"></i> Catat Retur Baru</button>
</div>

<div class="content-card">
  <div style="padding:18px 20px;border-bottom:1px solid rgba(244,63,136,.1);">
    <form method="GET" style="display:flex;gap:10px;align-items:center;">
      <input type="text" name="cari" class="form-control-gya" placeholder="Cari nama produk..." value="<?=htmlspecialchars($_GET['cari']??'')?>" style="max-width:300px;">
      <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-search"></i> Cari</button>
      <?php if(!empty($_GET['cari'])): ?><a href="retur.php" class="btn-gya btn-glass-gya">Reset</a><?php endif; ?>
    </form>
  </div>
  
  <div style="overflow-x:auto;">
    <table class="table-gya">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Produk</th>
          <th>Jumlah</th>
          <th>Jenis</th>
          <th>Keterangan</th>
          <th>Admin</th>
          <th style="width:70px;text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if($rows && $rows->num_rows>0): while($r = $rows->fetch_assoc()): ?>
        <tr>
          <td><span style="font-size:.83rem;color:#7c3f5e;font-weight:600;"><?=date('d/m/Y H:i', strtotime($r['created_at']))?></span></td>
          <td>
            <div style="font-weight:600;color:#1a0a14;"><?=htmlspecialchars($r['nama_produk'])?></div>
            <div style="font-size:.75rem;color:#b08fa0;"><?=htmlspecialchars($r['brand'])?></div>
          </td>
          <td><span class="badge-gya badge-pink" style="font-weight:700;"><?=$r['jumlah']?> pcs</span></td>
          <td>
            <?php 
              $bc = ['rusak'=>'badge-danger', 'expired'=>'badge-warning', 'retur_pelanggan'=>'badge-purple'];
              $bl = ['rusak'=>'Barang Rusak/Pecah', 'expired'=>'Expired', 'retur_pelanggan'=>'Retur Pelanggan'];
            ?>
            <span class="badge-gya <?=$bc[$r['jenis']]??'badge-info'?>"><?=$bl[$r['jenis']]??$r['jenis']?></span>
          </td>
          <td style="font-size:.85rem;color:#555;"><?=htmlspecialchars($r['keterangan']?:'—')?></td>
          <td style="font-size:.85rem;"><i class="bi bi-person-fill" style="color:#b08fa0;"></i> <?=htmlspecialchars($r['nm_usr'])?></td>
          <td style="text-align:center;">
            <form method="POST" onsubmit="return confirmDelete('Batalkan retur ini? Stok akan dikembalikan seperti semula.')">
              <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?=$r['id']?>">
              <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya" title="Batalkan Retur"><i class="bi bi-arrow-counterclockwise"></i></button>
            </form>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="7" class="empty-state"><span class="empty-icon"><i class="bi bi-inbox"></i></span><div>Belum ada data retur / barang rusak.</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <?php if($pages>1): ?>
  <div style="padding:15px 20px;border-top:1px solid rgba(244,63,136,.06);display:flex;justify-content:center;gap:5px;">
    <?php for($i=1;$i<=$pages;$i++): ?>
    <a href="?hal=<?=$i?><?=!empty($_GET['cari'])?'&cari='.urlencode($_GET['cari']):''?>" class="btn-gya <?= $i===$page?'btn-primary-gya':'btn-glass-gya' ?> btn-sm-gya" style="padding:5px 12px;"><?=$i?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-box-arrow-down-left" style="color:#f43f88;margin-right:8px;"></i>Catat Retur Baru</div>
      <button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group">
          <label class="form-label-gya">Pilih Produk <span style="color:#f43f88;">*</span></label>
          <select name="produk_id" class="form-select-gya select2-biasa" required style="width:100%;">
            <option value="">-- Pilih --</option>
            <?php if($produk_list) { $produk_list->data_seek(0); while($pl=$produk_list->fetch_assoc()): ?>
            <option value="<?=$pl['id']?>"><?=htmlspecialchars($pl['nama_produk'])?> (Stok: <?=$pl['stok']?>)</option>
            <?php endwhile; } ?>
          </select>
        </div>
        <div class="row g-3">
          <div class="col-6">
            <div class="form-group">
              <label class="form-label-gya">Jumlah (pcs) <span style="color:#f43f88;">*</span></label>
              <input type="number" name="jumlah" class="form-control-gya" min="1" required>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <label class="form-label-gya">Jenis <span style="color:#f43f88;">*</span></label>
              <select name="jenis" class="form-select-gya" required>
                <option value="rusak">Barang Rusak/Pecah</option>
                <option value="expired">Expired (Kedaluwarsa)</option>
                <option value="retur_pelanggan">Retur Pelanggan</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label-gya">Keterangan (Opsional)</label>
          <textarea name="keterangan" class="form-control-gya" rows="3" placeholder="Misal: Tutup botol pecah saat pengiriman..."></textarea>
        </div>
      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan & Kurangi Stok</button>
      </div>
    </form>
  </div>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-biasa').select2({
        dropdownParent: $('#mTambah'),
        placeholder: "-- Cari Produk --"
    });
});
</script>

<?php require_once '../views/admin_footer.php'; ?>
