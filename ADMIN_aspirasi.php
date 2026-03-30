<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$where = [];
$params = [];
$types = '';

if (!empty($_GET['tanggal'])) {
    $where[] = 'DATE(i.created_at) = ?';
    $types .= 's';
    $params[] = $_GET['tanggal'];
}
if (!empty($_GET['bulan'])) {
    $where[] = 'MONTH(i.created_at) = ?';
    $types .= 'i';
    $params[] = (int)$_GET['bulan'];
}
if (!empty($_GET['nis'])) {
    $where[] = 'i.nis = ?';
    $types .= 's';
    $params[] = $_GET['nis'];
}
if (!empty($_GET['id_kategori'])) {
    $where[] = 'i.id_kategori = ?';
    $types .= 'i';
    $params[] = (int)$_GET['id_kategori'];
}
if (!empty($_GET['kelas'])) {
    $where[] = 's.kelas = ?';
    $types .= 's';
    $params[] = $_GET['kelas'];
}
if (!empty($_GET['status'])) {
    $where[] = 'a.status = ?';
    $types .= 's';
    $params[] = $_GET['status'];
}

$sql = "SELECT i.id_pelaporan, i.created_at, i.nis, s.kelas, k.ket_kategori, i.lokasi, i.ket, a.status, a.feedback
FROM input_aspirasi i
LEFT JOIN siswa s ON s.nis = i.nis
LEFT JOIN kategori k ON k.id_kategori = i.id_kategori
LEFT JOIN aspirasi a ON a.id_aspirasi = i.id_pelaporan";
if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' ORDER BY i.created_at DESC, i.id_pelaporan DESC';

$stmt = $conn->prepare($sql);
if ($where) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();

$kelasList = $conn->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
$kat = $conn->query('SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori');

include __DIR__ . '/../layout/header.php';
?>
<h2>List Aspirasi</h2>
<p>Kelola aspirasi siswa</p>

<form method="get" class="row g-3 mb-4">
  <div class="col-md-2">
    <label class="form-label">Kategori</label>
    <select name="id_kategori" class="form-select">
      <option value="">Semua Kategori</option>
      <?php
      $kat = $conn->query('SELECT id_kategori, ket_kategori FROM kategori ORDER BY ket_kategori');
      while ($k = $kat->fetch_assoc()): ?>
      <option value="<?= $k['id_kategori'] ?>" <?= (isset($_GET['id_kategori']) && (int)$_GET['id_kategori']===$k['id_kategori'])?'selected':'' ?>>
        <?= htmlspecialchars($k['ket_kategori']) ?>
      </option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Kelas</label>
    <input type="text" name="kelas" value="<?= htmlspecialchars($_GET['kelas'] ?? '') ?>" class="form-control" placeholder="Cari kelas...">
  </div>
  <div class="col-md-2">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option value="">Semua Status</option>
      <option value="Menunggu" <?= ($_GET['status'] ?? '') === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
      <option value="Proses" <?= ($_GET['status'] ?? '') === 'Proses' ? 'selected' : '' ?>>Proses</option>
      <option value="Selesai" <?= ($_GET['status'] ?? '') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button type="submit" class="btn btn-primary me-2">Filter</button>
    <a href="aspirasi.php" class="btn btn-secondary">Reset</a>
  </div>
</form>

<table class="table table-striped">
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>NIS</th>
      <th>Kelas</th>
      <th>Kategori</th>
      <th>Lokasi</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($res->num_rows > 0): ?>
      <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
        <td><?= htmlspecialchars($row['nis']) ?></td>
        <td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['ket_kategori']) ?></td>
        <td><?= htmlspecialchars($row['lokasi']) ?></td>
        <td>
          <span class="badge bg-<?= $row['status'] === 'Selesai' ? 'success' : ($row['status'] === 'Proses' ? 'warning' : 'secondary') ?>">
            <?= htmlspecialchars($row['status'] ?? 'Menunggu') ?>
          </span>
        </td>
        <td>
          <a href="detail.php?id=<?= (int)$row['id_pelaporan'] ?>" class="btn btn-sm btn-primary">Detail</a>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="7" class="text-center">Tidak ada data</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>
