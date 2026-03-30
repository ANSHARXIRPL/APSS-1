<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>APSS - Aplikasi Pengaduan Sarana Sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/assets/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
  <div class="container">
    <a class="navbar-brand brand-gradient" href="#">APSS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample" aria-controls="navbarsExample" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExample">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'siswa'): ?>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/index.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/input_aspirasi.php">Input Aspirasi</a></li>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/histori.php">Histori</a></li>
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/index.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/aspirasi.php">List Aspirasi</a></li>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/histori.php">Histori</a></li>
              <li class="nav-item"><a class="nav-link" href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/kelola_admin.php">Kelola Admin</a></li>
            <?php endif; ?>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <?php if (isset($_SESSION['role'])): ?>
            <span class="navbar-text me-3">Role: <?= htmlspecialchars($_SESSION['role']) ?></span>
            <a href="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/logout.php" class="btn btn-outline-secondary btn-sm btn-logout" data-no-transition>Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="container page-wrap">
