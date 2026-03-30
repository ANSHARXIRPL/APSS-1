<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT i.id_pelaporan, i.created_at, i.nis, k.ket_kategori, i.lokasi, i.ket, a.status
FROM input_aspirasi i
LEFT JOIN kategori k ON k.id_kategori = i.id_kategori
LEFT JOIN aspirasi a ON a.id_aspirasi = i.id_pelaporan";

if ($filter_status) {
    if ($filter_status === 'Menunggu') {
        $query .= " WHERE a.status IS NULL";
    } else {
        $query .= " WHERE a.status = '" . $conn->real_escape_string($filter_status) . "'";
    }
}

$query .= " ORDER BY i.created_at DESC, i.id_pelaporan DESC";
$res = $conn->query($query);

include __DIR__ . '/../layout/header.php';
?>
<div class="histori-container">
  <div class="histori-header mb-4">
    <h2 class="section-heading" style="color: white; margin-bottom: 15px;">
      <i class="bi bi-clock-history"></i> Histori Aspirasi
    </h2>
    <p class="subtext" style="color: rgba(255,255,255,0.8); margin-bottom: 25px;">Kelola dan pantau semua aspirasi dari siswa</p>
    
    <div class="filter-section">
      <label style="color: white; font-weight: 600; margin-bottom: 12px; display: block;">Filter Status:</label>
      <div class="filter-buttons">
        <a href="histori.php" class="filter-btn <?= !$filter_status ? 'active' : '' ?>">
          <span class="badge-count">Semua</span>
        </a>
        <a href="?status=Menunggu" class="filter-btn <?= $filter_status === 'Menunggu' ? 'active' : '' ?>">
          <i class="status-icon pending"></i> Menunggu
        </a>
        <a href="?status=Proses" class="filter-btn <?= $filter_status === 'Proses' ? 'active' : '' ?>">
          <i class="status-icon processing"></i> Proses
        </a>
        <a href="?status=Selesai" class="filter-btn <?= $filter_status === 'Selesai' ? 'active' : '' ?>">
          <i class="status-icon completed"></i> Selesai
        </a>
      </div>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th style="width: 8%;">ID</th>
            <th style="width: 15%;">Tanggal</th>
            <th style="width: 10%;">NIS</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 15%;">Lokasi</th>
            <th style="width: 20%;">Keterangan</th>
            <th style="width: 12%;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res->num_rows > 0): ?>
            <?php while ($r = $res->fetch_assoc()): ?>
            <tr class="table-row">
              <td><strong>#<?= (int)$r['id_pelaporan'] ?></strong></td>
              <td>
                <small class="text-muted"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></small>
              </td>
              <td><code><?= htmlspecialchars($r['nis']) ?></code></td>
              <td><?= htmlspecialchars($r['ket_kategori'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['lokasi']) ?></td>
              <td>
                <small><?= htmlspecialchars(mb_substr($r['ket'], 0, 40)) ?><?= mb_strlen($r['ket']) > 40 ? '...' : '' ?></small>
              </td>
              <td>
                <?php 
                  $status = $r['status'] ?? 'Menunggu';
                  $badge_class = $status === 'Selesai' ? 'bg-success' : ($status === 'Proses' ? 'bg-info' : 'bg-secondary');
                  $icon = $status === 'Selesai' ? '✓' : ($status === 'Proses' ? '⟳' : '●');
                ?>
                <span class="badge <?= $badge_class ?> status-badge">
                  <span class="status-icon-badge"><?= $icon ?></span> <?= htmlspecialchars($status) ?>
                </span>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                <p style="margin-bottom: 0;">Tidak ada data aspirasi untuk filter ini</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .histori-container {
    animation: fadeIn 0.5s ease-in;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .histori-header {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.1);
  }

  .filter-section {
    margin-top: 20px;
  }

  .filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .filter-btn {
    padding: 10px 18px;
    border-radius: 20px;
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    font-size: 14px;
  }

  .filter-btn:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
    transform: translateY(-2px);
  }

  .filter-btn.active {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-color: #2563eb;
    color: white;
    box-shadow: 0 8px 20px rgba(37,99,235,0.3);
  }

  .badge-count {
    display: inline-block;
  }

  .status-icon {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }

  .status-icon.pending {
    background-color: #ef4444;
    animation: pulse 2s infinite;
  }

  .status-icon.processing {
    background-color: #f59e0b;
    animation: pulse 2s infinite;
  }

  .status-icon.completed {
    background-color: #10b981;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }

  .table-card {
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.3);
  }

  .table {
    margin-bottom: 0;
  }

  .table thead th {
    background: linear-gradient(90deg, rgba(0,31,63,0.8), rgba(30,58,138,0.8));
    color: white;
    font-weight: 700;
    border: none;
    padding: 18px 12px;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
  }

  .table tbody tr {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  .table tbody tr:hover {
    background: rgba(37,99,235,0.08);
    box-shadow: inset 3px 0 0 #2563eb;
  }

  .table tbody tr:last-child {
    border-bottom: none;
  }

  .table-row td {
    padding: 16px 12px;
    vertical-align: middle;
  }

  .status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .status-icon-badge {
    display: inline-block;
    font-weight: bold;
  }

  .bg-success {
    background-color: #10b981 !important;
    color: white !important;
  }

  .bg-info {
    background-color: #f59e0b !important;
    color: white !important;
  }

  .bg-secondary {
    background-color: #6b7280 !important;
    color: white !important;
  }

  @media (max-width: 768px) {
    .filter-buttons {
      flex-direction: column;
    }

    .filter-btn {
      width: 100%;
      justify-content: center;
    }

    .table {
      font-size: 13px;
    }

    .table thead th,
    .table tbody tr td {
      padding: 10px 8px;
    }
  }
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>