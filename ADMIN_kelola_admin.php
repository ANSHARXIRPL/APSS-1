<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$err = $_GET['err'] ?? '';
$success = $_GET['success'] ?? '';

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $username_to_delete = trim($_GET['delete']);
    
    // Jangan biarkan menghapus diri sendiri
    if ($username_to_delete === $_SESSION['username']) {
        redirect('kelola_admin.php?err=Tidak dapat menghapus akun sendiri');
    }
    
    $stmt = $conn->prepare('DELETE FROM admin WHERE username = ?');
    $stmt->bind_param('s', $username_to_delete);
    
    if ($stmt->execute()) {
        redirect('kelola_admin.php?success=Admin berhasil dihapus');
    } else {
        redirect('kelola_admin.php?err=Gagal menghapus admin');
    }
}

// Get all admins
$admins = [];
$res = $conn->query('SELECT username, nama FROM admin ORDER BY username');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $admins[] = $row;
    }
}

include __DIR__ . '/../layout/header.php';
?>
<h2 class="section-heading" style="color: white;">Kelola Admin</h2>
<p class="subtext mb-3" style="color: white;">Daftar admin yang terdaftar dalam sistem</p>

<?php if ($err): ?><div class="alert alert-danger mb-3"><?= htmlspecialchars($err) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success mb-3"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<div class="mb-3">
  <a href="tambah_admin.php" class="btn btn-primary">Tambah Admin Baru</a>
</div>

<div class="card">
  <div class="card-body">
    <?php if (count($admins) === 0): ?>
      <div class="text-muted text-center py-4">Belum ada admin terdaftar</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>Username</th>
              <th>Nama</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $index => $admin): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td>
                <?= htmlspecialchars($admin['username']) ?>
                <?php if ($admin['username'] === $_SESSION['username']): ?>
                  <span class="badge bg-info ms-2">Anda</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($admin['nama'] ?? 'Admin') ?></td>
              <td>
                <?php if ($admin['username'] !== $_SESSION['username']): ?>
                  <a href="kelola_admin.php?delete=<?= urlencode($admin['username']) ?>" 
                     class="btn btn-sm btn-danger" 
                     onclick="return confirm('Yakin ingin menghapus admin <?= htmlspecialchars($admin['username']) ?>?')">
                    Hapus
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
