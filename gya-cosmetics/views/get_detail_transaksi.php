<?php
// ============================================
// admin/get_detail_transaksi.php
// Ambil detail transaksi via AJAX
// ============================================
require_once '../config/config.php';
cekLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { echo '<p class="text-danger">ID tidak valid</p>'; exit(); }

$t = $conn->query("
    SELECT t.*, p.nama as nama_pelanggan, u.nama as nama_admin
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    LEFT JOIN users u ON t.user_id = u.id
    WHERE t.id = $id
")->fetch_assoc();

if (!$t) { echo '<p class="text-danger">Transaksi tidak ditemukan</p>'; exit(); }

$detail = $conn->query("
    SELECT dt.*, pr.nama_produk
    FROM detail_transaksi dt
    LEFT JOIN produk pr ON dt.produk_id = pr.id
    WHERE dt.transaksi_id = $id
");
?>

<div class="row g-3 mb-4">
    <div class="col-6">
        <small class="text-muted d-block">Kode Transaksi</small>
        <strong style="color:var(--pink-primary)"><?= $t['kode_transaksi'] ?></strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Tanggal</small>
        <strong><?= date('d M Y H:i', strtotime($t['created_at'])) ?></strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Pelanggan</small>
        <strong><?= htmlspecialchars($t['nama_pelanggan'] ?? 'Pelanggan Umum') ?></strong>
    </div>
    <div class="col-6">
        <small class="text-muted d-block">Admin</small>
        <strong><?= htmlspecialchars($t['nama_admin']) ?></strong>
    </div>
    <div class="col-4">
        <small class="text-muted d-block">Tipe</small>
        <span class="badge <?= $t['tipe_penjualan'] === 'online' ? 'bg-purple' : 'bg-secondary' ?>">
            <?= ucfirst($t['tipe_penjualan']) ?>
        </span>
    </div>
    <div class="col-4">
        <small class="text-muted d-block">Metode</small>
        <span class="badge <?= $t['metode_bayar'] === 'kredit' ? 'bg-warning text-dark' : 'bg-success' ?>">
            <?= ucfirst($t['metode_bayar']) ?>
        </span>
    </div>
    <div class="col-4">
        <small class="text-muted d-block">Status</small>
        <span class="badge-<?= $t['status_transaksi'] ?>"><?= ucfirst($t['status_transaksi']) ?></span>
    </div>
    <?php if ($t['jatuh_tempo']): ?>
    <div class="col-6">
        <small class="text-muted d-block">Jatuh Tempo</small>
        <strong class="text-warning"><?= formatTanggal($t['jatuh_tempo']) ?></strong>
    </div>
    <?php endif; ?>
    <?php if ($t['catatan']): ?>
    <div class="col-12">
        <small class="text-muted d-block">Catatan</small>
        <span><?= htmlspecialchars($t['catatan']) ?></span>
    </div>
    <?php endif; ?>
</div>

<h6 class="fw-bold mb-3" style="color:var(--pink-dark);">
    <i class="bi bi-bag me-2"></i>Rincian Produk
</h6>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($d = $detail->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                <td class="text-center"><?= $d['jumlah'] ?> pcs</td>
                <td class="text-end"><?= formatRupiah($d['harga_satuan']) ?></td>
                <td class="text-end"><strong><?= formatRupiah($d['subtotal']) ?></strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="background:var(--pink-soft);">
                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold" style="color:var(--pink-primary); font-size:1.1rem;">
                    <?= formatRupiah($t['total_harga']) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>