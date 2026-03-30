<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');
$nis = $_SESSION['nis'];

$q = $conn->prepare("SELECT i.id_pelaporan, i.lokasi, i.ket, i.id_kategori, k.ket_kategori, a.status, a.feedback, i.created_at, i.foto
FROM input_aspirasi i
LEFT JOIN kategori k ON k.id_kategori = i.id_kategori
LEFT JOIN aspirasi a ON a.id_aspirasi = i.id_pelaporan
WHERE i.nis = ?
ORDER BY i.created_at DESC, i.id_pelaporan DESC");
$q->bind_param('s', $nis);
$q->execute();
$res = $q->get_result();

include __DIR__ . '/../layout/header.php';
?>
<style>
  .histori-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }

  .histori-header {
    margin-bottom: 30px;
    animation: slideDown 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
  }

  .histori-title {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    margin-bottom: 8px;
  }

  .histori-subtitle {
    color: rgba(255, 255, 255, 0.85);
    font-weight: 500;
  }

  .histori-list {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 31, 63, 0.15);
    animation: slideUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    overflow-x: auto;
  }

  .table-wrapper {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 31, 63, 0.15);
    animation: slideUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    overflow-x: auto;
  }

  .table-custom {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
  }

  .table-custom thead {
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: white;
  }

  .table-custom th {
    padding: 16px 18px;
    text-align: left;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
  }

  .table-custom tbody tr {
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.3s ease;
  }

  .table-custom tbody tr:last-child {
    border-bottom: none;
  }

  .table-custom tbody tr:hover {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(30, 64, 175, 0.02));
  }

  .table-custom td {
    padding: 16px 18px;
    color: #374151;
    font-size: 0.9rem;
  }

  .td-id {
    font-weight: 800;
    color: #1e3a8a;
    min-width: 60px;
  }

  .td-date {
    min-width: 150px;
    font-size: 0.85rem;
    color: #6b7280;
  }

  .td-kategori {
    display: inline-block;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(30, 64, 175, 0.05));
    color: #1e3a8a;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    min-width: 100px;
  }

  .td-lokasi {
    min-width: 120px;
  }

  .td-description {
    max-width: 200px;
    word-break: break-word;
    color: #6b7280;
    min-width: 150px;
  }

  .status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    min-width: 100px;
    text-align: center;
  }

  .status-badge.selesai {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.1));
    color: #065f46;
    border: 1px solid rgba(16, 185, 129, 0.3);
  }

  .status-badge.proses {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
    color: #92400e;
    border: 1px solid rgba(245, 158, 11, 0.3);
  }

  .status-badge.menunggu {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.2), rgba(75, 85, 99, 0.1));
    color: #374151;
    border: 1px solid rgba(107, 114, 128, 0.3);
  }

  .btn-detail {
    padding: 8px 14px;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.8rem;
    display: inline-block;
    min-width: 100px;
    text-align: center;
  }

  .btn-detail:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
  }



  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: rgba(255, 255, 255, 0.7);
  }

  .empty-icon {
    font-size: 4rem;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  .empty-text {
    font-size: 1.2rem;
    margin-bottom: 8px;
    color: white;
  }

  .empty-subtext {
    font-size: 0.95rem;
    margin-bottom: 24px;
  }

  .btn-create {
    display: inline-block;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
  }

  .btn-create:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
  }

  .back-button {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 16px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .back-button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
  }

  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 768px) {
    .histori-title {
      font-size: 1.5rem;
    }

    .table-wrapper {
      border-radius: 12px;
    }

    .table-custom th,
    .table-custom td {
      padding: 12px 10px;
      font-size: 0.8rem;
    }

    .td-description {
      max-width: 100px;
    }

    .btn-detail {
      padding: 6px 10px;
      font-size: 0.75rem;
    }
  }
</style>

<div class="histori-container">
  <a href="index.php" class="back-button">← Kembali ke Dashboard</a>

  <div class="histori-header">
    <h1 class="histori-title">📜 Histori Aspirasi</h1>
    <p class="histori-subtitle">Lihat status dan umpan balik dari semua aspirasi Anda</p>
  </div>

  <?php if ($res->num_rows > 0): ?>
    <div class="table-wrapper">
      <table class="table-custom">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($r = $res->fetch_assoc()): 
            $status = $r['status'] ?? 'Menunggu';
            $statusClass = strtolower(str_replace(' ', '-', $status));
          ?>
            <tr>
              <td class="td-id">#<?= (int)$r['id_pelaporan'] ?></td>
              <td class="td-date"><?= date('d M Y, H:i', strtotime($r['created_at'] ?? now())) ?></td>
              <td><span class="td-kategori"><?= htmlspecialchars($r['ket_kategori'] ?? 'Umum') ?></span></td>
              <td class="td-lokasi"><?= htmlspecialchars($r['lokasi']) ?></td>
              <td class="td-description"><?= htmlspecialchars(mb_substr($r['ket'], 0, 80)) ?><?= mb_strlen($r['ket']) > 80 ? '...' : '' ?></td>
              <td>
                <span class="status-badge <?= $statusClass ?>">
                  <?php 
                    if ($status === 'Selesai') echo '✓ Selesai';
                    elseif ($status === 'Proses') echo '⚙ Proses';
                    else echo '⏳ Menunggu';
                  ?>
                </span>
              </td>
              <td>
                <a href="detail.php?id=<?= (int)$r['id_pelaporan'] ?>" class="btn-detail">Detail</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      <div class="empty-text">Belum Ada Aspirasi</div>
      <div class="empty-subtext">Anda belum mengirimkan aspirasi apapun. Mulai dengan membuat aspirasi baru.</div>
      <a href="input_aspirasi.php" class="btn-create">➕ Buat Aspirasi Baru</a>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>