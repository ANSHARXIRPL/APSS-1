<?php
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['role'])) {
    if (is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/admin/index.php');
    if (is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/siswa/index.php');
}

$err = $_GET['err'] ?? '';
$success = $_GET['success'] ?? '';

// Daftar kelas yang tersedia
$kelasOptions = [
    'X IPA 1', 'X IPA 2', 'X IPA 3', 'X IPA 4',
    'X IPS 1', 'X IPS 2', 'X IPS 3', 'X IPS 4',
    'XI IPA 1', 'XI IPA 2', 'XI IPA 3', 'XI IPA 4',
    'XI IPS 1', 'XI IPS 2', 'XI IPS 3', 'XI IPS 4',
    'XII IPA 1', 'XII IPA 2', 'XII IPA 3', 'XII IPA 4',
    'XII IPS 1', 'XII IPS 2', 'XII IPS 3', 'XII IPS 4',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    
    // Validasi
    if ($nis === '') {
        redirect('daftar.php?err=Masukkan NIS');
    }
    
    if ($kelas === '') {
        redirect('daftar.php?err=Pilih kategori kelas');
    }
    
    if (!in_array($kelas, $kelasOptions)) {
        redirect('daftar.php?err=Kelas tidak valid');
    }
    
    // Cek apakah NIS sudah terdaftar
    $stmt = $conn->prepare('SELECT nis FROM siswa WHERE nis = ? LIMIT 1');
    $stmt->bind_param('s', $nis);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        redirect('daftar.php?err=NIS sudah terdaftar. Silakan login atau gunakan NIS lain.');
    }
    
    // Insert siswa baru
    $stmt = $conn->prepare('INSERT INTO siswa (nis, kelas) VALUES (?, ?)');
    $stmt->bind_param('ss', $nis, $kelas);
    
    if ($stmt->execute()) {
        redirect('login.php?success=Pendaftaran berhasil! Silakan login dengan NIS Anda.');
    } else {
        redirect('daftar.php?err=Gagal mendaftar. Silakan coba lagi.');
    }
}

include __DIR__ . '/layout/header.php';
?>
<div class="hero-gradient">
  <div class="login-card card w-100">
    <div class="card-body p-4 p-md-5">
      <div class="logo-anim" id="apssLogo">
        APSS
      </div>
      <h2 class="section-heading text-center">Daftar sebagai Siswa</h2>
      <p class="subtext text-center mb-4">Daftarkan diri Anda untuk dapat mengirimkan aspirasi dan pengaduan sarana sekolah</p>
      
      <?php if ($err): ?><div class="alert alert-danger mb-3"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success mb-3"><?= htmlspecialchars($success) ?></div><?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Kategori Kelas <span class="text-danger">*</span></label>
          <select name="kelas" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            <optgroup label="Kelas X">
              <option value="X IPA 1">X IPA 1</option>
              <option value="X IPA 2">X IPA 2</option>
              <option value="X IPA 3">X IPA 3</option>
              <option value="X IPA 4">X IPA 4</option>
              <option value="X IPS 1">X IPS 1</option>
              <option value="X IPS 2">X IPS 2</option>
              <option value="X IPS 3">X IPS 3</option>
              <option value="X IPS 4">X IPS 4</option>
            </optgroup>
            <optgroup label="Kelas XI">
              <option value="XI IPA 1">XI IPA 1</option>
              <option value="XI IPA 2">XI IPA 2</option>
              <option value="XI IPA 3">XI IPA 3</option>
              <option value="XI IPA 4">XI IPA 4</option>
              <option value="XI IPS 1">XI IPS 1</option>
              <option value="XI IPS 2">XI IPS 2</option>
              <option value="XI IPS 3">XI IPS 3</option>
              <option value="XI IPS 4">XI IPS 4</option>
            </optgroup>
            <optgroup label="Kelas XII">
              <option value="XII IPA 1">XII IPA 1</option>
              <option value="XII IPA 2">XII IPA 2</option>
              <option value="XII IPA 3">XII IPA 3</option>
              <option value="XII IPA 4">XII IPA 4</option>
              <option value="XII IPS 1">XII IPS 1</option>
              <option value="XII IPS 2">XII IPS 2</option>
              <option value="XII IPS 3">XII IPS 3</option>
              <option value="XII IPS 4">XII IPS 4</option>
            </optgroup>
          </select>
        </div>
        
        <div class="mb-3">
          <label class="form-label">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
          <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS Anda" required maxlength="30">
          <small class="form-text text-muted">NIS akan digunakan untuk login setelah pendaftaran</small>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>
      </form>
      
      <div class="text-center">
        <p class="mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk di sini</a></p>
      </div>
    </div>
  </div>
</div>
<script>
  // Jika Anda mengganti logo, letakkan gambar di /assets/logo-apss.png lalu gantikan inner HTML logo di bawah ini.
  (function(){
    const el = document.getElementById('apssLogo');
    const img = new Image();
    img.src = '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/assets/logo-apss.png';
    img.alt = 'Logo APSS';
    img.style.maxWidth = '80%';
    img.style.maxHeight = '80%';
    img.style.borderRadius = '12px';
    img.onload = function(){
      el.innerHTML = '';
      el.appendChild(img);
    }
  })();
</script>
<?php include __DIR__ . '/layout/footer.php'; ?>
