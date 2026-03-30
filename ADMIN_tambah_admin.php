<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$err = $_GET['err'] ?? '';
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    
    // Validasi
    if ($username === '') {
        redirect('tambah_admin.php?err=Masukkan username');
    }
    
    if (strlen($username) > 50) {
        redirect('tambah_admin.php?err=Username maksimal 50 karakter');
    }

    if ($nama === '') {
        redirect('tambah_admin.php?err=Masukkan nama admin');
    }

    if (strlen($nama) > 100) {
        redirect('tambah_admin.php?err=Nama admin maksimal 100 karakter');
    }
    
    if ($password === '') {
        redirect('tambah_admin.php?err=Masukkan password');
    }
    
    if ($password !== $password_confirm) {
        redirect('tambah_admin.php?err=Password dan konfirmasi password tidak sama');
    }
    
    // Cek apakah username sudah ada
    $stmt = $conn->prepare('SELECT username FROM admin WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        redirect('tambah_admin.php?err=Username sudah digunakan. Silakan gunakan username lain.');
    }
    
    // Insert admin baru
    $stmt = $conn->prepare('INSERT INTO admin (username, nama, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $nama, $password);
    
    if ($stmt->execute()) {
        redirect('tambah_admin.php?success=Admin berhasil ditambahkan!');
    } else {
        redirect('tambah_admin.php?err=Gagal menambahkan admin. Silakan coba lagi.');
    }
}

include __DIR__ . '/../layout/header.php';
?>
<h2 class="section-heading" style="color: white;">Tambah Admin Baru</h2>
<p class="subtext mb-3" style="color: white;">Tambahkan admin baru untuk mengelola sistem</p>

<div class="row">
  <div class="col-md-8 col-lg-6">
    <div class="card">
      <div class="card-body">
        <?php if ($err): ?><div class="alert alert-danger mb-3"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success mb-3"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        
        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required maxlength="50" autocomplete="off">
            <small class="form-text text-muted">Username akan digunakan untuk login</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Nama Admin <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama admin" required maxlength="100" autocomplete="off">
            <small class="form-text text-muted">Nama yang akan ditampilkan saat memberikan umpan balik</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="new-password">
            <small class="form-text text-muted">Minimal 3 karakter</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirm" class="form-control" placeholder="Konfirmasi password" required autocomplete="new-password">
          </div>
          
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Tambah Admin</button>
            <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
