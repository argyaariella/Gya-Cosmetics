<?php
require_once '../config/config.php';
cekRole('owner');
$page_title = 'Manajemen User';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $aksi=$_POST['aksi']??'';
    if ($aksi==='tambah') {
        $nama=bersihkan($_POST['nama']);$uname=bersihkan($_POST['username']);
        $pass=$_POST['password']??'';$role=bersihkan($_POST['role']);$sts=bersihkan($_POST['status']);
        if(empty($nama)||empty($uname)||empty($pass)){setAlert('Semua field wajib diisi!','danger');}
        else {
            $cek=$conn->query("SELECT id FROM users WHERE username='$uname'")->fetch_assoc();
            if($cek){setAlert('Username sudah digunakan!','danger');}
            else {
                $hash=password_hash($pass,PASSWORD_DEFAULT);
                $st=$conn->prepare("INSERT INTO users(nama,username,password,role,status)VALUES(?,?,?,?,?)");
                $st->bind_param("sssss",$nama,$uname,$hash,$role,$sts);
                if($st->execute()){simpanLog($_SESSION['user_id'],'tambah_user',"Tambah: $uname");setAlert("User <b>$uname</b> berhasil ditambahkan!",'success');}
                else setAlert('Gagal menyimpan!','danger');
                $st->close();
            }
        }
    } elseif($aksi==='edit'){
        $id=(int)$_POST['id'];$nama=bersihkan($_POST['nama']);$uname=bersihkan($_POST['username']);
        $role=bersihkan($_POST['role']);$sts=bersihkan($_POST['status']);$pass=$_POST['password']??'';
        if($id===(int)$_SESSION['user_id']&&$sts==='nonaktif'){setAlert('Tidak bisa menonaktifkan akun sendiri!','danger');}
        else {
            if(!empty($pass)){
                $hash=password_hash($pass,PASSWORD_DEFAULT);
                $st=$conn->prepare("UPDATE users SET nama=?,username=?,password=?,role=?,status=? WHERE id=?");
                $st->bind_param("sssssi",$nama,$uname,$hash,$role,$sts,$id);
            } else {
                $st=$conn->prepare("UPDATE users SET nama=?,username=?,role=?,status=? WHERE id=?");
                $st->bind_param("ssssi",$nama,$uname,$role,$sts,$id);
            }
            if($st->execute()){simpanLog($_SESSION['user_id'],'edit_user',"Edit ID:$id");setAlert('User berhasil diperbarui!','success');}
            else setAlert('Gagal!','danger');
            $st->close();
        }
    } elseif($aksi==='hapus'){
        $id=(int)$_POST['id'];
        if($id===(int)$_SESSION['user_id']){setAlert('Tidak bisa menghapus akun sendiri!','danger');}
        else{$conn->query("DELETE FROM users WHERE id=$id");simpanLog($_SESSION['user_id'],'hapus_user',"Hapus ID:$id");setAlert('User dihapus!','success');}
    }
    header('Location: user.php');exit();
}

$rows=$conn->query("SELECT * FROM users ORDER BY role DESC,nama ASC");
require_once '../views/owner_header.php';
?>
<?php tampilAlert(); ?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:14px;">
  <div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;color:#1a0a14;margin-bottom:4px;">Manajemen User</h2>
    <p style="font-size:.84rem;color:#b08fa0;">Kelola akun admin dan owner sistem GYA Cosmetics</p>
  </div>
  <button onclick="openModal('mTambah')" class="btn-gya btn-primary-gya"><i class="bi bi-person-plus-fill"></i> Tambah User</button>
</div>

<div class="row g-3">
  <?php if($rows&&$rows->num_rows>0): while($u=$rows->fetch_assoc()):
    $isMe=$u['id']==(int)$_SESSION['user_id'];
    $isOwner=$u['role']==='owner';
  ?>
  <div class="col-xl-4 col-md-6">
    <div class="stat-card" style="border-top:3px solid <?= $isOwner?'rgba(139,92,246,.5)':'rgba(244,63,136,.5)' ?>;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="width:50px;height:50px;border-radius:15px;background:<?= $isOwner?'rgba(139,92,246,.15)':'rgba(244,63,136,.15)' ?>;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:<?= $isOwner?'#7c3aed':'#f43f88' ?>;">
            <i class="bi <?= $isOwner?'bi-crown-fill':'bi-shield-fill' ?>"></i>
          </div>
          <div>
            <div style="font-weight:700;font-size:.95rem;color:#1a0a14;"><?= htmlspecialchars($u['nama']) ?></div>
            <div style="font-size:.75rem;color:#b08fa0;margin-top:2px;">@<?= htmlspecialchars($u['username']) ?></div>
          </div>
        </div>
        <?php if(!$isMe): ?>
        <div style="display:flex;gap:6px;">
          <button class="btn-gya btn-glass-gya btn-sm-gya" onclick='editUser(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i></button>
          <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Hapus user <?= htmlspecialchars(addslashes($u['nama'])) ?>?')">
            <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $u['id'] ?>">
            <button type="submit" class="btn-gya btn-danger-gya btn-sm-gya"><i class="bi bi-trash3"></i></button>
          </form>
        </div>
        <?php else: ?>
        <span style="font-size:.72rem;background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2);padding:3px 10px;border-radius:20px;font-weight:700;">Akun Kamu</span>
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
        <span class="badge-gya <?= $isOwner?'badge-purple':'badge-pink' ?>">
          <i class="bi <?= $isOwner?'bi-crown':'bi-shield' ?>"></i> <?= ucfirst($u['role']) ?>
        </span>
        <span class="badge-gya <?= $u['status']==='aktif'?'badge-success':'badge-danger' ?>">
          <i class="bi bi-circle-fill" style="font-size:.45rem;"></i> <?= ucfirst($u['status']) ?>
        </span>
      </div>
      <div style="font-size:.74rem;color:#b08fa0;"><i class="bi bi-calendar3"></i> Bergabung <?= date('d M Y',strtotime($u['created_at'])) ?></div>
    </div>
  </div>
  <?php endwhile; endif; ?>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-gya" id="mTambah"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-person-plus" style="color:#7c3aed;margin-right:8px;"></i>Tambah User Baru</div><button class="modal-close" onclick="closeModal('mTambah')"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST"><input type="hidden" name="aksi" value="tambah">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Nama Lengkap <span style="color:#f43f88;">*</span></label><input type="text" name="nama" class="form-control-gya" placeholder="Nama lengkap" required></div>
        <div class="form-group"><label class="form-label-gya">Username <span style="color:#f43f88;">*</span></label><input type="text" name="username" class="form-control-gya" placeholder="Username untuk login" required></div>
        <div class="form-group"><label class="form-label-gya">Password <span style="color:#f43f88;">*</span></label><input type="password" name="password" class="form-control-gya" placeholder="Min. 6 karakter" required minlength="6"></div>
        <div class="row g-3">
          <div class="col-6"><div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Role</label><select name="role" class="form-select-gya"><option value="admin">Admin</option><option value="owner">Owner</option></select></div></div>
          <div class="col-6"><div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Status</label><select name="status" class="form-select-gya"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div></div>
        </div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mTambah')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan User</button></div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-gya" id="mEdit"><div class="modal-overlay"></div>
  <div class="modal-box">
    <div class="modal-header-gya"><div class="modal-title-gya"><i class="bi bi-pencil-square" style="color:#3b82f6;margin-right:8px;"></i>Edit User</div><button class="modal-close" onclick="closeModal('mEdit')"><i class="bi bi-x-lg"></i></button></div>
    <form method="POST"><input type="hidden" name="aksi" value="edit"><input type="hidden" name="id" id="eId">
      <div class="modal-body-gya">
        <div class="form-group"><label class="form-label-gya">Nama Lengkap</label><input type="text" name="nama" id="eNama" class="form-control-gya" required></div>
        <div class="form-group"><label class="form-label-gya">Username</label><input type="text" name="username" id="eUser" class="form-control-gya" required></div>
        <div class="form-group"><label class="form-label-gya">Password Baru <small style="color:#b08fa0;">(kosongkan jika tidak diubah)</small></label><input type="password" name="password" class="form-control-gya" placeholder="Password baru..." minlength="6"></div>
        <div class="row g-3">
          <div class="col-6"><div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Role</label><select name="role" id="eRole" class="form-select-gya"><option value="admin">Admin</option><option value="owner">Owner</option></select></div></div>
          <div class="col-6"><div class="form-group" style="margin-bottom:0;"><label class="form-label-gya">Status</label><select name="status" id="eSts" class="form-select-gya"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div></div>
        </div>
      </div>
      <div class="modal-footer-gya"><button type="button" class="btn-gya btn-glass-gya" onclick="closeModal('mEdit')">Batal</button><button type="submit" class="btn-gya btn-primary-gya"><i class="bi bi-check-circle"></i> Simpan</button></div>
    </form>
  </div>
</div>
<script>
function editUser(d){document.getElementById('eId').value=d.id;document.getElementById('eNama').value=d.nama;document.getElementById('eUser').value=d.username;document.getElementById('eRole').value=d.role;document.getElementById('eSts').value=d.status;openModal('mEdit');}
</script>
<?php require_once '../views/owner_footer.php'; ?>