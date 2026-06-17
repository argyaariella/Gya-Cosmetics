<?php
require_once '../config/config.php';
cekRole('admin');
$page_title = 'Kategori';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi']??'';
    if ($aksi==='tambah') {
        $nama = bersihkan($_POST['nama_kategori']);
        $desk = bersihkan($_POST['deskripsi']??'');
        if (empty($nama)) { setAlert('Nama kategori wajib diisi!','danger'); }
        else {
            $st=$conn->prepare("INSERT INTO kategori(nama_kategori,deskripsi) VALUES(?,?)");
            $st->bind_param("ss",$nama,$desk);
            if($st->execute()){ simpanLog($_SESSION['user_id'],'tambah_kategori',"Tambah: $nama"); setAlert("Kategori <b>$nama</b> ditambahkan!",'success'); }
            else setAlert('Gagal!','danger');
            $st->close();
        }
    } elseif ($aksi==='edit') {
        $id=$_POST['id']; $nama=bersihkan($_POST['nama_kategori']); $desk=bersihkan($_POST['deskripsi']??'');
        $st=$conn->prepare("UPDATE kategori SET nama_kategori=?,deskripsi=? WHERE id=?");
        $st->bind_param("ssi",$nama,$desk,$id);
        if($st->execute()) setAlert('Kategori diperbarui!','success'); else setAlert('Gagal!','danger');
        $st->close();
    } elseif ($aksi==='hapus') {
        $id=(int)$_POST['id'];
        $cek=$conn->query("SELECT COUNT(*) as n FROM produk WHERE kategori_id=$id")->fetch_assoc()['n'];
        if($cek>0) setAlert('Tidak bisa dihapus, masih ada produk di kategori ini!','warning');
        else { $conn->query("DELETE FROM kategori WHERE id=$id"); setAlert('Kategori dihapus!','success'); }
    }
    header('Location: kategori.php'); exit();
}

$rows = $conn->query("SELECT k.*,(SELECT COUNT(*) FROM produk p WHERE p.kategori_id=k.id) as jml_produk FROM kategori k ORDER BY k.nama_kategori");
require_once '../views/admin_header.php';
?>

<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Kategori Produk</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Kelola kategori untuk pengelompokan produk</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya">
    <i class="bi bi-plus-circle"></i> Tambah Kategori
  </button>
</div>

<!-- GRID KATEGORI -->
<div class="row g-3 mb-4">
  <?php
  $icons = ['Skincare'=>'bi-droplet-half','Makeup'=>'bi-palette','Sunscreen'=>'bi-brightness-high','Bodycare'=>'bi-heart','Haircare'=>'bi-wind','Nail & Tools'=>'bi-scissors','Aksesoris'=>'bi-stars','Parfum'=>'bi-flower3','Cleanser'=>'bi-droplet'];
  $colors = ['#f43f88','#e879f9','#fb923c','#f43f88','#34d399','#f59e0b','#a78bfa','#fb7185','#38bdf8'];
  $ci = 0; $rows->data_seek(0);
  while($k=$rows->fetch_assoc()):
    $ic = $icons[$k['nama_kategori']] ?? 'bi-tag';
    $cl = $colors[$ci % count($colors)]; $ci++;
  ?>
  <div class="col-xl-3 col-md-4 col-sm-6">
    <div class="stat-card" style="border-left:3px solid <?=$cl?>55;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div class="stat-icon-wrap" style="background:<?=$cl?>22;">
          <i class="bi <?=$ic?>" style="color:<?=$cl?>;font-size:1.2rem;"></i>
        </div>
        <div style="display:flex;gap:6px;">
          <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editKat(<?=json_encode($k)?>,`<?=$cl?>`)'>
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Hapus kategori <?=htmlspecialchars(addslashes($k['nama_kategori']))?> ?')">
            <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?=$k['id']?>">
            <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya"><i class="bi bi-trash3"></i></button>
          </form>
        </div>
      </div>
      <div class="stat-value" style="font-size:1.5rem;margin-top:12px;"><?=htmlspecialchars($k['nama_kategori'])?></div>
      <div class="stat-label"><?=$k['jml_produk']?> produk</div>
      <?php if($k['deskripsi']):?>
      <div style="font-size:.75rem;color:#b08fa0;margin-top:6px;line-height:1.5;"><?=htmlspecialchars($k['deskripsi'])?></div>
      <?php endif;?>
    </div>
  </div>
  <?php endwhile;?>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah">
  <div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-plus-circle" style="color:#f43f88;margin-right:8px;"></i>Tambah Kategori</div>
      <button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group">
          <label class="form-label-gya">Nama Kategori <span style="color:#f43f88;">*</span></label>
          <input type="text" name="nama_kategori" class="form-control-gya" placeholder="Contoh: Skincare" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label-gya">Deskripsi</label>
          <textarea name="deskripsi" class="form-control-gya" rows="3" placeholder="Deskripsi kategori..."></textarea>
        </div>
      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit">
  <div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya">
      <div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#3b82f6;margin-right:8px;"></i>Edit Kategori</div>
      <button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="aksi" value="edit"><input type="hidden" name="id" id="eId">
      <div class="modal-body-gya">
        <div class="form-group">
          <label class="form-label-gya">Nama Kategori <span style="color:#f43f88;">*</span></label>
          <input type="text" name="nama_kategori" id="eNama" class="form-control-gya" required>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label-gya">Deskripsi</label>
          <textarea name="deskripsi" id="eDesk" class="form-control-gya" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer-gya">
        <button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mEdit')">Batal</button>
        <button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editKat(d,c){
  document.getElementById('eId').value=d.id;
  document.getElementById('eNama').value=d.nama_kategori;
  document.getElementById('eDesk').value=d.deskripsi||'';
  openModal('mEdit');
}
</script>
<?php require_once '../views/admin_footer.php'; ?>