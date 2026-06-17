<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Supplier';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah') {
        $nama = bersihkan($_POST['nama']);
        $hp   = bersihkan($_POST['no_hp'] ?? '');
        $al   = bersihkan($_POST['alamat'] ?? '');
        if (empty($nama)) { setAlert('Nama supplier wajib!', 'danger'); }
        else {
            $st = $conn->prepare("INSERT INTO supplier(nama,no_hp,alamat)VALUES(?,?,?)");
            $st->bind_param("sss", $nama, $hp, $al);
            if ($st->execute()) { simpanLog($_SESSION['user_id'], 'tambah_supplier', "Tambah: $nama"); setAlert("Supplier <b>$nama</b> ditambahkan!", 'success'); }
            else setAlert('Gagal!', 'danger');
            $st->close();
        }
    } elseif ($aksi === 'edit') {
        $id = (int)$_POST['id']; $nama = bersihkan($_POST['nama']); $hp = bersihkan($_POST['no_hp'] ?? ''); $al = bersihkan($_POST['alamat'] ?? '');
        $st = $conn->prepare("UPDATE supplier SET nama=?,no_hp=?,alamat=? WHERE id=?");
        $st->bind_param("sssi", $nama, $hp, $al, $id);
        if ($st->execute()) setAlert('Supplier diperbarui!', 'success'); else setAlert('Gagal!', 'danger');
        $st->close();
    } elseif ($aksi === 'hapus') {
        $id = (int)$_POST['id'];
        $cek = $conn->query("SELECT COUNT(*) as n FROM barang_masuk WHERE supplier_id=$id")->fetch_assoc()['n'];
        if ($cek > 0) setAlert('Tidak bisa dihapus, supplier sudah punya riwayat barang masuk!', 'warning');
        else { $conn->query("DELETE FROM supplier WHERE id=$id"); setAlert('Supplier dihapus!', 'success'); }
    }
    header('Location: supplier.php'); exit();
}

$rows = $conn->query("
    SELECT s.*, COUNT(DISTINCT b.id) as jml_masuk, COALESCE(SUM(b.total_harga),0) as total_nilai
    FROM supplier s
    LEFT JOIN barang_masuk b ON s.id = b.supplier_id
    GROUP BY s.id ORDER BY s.nama
");

require_once '../views/owner_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Data Supplier</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Kelola data pemasok produk GYA Cosmetics</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya">
    <i class="bi bi-plus-circle"></i> Tambah Supplier
  </button>
</div>

<div class="content-card">
  <div style="overflow-x:auto;">
    <table class="table-gya" style="min-width:650px;">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Supplier</th>
          <th>No. HP</th>
          <th>Alamat</th>
          <th>Total Masuk</th>
          <th>Nilai Barang</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows && $rows->num_rows > 0): $no = 1; while ($s = $rows->fetch_assoc()): ?>
        <tr>
          <td style="color:#b08fa0;font-size:.79rem;"><?= $no++ ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(244,63,136,.1));display:flex;align-items:center;justify-content:center;color:#7c3aed;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-truck"></i>
              </div>
              <span style="font-weight:600;font-size:.87rem;"><?= htmlspecialchars($s['nama']) ?></span>
            </div>
          </td>
          <td>
            <?php if ($s['no_hp']): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $s['no_hp']) ?>" target="_blank"
               style="color:#25d366;text-decoration:none;font-size:.85rem;display:flex;align-items:center;gap:5px;">
              <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($s['no_hp']) ?>
            </a>
            <?php else: ?><span style="color:#b08fa0;font-size:.83rem;">—</span><?php endif; ?>
          </td>
          <td style="font-size:.83rem;color:#7c3f5e;max-width:200px;"><?= htmlspecialchars($s['alamat'] ?? '—') ?></td>
          <td><span class="badge-gya badge-purple"><?= $s['jml_masuk'] ?> kali</span></td>
          <td style="font-weight:700;font-family:'Cormorant Garamond',serif;font-size:1rem;"><?= formatRupiah($s['total_nilai']) ?></td>
          <td style="text-align:center;">
            <div style="display:flex;gap:6px;justify-content:center;">
              <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editSup(<?= json_encode($s) ?>)'><i class="bi bi-pencil"></i></button>
              <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Hapus supplier ini?')">
                <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya"><i class="bi bi-trash3"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="7" class="empty-state"><span class="empty-icon"><i class="bi bi-truck"></i></span><div>Belum ada data supplier</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-truck" style="color:#7c3aed;margin-right:8px;"></i>Tambah Supplier</div><button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Nama Supplier <span style="color:#f43f88;">*</span></label><input type="text" name="nama" class="form-control-gya" placeholder="Nama perusahaan/distributor" required></div>
        <div class="form-group"><label class="form-label-gya">No. HP / WhatsApp</label><input type="text" name="no_hp" class="form-control-gya" placeholder="08xxxxxxxxxx"></div>
        <div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Alamat</label><textarea name="alamat" class="form-control-gya" rows="2" placeholder="Alamat supplier..."></textarea></div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button></div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#3b82f6;margin-right:8px;"></i>Edit Supplier</div><button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button></div>
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
function editSup(d) {
  document.getElementById('eId').value    = d.id;
  document.getElementById('eNama').value  = d.nama;
  document.getElementById('eHp').value    = d.no_hp || '';
  document.getElementById('eAl').value    = d.alamat || '';
  openModal('mEdit');
}
</script>

<?php require_once '../views/owner_footer.php'; ?>