<?php
require_once '../config/config.php';
cekLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die('ID tidak valid'); }

$t = $conn->query("
    SELECT t.*, p.nama as nm_pel, p.no_hp, u.nama as nm_usr, pm.judul as promo_judul
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.pelanggan_id=p.id
    LEFT JOIN users u ON t.user_id=u.id
    LEFT JOIN promo pm ON t.promo_id=pm.id
    WHERE t.id=$id
")->fetch_assoc();

if (!$t) { die('Transaksi tidak ditemukan'); }

$detail = $conn->query("
    SELECT dt.*, pr.nama_produk, pr.brand
    FROM detail_transaksi dt
    LEFT JOIN produk pr ON dt.produk_id=pr.id
    WHERE dt.transaksi_id=$id
");

// Tentukan kembalian jika ada (untuk kredit kembalian 0)
// Jika kasir input tunai > total, ini bisa dihitung. Tapi sistem saat ini tidak nyimpen 'uang cash' pelanggan, jadi kita pakai asumsi Lunas.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - <?=htmlspecialchars($t['kode_transaksi'])?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk standar */
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .struk-container {
            width: 80mm; /* Lebar kertas thermal 80mm, kalau 58mm bisa diubah via CSS media print */
            margin: 0 auto;
            padding: 5mm;
            box-sizing: border-box;
        }
        h2 { margin: 0 0 5px 0; text-align: center; font-size: 16px; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .info { margin-bottom: 10px; font-size: 11px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        td, th { padding: 2px 0; font-size: 12px; vertical-align: top; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .footer { text-align: center; margin-top: 10px; font-size: 11px; }
        @media print {
            body { margin: 0; padding: 0; }
            .struk-container { width: 100%; max-width: 80mm; padding: 0; margin: 0; }
            .btn-print { display: none; }
        }
        .btn-print {
            display: block;
            width: 80mm;
            margin: 10px auto;
            padding: 10px;
            background: #f43f88;
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-family: sans-serif;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">

<a href="#" class="btn-print" onclick="window.print()">🖨️ Cetak Struk</a>

<div class="struk-container">
    <h2>GYA COSMETICS</h2>
    <div class="text-center" style="font-size:11px;margin-bottom:8px;">
        Jl. Kosmetik Indah No. 123<br>
        Telp: 0812-3456-7890<br>
        IG: @gyacosmetics
    </div>

    <div class="info">
        <table style="font-size:11px;">
            <tr><td width="30%">Kode</td><td width="5%">:</td><td><?=htmlspecialchars($t['kode_transaksi'])?></td></tr>
            <tr><td>Tanggal</td><td>:</td><td><?=date('d/m/Y H:i', strtotime($t['created_at']))?></td></tr>
            <tr><td>Kasir</td><td>:</td><td><?=htmlspecialchars($t['nm_usr'])?></td></tr>
            <tr><td>Pelanggan</td><td>:</td><td><?=htmlspecialchars($t['nm_pel'] ?? 'Umum')?></td></tr>
        </table>
    </div>

    <table>
        <?php while($d = $detail->fetch_assoc()): ?>
        <tr>
            <td colspan="3"><?=htmlspecialchars($d['nama_produk'])?></td>
        </tr>
        <tr>
            <td width="20%"><?=$d['jumlah']?>x</td>
            <td width="40%" class="text-right"><?=number_format($d['harga_satuan'],0,',','.')?></td>
            <td width="40%" class="text-right"><?=number_format($d['subtotal'],0,',','.')?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <table style="margin-top:5px;" class="border-top">
        <tr>
            <td width="60%" class="text-right" style="padding-top:5px;"><b>TOTAL KOTOR</b></td>
            <td width="40%" class="text-right" style="padding-top:5px;"><b><?=number_format($t['total_harga'],0,',','.')?></b></td>
        </tr>
        <?php if(isset($t['diskon']) && $t['diskon'] > 0): ?>
        <tr>
            <td class="text-right">Promo (<?=htmlspecialchars($t['promo_judul']??'Diskon')?>)</td>
            <td class="text-right">-<?=number_format($t['diskon'],0,',','.')?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="text-right border-bottom" style="font-size:14px; font-weight:bold; padding-bottom:5px;">TOTAL</td>
            <td class="text-right border-bottom" style="font-size:14px; font-weight:bold; padding-bottom:5px;"><?=number_format($t['total_harga'] - ($t['diskon']??0),0,',','.')?></td>
        </tr>
        
        <tr>
            <td class="text-right" style="padding-top:5px;">Bayar</td>
            <td class="text-right" style="padding-top:5px;">
                <?php if($t['metode_bayar'] === 'kredit'): ?>
                    0 (KREDIT)
                <?php else: ?>
                    <?=number_format($t['total_bayar'],0,',','.')?>
                <?php endif; ?>
            </td>
        </tr>
        <?php if($t['metode_bayar'] === 'kredit' && $t['jatuh_tempo']): ?>
        <tr>
            <td class="text-right">Jatuh Tempo</td>
            <td class="text-right"><?=date('d/m/Y', strtotime($t['jatuh_tempo']))?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="footer">
        *** TERIMA KASIH ***<br>
        Barang yang sudah dibeli tidak<br>
        dapat ditukar/dikembalikan
    </div>
</div>

</body>
</html>
