<?php
session_start();

// Load env (simple)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $_ENV[trim($k)] = trim($v);
    }
}

$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASS'] ?? '';
$DB_NAME = $_ENV['DB_NAME'] ?? 'apss_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

function is_admin() { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function is_siswa() { return isset($_SESSION['role']) && $_SESSION['role'] === 'siswa'; }
function require_login() {
    if (!isset($_SESSION['role'])) {
        header('Location: /APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');
        exit;
    }
}
function redirect($path) {
    header('Location: ' . $path);
    exit;
}
?>