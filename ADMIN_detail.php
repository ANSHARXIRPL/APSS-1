<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$id = (int)($_GET['id'] ?? 0);
if ($id<=0) redirect('aspirasi.php');

$msg = '';$err='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'Menunggu';
    $feedback = trim($_POST['feedback'] ?? '');
    
    // Get admin name from database
    $adminQuery = $conn->prepare('SELECT nama FROM admin WHERE username = ?');
    $adminQuery->bind_param('s', $_SESSION['username']);
    $adminQuery->execute();
    $adminResult = $adminQuery->get_result()->fetch_assoc();
    $adminName = $adminResult['nama'] ?? 'Admin';
    
    $stmt = $conn->prepare('UPDATE aspirasi SET status=?, feedback=?, admin_name=? WHERE id_aspirasi=?');
    $stmt->bind_param('sssi', $status, $feedback, $adminName, $id);
    if ($stmt->execute()) { $msg='Perubahan disimpan'; } else { $err='Gagal menyimpan perubahan'; }
}

$q = $conn->prepare("SELECT i.id_pelaporan, i.created_at, i.nis, s.kelas, k.ket_kategori, i.lokasi, i.ket, i.foto, a.status, a.feedback
FROM input_aspirasi i
LEFT JOIN siswa s ON s.nis = i.nis
LEFT JOIN kategori k ON k.id_kategori = i.id_kategori
LEFT JOIN aspirasi a ON a.id_aspirasi = i.id_pelaporan
WHERE i.id_pelaporan = ?");
$q->bind_param('i', $id);
$q->execute();
$detail = $q->get_result()->fetch_assoc();
if (!$detail) redirect('aspirasi.php');

include __DIR__ . '/../layout/header.php';
?>
<a href="aspirasi.php" class="btn btn-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Detail Aspirasi #<?= (int)$detail['id_pelaporan'] ?></h5>
    <div class="row mb-3">
      <div class="col-md-6">
        <div><strong>Tanggal</strong>: <?= htmlspecialchars($detail['created_at']) ?></div>
        <div><strong>NIS</strong>: <?= htmlspecialchars($detail['nis']) ?> (Kelas: <?= htmlspecialchars($detail['kelas']) ?>)</div>
        <div><strong>Kategori</strong>: <?= htmlspecialchars($detail['ket_kategori']) ?></div>
      </div>
      <div class="col-md-6">
        <div><strong>Lokasi</strong>: <?= htmlspecialchars($detail['lokasi']) ?></div>
        <div><strong>Keterangan</strong>: <?= nl2br(htmlspecialchars($detail['ket'])) ?></div>
        <?php if (!empty($detail['foto'])): ?>
          <div class="mt-3">
            <strong>Foto Bukti:</strong><br>
            <?php $fotoPath = '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/' . ltrim($detail['foto'], '/'); ?>
            <a href="<?= htmlspecialchars($fotoPath) ?>" target="_blank" class="d-inline-block mt-2">
              <img src="<?= htmlspecialchars($fotoPath) ?>" 
                   alt="Foto Bukti" 
                   class="img-thumbnail" 
                   style="max-width: 400px; max-height: 400px; cursor: pointer; border: 2px solid #ddd;">
            </a>
            <br>
            <small class="text-muted">Klik gambar untuk melihat ukuran penuh</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <form method="post" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Status Penyelesaian</label>
        <select name="status" class="form-select">
          <?php foreach (['Menunggu','Proses','Selesai'] as $st): ?>
          <option value="<?= $st ?>" <?= $detail['status']===$st?'selected':'' ?>><?= $st ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label">Umpan Balik</label>
        <textarea name="feedback" class="form-control" rows="3"><?= htmlspecialchars($detail['feedback'] ?? '') ?></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>