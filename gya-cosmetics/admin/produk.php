<?php
// ============================================
// admin/produk.php
// Halaman Manajemen Produk (CRUD)
// ============================================

require_once '../config/config.php';
cekRole('admin');

$page_title = 'Manajemen Produk';
$pesan = '';
$tipe_pesan = '';

// ============================================
// PROSES FORM (Tambah / Edit / Hapus)
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    
    // ---------- TAMBAH ----------
    if ($aksi === 'tambah') {
        $nama      = bersihkan($_POST['nama_produk']);
        $kat_id    = (int)$_POST['kategori_id'];
        $brand     = bersihkan($_POST['brand']);
        $deskripsi = bersihkan($_POST['deskripsi']);
        $cara      = bersihkan($_POST['cara_pakai']);
        $h_beli    = (float)$_POST['harga_beli'];
        $h_offline = (float)$_POST['harga_jual_offline'];
        $h_online  = (float)$_POST['harga_jual_online'];
        $stok      = (int)$_POST['stok'];
        $stok_min  = (int)$_POST['stok_minimum'];
        $status    = bersihkan($_POST['status']);

        // Handle Image Upload
        $gambar_name = NULL;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $gambar_name = 'produk_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/images/' . $gambar_name);
            }
        }

        // Validasi
        if (empty($nama) || $kat_id <= 0 || $h_beli <= 0 || $h_offline <= 0) {
            setAlert('Semua field wajib diisi dengan benar!', 'danger');
        } elseif ($stok < 0) {
            setAlert('Stok tidak boleh minus!', 'danger');
        } else {
            $stmt = $conn->prepare("INSERT INTO produk (nama_produk, kategori_id, brand, deskripsi, cara_pakai, harga_beli, harga_jual_offline, harga_jual_online, stok, stok_minimum, status, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssdddisss", $nama, $kat_id, $brand, $deskripsi, $cara, $h_beli, $h_offline, $h_online, $stok, $stok_min, $status, $gambar_name);
            
            if ($stmt->execute()) {
                simpanLog($_SESSION['user_id'], 'tambah_produk', "Tambah produk: $nama");
                setAlert("Produk <strong>$nama</strong> berhasil ditambahkan!", 'success');
            } else {
                setAlert('Gagal menyimpan produk!', 'danger');
            }
            $stmt->close();
        }
    }
    
    // ---------- EDIT ----------
    elseif ($aksi === 'edit') {
        $id        = (int)$_POST['id'];
        $nama      = bersihkan($_POST['nama_produk']);
        $kat_id    = (int)$_POST['kategori_id'];
        $brand     = bersihkan($_POST['brand']);
        $deskripsi = bersihkan($_POST['deskripsi']);
        $cara      = bersihkan($_POST['cara_pakai']);
        $h_beli    = (float)$_POST['harga_beli'];
        $h_offline = (float)$_POST['harga_jual_offline'];
        $h_online  = (float)$_POST['harga_jual_online'];
        $stok      = (int)$_POST['stok'];
        $stok_min  = (int)$_POST['stok_minimum'];
        $status    = bersihkan($_POST['status']);

        // Handle Image Upload
        $gambar_query = "";
        $gambar_name = "";
        $bind_types = "sisssdddissi";
        $bind_params = [&$nama, &$kat_id, &$brand, &$deskripsi, &$cara, &$h_beli, &$h_offline, &$h_online, &$stok, &$stok_min, &$status, &$id];

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $gambar_name = 'produk_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if(move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/images/' . $gambar_name)) {
                    $gambar_query = ", gambar=?";
                    $bind_types = "sisssdddisssi";
                    // Insert gambar param before id
                    array_pop($bind_params);
                    $bind_params[] = &$gambar_name;
                    $bind_params[] = &$id;
                }
            }
        }

        if (empty($nama) || $kat_id <= 0 || $h_offline <= 0) {
            setAlert('Semua field wajib diisi!', 'danger');
        } elseif ($stok < 0) {
            setAlert('Stok tidak boleh minus!', 'danger');
        } else {
            $sql = "UPDATE produk SET nama_produk=?, kategori_id=?, brand=?, deskripsi=?, cara_pakai=?, harga_beli=?, harga_jual_offline=?, harga_jual_online=?, stok=?, stok_minimum=?, status=? $gambar_query WHERE id=?";
            $stmt = $conn->prepare($sql);
            
            // Call bind_param dynamically
            $stmt->bind_param($bind_types, ...$bind_params);
            
            if ($stmt->execute()) {
                simpanLog($_SESSION['user_id'], 'edit_produk', "Edit produk ID: $id");
                setAlert("Produk berhasil diperbarui!", 'success');
            } else {
                setAlert('Gagal memperbarui produk!', 'danger');
            }
            $stmt->close();
        }
    }
    
    // ---------- HAPUS ----------
    elseif ($aksi === 'hapus') {
        $id = (int)$_POST['id'];
        // Cek apakah produk sudah pernah ada di transaksi
        $cek = $conn->query("SELECT COUNT(*) as total FROM detail_transaksi WHERE produk_id = $id");
        if ($cek->fetch_assoc()['total'] > 0) {
            setAlert('Produk tidak bisa dihapus karena sudah ada di transaksi! Nonaktifkan saja.', 'warning');
        } else {
            $conn->query("DELETE FROM produk WHERE id = $id");
            simpanLog($_SESSION['user_id'], 'hapus_produk', "Hapus produk ID: $id");
            setAlert('Produk berhasil dihapus!', 'success');
        }
    }
    
    header('Location: produk.php');
    exit();
}

// ============================================
// AMBIL DATA UNTUK DITAMPILKAN
// ============================================

// Pagination
$per_page = 15;
$halaman  = max(1, (int)($_GET['hal'] ?? 1));
$offset   = ($halaman - 1) * $per_page;

// Filter & Search
$search     = bersihkan($_GET['cari'] ?? '');
$filter_kat = (int)($_GET['kategori'] ?? 0);
$filter_status = bersihkan($_GET['status'] ?? '');

$where = "WHERE 1=1";
if (!empty($search)) $where .= " AND p.nama_produk LIKE '%$search%'";
if ($filter_kat > 0)  $where .= " AND p.kategori_id = $filter_kat";
if (!empty($filter_status)) $where .= " AND p.status = '$filter_status'";

// Hitung total
$total_row  = $conn->query("SELECT COUNT(*) as total FROM produk p $where")->fetch_assoc()['total'];
$total_hal  = ceil($total_row / $per_page);

// Ambil data produk
$produk_list = $conn->query("
    SELECT p.*, k.nama_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    $where
    ORDER BY p.created_at DESC
    LIMIT $per_page OFFSET $offset
");

// Ambil semua kategori untuk dropdown
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");

require_once '../views/admin_header.php';
?>

<!-- Alert -->
<?php tampilAlert(); ?>

<!-- ============================================ -->
<!-- HEADER HALAMAN -->
<!-- ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold">Daftar Produk</h5>
        <small class="text-muted">Total: <?= $total_row ?> produk</small>
    </div>
    <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i>Tambah Produk
    </button>
</div>

<!-- ============================================ -->
<!-- FILTER & SEARCH -->
<!-- ============================================ -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cari Produk</label>
                <input type="text" name="cari" class="form-control" placeholder="Nama produk..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="0">Semua Kategori</option>
                    <?php 
                    $kategori_list->data_seek(0);
                    while ($k = $kategori_list->fetch_assoc()): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kat == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="aktif" <?= $filter_status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $filter_status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-pink flex-grow-1">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="produk.php" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- ============================================ -->
<!-- TABEL PRODUK -->
<!-- ============================================ -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Offline</th>
                        <th>Harga Online</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produk_list->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($p = $produk_list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong style="font-size:0.875rem;"><?= htmlspecialchars($p['nama_produk']) ?></strong>
                                <?php if ($p['brand']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($p['brand']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($p['nama_kategori']) ?></small></td>
                            <td><?= formatRupiah($p['harga_beli']) ?></td>
                            <td><span class="text-success fw-600"><?= formatRupiah($p['harga_jual_offline']) ?></span></td>
                            <td><span class="text-info fw-600"><?= formatRupiah($p['harga_jual_online']) ?></span></td>
                            <td>
                                <?php 
                                $stok_class = 'success';
                                if ($p['stok'] == 0) $stok_class = 'danger';
                                elseif ($p['stok'] <= $p['stok_minimum']) $stok_class = 'warning';
                                ?>
                                <span class="badge bg-<?= $stok_class ?>"><?= $p['stok'] ?> pcs</span>
                            </td>
                            <td>
                                <span class="badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                    onclick="editProduk(<?= htmlspecialchars(json_encode($p)) ?>)"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" style="display:inline;" 
                                    onsubmit="return confirm('Hapus produk ini?')">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-search" style="font-size:2.5rem; opacity:0.3;"></i>
                                <p class="mt-3 mb-0">Produk tidak ditemukan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_hal > 1): ?>
    <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center px-4 py-3">
        <small class="text-muted">
            Menampilkan <?= $offset+1 ?>–<?= min($offset+$per_page, $total_row) ?> dari <?= $total_row ?> data
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $total_hal; $i++): ?>
                <li class="page-item <?= $i == $halaman ? 'active' : '' ?>">
                    <a class="page-link" href="?hal=<?= $i ?>&cari=<?= urlencode($search) ?>&kategori=<?= $filter_kat ?>&status=<?= $filter_status ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================ -->
<!-- MODAL TAMBAH PRODUK -->
<!-- ============================================ -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#e91e8c,#be185d); color:white; border:none;">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="aksi" value="tambah">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" class="form-control" required placeholder="Contoh: Handbody Lotion Whitening">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Contoh: Vaseline">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php 
                                $kategori_list->data_seek(0);
                                while ($k = $kategori_list->fetch_assoc()): ?>
                                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_beli" class="form-control" required min="0" placeholder="25000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual Offline <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_jual_offline" class="form-control" required min="0" placeholder="35000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual Online</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_jual_online" class="form-control" min="0" placeholder="38000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok Minimum (Notif)</label>
                            <input type="number" name="stok_minimum" class="form-control" value="5" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi produk..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cara Pakai</label>
                            <textarea name="cara_pakai" class="form-control" rows="2" placeholder="Cara penggunaan produk..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" name="gambar" class="form-control" accept="image/jpeg, image/png, image/webp">
                            <small class="text-muted">Format: JPG, PNG, WEBP. Boleh dikosongkan.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-pink px-4">
                        <i class="bi bi-check-circle me-1"></i>Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT PRODUK -->
<!-- ============================================ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#2563eb,#1d4ed8); color:white; border:none;">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formEdit" enctype="multipart/form-data">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" id="edit_brand" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="edit_kat" class="form-select" required>
                                <?php 
                                $kategori_list->data_seek(0);
                                while ($k = $kategori_list->fetch_assoc()): ?>
                                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_beli" id="edit_hbeli" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Offline <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_jual_offline" id="edit_hoffline" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Online</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_jual_online" id="edit_honline" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" id="edit_stok" class="form-control" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok Minimum</label>
                            <input type="number" name="stok_minimum" id="edit_stokmin" class="form-control" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cara Pakai</label>
                            <textarea name="cara_pakai" id="edit_cara" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ganti Gambar Produk</label>
                            <input type="file" name="gambar" class="form-control" accept="image/jpeg, image/png, image/webp">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Isi form edit dengan data produk yang dipilih
function editProduk(data) {
    document.getElementById('edit_id').value      = data.id;
    document.getElementById('edit_nama').value    = data.nama_produk;
    document.getElementById('edit_brand').value   = data.brand || '';
    document.getElementById('edit_kat').value     = data.kategori_id;
    document.getElementById('edit_status').value  = data.status;
    document.getElementById('edit_hbeli').value   = parseFloat(data.harga_beli);
    document.getElementById('edit_hoffline').value = parseFloat(data.harga_jual_offline);
    document.getElementById('edit_honline').value  = parseFloat(data.harga_jual_online);
    document.getElementById('edit_stok').value    = data.stok;
    document.getElementById('edit_stokmin').value = data.stok_minimum;
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    document.getElementById('edit_cara').value    = data.cara_pakai || '';
    
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<style>
/* Warna tombol pagination aktif */
.page-item.active .page-link {
    background-color: var(--pink-primary) !important;
    border-color: var(--pink-primary) !important;
}
.page-link { color: var(--pink-primary); }
.page-link:hover { color: var(--pink-dark); }
.fw-600 { font-weight: 600; }
</style>

<?php require_once '../views/admin_footer.php'; ?>