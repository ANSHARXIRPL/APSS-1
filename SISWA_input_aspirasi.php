<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$nis = $_SESSION['nis'];
$msg = '';
$err = '';

// Auto-setup: Cek dan tambahkan kolom foto jika belum ada
$checkFoto = $conn->query("SHOW COLUMNS FROM input_aspirasi LIKE 'foto'");
if ($checkFoto->num_rows == 0) {
    $conn->query("ALTER TABLE input_aspirasi ADD COLUMN foto VARCHAR(255) NULL AFTER ket");
}

// Buat folder uploads jika belum ada
$uploadDir = __DIR__ . '/../uploads/bukti/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$kategori = $conn->query('SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori = $_POST['id_kategori'] ?? '';
    $lokasi = trim($_POST['lokasi'] ?? '');
    $ket = trim($_POST['ket'] ?? '');
    $foto = null;

    if ($id_kategori === '' || $lokasi === '' || $ket === '') {
        $err = 'Semua field wajib diisi';
    } else {
        // Handle upload foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowedTypes)) {
                $err = 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF';
            } elseif ($file['size'] > $maxSize) {
                $err = 'Ukuran file terlalu besar. Maksimal 5MB';
            } else {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'bukti_' . time() . '_' . uniqid() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $foto = 'uploads/bukti/' . $filename;
                } else {
                    $err = 'Gagal mengupload foto';
                }
            }
        }
        
        if (!$err) {
            // Cek apakah kolom foto ada di database (sudah dicek di atas, tapi pastikan lagi)
            $checkFoto = $conn->query("SHOW COLUMNS FROM input_aspirasi LIKE 'foto'");
            $hasFotoColumn = $checkFoto->num_rows > 0;
            
            if ($foto && $hasFotoColumn) {
                $stmt = $conn->prepare('INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, foto) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sisss', $nis, $id_kategori, $lokasi, $ket, $foto);
            } else {
                $stmt = $conn->prepare('INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('siss', $nis, $id_kategori, $lokasi, $ket);
            }
            
            if ($stmt->execute()) {
                $id = $conn->insert_id;
                // Buat entri aspirasi awal dengan status Menunggu
                $stmt2 = $conn->prepare("INSERT INTO aspirasi (id_aspirasi, status, id_kategori, feedback) VALUES (?, 'Menunggu', ?, '')");
                $stmt2->bind_param('ii', $id, $id_kategori);
                $stmt2->execute();
                $msg = 'Aspirasi berhasil dikirim';
            } else {
                $err = 'Gagal menyimpan data';
            }
        }
    }
}

include __DIR__ . '/../layout/header.php';
?>
<h2 class="section-heading" style="color: white;">Input Aspirasi</h2>
<p class="subtext mb-3" style="color: white;">Sampaikan aspirasi Anda secara jelas dan lengkap</p>
<div class="row justify-content-center">
  <div class="col-md-8">
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data" novalidate>
          <div class="mb-3">
            <label class="form-label">NIS</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($nis) ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="id_kategori" class="form-select" required>
              <option value="">-- Pilih --</option>
              <?php while ($row = $kategori->fetch_assoc()): ?>
                <option value="<?= (int)$row['id_kategori'] ?>"><?= htmlspecialchars($row['ket_kategori']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Lokasi Kejadian</label>
            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lapangan olahraga" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="ket" class="form-control" rows="4" placeholder="Tuliskan kronologi dan detail permasalahan" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Foto Bukti (Opsional)</label>
            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif">
            <small class="form-text text-muted">Format: JPG, PNG, atau GIF. Maksimal 5MB</small>
            <div id="fotoPreview" class="mt-2" style="display: none;">
              <img id="previewImg" src="" alt="Preview" style="max-width: 300px; max-height: 300px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Kirim Aspirasi</button>
        </form>
        <script>
          document.querySelector('input[name="foto"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('fotoPreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
              };
              reader.readAsDataURL(file);
            } else {
              preview.style.display = 'none';
            }
          });
        </script>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>