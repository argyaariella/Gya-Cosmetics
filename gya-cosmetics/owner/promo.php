<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Manajemen Promo';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $judul = bersihkan($_POST['judul']);
        $desk  = bersihkan($_POST['deskripsi'] ?? '');
        $dis   = (int)$_POST['diskon_persen'];
        $tm    = bersihkan($_POST['tanggal_mulai'] ?? '');
        $ts    = bersihkan($_POST['tanggal_selesai'] ?? '');
        $sts   = bersihkan($_POST['status']);

        if (empty($judul)) { setAlert('Judul promo wajib diisi!', 'danger'); }
        else {
            $st = $conn->prepare("INSERT INTO promo(judul,deskripsi,diskon_persen,tanggal_mulai,tanggal_selesai,status)VALUES(?,?,?,?,?,?)");
            $st->bind_param("ssisss", $judul, $desk, $dis, $tm, $ts, $sts);
            if ($st->execute()) {
                simpanLog($_SESSION['user_id'], 'tambah_promo', "Tambah promo: $judul");
                setAlert("Promo <b>$judul</b> berhasil ditambahkan!", 'success');
            } else setAlert('Gagal menyimpan!', 'danger');
            $st->close();
        }
    } elseif ($aksi === 'edit') {
        $id    = (int)$_POST['id'];
        $judul = bersihkan($_POST['judul']);
        $desk  = bersihkan($_POST['deskripsi'] ?? '');
        $dis   = (int)$_POST['diskon_persen'];
        $tm    = bersihkan($_POST['tanggal_mulai'] ?? '');
        $ts    = bersihkan($_POST['tanggal_selesai'] ?? '');
        $sts   = bersihkan($_POST['status']);

        $st = $conn->prepare("UPDATE promo SET judul=?,deskripsi=?,diskon_persen=?,tanggal_mulai=?,tanggal_selesai=?,status=? WHERE id=?");
        $st->bind_param("ssisssi", $judul, $desk, $dis, $tm, $ts, $sts, $id);
        if ($st->execute()) setAlert('Promo diperbarui!', 'success');
        else setAlert('Gagal!', 'danger');
        $st->close();
    } elseif ($aksi === 'hapus') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM promo WHERE id=$id");
        simpanLog($_SESSION['user_id'], 'hapus_promo', "Hapus promo ID:$id");
        setAlert('Promo berhasil dihapus!', 'success');
    } elseif ($aksi === 'toggle') {
        // Toggle aktif/nonaktif cepat
        $id   = (int)$_POST['id'];
        $sts  = bersihkan($_POST['current_status']) === 'aktif' ? 'nonaktif' : 'aktif';
        $conn->query("UPDATE promo SET status='$sts' WHERE id=$id");
        setAlert('Status promo diperbarui!', 'success');
    }
    header('Location: promo.php'); exit();
}

$rows       = $conn->query("SELECT * FROM promo ORDER BY created_at DESC");
$promo_aktif= (int)$conn->query("SELECT COUNT(*) as n FROM promo WHERE status='aktif' AND tanggal_selesai>=CURDATE()")->fetch_assoc()['n'];
$promo_habis= (int)$conn->query("SELECT COUNT(*) as n FROM promo WHERE tanggal_selesai<CURDATE()")->fetch_assoc()['n'];

require_once '../views/owner_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Manajemen Promo</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Kelola promo & diskon untuk pelanggan GYA Cosmetics</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya">
    <i class="bi bi-megaphone"></i> Buat Promo Baru
  </button>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(16,185,129,.5);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(16,185,129,.15);margin-bottom:0;"><i class="bi bi-megaphone-fill" style="color:#10b981;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $promo_aktif ?></div><div class="stat-label">Promo Aktif Sekarang</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(244,63,136,.5);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(244,63,136,.15);margin-bottom:0;"><i class="bi bi-collection" style="color:#f43f88;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $rows ? $rows->num_rows : 0 ?></div><div class="stat-label">Total Promo Dibuat</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid rgba(100,100,100,.3);padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="stat-icon-wrap" style="background:rgba(100,100,100,.1);margin-bottom:0;"><i class="bi bi-clock-history" style="color:#94a3b8;"></i></div>
        <div><div class="stat-value" style="font-size:1.4rem;"><?= $promo_habis ?></div><div class="stat-label">Promo Kadaluarsa</div></div>
      </div>
    </div>
  </div>
</div>

<!-- GRID PROMO -->
<?php if ($rows && $rows->num_rows > 0): ?>
<div class="row g-3">
  <?php $rows->data_seek(0); while ($p = $rows->fetch_assoc()):
    $aktif_skrg = $p['status'] === 'aktif' && (!$p['tanggal_selesai'] || $p['tanggal_selesai'] >= date('Y-m-d'));
    $kadaluarsa = $p['tanggal_selesai'] && $p['tanggal_selesai'] < date('Y-m-d');
  ?>
  <div class="col-xl-4 col-md-6">
    <div class="stat-card" style="border-top:3px solid <?= $aktif_skrg ? 'rgba(16,185,129,.5)' : ($kadaluarsa ? 'rgba(100,100,100,.2)' : 'rgba(245,158,11,.4)') ?>;">

      <!-- Header Card -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="stat-icon-wrap" style="background:<?= $aktif_skrg ? 'rgba(16,185,129,.15)' : 'rgba(100,100,100,.1)' ?>;margin-bottom:0;">
            <i class="bi bi-megaphone-fill" style="color:<?= $aktif_skrg ? '#10b981' : '#94a3b8' ?>;"></i>
          </div>
          <div>
            <?php if ($aktif_skrg): ?>
              <span class="badge-gya badge-success"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Aktif</span>
            <?php elseif ($kadaluarsa): ?>
              <span class="badge-gya badge-danger">Kadaluarsa</span>
            <?php else: ?>
              <span class="badge-gya" style="background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.2);">Nonaktif</span>
            <?php endif; ?>
          </div>
        </div>
        <div style="display:flex;gap:6px;">
          <!-- Toggle aktif/nonaktif -->
          <form method="POST" style="display:inline;">
            <input type="hidden" name="aksi" value="toggle">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="current_status" value="<?= $p['status'] ?>">
            <button type="submit" class="btn-gya btn-glass-gya btn-sm-gya" title="<?= $p['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
              <i class="bi bi-<?= $p['status'] === 'aktif' ? 'pause-circle' : 'play-circle' ?>"></i>
            </button>
          </form>
          <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editPromo(<?= json_encode($p) ?>)' title="Edit">
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Hapus promo ini?')">
            <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya" title="Hapus"><i class="bi bi-trash3"></i></button>
          </form>
        </div>
      </div>

      <!-- Konten Promo -->
      <div style="font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:600;color:#1a0a14;margin-bottom:6px;line-height:1.3;">
        <?= htmlspecialchars($p['judul']) ?>
      </div>

      <?php if ($p['diskon_persen'] > 0): ?>
      <div style="font-family:'Cormorant Garamond',serif;font-size:2.8rem;font-weight:700;color:#f43f88;line-height:1;margin-bottom:6px;">
        <?= $p['diskon_persen'] ?>%
        <span style="font-size:1rem;font-weight:400;color:#b08fa0;">DISKON</span>
      </div>
      <?php endif; ?>

      <?php if ($p['deskripsi']): ?>
      <p style="font-size:.81rem;color:#7c3f5e;margin-bottom:10px;line-height:1.55;"><?= htmlspecialchars($p['deskripsi']) ?></p>
      <?php endif; ?>

      <?php if ($p['tanggal_mulai'] || $p['tanggal_selesai']): ?>
      <div style="background:rgba(244,63,136,.05);border:1px solid rgba(244,63,136,.1);border-radius:10px;padding:8px 12px;font-size:.75rem;color:#9d5a78;display:flex;align-items:center;gap:6px;">
        <i class="bi bi-calendar-range"></i>
        <?= $p['tanggal_mulai'] ? formatTanggal($p['tanggal_mulai']) : '—' ?>
        &nbsp;→&nbsp;
        <?= $p['tanggal_selesai'] ? formatTanggal($p['tanggal_selesai']) : '—' ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
  <?php endwhile; ?>
</div>
<?php else: ?>
<div class="content-card">
  <div class="empty-state" style="padding:70px 20px;">
    <span class="empty-icon"><i class="bi bi-megaphone"></i></span>
    <h5 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;margin-bottom:8px;color:#7c3f5e;">Belum Ada Promo</h5>
    <p style="font-size:.86rem;margin-bottom:20px;">Buat promo menarik untuk meningkatkan penjualan!</p>
    <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya">
      <i class="bi bi-plus-circle"></i> Buat Promo Pertama
    </button>
  </div>
</div>
<?php endif; ?>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-megaphone" style="color:#f43f88;margin-right:8px;"></i>Buat Promo Baru</div>
      <button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Judul Promo <span style="color:#f43f88;">*</span></label><input type="text" name="judul" class="form-control-gya" placeholder="Contoh: Flash Sale Weekend!" required></div>
        <div class="form-group"><label class="form-label-gya">Deskripsi</label><textarea name="deskripsi" class="form-control-gya" rows="2" placeholder="Deskripsi singkat promo..."></textarea></div>
        <div class="row g-3">
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Diskon (%)</label><input type="number" name="diskon_persen" class="form-control-gya" value="0" min="0" max="100"></div></div>
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control-gya" value="<?= date('Y-m-d') ?>"></div></div>
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control-gya"></div></div>
        </div>
        <div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Status</label><select name="status" class="form-select-gya"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan Promo</button></div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#3b82f6;margin-right:8px;"></i>Edit Promo</div>
      <button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST"><input type="hidden" name="aksi" value="edit"><input type="hidden" name="id" id="eId">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Judul Promo <span style="color:#f43f88;">*</span></label><input type="text" name="judul" id="eJudul" class="form-control-gya" required></div>
        <div class="form-group"><label class="form-label-gya">Deskripsi</label><textarea name="deskripsi" id="eDesk" class="form-control-gya" rows="2"></textarea></div>
        <div class="row g-3">
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Diskon (%)</label><input type="number" name="diskon_persen" id="eDis" class="form-control-gya" min="0" max="100"></div></div>
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Tanggal Mulai</label><input type="date" name="tanggal_mulai" id="eTm" class="form-control-gya"></div></div>
          <div class="col-md-4"><div class="form-group"><label class="form-label-gya">Tanggal Selesai</label><input type="date" name="tanggal_selesai" id="eTs" class="form-control-gya"></div></div>
        </div>
        <div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Status</label><select name="status" id="eSts" class="form-select-gya"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mEdit')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button></div>
    </form>
  </div>
</div>

<script>
function editPromo(d) {
  document.getElementById('eId').value    = d.id;
  document.getElementById('eJudul').value = d.judul;
  document.getElementById('eDesk').value  = d.deskripsi || '';
  document.getElementById('eDis').value   = d.diskon_persen || 0;
  document.getElementById('eTm').value    = d.tanggal_mulai || '';
  document.getElementById('eTs').value    = d.tanggal_selesai || '';
  document.getElementById('eSts').value   = d.status;
  openModal('mEdit');
}
</script>

<?php require_once '../views/owner_footer.php'; ?>