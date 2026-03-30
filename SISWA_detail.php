<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_siswa()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

$nis = $_SESSION['nis'];
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('histori.php');

$q = $conn->prepare("SELECT i.id_pelaporan, i.created_at, i.nis, s.kelas, k.ket_kategori, i.lokasi, i.ket, i.foto, a.status, a.feedback, a.admin_name
FROM input_aspirasi i
LEFT JOIN siswa s ON s.nis = i.nis
LEFT JOIN kategori k ON k.id_kategori = i.id_kategori
LEFT JOIN aspirasi a ON a.id_aspirasi = i.id_pelaporan
WHERE i.id_pelaporan = ? AND i.nis = ?");
$q->bind_param('is', $id, $nis);
$q->execute();
$detail = $q->get_result()->fetch_assoc();

if (!$detail) {
    redirect('histori.php');
}

include __DIR__ . '/../layout/header.php';
?>
<style>
  .detail-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
  }

  .back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 30px;
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

  .detail-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 32px;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 31, 63, 0.15);
    margin-bottom: 24px;
    animation: slideDown 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
  }

  .detail-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
  }

  .detail-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #001f3f;
  }

  .status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
  }

  .info-item {
    padding: 16px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(30, 64, 175, 0.02));
    border-radius: 12px;
    border-left: 4px solid #2563eb;
  }

  .info-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 6px;
  }

  .info-value {
    color: #1f2937;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .detail-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  .detail-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 24px;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 31, 63, 0.15);
    animation: slideUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
  }

  .detail-section:nth-child(2) {
    animation-delay: 0.1s;
  }

  .section-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: #001f3f;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
  }

  .section-text {
    color: #374151;
    line-height: 1.8;
    word-break: break-word;
  }

  .photo-container {
    margin-top: 12px;
  }

  .photo-img {
    max-width: 100%;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .photo-img:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(0, 31, 63, 0.2);
  }

  .photo-hint {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 8px;
    text-align: center;
  }

  .feedback-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
    border-left: 4px solid #10b981;
    padding: 20px;
    border-radius: 12px;
    color: #065f46;
  }

  .feedback-box.empty {
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(75, 85, 99, 0.05));
    border-left: 4px solid #9ca3af;
    color: #6b7280;
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
    .detail-content {
      grid-template-columns: 1fr;
    }

    .detail-title-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }

    .detail-header,
    .detail-section {
      padding: 20px;
    }

    .detail-title {
      font-size: 1.4rem;
    }
  }
</style>

<div class="detail-container">
  <a href="histori.php" class="back-button">← Kembali ke Histori</a>

  <div class="detail-header">
    <div class="detail-title-row">
      <h1 class="detail-title">Aspirasi #<?= (int)$detail['id_pelaporan'] ?></h1>
      <?php 
        $status = $detail['status'] ?? 'Menunggu';
        $statusClass = strtolower(str_replace(' ', '-', $status));
      ?>
      <span class="status-badge <?= $statusClass ?>">
        <?php 
          if ($status === 'Selesai') echo '✓ Selesai';
          elseif ($status === 'Proses') echo '⚙ Proses';
          else echo '⏳ Menunggu';
        ?>
      </span>
    </div>

    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">📅 Tanggal Laporan</div>
        <div class="info-value"><?= date('d M Y, H:i', strtotime($detail['created_at'])) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">👤 NIS</div>
        <div class="info-value"><?= htmlspecialchars($detail['nis']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">🎓 Kelas</div>
        <div class="info-value"><?= htmlspecialchars($detail['kelas']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">📂 Kategori</div>
        <div class="info-value"><?= htmlspecialchars($detail['ket_kategori'] ?? 'Umum') ?></div>
      </div>
    </div>
  </div>

  <div class="detail-content">
    <div class="detail-section">
      <h3 class="section-title">📍 Lokasi</h3>
      <p class="section-text"><?= htmlspecialchars($detail['lokasi']) ?></p>

      <h3 class="section-title" style="margin-top: 24px;">📝 Keterangan</h3>
      <p class="section-text"><?= nl2br(htmlspecialchars($detail['ket'])) ?></p>
    </div>

    <div class="detail-section">
      <h3 class="section-title">📸 Bukti Pendukung</h3>
      <?php if (!empty($detail['foto'])): ?>
        <div class="photo-container">
          <?php $fotoPath = '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/' . ltrim($detail['foto'], '/'); ?>
          <a href="<?= htmlspecialchars($fotoPath) ?>" target="_blank">
            <img src="<?= htmlspecialchars($fotoPath) ?>" 
                 alt="Foto Bukti" 
                 class="photo-img">
          </a>
          <p class="photo-hint">Klik gambar untuk melihat ukuran penuh</p>
        </div>
      <?php else: ?>
        <p class="section-text" style="color: #9ca3af; font-style: italic;">Tidak ada foto bukti yang dilampirkan.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="detail-section">
    <h3 class="section-title">💬 Umpan Balik dari Admin</h3>
    <?php if (!empty($detail['feedback'])): ?>
      <div class="feedback-box">
        <p class="section-text" style="color: inherit; margin-bottom: 0;">
          <?= nl2br(htmlspecialchars($detail['feedback'])) ?>
        </p>
        <?php if (!empty($detail['admin_name'])): ?>
          <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.85rem; color: #047857;">
            <strong>Dari:</strong> <?= htmlspecialchars($detail['admin_name']) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="feedback-box empty">
        <p class="section-text" style="color: inherit; margin-bottom: 0;">
          Belum ada umpan balik dari admin. Silakan tunggu aspirasi Anda diproses.
        </p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
