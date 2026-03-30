<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$nis = $_SESSION['nis'];

$total = $conn->query("SELECT COUNT(*) c FROM input_aspirasi WHERE nis='$nis'")->fetch_assoc()['c'] ?? 0;
$menunggu = $conn->query("SELECT COUNT(*) c FROM aspirasi a JOIN input_aspirasi i ON a.id_aspirasi=i.id_pelaporan WHERE i.nis='$nis' AND a.status='Menunggu'")->fetch_assoc()['c'] ?? 0;
$proses = $conn->query("SELECT COUNT(*) c FROM aspirasi a JOIN input_aspirasi i ON a.id_aspirasi=i.id_pelaporan WHERE i.nis='$nis' AND a.status='Proses'")->fetch_assoc()['c'] ?? 0;
$selesai = $conn->query("SELECT COUNT(*) c FROM aspirasi a JOIN input_aspirasi i ON a.id_aspirasi=i.id_pelaporan WHERE i.nis='$nis' AND a.status='Selesai'")->fetch_assoc()['c'] ?? 0;

include __DIR__ . '/../layout/header.php';
?>
<h2>Dashboard Siswa</h2>
<p>Ringkasan aspirasi Anda</p>
<div class="row">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body text-center">
        <h5>Total Aspirasi</h5>
        <h3><?php echo (int)$total; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body text-center">
        <h5>Menunggu</h5>
        <h3><?php echo (int)$menunggu; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body text-center">
        <h5>Proses</h5>
        <h3><?php echo (int)$proses; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body text-center">
        <h5>Selesai</h5>
        <h3><?php echo (int)$selesai; ?></h3>
      </div>
    </div>
  </div>
</div>
<div class="mt-4">
  <a href="input_aspirasi.php" class="btn btn-primary">Input Aspirasi Baru</a>
  <a href="histori.php" class="btn btn-secondary">Lihat Histori</a>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>