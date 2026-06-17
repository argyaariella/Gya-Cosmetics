<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Data Produk';

// Owner bisa edit harga dan status tapi tidak hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'edit') {
        $id        = (int)$_POST['id'];
        $nama      = bersihkan($_POST['nama_produk']);
        $kat       = (int)$_POST['kategori_id'];
        $brand     = bersihkan($_POST['brand'] ?? '');
        $desk      = bersihkan($_POST['deskripsi'] ?? '');
        $cara      = bersihkan($_POST['cara_pakai'] ?? '');
        $hb        = (float)($_POST['harga_beli'] ?? 0);
        $ho        = (float)($_POST['harga_jual_offline'] ?? 0);
        $hn        = (float)($_POST['harga_jual_online'] ?? 0);
        $stok      = (int)($_POST['stok'] ?? 0);
        $smin      = (int)($_POST['stok_minimum'] ?? 5);
        $status    = bersihkan($_POST['status'] ?? 'aktif');

        if (empty($nama) || $kat <= 0 || $ho <= 0) {
            setAlert('Field wajib harus diisi!', 'danger');
        } else {
            $st = $conn->prepare("UPDATE produk SET nama_produk=?,kategori_id=?,brand=?,deskripsi=?,cara_pakai=?,harga_beli=?,harga_jual_offline=?,harga_jual_online=?,stok=?,stok_minimum=?,status=? WHERE id=?");
            $st->bind_param("sisssdddissi", $nama, $kat, $brand, $desk, $cara, $hb, $ho, $hn, $stok, $smin, $status, $id);
            if ($st->execute()) {
                simpanLog($_SESSION['user_id'], 'edit_produk', "Owner edit produk ID:$id");
                setAlert('Produk berhasil diperbarui!', 'success');
            } else {
                setAlert('Gagal memperbarui!', 'danger');
            }
            $st->close();
        }
    }
    header('Location: produk.php');
    exit();
}

// QUERY
$per  = 15;
$hal  = max(1, (int)($_GET['hal'] ?? 1));
$off  = ($hal - 1) * $per;
$cari = bersihkan($_GET['cari'] ?? '');
$fkat = (int)($_GET['kat'] ?? 0);
$fsts = bersihkan($_GET['sts'] ?? '');

$where = "WHERE 1=1";
if ($cari) $where .= " AND p.nama_produk LIKE '%$cari%'";
if ($fkat) $where .= " AND p.kategori_id=$fkat";
if ($fsts) $where .= " AND p.status='$fsts'";

$total_row = (int)$conn->query("SELECT COUNT(*) as n FROM produk p $where")->fetch_assoc()['n'];
$pages     = max(1, ceil($total_row / $per));
$rows      = $conn->query("SELECT p.*,k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id $where ORDER BY p.created_at DESC LIMIT $per OFFSET $off");
$kats      = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");

// Summary stats
$total_aktif  = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE status='aktif'")->fetch_assoc()['n'];
$stok_menipis = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE stok<=stok_minimum AND status='aktif'")->fetch_assoc()['n'];
$stok_habis   = (int)$conn->query("SELECT COUNT(*) as n FROM produk WHERE stok=0 AND status='aktif'")->fetch_assoc()['n'];

require_once '../views/owner_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Data Produk</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Pantau & kelola seluruh produk GYA Cosmetics</p>
  </div>
</div>

<!-- STAT MINI -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(139,92,246,.5);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(139,92,246,.15);margin-bottom:0;"><i class="bi bi-box-seam" style="color:#7c3aed;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $total_aktif ?></div><div class="stat-label">Produk Aktif</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(245,158,11,.5);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(245,158,11,.15);margin-bottom:0;"><i class="bi bi-exclamation-triangle" style="color:#f59e0b;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $stok_menipis ?></div><div class="stat-label">Stok Menipis</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(239,68,68,.5);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(239,68,68,.15);margin-bottom:0;"><i class="bi bi-x-circle" style="color:#ef4444;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $stok_habis ?></div><div class="stat-label">Stok Habis</div></div>
      </div>
    </div>
  </div>
</div>

<!-- FILTER -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      <div style="flex:1;min-width:200px;">
        <label class="form-label-gya">Cari Produk</label>
        <div class="input-group-gya">
          <span class="input-addon"><i class="bi bi-search"></i></span>
          <input type="text" name="cari" placeholder="Nama produk..." value="<?= htmlspecialchars($cari) ?>"
            style="flex:1;border:none;background:transparent;padding:10px 14px;font-family:'DM Sans',sans-serif;font-size:.88rem;color:#1a0a14;outline:none;">
        </div>
      </div>
      <div style="min-width:160px;">
        <label class="form-label-gya">Kategori</label>
        <select name="kat" class="form-select-gya">
          <option value="0">Semua Kategori</option>
          <?php $kats->data_seek(0); while ($k = $kats->fetch_assoc()): ?>
          <option value="<?= $k['id'] ?>" <?= $fkat == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div style="min-width:130px;">
        <label class="form-label-gya">Status</label>
        <select name="sts" class="form-select-gya">
          <option value="">Semua</option>
          <option value="aktif"    <?= $fsts === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
          <option value="nonaktif" <?= $fsts === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-funnel"></i> Filter</button>
        <a href="produk.php" class="btn-gya btn-glass-gya">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:920px;">
      <thead>
        <tr>
          <th>#</th>
          <th>Produk</th>
          <th>Kategori</th>
          <th>Harga Beli</th>
          <th>Harga Offline</th>
          <th>Harga Online</th>
          <th>Stok</th>
          <th>Status</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows && $rows->num_rows > 0): $no = $off + 1; while ($p = $rows->fetch_assoc()):
          $sc = $p['stok'] == 0 ? 'badge-danger' : ($p['stok'] <= $p['stok_minimum'] ? 'badge-warning' : 'badge-success');
        ?>
        <tr>
          <td style="color:#b08fa0;font-size:.79rem;"><?= $no++ ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:11px;">
              <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(244,63,136,.1));display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:rgba(139,92,246,.5);flex-shrink:0;">
                <i class="bi bi-bag-heart"></i>
              </div>
              <div>
                <div style="font-weight:600;font-size:.86rem;color:#1a0a14;max-width:190px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($p['nama_produk']) ?></div>
                <?php if ($p['brand']): ?><div style="font-size:.73rem;color:#b08fa0;margin-top:2px;"><?= htmlspecialchars($p['brand']) ?></div><?php endif; ?>
              </div>
            </div>
          </td>
          <td><span class="badge-gya badge-purple"><?= htmlspecialchars($p['nama_kategori']) ?></span></td>
          <td style="font-size:.85rem;color:#7c3f5e;"><?= formatRupiah($p['harga_beli']) ?></td>
          <td style="font-weight:600;color:#059669;"><?= formatRupiah($p['harga_jual_offline']) ?></td>
          <td style="font-weight:600;color:#7c3aed;"><?= formatRupiah($p['harga_jual_online']) ?></td>
          <td><span class="badge-gya <?= $sc ?>"><?= $p['stok'] ?> pcs</span></td>
          <td>
            <?php if ($p['status'] === 'aktif'): ?>
              <span class="badge-gya badge-success"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Aktif</span>
            <?php else: ?>
              <span class="badge-gya badge-danger"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Nonaktif</span>
            <?php endif; ?>
          </td>
          <td style="text-align:center;">
            <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editProduk(<?= json_encode($p) ?>)' title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr>
          <td colspan="9" class="empty-state">
            <span class="empty-icon"><i class="bi bi-box-seam"></i></span>
            <div>Produk tidak ditemukan</div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(139,92,246,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?= $total_row ?> produk</span>
    <div class="pagination-gya">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?hal=<?= $i ?>&cari=<?= urlencode($cari) ?>&kat=<?= $fkat ?>&sts=<?= $fsts ?>" class="page-btn <?= $i == $hal ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit">
  <div class="modal-overlay"></div>
  <div class="modal-box modal-box-lg">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#7c3aed;margin-right:8px;"></i>Edit Produk</div>
      <button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" id="fEdit">
      <input type="hidden" name="aksi" value="edit">
      <input type="hidden" name="id" id="eId">
      <div class="modal-body-gya">
        <div class="row g-3">
          <div class="col-md-8">
            <div class="form-group">
              <label class="form-label-gya">Nama Produk <span style="color:#f43f88;">*</span></label>
              <input type="text" name="nama_produk" id="eNama" class="form-control-gya" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Brand</label>
              <input type="text" name="brand" id="eBrand" class="form-control-gya">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label-gya">Kategori <span style="color:#f43f88;">*</span></label>
              <select name="kategori_id" id="eKat" class="form-select-gya" required>
                <?php $kats->data_seek(0); while ($k = $kats->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label-gya">Status</label>
              <select name="status" id="eSts" class="form-select-gya">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Harga Beli</label>
              <div class="input-group-gya"><span class="input-addon">Rp</span><input type="number" name="harga_beli" id="eHb" min="0"></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Harga Offline <span style="color:#f43f88;">*</span></label>
              <div class="input-group-gya"><span class="input-addon">Rp</span><input type="number" name="harga_jual_offline" id="eHo" min="0" required></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label-gya">Harga Online</label>
              <div class="input-group-gya"><span class="input-addon">Rp</span><input type="number" name="harga_jual_online" id="eHn" min="0"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label-gya">Stok</label>
              <input type="number" name="stok" id="eStok" class="form-control-gya" min="0">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label-gya">Stok Minimum</label>
              <input type="number" name="stok_minimum" id="eSmin" class="form-control-gya" min="0">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label class="form-label-gya">Deskripsi</label>
              <textarea name="deskripsi" id="eDesk" class="form-control-gya" rows="2"></textarea>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label-gya">Cara Pakai</label>
              <textarea name="cara_pakai" id="eCara" class="form-control-gya" rows="2"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mEdit')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editProduk(d) {
  document.getElementById('eId').value    = d.id;
  document.getElementById('eNama').value  = d.nama_produk;
  document.getElementById('eBrand').value = d.brand || '';
  document.getElementById('eKat').value   = d.kategori_id;
  document.getElementById('eSts').value   = d.status;
  document.getElementById('eHb').value    = d.harga_beli;
  document.getElementById('eHo').value    = d.harga_jual_offline;
  document.getElementById('eHn').value    = d.harga_jual_online;
  document.getElementById('eStok').value  = d.stok;
  document.getElementById('eSmin').value  = d.stok_minimum;
  document.getElementById('eDesk').value  = d.deskripsi || '';
  document.getElementById('eCara').value  = d.cara_pakai || '';
  openModal('mEdit');
}
</script>

<?php require_once '../views/owner_footer.php'; ?>