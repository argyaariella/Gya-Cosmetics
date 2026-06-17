<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Pelanggan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah') {
        $nama = bersihkan($_POST['nama']); $hp = bersihkan($_POST['no_hp']??''); $al = bersihkan($_POST['alamat']??'');
        if (empty($nama)) { setAlert('Nama pelanggan wajib!','danger'); }
        else {
            $st=$conn->prepare("INSERT INTO pelanggan(nama,no_hp,alamat)VALUES(?,?,?)");
            $st->bind_param("sss",$nama,$hp,$al);
            if($st->execute()) setAlert("Pelanggan <b>$nama</b> berhasil ditambahkan!",'success');
            else setAlert('Gagal!','danger');
            $st->close();
        }
    } elseif ($aksi==='edit') {
        $id=(int)$_POST['id']; $nama=bersihkan($_POST['nama']); $hp=bersihkan($_POST['no_hp']??''); $al=bersihkan($_POST['alamat']??'');
        $st=$conn->prepare("UPDATE pelanggan SET nama=?,no_hp=?,alamat=? WHERE id=?");
        $st->bind_param("sssi",$nama,$hp,$al,$id);
        if($st->execute()) setAlert('Pelanggan diperbarui!','success'); else setAlert('Gagal!','danger');
        $st->close();
    } elseif ($aksi==='hapus') {
        $id=(int)$_POST['id'];
        $conn->query("DELETE FROM pelanggan WHERE id=$id");
        setAlert('Pelanggan dihapus!','success');
    }
    header('Location: pelanggan.php'); exit();
}

$cari = bersihkan($_GET['cari'] ?? '');
$per=15; $hal=max(1,(int)($_GET['hal']??1)); $off=($hal-1)*$per;
$where = $cari ? "WHERE p.nama LIKE '%$cari%' OR p.no_hp LIKE '%$cari%'" : "WHERE 1=1";
$total_row=(int)$conn->query("SELECT COUNT(*) as n FROM pelanggan p $where")->fetch_assoc()['n'];
$pages=max(1,ceil($total_row/$per));
$rows=$conn->query("
    SELECT p.*,
           COUNT(DISTINCT t.id) as jml_trx,
           COALESCE(SUM(t.total_harga),0) as total_belanja
    FROM pelanggan p
    LEFT JOIN transaksi t ON p.id=t.pelanggan_id
    $where GROUP BY p.id ORDER BY p.nama LIMIT $per OFFSET $off
");

require_once '../views/admin_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Data Pelanggan</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Total <b style="color:#f43f88;"><?= $total_row ?></b> pelanggan terdaftar</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya"><i class="bi bi-person-plus-fill"></i> Tambah Pelanggan</button>
</div>

<!-- SEARCH -->
<div class="content-card" style="margin-bottom:20px;">
  <div style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
      <div style="flex:1;min-width:220px;">
        <label class="form-label-gya">Cari Pelanggan</label>
        <div class="input-group-gya">
          <span class="input-addon"><i class="bi bi-search"></i></span>
          <input type="text" name="cari" placeholder="Nama atau nomor HP..." value="<?= htmlspecialchars($cari) ?>"
            style="flex:1;border:none;background:transparent;padding:10px 14px;font-family:'DM Sans',sans-serif;font-size:.88rem;color:#1a0a14;outline:none;">
        </div>
      </div>
      <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-search"></i> Cari</button>
      <a href="pelanggan.php" class="btn-gya btn-glass-gya">Reset</a>
    </form>
  </div>
</div>

<!-- TABLE -->
<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:680px;">
      <thead>
        <tr><th>#</th><th>Pelanggan</th><th>No. HP</th><th>Alamat</th><th>Total Transaksi</th><th>Total Belanja</th><th style="text-align:center;">Aksi</th></tr>
      </thead>
      <tbody>
        <?php if ($rows&&$rows->num_rows>0): $no=$off+1; while ($p=$rows->fetch_assoc()): ?>
        <tr>
          <td style="color:#b08fa0;font-size:.79rem;"><?= $no++ ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,rgba(244,63,136,.2),rgba(232,121,249,.15));display:flex;align-items:center;justify-content:center;font-weight:700;color:#c2185b;font-size:.9rem;flex-shrink:0;">
                <?= strtoupper(substr($p['nama'],0,1)) ?>
              </div>
              <span style="font-weight:600;font-size:.87rem;"><?= htmlspecialchars($p['nama']) ?></span>
            </div>
          </td>
          <td>
            <?php if ($p['no_hp']): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/','', $p['no_hp']) ?>" target="_blank"
               style="color:#25d366;text-decoration:none;font-size:.84rem;display:flex;align-items:center;gap:5px;">
              <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($p['no_hp']) ?>
            </a>
            <?php else: ?><span style="color:#b08fa0;font-size:.83rem;">—</span><?php endif; ?>
          </td>
          <td style="font-size:.82rem;color:#7c3f5e;max-width:180px;"><?= htmlspecialchars($p['alamat'] ?? '—') ?></td>
          <td><span class="badge-gya badge-pink"><?= $p['jml_trx'] ?>x transaksi</span></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1rem;"><?= formatRupiah($p['total_belanja']) ?></td>
          <td style="text-align:center;">
            <div style="display:flex;gap:6px;justify-content:center;">
              <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editPel(<?= json_encode($p) ?>)'><i class="bi bi-pencil"></i></button>
              <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Hapus pelanggan <?= htmlspecialchars(addslashes($p['nama'])) ?>?')">
                <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya"><i class="bi bi-trash3"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="7" class="empty-state"><span class="empty-icon"><i class="bi bi-people"></i></span><div>Belum ada pelanggan terdaftar</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages>1): ?>
  <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(244,63,136,.07);flex-wrap:wrap;gap:10px;">
    <span style="font-size:.8rem;color:#b08fa0;"><?= $total_row ?> pelanggan</span>
    <div class="pagination-gya"><?php for($i=1;$i<=$pages;$i++): ?><a href="?hal=<?=$i?>&cari=<?=urlencode($cari)?>" class="page-btn <?=$i==$hal?'active':''?>"><?=$i?></a><?php endfor; ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-person-plus" style="color:#f43f88;margin-right:8px;"></i>Tambah Pelanggan</div><button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Nama Pelanggan <span style="color:#f43f88;">*</span></label><input type="text" name="nama" class="form-control-gya" placeholder="Nama lengkap" required></div>
        <div class="form-group"><label class="form-label-gya">No. HP / WhatsApp</label><input type="text" name="no_hp" class="form-control-gya" placeholder="08xxxxxxxxxx"></div>
        <div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Alamat</label><textarea name="alamat" class="form-control-gya" rows="2" placeholder="Alamat pelanggan..."></textarea></div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button></div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#3b82f6;margin-right:8px;"></i>Edit Pelanggan</div><button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST"><input type="hidden" name="aksi" value="edit"><input type="hidden" name="id" id="eId">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Nama</label><input type="text" name="nama" id="eNama" class="form-control-gya" required></div>
        <div class="form-group"><label class="form-label-gya">No. HP</label><input type="text" name="no_hp" id="eHp" class="form-control-gya"></div>
        <div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Alamat</label><textarea name="alamat" id="eAl" class="form-control-gya" rows="2"></textarea></div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mEdit')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button></div>
    </form>
  </div>
</div>

<script>
function editPel(d){document.getElementById('eId').value=d.id;document.getElementById('eNama').value=d.nama;document.getElementById('eHp').value=d.no_hp||'';document.getElementById('eAl').value=d.alamat||'';openModal('mEdit');}
</script>

<?php require_once '../views/admin_footer.php'; ?>