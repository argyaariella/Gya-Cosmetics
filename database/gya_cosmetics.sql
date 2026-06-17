-- phpMyAdmin SQL Dump
-- Database: `gya_cosmetics`
-- Generated: 2026-06-10 14:21:00
-- Gambar produk sudah terisi (produk_1.jpg s/d produk_163.jpg)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------


CREATE TABLE retur_produk (
    id_retur INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    alasan TEXT,
    tanggal_retur DATE NOT NULL,
    status ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `aktivitas`, `keterangan`, `ip_address`, `created_at`) VALUES
('1', '1', 'login', 'Login berhasil', '::1', '2026-05-07 15:17:18'),
('2', '2', 'login', 'Login berhasil', '::1', '2026-05-07 15:21:24'),
('3', '2', 'login', 'Login berhasil', '::1', '2026-05-09 11:27:09'),
('4', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:31:58'),
('5', '2', 'login', 'Login berhasil', '::1', '2026-05-09 11:33:02'),
('6', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:33:32'),
('7', '2', 'login', 'Login berhasil', '::1', '2026-05-09 11:34:56'),
('8', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:36:05'),
('9', '1', 'login', 'Login berhasil', '::1', '2026-05-09 11:36:18'),
('10', '1', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:38:24'),
('11', '2', 'login', 'Login berhasil', '::1', '2026-05-09 11:39:12'),
('12', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:40:26'),
('13', '1', 'login', 'Login berhasil', '::1', '2026-05-09 11:40:33'),
('14', '1', 'logout', 'Logout dari sistem', '::1', '2026-05-09 11:40:55'),
('15', '2', 'login', 'Login berhasil', '::1', '2026-05-10 00:15:03'),
('16', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-10 00:15:10'),
('17', '1', 'login', 'Login berhasil', '::1', '2026-05-10 00:15:14'),
('18', '1', 'backup_database', 'Export: gya_cosmetics_backup_20260509_191536.sql', '::1', '2026-05-10 00:15:36'),
('19', '1', 'login', 'Login berhasil', '::1', '2026-05-10 15:29:38'),
('20', '1', 'logout', 'Logout dari sistem', '::1', '2026-05-10 15:33:21'),
('21', '2', 'login', 'Login berhasil', '::1', '2026-05-10 15:33:30'),
('22', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-10 15:33:50'),
('23', '2', 'login', 'Login berhasil', '::1', '2026-05-11 02:24:46'),
('24', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-11 02:25:14'),
('25', '1', 'login', 'Login berhasil', '::1', '2026-05-11 02:25:22'),
('26', '1', 'logout', 'Logout dari sistem', '::1', '2026-05-11 02:25:44'),
('27', '2', 'login', 'Login berhasil', '::1', '2026-05-11 18:50:30'),
('28', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-11 18:52:53'),
('29', '1', 'login', 'Login berhasil', '::1', '2026-05-11 18:53:00'),
('30', '2', 'login', 'Login berhasil', '::1', '2026-05-16 18:10:34'),
('31', '2', 'logout', 'Logout dari sistem', '::1', '2026-05-16 18:10:58'),
('32', '1', 'login', 'Login berhasil', '::1', '2026-05-16 18:12:08'),
('33', '1', 'logout', 'Logout dari sistem', '::1', '2026-05-16 18:13:09'),
('34', '1', 'login', 'Login berhasil', '::1', '2026-06-10 16:14:43'),
('35', '1', 'logout', 'Logout dari sistem', '::1', '2026-06-10 17:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `produk_id` (`produk_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  CONSTRAINT `barang_masuk_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  CONSTRAINT `barang_masuk_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `transaksi_id` (`transaksi_id`),
  KEY `produk_id` (`produk_id`),
  CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
('1', 'Skincare', 'Serum, moisturizer, toner, essence, eye cream', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('2', 'Makeup', 'Lip cream, cushion, eyeshadow, blush, mascara', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('3', 'Sunscreen', 'Sunscreen wajah SPF 30 hingga SPF 50+', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('4', 'Bodycare', 'Body lotion, body scrub, body serum, deodorant', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('5', 'Haircare', 'Shampoo, conditioner, hair mask, hair mist', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('6', 'Nail & Tools', 'Kuku palsu, kuteks, kuas makeup, beauty tools', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('7', 'Aksesoris', 'Claw clip, scrunchie, pouch, strap HP, bros hijab', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('8', 'Parfum', 'Eau de parfum, body mist, roll on perfume', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('9', 'Cleanser', 'Facial wash, micellar water, cleansing balm', '2026-05-07 09:04:49', '2026-05-07 09:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `kredit`
--

CREATE TABLE `kredit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) NOT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `total_hutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sisa_hutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
  `tanggal_lunas` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksi_id` (`transaksi_id`),
  KEY `pelanggan_id` (`pelanggan_id`),
  CONSTRAINT `kredit_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kredit_ibfk_2` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(150) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `cara_pakai` text DEFAULT NULL,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual_offline` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual_online` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `stok_minimum` int(11) NOT NULL DEFAULT 5,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `kategori_id`, `brand`, `deskripsi`, `cara_pakai`, `harga_beli`, `harga_jual_offline`, `harga_jual_online`, `stok`, `stok_minimum`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Somethinc Niacinamide 10% + Zinc Serum', '1', 'Somethinc', 'Serum niacinamide viral untuk cerahkan kulit & minimalisir pori', 'Teteskan 3-4 tetes pagi & malam hari', '45000.00', '68000.00', '72000.00', '30', '5', 'produk_1.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('2', 'Somethinc Watermelon Wow Toner', '1', 'Somethinc', 'Toner hydrating dengan ekstrak watermelon untuk kulit lembab', 'Tuang ke kapas, usapkan ke wajah', '55000.00', '82000.00', '87000.00', '25', '5', 'produk_2.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('3', 'Somethinc Level 1% Retinol Serum', '1', 'Somethinc', 'Retinol serum anti penuaan & flek hitam untuk malam hari', 'Gunakan malam hari setelah toner, 2-3 tetes', '75000.00', '115000.00', '120000.00', '20', '5', 'produk_3.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('4', 'Skintific 5X Ceramide Barrier Moisturizer', '1', 'Skintific', 'Moisturizer barrier repair viral dengan 5 ceramide kompleks', 'Oleskan merata pagi & malam setelah serum', '55000.00', '85000.00', '90000.00', '35', '5', 'produk_4.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('5', 'Skintific Mugwort Pore Clarifying Clay Mask', '1', 'Skintific', 'Clay mask mugwort untuk bersihkan pori dan kontrol minyak', 'Aplikasikan tipis, diamkan 10-15 menit, bilas', '45000.00', '70000.00', '75000.00', '25', '5', 'produk_5.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('6', 'Skintific Symbol AF Acne Serum', '1', 'Skintific', 'Serum acne teknologi Symbol AF untuk atasi jerawat meradang', 'Teteskan ke area berjerawat pagi dan malam', '60000.00', '92000.00', '98000.00', '20', '5', 'produk_6.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('7', 'Avoskin Your Skin Bae Retinol 0.1% Serum', '1', 'Avoskin', 'Retinol 0.1% untuk anti aging, perbaikan tekstur dan warna kulit', 'Gunakan malam hari setelah toner', '65000.00', '100000.00', '105000.00', '20', '5', 'produk_7.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('8', 'Avoskin Miraculous Refining Toner AHA BHA PHA', '1', 'Avoskin', 'Toner eksfoliasi lembut AHA BHA PHA untuk kulit cerah & halus', 'Usap ke wajah dengan kapas, 2-3x seminggu malam', '55000.00', '85000.00', '90000.00', '15', '5', 'produk_8.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('9', 'Glad2Glow Hydra-Soothe Toner', '1', 'Glad2Glow', 'Toner hydrating dengan aloe vera & hyaluronic acid 3 layer', 'Tuang ke kapas atau tepuk langsung ke wajah', '28000.00', '42000.00', '45000.00', '30', '5', 'produk_9.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('10', 'Glad2Glow Brightening Vitamin C Serum 10%', '1', 'Glad2Glow', 'Serum vitamin C 10% untuk mencerahkan dan meratakan warna kulit', 'Teteskan 2-3 tetes pagi hari sebelum moisturizer', '32000.00', '48000.00', '52000.00', '25', '5', 'produk_10.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('11', 'Facetology Hydra Glowing Moisturizer', '1', 'Facetology', 'Moisturizer glowing dengan niacinamide & ceramide untuk kulit lembab', 'Aplikasikan pagi dan malam setelah serum', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_11.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('12', 'Facetology Acne Spot Gel', '1', 'Facetology', 'Spot gel jerawat drying lotion untuk jerawat meradang', 'Oleskan tipis pada jerawat malam hari', '22000.00', '35000.00', '38000.00', '30', '5', 'produk_12.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('13', 'The Originote Ceramide Moisturizer', '1', 'The Originote', 'Moisturizer ceramide harga terjangkau untuk perbaikan skin barrier', 'Aplikasikan pagi dan malam hari setelah serum', '18000.00', '28000.00', '30000.00', '40', '5', 'produk_13.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('14', 'The Originote Hyalufiller Essence', '1', 'The Originote', 'Essence hyaluronic acid multi layer untuk kulit super lembab', 'Tepuk merata ke wajah setelah toner', '22000.00', '35000.00', '37000.00', '35', '5', 'produk_14.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('15', 'NPure Cica Serum Centella', '1', 'Npure', 'Serum centella asiatica untuk kulit sensitif, kemerahan & iritasi', 'Teteskan 2-3 tetes pagi dan malam hari', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_15.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('16', 'COSRX Advanced Snail 96 Mucin Power Essence', '1', 'COSRX', 'Essence snail 96% untuk regenerasi kulit dan perbaikan tekstur', 'Tepuk merata ke wajah setelah toner', '90000.00', '138000.00', '145000.00', '15', '5', 'produk_16.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('17', 'Beauty of Joseon Glow Serum Propolis Niacinamide', '1', 'Beauty of Joseon', 'Serum brightening propolis + niacinamide khas Korean Beauty', 'Teteskan 2-3 tetes pagi dan malam hari', '85000.00', '130000.00', '138000.00', '15', '5', 'produk_17.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('18', 'Skin1004 Madagascar Centella Ampoule', '1', 'Skin1004', 'Ampoule centella 100% untuk kulit meradang, sensitif dan kemerahan', 'Teteskan setelah essence, sebelum moisturizer', '75000.00', '115000.00', '122000.00', '15', '5', 'produk_18.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('19', 'Some By Mi AHA BHA PHA 30 Days Miracle Toner', '1', 'Some By Mi', 'Toner eksfoliasi 3 asam untuk kulit mulus dalam 30 hari pemakaian', 'Usapkan ke wajah malam hari dengan kapas', '60000.00', '92000.00', '98000.00', '20', '5', 'produk_19.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('20', 'Some By Mi Snail Truecica Miracle Repair Serum', '1', 'Some By Mi', 'Serum repair kulit rusak dengan snail secretion & centella asiatica', 'Teteskan 2-3 tetes pagi dan malam hari', '70000.00', '108000.00', '115000.00', '15', '5', 'produk_20.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('21', 'Round Lab Birch Juice Moisturizing Cream', '1', 'Round Lab', 'Moisturizer birch juice Korea untuk hidrasi mendalam tahan lama', 'Aplikasikan pagi dan malam sebagai step terakhir', '80000.00', '122000.00', '130000.00', '12', '5', 'produk_21.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('22', 'Anua Heartleaf 77% Soothing Toner', '1', 'Anua', 'Toner heartleaf viral Korea untuk kulit sensitif & menenangkan', 'Tuang ke kapas atau tepuk langsung ke wajah', '75000.00', '115000.00', '122000.00', '15', '5', 'produk_22.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('23', 'The Ordinary Niacinamide 10% + Zinc 1%', '1', 'The Ordinary', 'Serum niacinamide affordable internasional, bestseller semua platform', 'Teteskan beberapa tetes pagi dan malam hari', '85000.00', '128000.00', '135000.00', '15', '5', 'produk_23.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('24', 'The Ordinary AHA 30% + BHA 2% Peeling Solution', '1', 'The Ordinary', 'Chemical exfoliator peeling untuk kulit glowing dan cerah merata', 'Aplikasikan 10 menit malam hari, 2x seminggu', '90000.00', '135000.00', '142000.00', '10', '5', 'produk_24.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('25', 'Medicube Zero Pore Pad', '1', 'Medicube', 'Pad eksfoliasi Korea untuk minimalisir pori besar dan tekstur', 'Usap lembut ke wajah malam hari', '85000.00', '130000.00', '138000.00', '10', '5', 'produk_25.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('26', 'Tirtir Mask Fit Red Cushion SPF 40+', '2', 'Tirtir', 'Cushion viral Korea coverage tinggi dengan finish natural lembab', 'Tempel ke wajah dengan spon cushion secara merata', '100000.00', '155000.00', '162000.00', '15', '5', 'produk_26.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('27', 'Dear Me Beauty Retinol Sleeping Mask', '1', 'Dear Me Beauty', 'Sleeping mask retinol untuk regenerasi kulit dan anti aging overnight', 'Aplikasikan sebagai step terakhir malam hari', '45000.00', '70000.00', '75000.00', '20', '5', 'produk_27.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('28', 'Elsheskin Brightening Serum Vitamin C', '1', 'Elsheskin', 'Serum brightening lokal dengan vitamin C untuk kulit cerah merata', 'Teteskan 2-3 tetes pagi dan malam hari', '38000.00', '58000.00', '62000.00', '20', '5', 'produk_28.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('29', 'Ms Glow Acne Serum', '1', 'Ms Glow', 'Serum acne Ms Glow untuk kulit berjerawat dan berminyak', 'Teteskan pada wajah bersih pagi dan malam', '40000.00', '62000.00', '65000.00', '15', '5', 'produk_29.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('30', 'Ms Glow Moisturizer Cream', '1', 'Ms Glow', 'Moisturizer lembab dengan SPF untuk proteksi kulit harian', 'Oleskan merata pagi hari setelah serum', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_30.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('31', 'Torriden Dive-In Hyaluronic Acid Serum', '1', 'Torriden', 'Serum hyaluronic acid low molecular weight untuk hidrasi super dalam', 'Teteskan 3-4 tetes setelah toner, pagi dan malam', '80000.00', '122000.00', '130000.00', '12', '5', 'produk_31.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('32', 'Jumiso All Day Vitamin C Serum', '1', 'Jumiso', 'Serum vitamin C 5% gentle untuk kulit sensitif, cerahkan merata', 'Teteskan 2-3 tetes pagi hari', '70000.00', '108000.00', '115000.00', '12', '5', 'produk_32.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('33', 'Illiyoon Ceramide Ato Concentrate Cream', '1', 'Illiyoon', 'Moisturizer ceramide Korea untuk kulit super kering dan sensitif', 'Oleskan merata pagi dan malam hari', '90000.00', '138000.00', '145000.00', '10', '5', 'produk_33.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('34', 'Pyunkang Yul Moisture Serum', '1', 'Pyunkang Yul', 'Serum herbal Korea dengan 70% astragalus root extract untuk hidrasi', 'Teteskan 2-3 tetes setelah toner', '75000.00', '115000.00', '122000.00', '12', '5', 'produk_34.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('35', 'Wardah White Secret Day Cream SPF 28', '1', 'Wardah', 'Day cream brightening dengan SPF 28 untuk perlindungan harian', 'Oleskan pagi hari setelah cuci muka', '28000.00', '42000.00', '45000.00', '30', '5', 'produk_35.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('36', 'Wardah Hydrating Aloe Vera Gel', '1', 'Wardah', 'Gel aloe vera multifungsi untuk wajah, rambut, dan tubuh', 'Aplikasikan tipis ke area yang dibutuhkan', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_36.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('37', 'Pond\'s Age Miracle Day Cream SPF 18', '1', 'Pond\'s', 'Day cream anti aging Ponds dengan vitamin B3 dan SPF 18', 'Oleskan pagi hari setelah cuci muka', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_37.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('38', 'Garnier Bright Complete Vitamin C Serum Cream', '1', 'Garnier', 'Serum krim vitamin C brightening dari Garnier untuk kulit cerah', 'Oleskan pagi dan malam hari', '25000.00', '38000.00', '40000.00', '25', '5', 'produk_38.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('39', 'Cetaphil Moisturizing Cream', '1', 'Cetaphil', 'Moisturizer krim tebal untuk kulit kering dan sensitif, dermatologist approved', 'Oleskan ke wajah dan tubuh yang masih lembab', '65000.00', '100000.00', '105000.00', '15', '5', 'produk_39.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('40', 'CeraVe Moisturizing Cream', '1', 'CeraVe', 'Moisturizer dermatologis dengan 3 ceramide esensial dan hyaluronic acid', 'Oleskan ke wajah dan tubuh pagi dan malam', '85000.00', '130000.00', '138000.00', '10', '5', 'produk_40.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('41', 'Holika Holika Aloe 99% Soothing Gel', '1', 'Holika Holika', 'Gel soothing aloe vera 99% untuk cooling dan menenangkan kulit', 'Oleskan tipis ke wajah dan tubuh', '35000.00', '55000.00', '58000.00', '25', '5', 'produk_41.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('42', 'Etude House Soon Jung pH 5.5 Relief Toner', '1', 'Etude House', 'Toner pH aman untuk kulit sensitif dan damaged skin barrier', 'Tuang ke kapas, usap lembut ke wajah', '55000.00', '85000.00', '90000.00', '15', '5', 'produk_42.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('43', 'Somethinc Cleanser Balm Low pH', '9', 'Somethinc', 'Cleansing balm low pH untuk angkat makeup tebal dan sunscreen', 'Pijat ke wajah kering, emulsi dengan air, bilas', '45000.00', '68000.00', '72000.00', '20', '5', 'produk_43.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('44', 'COSRX Low pH Good Morning Gel Cleanser', '9', 'COSRX', 'Facial wash pH rendah lembut untuk semua jenis kulit, Korea bestseller', 'Busa lalu pijat lembut ke wajah, bilas bersih', '55000.00', '85000.00', '90000.00', '20', '5', 'produk_44.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('45', 'CeraVe Hydrating Facial Cleanser', '9', 'CeraVe', 'Facial wash dengan ceramide & hyaluronic acid untuk kelembaban kulit', 'Pijat lembut ke wajah, bilas dengan air dingin', '70000.00', '108000.00', '115000.00', '12', '5', 'produk_45.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('46', 'Cetaphil Gentle Skin Cleanser', '9', 'Cetaphil', 'Facial wash lembut untuk semua jenis kulit, dermatologist recommended', 'Gunakan pagi dan malam, bilas atau lap bersih', '55000.00', '85000.00', '90000.00', '20', '5', 'produk_46.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('47', 'Senka Perfect Whip Facial Foam', '9', 'Senka', 'Sabun wajah Jepang dengan silk cocoon protein busa lebat', 'Busa lalu pijat lembut ke wajah, bilas', '32000.00', '50000.00', '53000.00', '25', '5', 'produk_47.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('48', 'Biore Marshmallow Whip Moisture Facial Foam', '9', 'Biore', 'Facial wash busa marshmallow lembut untuk kulit lembab', 'Ratakan busa ke wajah, pijat lembut, bilas', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_48.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('49', 'Emina Acne Solution Facial Wash', '9', 'Emina', 'Facial wash khusus jerawat dengan salicylic acid untuk remaja', 'Busa dan pijat lembut ke wajah 2x sehari', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_49.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('50', 'Simple Kind to Skin Micellar Cleansing Water', '9', 'Simple', 'Micellar water lembut tanpa alkohol untuk kulit sensitif', 'Tuang ke kapas, usap lembut ke wajah tanpa dibilas', '28000.00', '42000.00', '45000.00', '25', '5', 'produk_50.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('51', 'Garnier Micellar Rose Water Cleanser', '9', 'Garnier', 'Micellar water rose water untuk angkat kotoran, makeup, dan sunscreen', 'Tuang ke kapas, usap ke seluruh wajah', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_51.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('52', 'Wardah Lightening Facial Wash', '9', 'Wardah', 'Facial wash brightening wardah untuk kulit cerah dan bersih', 'Busa lalu pijat wajah, bilas bersih', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_52.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('53', 'Azarine Hydrasoothe Sunscreen Gel SPF 45', '3', 'Azarine', 'Sunscreen gel viral lokal SPF 45 PA+++ ringan & tidak lengket', 'Oleskan merata ke wajah 15 menit sebelum keluar', '28000.00', '42000.00', '45000.00', '50', '5', 'produk_53.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('54', 'Amaterasun Shield Invisible Fluid SPF 50+', '3', 'Amaterasun', 'Sunscreen fluid SPF 50+ PA++++ tone up tanpa white cast', 'Oleskan merata pagi hari, reapply tiap 2-3 jam', '38000.00', '58000.00', '62000.00', '30', '5', 'produk_54.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('55', 'Carasun Daily Smooth Sunscreen SPF 50+', '3', 'Carasun', 'Sunscreen lokal SPF 50+ dengan vitamin C, tone up dan glowing effect', 'Pakai setelah skincare, sebelum makeup', '32000.00', '50000.00', '53000.00', '30', '5', 'produk_55.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('56', 'Skin Aqua UV Moisture Milk SPF 50+ PA++++', '3', 'Skin Aqua', 'Sunscreen milk Jepang ringan dengan moisture care untuk kulit normal', 'Oleskan merata pagi hari sebelum beraktivitas', '42000.00', '65000.00', '68000.00', '25', '5', 'produk_56.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('57', 'Anessa Perfect UV Skincare Milk SPF 50+', '3', 'Anessa', 'Sunscreen premium Jepang sweat-proof terbaik untuk outdoor activities', 'Oleskan merata sebelum keluar, reapply 2-3 jam', '95000.00', '145000.00', '152000.00', '15', '5', 'produk_57.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('58', 'Trueve Daily SPF 50 PA++++ Serum Sunscreen', '3', 'Trueve', 'Serum sunscreen lokal viral 2025, ringan dan menyerap cepat', 'Aplikasikan ke wajah setelah skincare pagi hari', '38000.00', '58000.00', '62000.00', '25', '5', 'produk_58.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('59', 'Npure Centella Sunscreen SPF 50+ PA++++', '3', 'Npure', 'Sunscreen dengan centella asiatica untuk kulit sensitif dan berjerawat', 'Oleskan merata pagi hari ke wajah', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_59.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('60', 'Beauty of Joseon Relief Sun Rice Probiotics', '3', 'Beauty of Joseon', 'Sunscreen viral Korea SPF 50+ PA++++ dengan kandungan beras dan probiotik', 'Oleskan merata 15 menit sebelum keluar', '95000.00', '145000.00', '152000.00', '20', '5', 'produk_60.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('61', 'Skin1004 Hyalu-Cica Water-Fit Sun Serum SPF50', '3', 'Skin1004', 'Sunscreen serum ringan SPF 50+ PA++++, tekstur cair seperti air', 'Oleskan merata sebelum aktivitas outdoor', '100000.00', '152000.00', '160000.00', '12', '5', 'produk_61.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('62', 'Emina Sun Protection SPF 30 PA+++', '3', 'Emina', 'Sunscreen terjangkau SPF 30 untuk remaja dan kulit normal', 'Oleskan 15 menit sebelum aktivitas outdoor', '18000.00', '28000.00', '30000.00', '40', '5', 'produk_62.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('63', 'Wardah UV Shield Essential Sunscreen Serum', '3', 'Wardah', 'Serum sunscreen Wardah SPF 35 PA+++ untuk perlindungan harian', 'Oleskan ke wajah 15 menit sebelum keluar rumah', '25000.00', '38000.00', '40000.00', '35', '5', 'produk_63.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('64', 'Biore UV Perfect Milk SPF 50+ PA++++', '3', 'Biore', 'Sunscreen milk ringan Biore UV terlaris untuk kulit berminyak', 'Oleskan merata pagi hari sebelum beraktivitas', '35000.00', '55000.00', '58000.00', '30', '5', 'produk_64.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('65', 'Tirtir Mugener Sun Cream SPF 50+', '3', 'Tirtir', 'Sunscreen cream Korea vibes dengan finish tone-up natural', 'Oleskan ke wajah setelah skincare pagi hari', '85000.00', '130000.00', '138000.00', '12', '5', 'produk_65.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('66', 'Make Over Powerstay Matte Lip Cream', '2', 'Make Over', 'Lip cream matte Make Over tahan lama dengan 30+ pilihan warna', 'Aplikasikan dari tengah bibir ke sudut', '38000.00', '58000.00', '62000.00', '30', '5', 'produk_66.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('67', 'Make Over Powerstay Demi-Matte Cushion', '2', 'Make Over', 'Cushion coverage tinggi dengan finish demi-matte alami dari Make Over', 'Tempel dengan spon, ratakan merata ke wajah', '75000.00', '115000.00', '122000.00', '20', '5', 'produk_67.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('68', 'BLP Beauty Lip Coat', '2', 'BLP Beauty', 'Lip coat matte tahan lama, pigmented & tahan makan minum', 'Aplikasikan ke bibir kering, tunggu mengering', '45000.00', '68000.00', '72000.00', '25', '5', 'produk_68.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('69', 'BLP Beauty Face Base Cushion SPF 20', '2', 'BLP Beauty', 'Cushion base natural finish dengan SPF 20 untuk tampilan no-makeup makeup', 'Tempel ke wajah dengan spon cushion secara merata', '85000.00', '130000.00', '138000.00', '15', '5', 'produk_69.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('70', 'ESQA Cosmic Eyes Eyeshadow Palette 9 Pan', '2', 'ESQA', 'Eyeshadow palette 9 pan dengan warna neutral dan shimmer premium', 'Aplikasikan dengan kuas eyeshadow sesuai kreasi', '95000.00', '145000.00', '152000.00', '15', '5', 'produk_70.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('71', 'ESQA Starlit Highlighter', '2', 'ESQA', 'Highlighter glowing dengan pigmen tinggi, finish blinding untuk foto', 'Sapukan di tulang pipi, hidung, dahi, dan bahu', '55000.00', '85000.00', '90000.00', '20', '5', 'produk_71.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('72', 'Luxcrime Ultra Plump Lip Serum', '2', 'Luxcrime', 'Lip serum dengan warna sheer dan efek plumping untuk bibir lembab', 'Aplikasikan ke bibir kapanpun sebagai lip treatment', '38000.00', '58000.00', '62000.00', '25', '5', 'produk_72.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('73', 'Luxcrime Blur & Glow Setting Powder', '2', 'Luxcrime', 'Setting powder dengan efek blur dan glowing, viral untuk foto', 'Sapukan dengan kuas atau puff ke seluruh wajah', '45000.00', '68000.00', '72000.00', '20', '5', 'produk_73.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('74', 'Hanasui Tintdorable Lip Tint', '2', 'Hanasui', 'Lip tint water-based viral dengan warna cerah tahan lama seharian', 'Tempel ke bibir atau blend untuk ombre lips', '15000.00', '23000.00', '25000.00', '50', '5', 'produk_74.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('75', 'Hanasui Powerstay Matte Cushion', '2', 'Hanasui', 'Cushion matte affordable Hanasui dengan coverage medium', 'Tempel dengan spon ke seluruh wajah', '28000.00', '42000.00', '45000.00', '30', '5', 'produk_75.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('76', 'Implora Lip Matte', '2', 'Implora', 'Lip matte lokal harga paling terjangkau dengan warna pigmented', 'Aplikasikan ke bibir kering dengan aplikator', '12000.00', '18000.00', '20000.00', '60', '5', 'produk_76.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('77', 'Implora Eyeliner Pencil Waterproof', '2', 'Implora', 'Eyeliner pensil hitam waterproof dengan ujung smudger', 'Gambar garis di kelopak mata atas dan bawah', '10000.00', '15000.00', '17000.00', '50', '5', 'produk_77.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('78', 'Pinkflash Oh My Glitter Eyeshadow', '2', 'Pinkflash', 'Eyeshadow glitter affordable dengan pigmen glitter tinggi, warna cerah', 'Aplikasikan di kelopak mata dengan jari atau kuas', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_78.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('79', 'Pinkflash Waterproof Mascara', '2', 'Pinkflash', 'Mascara waterproof dengan brush volumizing untuk bulu mata tebal', 'Aplikasikan dari akar ke ujung bulu mata berlapis', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_79.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('80', 'Focallure Eyebrow Pomade Waterproof', '2', 'Focallure', 'Pomade alis tahan air dengan warna natural coklat & hitam', 'Isi alis dengan angled brush, blend perlahan', '22000.00', '33000.00', '35000.00', '35', '5', 'produk_80.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('81', 'Focallure Blush Palette 3 Warna', '2', 'Focallure', 'Blush palette 3 warna shimmer dan matte untuk daily look fresh', 'Sapukan di pipi dengan kuas blush besar', '25000.00', '38000.00', '40000.00', '30', '5', 'produk_81.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('82', 'Madame Gie Get It On Lipcream', '2', 'Madame Gie', 'Lip cream matte Madame Gie dengan 40+ pilihan warna trendi', 'Aplikasikan ke bibir dengan presisi', '18000.00', '28000.00', '30000.00', '40', '5', 'produk_82.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('83', 'Madame Gie Conceal Me Concealer', '2', 'Madame Gie', 'Concealer coverage medium untuk sembunyikan lingkar hitam dan jerawat', 'Tap lembut dengan jari atau beauty blender', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_83.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('84', 'Romand Juicy Lasting Tint', '2', 'Romand', 'Lip tint Korea viral paling laris dengan warna vivid tahan lama', 'Aplikasikan ke bibir, tahan hingga 8-12 jam', '65000.00', '100000.00', '105000.00', '20', '5', 'produk_84.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('85', 'Romand Zero Velvet Tint', '2', 'Romand', 'Velvet lip tint matte sempurna dengan warna sangat pigmented', 'Aplikasikan ke bibir kering untuk finish velvet', '68000.00', '105000.00', '110000.00', '15', '5', 'produk_85.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('86', '3CE Mood Recipe Lip Color', '2', '3CE', 'Lipstik 3CE Korea dengan formula lembab dan warna trendi 2026', 'Aplikasikan langsung ke bibir dari bullet', '75000.00', '115000.00', '122000.00', '15', '5', 'produk_86.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('87', 'Clio Kill Lash Superproof Mascara', '2', 'Clio', 'Mascara Korea tahan air dan keringat, bulu mata panjang dramatis', 'Aplikasikan berlapis dari akar ke ujung bulu mata', '75000.00', '115000.00', '122000.00', '12', '5', 'produk_87.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('88', 'Clio Sharp So Simple Waterproof Pencil Liner', '2', 'Clio', 'Eyeliner pensil waterproof dengan ujung presisi untuk garis rapi', 'Gambar garis eyeliner sesuai keinginan', '65000.00', '100000.00', '105000.00', '15', '5', 'produk_88.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('89', 'Dasique Eyeshadow Palette Cozy Knit', '2', 'Dasique', 'Eyeshadow palette Korea warna hangat coklat dengan pigmen premium', 'Aplikasikan dengan kuas atau jari ke kelopak mata', '100000.00', '152000.00', '160000.00', '10', '5', 'produk_89.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('90', 'Wardah Exclusive Matte Lip Cream', '2', 'Wardah', 'Lip cream matte Wardah terlaris dengan formula tahan lama', 'Aplikasikan ke bibir, biarkan mengering sempurna', '22000.00', '33000.00', '35000.00', '35', '5', 'produk_90.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('91', 'Wardah Instaperfect Mattening BB Cream', '2', 'Wardah', 'BB cream mattening Wardah untuk tampilan natural sehari-hari', 'Oleskan merata ke wajah setelah skincare pagi', '28000.00', '42000.00', '45000.00', '30', '5', 'produk_91.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('92', 'Maybelline Superstay Matte Ink Lip Color', '2', 'Maybelline', 'Lip color matte ink 16H tahan lama dari Maybelline New York', 'Aplikasikan ke bibir kering, ratakan merata', '42000.00', '65000.00', '68000.00', '25', '5', 'produk_92.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('93', 'Maybelline Sky High Washable Mascara', '2', 'Maybelline', 'Mascara washable dengan formula fiber untuk bulu mata panjang natural', 'Aplikasikan berlapis ke bulu mata atas dan bawah', '45000.00', '68000.00', '72000.00', '20', '5', 'produk_93.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('94', 'PIXY BB Cream UV Whitening', '2', 'PIXY', 'BB cream lokal terpopuler dengan efek whitening dan UV protection lokal', 'Oleskan merata ke seluruh wajah', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_94.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('95', 'Y.O.U Weightless Matte Lip Cream', '2', 'Y.O.U Beauty', 'Lip cream matte ringan tidak kering dengan warna vivid tahan lama', 'Aplikasikan ke bibir dengan aplikator presisi', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_95.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('96', 'Sea Makeup Serum Lip Tint', '2', 'Sea Makeup', 'Lip tint dengan kandungan serum untuk bibir lembab dan berwarna', 'Tempel ke bibir atau blending untuk ombre look', '25000.00', '38000.00', '40000.00', '25', '5', 'produk_96.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('97', 'Rose All Day Realest Lightweight Foundation', '2', 'Rose All Day', 'Foundation ringan coverage buildable untuk tampilan natural flawless', 'Ratakan ke wajah dengan beauty blender atau kuas', '55000.00', '85000.00', '90000.00', '20', '5', 'produk_97.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('98', 'Barenbliss Apple Makes Perfect Lip Balm Tint', '2', 'Barenbliss', 'Lip balm tint dengan aroma apel segar, lembab dan berwarna natural', 'Aplikasikan ke bibir kapanpun untuk perawatan harian', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_98.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('99', 'Judydoll Embossed Powder Blush', '2', 'Judydoll', 'Blush powder dengan embossed pattern cantik, pigmen buildable', 'Sapukan di pipi dengan kuas blush', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_99.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('100', 'Flower Knows Strawberry Rococo Blush', '2', 'Flower Knows', 'Blush viral aesthetic dengan packaging bunga, warna coral dan pink', 'Sapukan di pipi untuk tampilan segar dan manis', '55000.00', '85000.00', '90000.00', '15', '5', 'produk_100.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('101', 'Mineral Botanica Flawless Cushion', '2', 'Mineral Botanica', 'Cushion mineral dengan coverage medium dan formula ringan', 'Tempel ke wajah dengan spon cushion', '55000.00', '85000.00', '90000.00', '15', '5', 'produk_101.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('102', 'Viva Cosmetics Bedak Two Way Cake', '2', 'Viva Cosmetics', 'Bedak two way cake lokal terpercaya, basah dan kering', 'Gunakan basah untuk coverage, kering untuk touch up', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_102.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('103', 'Scarlett Whitening Body Lotion Charming', '4', 'Scarlett', 'Body lotion whitening viral Scarlett aroma manis charming', 'Oleskan merata ke seluruh tubuh setelah mandi', '38000.00', '58000.00', '62000.00', '40', '5', 'produk_103.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('104', 'Scarlett Whitening Shower Scrub', '4', 'Scarlett', 'Shower scrub whitening Scarlett untuk kulit cerah dan lembut', 'Gosok lembut ke seluruh tubuh saat mandi', '35000.00', '55000.00', '58000.00', '30', '5', 'produk_104.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('105', 'Scarlett Body Serum Brightly Ever After', '4', 'Scarlett', 'Body serum brightening Scarlett untuk kulit tubuh cerah dan glowing', 'Oleskan ke kulit bersih dan kering pagi hari', '42000.00', '65000.00', '68000.00', '25', '5', 'produk_105.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('106', 'Vaseline Healthy Bright Body Lotion', '4', 'Vaseline', 'Body lotion brightening Vaseline dengan vitamin B3 untuk kulit cerah', 'Oleskan merata ke seluruh tubuh setelah mandi', '25000.00', '38000.00', '40000.00', '40', '5', 'produk_106.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('107', 'Dove Body Lotion Glowing Ritual', '4', 'Dove', 'Body lotion Dove dengan formula lembab 24 jam alami', 'Oleskan ke seluruh tubuh pagi dan malam hari', '22000.00', '33000.00', '35000.00', '35', '5', 'produk_107.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('108', 'Marina UV White Body Lotion SPF 15', '4', 'Marina', 'Body lotion UV protection SPF 15 harga terjangkau', 'Oleskan pagi hari setelah mandi ke seluruh tubuh', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_108.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:18'),
('109', 'Nivea Extra White Body Serum', '4', 'Nivea', 'Body serum whitening Nivea dengan Q10 dan vitamin C', 'Oleskan ke kulit bersih dan kering pagi hari', '35000.00', '55000.00', '58000.00', '25', '5', 'produk_109.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('110', 'Lacoco Sea Grape Exfoliating Body Serum', '4', 'Lacoco', 'Body serum eksfoliasi dengan sea grape untuk kulit cerah merata', 'Oleskan ke tubuh 2-3x seminggu sebelum tidur', '45000.00', '68000.00', '72000.00', '20', '5', 'produk_110.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('111', 'Everwhite Underarm Serum', '4', 'Everwhite', 'Underarm serum untuk cerahkan ketiak gelap dan membuat lembut', 'Oleskan tipis ke area ketiak bersih pagi malam', '38000.00', '58000.00', '62000.00', '20', '5', 'produk_111.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('112', 'Moko Moko Body Scrub Macchiato', '4', 'Moko Moko', 'Body scrub aroma kopi macchiato untuk kulit lembut dan cerah', 'Gosok lembut ke tubuh basah saat mandi', '28000.00', '42000.00', '45000.00', '30', '5', 'produk_112.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('113', 'Herborist Body Lotion Olive', '4', 'Herborist', 'Body lotion herbal dengan ekstrak zaitun untuk kelembaban alami', 'Oleskan merata ke seluruh tubuh setelah mandi', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_113.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('114', 'Safi Dermacare Body Lotion', '4', 'Safi', 'Body lotion halal dengan bahan dermacare untuk kulit sehat', 'Oleskan merata ke tubuh setiap hari', '20000.00', '30000.00', '32000.00', '30', '5', 'produk_114.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('115', 'Studio Tropik Body Mist Soleil de Minuit', '8', 'Studio Tropik', 'Body mist lokal viral Indonesia dengan aroma mewah tahan lama', 'Semprotkan ke tubuh dan pakaian dari jarak 20cm', '55000.00', '85000.00', '90000.00', '25', '5', 'produk_115.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('116', 'Studio Tropik EDP Coral Fantasie', '8', 'Studio Tropik', 'Eau De Parfum lokal viral 2025 dengan aroma bunga tropis', 'Semprot ke titik nadi, tahan hingga 6-8 jam', '85000.00', '130000.00', '138000.00', '15', '5', 'produk_116.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('117', 'Roll On Perfume Sweet Floral', '8', 'Pinkflash', 'Parfum roll on pocket-friendly dengan aroma bunga manis lembut', 'Oleskan di pergelangan tangan dan leher', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_117.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('118', 'Pocket Perfume Vanilla Bomb', '8', 'Implora', 'Parfum pocket ukuran kecil aroma vanilla lembut tahan seharian', 'Semprotkan ke pakaian dan tubuh', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_118.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('119', 'Body Mist Fruity Fresh', '8', 'Hanasui', 'Body mist aroma buah segar untuk sehari-hari, ringan dan ceria', 'Semprotkan ke seluruh tubuh dan rambut', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_119.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('120', 'Hair Mist Floral Sweet', '8', 'Generic', 'Hair mist dengan aroma bunga manis untuk rambut harum seharian', 'Semprotkan ke rambut dari jarak 15-20cm', '18000.00', '28000.00', '30000.00', '25', '5', 'produk_120.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('121', 'Dove Nutritive Solutions Shampoo Dandruff Care', '5', 'Dove', 'Shampo anti ketombe Dove dengan moisture formula', 'Keramas 2-3x seminggu, pijat dan bilas bersih', '22000.00', '33000.00', '35000.00', '35', '5', 'produk_121.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('122', 'Wardah Hair Vitamin Serum', '5', 'Wardah', 'Serum rambut Wardah untuk rambut sehat berkilau', 'Oleskan ke rambut lembab tanpa dibilas', '22000.00', '33000.00', '35000.00', '25', '5', 'produk_122.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('123', 'TRESemme Keratin Smooth Shampoo', '5', 'TRESemme', 'Shampo keratin untuk rambut halus dan mudah diatur', 'Keramas rutin, pijat lalu bilas bersih', '28000.00', '42000.00', '45000.00', '25', '5', 'produk_123.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('124', 'Sunsilk Hair Mask Hijab Recharge', '5', 'Sunsilk', 'Hair mask khusus untuk rambut berhijab agar tetap sehat dan kuat', 'Aplikasikan setelah shampo, diamkan 3-5 menit', '18000.00', '28000.00', '30000.00', '30', '5', 'produk_124.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('125', 'Press On Nails Set French Tip 24 pcs', '6', 'Implora', 'Kuku palsu press on french tip aesthetic bersih, 24 pcs berbagai ukuran', 'Tempel dengan nail glue, tahan 1-2 minggu', '12000.00', '18000.00', '20000.00', '50', '5', 'produk_125.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('126', 'Press On Nails Set Ombre Pink 24 pcs', '6', 'Pinkflash', 'Kuku palsu ombre pink gradient cantik, set 24 pcs lengkap dengan lem', 'Tempel dengan nail glue atau double tape kuku', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_126.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('127', 'Press On Nails Coffin Shape Nude', '6', 'Generic', 'Kuku palsu coffin shape warna nude aesthetic, 24 pcs siap pakai', 'Tempel dengan nail glue, pastikan kuku bersih', '18000.00', '28000.00', '30000.00', '35', '5', 'produk_127.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('128', 'Kuteks Nail Polish Glossy Red', '6', 'Viva Cosmetics', 'Kuteks nail polish merah glossy tahan lama dari Viva Cosmetics', 'Cat kuku 2 lapis, diamkan hingga benar-benar kering', '12000.00', '18000.00', '20000.00', '40', '5', 'produk_128.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('129', 'Kuteks Nail Polish Pastel Set 6 Warna', '6', 'Implora', 'Set kuteks 6 warna pastel aesthetic untuk nail art sehari-hari', 'Cat kuku tipis 2 lapis, setiap lapis tunggu kering', '25000.00', '38000.00', '40000.00', '30', '5', 'produk_129.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('130', 'Nail Sticker Butterfly Motif', '6', 'Generic', 'Stiker kuku motif kupu-kupu aesthetic, 1 sheet isi 30 pcs', 'Tempel di atas kuteks yang sudah benar-benar kering', '8000.00', '12000.00', '13000.00', '60', '5', 'produk_130.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('131', 'Nail Sticker Flower Motif', '6', 'Generic', 'Stiker kuku motif bunga cantik, 1 sheet isi 30 pcs berbagai motif', 'Tempel di atas kuteks kering, beri top coat', '8000.00', '12000.00', '13000.00', '60', '5', 'produk_131.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('132', 'Nail Glue Extra Strong Waterproof', '6', 'Generic', 'Lem kuku extra strong waterproof untuk press on nails', 'Oleskan tipis di kuku asli, tempel kuku palsu', '8000.00', '12000.00', '13000.00', '50', '5', 'produk_132.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('133', 'Nail Buffer 4 Way Shine', '6', 'Generic', 'Nail buffer 4 tahap untuk menghaluskan dan mengkilapkan kuku alami', 'Gunakan tiap sisi sesuai urutan 1-4 pada kuku', '8000.00', '12000.00', '13000.00', '40', '5', 'produk_133.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('134', 'Beauty Blender Sponge Pink', '6', 'Generic', 'Beauty blender sponge latex-free untuk aplikasi foundation dan concealer', 'Basahi sponge, peras, tepuk foundation merata', '12000.00', '18000.00', '20000.00', '40', '5', 'produk_134.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('135', 'Makeup Brush Set 12 pcs Rose Gold', '6', 'Generic', 'Set kuas makeup lengkap 12 pcs dengan handle rose gold aesthetic cantik', 'Gunakan tiap kuas sesuai fungsinya masing-masing', '35000.00', '55000.00', '58000.00', '20', '5', 'produk_135.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('136', 'Makeup Brush Set 5 pcs Basic', '6', 'Generic', 'Set kuas makeup basic 5 pcs untuk pemula, ada semua kuas dasar', 'Gunakan kuas sesuai kebutuhan makeup', '18000.00', '28000.00', '30000.00', '30', '5', 'produk_136.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('137', 'Eyelash Curler Stainless Steel', '6', 'Generic', 'Penjepit bulu mata stainless steel untuk bulu mata melentik natural', 'Jepit bulu mata dari pangkal, tahan 10-15 detik', '15000.00', '23000.00', '25000.00', '25', '5', 'produk_137.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('138', 'Makeup Organizer Acrylic Clear Large', '6', 'Generic', 'Rak organizer makeup akrilik transparan besar, muat banyak produk', 'Susun produk makeup di atas meja rias sesuai selera', '55000.00', '85000.00', '90000.00', '15', '5', 'produk_138.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('139', 'Cermin Mini Lipstik Shape', '6', 'Generic', 'Cermin mini portable berbentuk lipstik untuk touch up di luar rumah', 'Buka dan gunakan sebagai cermin saku kapanpun', '8000.00', '12000.00', '13000.00', '60', '5', 'produk_139.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('140', 'Powder Puff Velvet Round Large', '6', 'Generic', 'Powder puff velvet lembut untuk aplikasi bedak tabur yang merata', 'Tekan lembut ke wajah setelah bedak tabur', '6000.00', '10000.00', '11000.00', '50', '5', 'produk_140.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('141', 'False Eyelashes Natural Look 10 Pairs', '6', 'Generic', 'Bulu mata palsu natural look 10 pasang, cocok untuk sehari-hari', 'Tempel dengan lem bulu mata, tunggu 30 detik', '8000.00', '12000.00', '13000.00', '40', '5', 'produk_141.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('142', 'Eyelash Glue Black Waterproof', '6', 'Generic', 'Lem bulu mata hitam waterproof tahan sepanjang hari', 'Oleskan tipis ke pangkal bulu mata palsu, tunggu 30 detik', '6000.00', '10000.00', '11000.00', '50', '5', 'produk_142.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('143', 'Claw Clip Butterfly Transparan Large', '7', 'Generic', 'Claw clip kupu-kupu transparan viral TikTok ukuran besar untuk rambut tebal', 'Pasang ke rambut untuk half-up atau full up look', '6000.00', '10000.00', '11000.00', '80', '10', 'produk_143.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('144', 'Claw Clip Butterfly Small 3 pcs', '7', 'Generic', 'Set claw clip butterfly kecil 3 pcs untuk aksen rambut aesthetic', 'Pasang sebagai aksesori rambut di berbagai posisi', '8000.00', '12000.00', '13000.00', '70', '10', 'produk_144.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('145', 'Claw Clip Set 5 pcs Warna Pastel', '7', 'Generic', 'Set claw clip 5 pcs warna pastel aesthetic untuk styling rambut harian', 'Gunakan untuk berbagai gaya rambut aesthetic', '12000.00', '18000.00', '20000.00', '60', '10', 'produk_145.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('146', 'Claw Clip Marble Pattern', '7', 'Generic', 'Claw clip dengan motif marble aesthetic, ukuran medium', 'Pasang ke rambut untuk tampilan elegan', '8000.00', '12000.00', '13000.00', '60', '10', 'produk_146.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('147', 'Bando Rambut Bunga Aesthetic', '7', 'Generic', 'Bando bunga untuk tampilan sweet dan feminine sehari-hari', 'Pasang di kepala sebagai aksesori cantik', '10000.00', '15000.00', '17000.00', '50', '10', 'produk_147.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('148', 'Bando Satin Polos', '7', 'Generic', 'Bando satin polos warna pastel untuk tampilan clean dan minimalis', 'Pasang di kepala, cocok untuk ke kampus atau kerja', '8000.00', '12000.00', '13000.00', '50', '10', 'produk_148.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('149', 'Scrunchie Satin Set 6 pcs', '7', 'Generic', 'Scrunchie satin lembut 6 warna untuk tampilan aesthetic tidak rusak rambut', 'Ikat rambut dengan lembut menggunakan scrunchie', '10000.00', '15000.00', '17000.00', '60', '10', 'produk_149.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('150', 'Scrunchie Velvet Set 5 pcs', '7', 'Generic', 'Scrunchie velvet premium 5 warna gelap untuk tampilan elegant', 'Ikat rambut atau jadikan gelang aksesoris', '12000.00', '18000.00', '20000.00', '50', '10', 'produk_150.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('151', 'Karet Rambut Korea Spiral 10 pcs', '7', 'Generic', 'Karet rambut spiral Korea tidak merusak rambut, 10 pcs mix warna', 'Ikat rambut dengan lembut, tidak meninggalkan bekas', '8000.00', '12000.00', '13000.00', '80', '10', 'produk_151.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('152', 'Karet Rambut Ribbon Aesthetic', '7', 'Generic', 'Karet rambut dengan pita ribbon aesthetic untuk tampilan manis', 'Ikat rambut menjadi ponytail atau half-up', '6000.00', '10000.00', '11000.00', '80', '10', 'produk_152.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('153', 'Bros Hijab Diamond Cantik', '7', 'Generic', 'Bros hijab dengan hiasan diamond untuk penampilan elegan berhijab', 'Pasang di hijab sesuai selera dan outfit', '8000.00', '12000.00', '13000.00', '60', '10', 'produk_153.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('154', 'Bros Hijab Bunga Pearl', '7', 'Generic', 'Bros hijab bunga dengan pearl cantik untuk tampilan feminin', 'Pasang di hijab atau kerah baju', '8000.00', '12000.00', '13000.00', '60', '10', 'produk_154.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('155', 'Pouch Makeup Canvas Aesthetic', '7', 'Generic', 'Pouch makeup kanvas dengan desain aesthetic untuk simpan kosmetik', 'Simpan makeup dan skincare kecil di dalamnya', '22000.00', '33000.00', '35000.00', '30', '5', 'produk_155.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('156', 'Pouch Transparan Zipper Large', '7', 'Generic', 'Pouch transparan dengan zipper untuk skincare saat travel', 'Masukkan produk skincare untuk perjalanan', '15000.00', '23000.00', '25000.00', '40', '5', 'produk_156.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('157', 'Pouch Makeup Ruffle Pink', '7', 'Generic', 'Pouch makeup ruffle warna pink aesthetic untuk tas harian', 'Simpan produk makeup di dalamnya', '25000.00', '38000.00', '40000.00', '25', '5', 'produk_157.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('158', 'Gantungan Kunci Pompom Fluffy', '7', 'Generic', 'Gantungan kunci pompom fluffy aesthetic untuk tas dan kunci', 'Pasang di kunci, tas, atau ritsleting ransel', '8000.00', '12000.00', '13000.00', '80', '10', 'produk_158.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('159', 'Gantungan Kunci Beads Custom', '7', 'Generic', 'Gantungan kunci manik-manik beads handmade aesthetic colorful', 'Pasang di kunci atau tas sebagai aksesori lucu', '10000.00', '15000.00', '17000.00', '70', '10', 'produk_159.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('160', 'Strap HP Warna-warni Aesthetic', '7', 'Generic', 'Strap HP tali panjang aesthetic berbagai warna, cocok semua jenis HP', 'Pasang di case HP melalui lubang strap case', '8000.00', '12000.00', '13000.00', '100', '10', 'produk_160.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('161', 'Strap HP Manik-manik Beads Colorful', '7', 'Generic', 'Strap HP dengan manik-manik warna-warni handmade aesthetic vintage', 'Pasang di lubang strap case HP', '12000.00', '18000.00', '20000.00', '80', '10', 'produk_161.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('162', 'Strap HP Tali Chunky Korea Style', '7', 'Generic', 'Strap HP tali tebal chunky style Korea viral 2025-2026', 'Pasang di case HP untuk tampilan Y2K aesthetic', '10000.00', '15000.00', '17000.00', '80', '10', 'produk_162.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19'),
('163', 'Strap HP Pearl Aesthetic', '7', 'Generic', 'Strap HP dengan hiasan mutiara pearl aesthetic untuk tampilan elegan', 'Pasang di case HP, cocok untuk casual dan formal', '12000.00', '18000.00', '20000.00', '70', '10', 'produk_163.jpg', 'aktif', '2026-05-07 09:04:49', '2026-06-10 19:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `diskon_persen` int(11) DEFAULT 0,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id`, `judul`, `deskripsi`, `diskon_persen`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Flash Sale Korean Beauty', 'Diskon spesial produk skincare dan makeup Korea pilihan!', '15', '2026-05-01', '2026-05-31', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('2', 'Glow Up May Special', 'Paket skincare lengkap harga spesial di bulan Mei 2026', '10', '2026-05-01', '2026-05-30', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('3', 'Buy 2 Get 1 Aksesoris', 'Beli 2 item aksesoris gratis 1 item, berlaku semua item', '0', '2026-05-10', '2026-05-31', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('4', 'New Arrival Viral Products', 'Produk baru viral TikTok & Shopee kini hadir di GYA!', '5', '2026-05-01', '2026-06-01', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama`, `no_hp`, `alamat`, `created_at`, `updated_at`) VALUES
('1', 'PT Kosmetik Jaya Medan', '081234567890', 'Jl. Industri No. 1, Medan', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('2', 'CV Beauty Supply Sumut', '082345678901', 'Jl. Pasar Baru No. 5, Medan', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('3', 'UD Kecantikan Nusantara', '083456789012', 'Jl. Gatot Subroto No. 12, Medan', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('4', 'PT Glowing Indonesia', '084567890123', 'Jl. Adam Malik No. 7, Medan', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('5', 'CV Korean Beauty Import', '085678901234', 'Jl. Pancing No. 3, Medan', '2026-05-07 09:04:49', '2026-05-07 09:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(50) NOT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `tipe_penjualan` enum('offline','online') NOT NULL DEFAULT 'offline',
  `metode_bayar` enum('tunai','kredit') NOT NULL DEFAULT 'tunai',
  `status_transaksi` enum('selesai','kredit','lunas') NOT NULL DEFAULT 'selesai',
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jatuh_tempo` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `pelanggan_id` (`pelanggan_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`promo_id`) REFERENCES `promo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','owner') NOT NULL DEFAULT 'admin',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Owner GYA', 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49'),
('2', 'Admin GYA', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif', '2026-05-07 09:04:49', '2026-05-07 09:04:49');

-- --------------------------------------------------------

--
-- Indexes and AUTO_INCREMENT
--

ALTER TABLE `activity_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `activity_log` AUTO_INCREMENT=36;

ALTER TABLE `barang_masuk` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `barang_masuk` AUTO_INCREMENT=1;

ALTER TABLE `detail_transaksi` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `detail_transaksi` AUTO_INCREMENT=1;

ALTER TABLE `kategori` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `kategori` AUTO_INCREMENT=10;

ALTER TABLE `kredit` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `kredit` AUTO_INCREMENT=1;

ALTER TABLE `pelanggan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `pelanggan` AUTO_INCREMENT=1;

ALTER TABLE `produk` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `produk` AUTO_INCREMENT=164;

ALTER TABLE `promo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `promo` AUTO_INCREMENT=5;

ALTER TABLE `supplier` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `supplier` AUTO_INCREMENT=6;

ALTER TABLE `transaksi` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `transaksi` AUTO_INCREMENT=1;

ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);
ALTER TABLE `users` AUTO_INCREMENT=3;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Update gambar produk (auto-generated)
-- Jalankan query ini setelah import database utama

UPDATE `produk` SET `gambar` = 'produk_1.jpg' WHERE `id` = 1;
UPDATE `produk` SET `gambar` = 'produk_2.jpg' WHERE `id` = 2;
UPDATE `produk` SET `gambar` = 'produk_3.jpg' WHERE `id` = 3;
UPDATE `produk` SET `gambar` = 'produk_4.jpg' WHERE `id` = 4;
UPDATE `produk` SET `gambar` = 'produk_5.jpg' WHERE `id` = 5;
UPDATE `produk` SET `gambar` = 'produk_6.jpg' WHERE `id` = 6;
UPDATE `produk` SET `gambar` = 'produk_7.jpg' WHERE `id` = 7;
UPDATE `produk` SET `gambar` = 'produk_8.jpg' WHERE `id` = 8;
UPDATE `produk` SET `gambar` = 'produk_9.jpg' WHERE `id` = 9;
UPDATE `produk` SET `gambar` = 'produk_10.jpg' WHERE `id` = 10;
UPDATE `produk` SET `gambar` = 'produk_11.jpg' WHERE `id` = 11;
UPDATE `produk` SET `gambar` = 'produk_12.jpg' WHERE `id` = 12;
UPDATE `produk` SET `gambar` = 'produk_13.jpg' WHERE `id` = 13;
UPDATE `produk` SET `gambar` = 'produk_14.jpg' WHERE `id` = 14;
UPDATE `produk` SET `gambar` = 'produk_15.jpg' WHERE `id` = 15;
UPDATE `produk` SET `gambar` = 'produk_16.jpg' WHERE `id` = 16;
UPDATE `produk` SET `gambar` = 'produk_17.jpg' WHERE `id` = 17;
UPDATE `produk` SET `gambar` = 'produk_18.jpg' WHERE `id` = 18;
UPDATE `produk` SET `gambar` = 'produk_19.jpg' WHERE `id` = 19;
UPDATE `produk` SET `gambar` = 'produk_20.jpg' WHERE `id` = 20;
UPDATE `produk` SET `gambar` = 'produk_21.jpg' WHERE `id` = 21;
UPDATE `produk` SET `gambar` = 'produk_22.jpg' WHERE `id` = 22;
UPDATE `produk` SET `gambar` = 'produk_23.jpg' WHERE `id` = 23;
UPDATE `produk` SET `gambar` = 'produk_24.jpg' WHERE `id` = 24;
UPDATE `produk` SET `gambar` = 'produk_25.jpg' WHERE `id` = 25;
UPDATE `produk` SET `gambar` = 'produk_26.jpg' WHERE `id` = 26;
UPDATE `produk` SET `gambar` = 'produk_27.jpg' WHERE `id` = 27;
UPDATE `produk` SET `gambar` = 'produk_28.jpg' WHERE `id` = 28;
UPDATE `produk` SET `gambar` = 'produk_29.jpg' WHERE `id` = 29;
UPDATE `produk` SET `gambar` = 'produk_30.jpg' WHERE `id` = 30;
UPDATE `produk` SET `gambar` = 'produk_31.jpg' WHERE `id` = 31;
UPDATE `produk` SET `gambar` = 'produk_32.jpg' WHERE `id` = 32;
UPDATE `produk` SET `gambar` = 'produk_33.jpg' WHERE `id` = 33;
UPDATE `produk` SET `gambar` = 'produk_34.jpg' WHERE `id` = 34;
UPDATE `produk` SET `gambar` = 'produk_35.jpg' WHERE `id` = 35;
UPDATE `produk` SET `gambar` = 'produk_36.jpg' WHERE `id` = 36;
UPDATE `produk` SET `gambar` = 'produk_37.jpg' WHERE `id` = 37;
UPDATE `produk` SET `gambar` = 'produk_38.jpg' WHERE `id` = 38;
UPDATE `produk` SET `gambar` = 'produk_39.jpg' WHERE `id` = 39;
UPDATE `produk` SET `gambar` = 'produk_40.jpg' WHERE `id` = 40;
UPDATE `produk` SET `gambar` = 'produk_41.jpg' WHERE `id` = 41;
UPDATE `produk` SET `gambar` = 'produk_42.jpg' WHERE `id` = 42;
UPDATE `produk` SET `gambar` = 'produk_43.jpg' WHERE `id` = 43;
UPDATE `produk` SET `gambar` = 'produk_44.jpg' WHERE `id` = 44;
UPDATE `produk` SET `gambar` = 'produk_45.jpg' WHERE `id` = 45;
UPDATE `produk` SET `gambar` = 'produk_46.jpg' WHERE `id` = 46;
UPDATE `produk` SET `gambar` = 'produk_47.jpg' WHERE `id` = 47;
UPDATE `produk` SET `gambar` = 'produk_48.jpg' WHERE `id` = 48;
UPDATE `produk` SET `gambar` = 'produk_49.jpg' WHERE `id` = 49;
UPDATE `produk` SET `gambar` = 'produk_50.jpg' WHERE `id` = 50;
UPDATE `produk` SET `gambar` = 'produk_51.jpg' WHERE `id` = 51;
UPDATE `produk` SET `gambar` = 'produk_52.jpg' WHERE `id` = 52;
UPDATE `produk` SET `gambar` = 'produk_53.jpg' WHERE `id` = 53;
UPDATE `produk` SET `gambar` = 'produk_54.jpg' WHERE `id` = 54;
UPDATE `produk` SET `gambar` = 'produk_55.jpg' WHERE `id` = 55;
UPDATE `produk` SET `gambar` = 'produk_56.jpg' WHERE `id` = 56;
UPDATE `produk` SET `gambar` = 'produk_57.jpg' WHERE `id` = 57;
UPDATE `produk` SET `gambar` = 'produk_58.jpg' WHERE `id` = 58;
UPDATE `produk` SET `gambar` = 'produk_59.jpg' WHERE `id` = 59;
UPDATE `produk` SET `gambar` = 'produk_60.jpg' WHERE `id` = 60;
UPDATE `produk` SET `gambar` = 'produk_61.jpg' WHERE `id` = 61;
UPDATE `produk` SET `gambar` = 'produk_62.jpg' WHERE `id` = 62;
UPDATE `produk` SET `gambar` = 'produk_63.jpg' WHERE `id` = 63;
UPDATE `produk` SET `gambar` = 'produk_64.jpg' WHERE `id` = 64;
UPDATE `produk` SET `gambar` = 'produk_65.jpg' WHERE `id` = 65;
UPDATE `produk` SET `gambar` = 'produk_66.jpg' WHERE `id` = 66;
UPDATE `produk` SET `gambar` = 'produk_67.jpg' WHERE `id` = 67;
UPDATE `produk` SET `gambar` = 'produk_68.jpg' WHERE `id` = 68;
UPDATE `produk` SET `gambar` = 'produk_69.jpg' WHERE `id` = 69;
UPDATE `produk` SET `gambar` = 'produk_70.jpg' WHERE `id` = 70;
UPDATE `produk` SET `gambar` = 'produk_71.jpg' WHERE `id` = 71;
UPDATE `produk` SET `gambar` = 'produk_72.jpg' WHERE `id` = 72;
UPDATE `produk` SET `gambar` = 'produk_73.jpg' WHERE `id` = 73;
UPDATE `produk` SET `gambar` = 'produk_74.jpg' WHERE `id` = 74;
UPDATE `produk` SET `gambar` = 'produk_75.jpg' WHERE `id` = 75;
UPDATE `produk` SET `gambar` = 'produk_76.jpg' WHERE `id` = 76;
UPDATE `produk` SET `gambar` = 'produk_77.jpg' WHERE `id` = 77;
UPDATE `produk` SET `gambar` = 'produk_78.jpg' WHERE `id` = 78;
UPDATE `produk` SET `gambar` = 'produk_79.jpg' WHERE `id` = 79;
UPDATE `produk` SET `gambar` = 'produk_80.jpg' WHERE `id` = 80;
UPDATE `produk` SET `gambar` = 'produk_81.jpg' WHERE `id` = 81;
UPDATE `produk` SET `gambar` = 'produk_82.jpg' WHERE `id` = 82;
UPDATE `produk` SET `gambar` = 'produk_83.jpg' WHERE `id` = 83;
UPDATE `produk` SET `gambar` = 'produk_84.jpg' WHERE `id` = 84;
UPDATE `produk` SET `gambar` = 'produk_85.jpg' WHERE `id` = 85;
UPDATE `produk` SET `gambar` = 'produk_86.jpg' WHERE `id` = 86;
UPDATE `produk` SET `gambar` = 'produk_87.jpg' WHERE `id` = 87;
UPDATE `produk` SET `gambar` = 'produk_88.jpg' WHERE `id` = 88;
UPDATE `produk` SET `gambar` = 'produk_89.jpg' WHERE `id` = 89;
UPDATE `produk` SET `gambar` = 'produk_90.jpg' WHERE `id` = 90;
UPDATE `produk` SET `gambar` = 'produk_91.jpg' WHERE `id` = 91;
UPDATE `produk` SET `gambar` = 'produk_92.jpg' WHERE `id` = 92;
UPDATE `produk` SET `gambar` = 'produk_93.jpg' WHERE `id` = 93;
UPDATE `produk` SET `gambar` = 'produk_94.jpg' WHERE `id` = 94;
UPDATE `produk` SET `gambar` = 'produk_95.jpg' WHERE `id` = 95;
UPDATE `produk` SET `gambar` = 'produk_96.jpg' WHERE `id` = 96;
UPDATE `produk` SET `gambar` = 'produk_97.jpg' WHERE `id` = 97;
UPDATE `produk` SET `gambar` = 'produk_98.jpg' WHERE `id` = 98;
UPDATE `produk` SET `gambar` = 'produk_99.jpg' WHERE `id` = 99;
UPDATE `produk` SET `gambar` = 'produk_100.jpg' WHERE `id` = 100;
UPDATE `produk` SET `gambar` = 'produk_101.jpg' WHERE `id` = 101;
UPDATE `produk` SET `gambar` = 'produk_102.jpg' WHERE `id` = 102;
UPDATE `produk` SET `gambar` = 'produk_103.jpg' WHERE `id` = 103;
UPDATE `produk` SET `gambar` = 'produk_104.jpg' WHERE `id` = 104;
UPDATE `produk` SET `gambar` = 'produk_105.jpg' WHERE `id` = 105;
UPDATE `produk` SET `gambar` = 'produk_106.jpg' WHERE `id` = 106;
UPDATE `produk` SET `gambar` = 'produk_107.jpg' WHERE `id` = 107;
UPDATE `produk` SET `gambar` = 'produk_108.jpg' WHERE `id` = 108;
UPDATE `produk` SET `gambar` = 'produk_109.jpg' WHERE `id` = 109;
UPDATE `produk` SET `gambar` = 'produk_110.jpg' WHERE `id` = 110;
UPDATE `produk` SET `gambar` = 'produk_111.jpg' WHERE `id` = 111;
UPDATE `produk` SET `gambar` = 'produk_112.jpg' WHERE `id` = 112;
UPDATE `produk` SET `gambar` = 'produk_113.jpg' WHERE `id` = 113;
UPDATE `produk` SET `gambar` = 'produk_114.jpg' WHERE `id` = 114;
UPDATE `produk` SET `gambar` = 'produk_115.jpg' WHERE `id` = 115;
UPDATE `produk` SET `gambar` = 'produk_116.jpg' WHERE `id` = 116;
UPDATE `produk` SET `gambar` = 'produk_117.jpg' WHERE `id` = 117;
UPDATE `produk` SET `gambar` = 'produk_118.jpg' WHERE `id` = 118;
UPDATE `produk` SET `gambar` = 'produk_119.jpg' WHERE `id` = 119;
UPDATE `produk` SET `gambar` = 'produk_120.jpg' WHERE `id` = 120;
UPDATE `produk` SET `gambar` = 'produk_121.jpg' WHERE `id` = 121;
UPDATE `produk` SET `gambar` = 'produk_122.jpg' WHERE `id` = 122;
UPDATE `produk` SET `gambar` = 'produk_123.jpg' WHERE `id` = 123;
UPDATE `produk` SET `gambar` = 'produk_124.jpg' WHERE `id` = 124;
UPDATE `produk` SET `gambar` = 'produk_125.jpg' WHERE `id` = 125;
UPDATE `produk` SET `gambar` = 'produk_126.jpg' WHERE `id` = 126;
UPDATE `produk` SET `gambar` = 'produk_127.jpg' WHERE `id` = 127;
UPDATE `produk` SET `gambar` = 'produk_128.jpg' WHERE `id` = 128;
UPDATE `produk` SET `gambar` = 'produk_129.jpg' WHERE `id` = 129;
UPDATE `produk` SET `gambar` = 'produk_130.jpg' WHERE `id` = 130;
UPDATE `produk` SET `gambar` = 'produk_131.jpg' WHERE `id` = 131;
UPDATE `produk` SET `gambar` = 'produk_132.jpg' WHERE `id` = 132;
UPDATE `produk` SET `gambar` = 'produk_133.jpg' WHERE `id` = 133;
UPDATE `produk` SET `gambar` = 'produk_134.jpg' WHERE `id` = 134;
UPDATE `produk` SET `gambar` = 'produk_135.jpg' WHERE `id` = 135;
UPDATE `produk` SET `gambar` = 'produk_136.jpg' WHERE `id` = 136;
UPDATE `produk` SET `gambar` = 'produk_137.jpg' WHERE `id` = 137;
UPDATE `produk` SET `gambar` = 'produk_138.jpg' WHERE `id` = 138;
UPDATE `produk` SET `gambar` = 'produk_139.jpg' WHERE `id` = 139;
UPDATE `produk` SET `gambar` = 'produk_140.jpg' WHERE `id` = 140;
UPDATE `produk` SET `gambar` = 'produk_141.jpg' WHERE `id` = 141;
UPDATE `produk` SET `gambar` = 'produk_142.jpg' WHERE `id` = 142;
UPDATE `produk` SET `gambar` = 'produk_143.jpg' WHERE `id` = 143;
UPDATE `produk` SET `gambar` = 'produk_144.jpg' WHERE `id` = 144;
UPDATE `produk` SET `gambar` = 'produk_145.jpg' WHERE `id` = 145;
UPDATE `produk` SET `gambar` = 'produk_146.jpg' WHERE `id` = 146;
UPDATE `produk` SET `gambar` = 'produk_147.jpg' WHERE `id` = 147;
UPDATE `produk` SET `gambar` = 'produk_148.jpg' WHERE `id` = 148;
UPDATE `produk` SET `gambar` = 'produk_149.jpg' WHERE `id` = 149;
UPDATE `produk` SET `gambar` = 'produk_150.jpg' WHERE `id` = 150;
UPDATE `produk` SET `gambar` = 'produk_151.jpg' WHERE `id` = 151;
UPDATE `produk` SET `gambar` = 'produk_152.jpg' WHERE `id` = 152;
UPDATE `produk` SET `gambar` = 'produk_153.jpg' WHERE `id` = 153;
UPDATE `produk` SET `gambar` = 'produk_154.jpg' WHERE `id` = 154;
UPDATE `produk` SET `gambar` = 'produk_155.jpg' WHERE `id` = 155;
UPDATE `produk` SET `gambar` = 'produk_156.jpg' WHERE `id` = 156;
UPDATE `produk` SET `gambar` = 'produk_157.jpg' WHERE `id` = 157;
UPDATE `produk` SET `gambar` = 'produk_158.jpg' WHERE `id` = 158;
UPDATE `produk` SET `gambar` = 'produk_159.jpg' WHERE `id` = 159;
UPDATE `produk` SET `gambar` = 'produk_160.jpg' WHERE `id` = 160;
UPDATE `produk` SET `gambar` = 'produk_161.jpg' WHERE `id` = 161;
UPDATE `produk` SET `gambar` = 'produk_162.jpg' WHERE `id` = 162;
UPDATE `produk` SET `gambar` = 'produk_163.jpg' WHERE `id` = 163;