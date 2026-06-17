<?php
// ============================================
// logout.php
// Proses logout & hapus session
// ============================================
require_once 'config/config.php';

if (isset($_SESSION['user_id'])) {
    simpanLog($_SESSION['user_id'], 'logout', 'Logout dari sistem');
}

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit();
?>