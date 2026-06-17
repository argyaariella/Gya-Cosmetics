<?php
// ============================================
// config/config.php
// Konfigurasi koneksi database & session
// ============================================

// Pengaturan database (Dinamis untuk Localhost & InfinityFree)
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Kredensial XAMPP Localhost
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'gya_cosmetics');
} else {
    // Kredensial Hosting InfinityFree
    define('DB_HOST', 'sql309.infinityfree.com');
    define('DB_USER', 'if0_42200472');
    define('DB_PASS', '***_SENSOR_DEMI_KEAMANAN_***');
    define('DB_NAME', 'if0_42200472_gya');
}

// Pengaturan aplikasi
define('APP_NAME', 'GYA Cosmetics');
// Buat APP_URL dinamis (untuk localhost pakai subfolder, untuk hosting pakai root domain)
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    define('APP_URL', 'http://localhost/gya-cosmetics/gya-cosmetics');
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST']);
}
define('WA_NUMBER', '6288245680639'); // Ganti dengan nomor WA toko

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database menggunakan MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("
    <div style='font-family:sans-serif; background:#fff0f0; padding:20px; margin:20px; border-radius:8px; border:1px solid #ffcccc;'>
        <h3 style='color:#cc0000;'>❌ Koneksi Database Gagal</h3>
        <p>Error: " . $conn->connect_error . "</p>
        <p><strong>Solusi:</strong></p>
        <ul>
            <li>Pastikan XAMPP sudah dijalankan (Apache + MySQL)</li>
            <li>Cek username dan password di config.php</li>
            <li>Pastikan database <strong>gya_cosmetics</strong> sudah dibuat</li>
        </ul>
    </div>
    ");
}

// Set charset ke UTF-8 agar mendukung karakter Indonesia
$conn->set_charset("utf8mb4");

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Bersihkan input dari karakter berbahaya (XSS)
 */
function bersihkan($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Format angka ke format rupiah
 * Contoh: formatRupiah(50000) → "Rp 50.000"
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 * Contoh: formatTanggal('2025-01-15') → "15 Januari 2025"
 */
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $t = explode('-', $tanggal);
    return $t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
}

/**
 * Cek apakah user sudah login
 * Redirect ke login.php jika belum
 */
function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . APP_URL . '/login.php');
        exit();
    }
}

/**
 * Cek role user (admin atau owner)
 * Redirect jika role tidak sesuai
 */
function cekRole($role_yang_dibutuhkan) {
    cekLogin();
    if ($_SESSION['role'] !== $role_yang_dibutuhkan) {
        // Jika admin coba akses halaman owner, atau sebaliknya
        if ($_SESSION['role'] === 'admin') {
            header('Location: ' . APP_URL . '/admin/dashboard.php');
        } else {
            header('Location: ' . APP_URL . '/owner/dashboard.php');
        }
        exit();
    }
}

/**
 * Simpan activity log
 */
function simpanLog($user_id, $aktivitas, $keterangan = '') {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, aktivitas, keterangan, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $aktivitas, $keterangan, $ip);
    $stmt->execute();
    $stmt->close();
}

/**
 * Generate kode transaksi unik
 * Contoh: TRX-20250305-001
 */
function generateKodeTransaksi() {
    global $conn;
    $tanggal = date('Ymd');
    $prefix = 'TRX-' . $tanggal . '-';
    
    $result = $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE kode_transaksi LIKE '$prefix%'");
    $row = $result->fetch_assoc();
    $nomor = str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);
    
    return $prefix . $nomor;
}

/**
 * Tampilkan alert (dipanggil dari session flash message)
 */
function tampilAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $type = $_SESSION['alert_type'] ?? 'success';
        unset($_SESSION['alert']);
        unset($_SESSION['alert_type']);
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$alert}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}

/**
 * Set flash message untuk ditampilkan di halaman berikutnya
 */
function setAlert($pesan, $type = 'success') {
    $_SESSION['alert'] = $pesan;
    $_SESSION['alert_type'] = $type;
}
?>