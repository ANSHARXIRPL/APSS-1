<?php
require_once __DIR__ . '/../config/config.php';
require_login();
if (!is_admin()) redirect('/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php');

// KPI ringkasan
$total = $conn->query("SELECT COUNT(*) c FROM input_aspirasi")->fetch_assoc()['c'] ?? 0;
$menunggu = $conn->query("SELECT COUNT(*) c FROM aspirasi WHERE status='Menunggu'")->fetch_assoc()['c'] ?? 0;
$proses = $conn->query("SELECT COUNT(*) c FROM aspirasi WHERE status='Proses'")->fetch_assoc()['c'] ?? 0;
$selesai = $conn->query("SELECT COUNT(*) c FROM aspirasi WHERE status='Selesai'")->fetch_assoc()['c'] ?? 0;

// Data grafik pertambahan aspirasi per hari (default 14 hari terakhir)
$days = 14;
$dailyMap = [];
$resDaily = $conn->query("SELECT DATE(created_at) d, COUNT(*) c FROM input_aspirasi WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY) GROUP BY DATE(created_at) ORDER BY d");
if ($resDaily) {
    while ($r = $resDaily->fetch_assoc()) {
        $dailyMap[$r['d']] = (int)$r['c'];
    }
}
$dailyLabels = [];
$dailyCounts = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $dateYmd = date('Y-m-d', strtotime("-$i days"));
    $dailyLabels[] = date('d M', strtotime($dateYmd));
    $dailyCounts[] = $dailyMap[$dateYmd] ?? 0;
}

// List aspirasi yang masih Menunggu (tampilkan maksimal 10 terbaru)
$pendingRows = [];
$sqlPending = "SELECT ia.id_pelaporan, ia.created_at, ia.nis, ia.lokasi, k.ket_kategori, ia.ket
               FROM aspirasi a
               JOIN input_aspirasi ia ON ia.id_pelaporan = a.id_aspirasi
               JOIN kategori k ON k.id_kategori = ia.id_kategori
               WHERE a.status='Menunggu'
               ORDER BY ia.created_at DESC
               LIMIT 10";
$resPending = $conn->query($sqlPending);
if ($resPending) {
    while ($row = $resPending->fetch_assoc()) { $pendingRows[] = $row; }
}

include __DIR__ . '/../layout/header.php';
?>
<h2 class="section-heading" style="color: white;">Dashboard Admin</h2>
<p class="subtext mb-3" style="color: white;">Ringkasan progres penanganan aspirasi</p>
<div class="row g-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Total Aspirasi</div><div class="fs-3"><?= (int)$total ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Menunggu</div><div class="fs-3"><?= (int)$menunggu ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Proses</div><div class="fs-3"><?= (int)$proses ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Selesai</div><div class="fs-3"><?= (int)$selesai ?></div></div></div></div>
</div>
<div class="mt-4 mb-2 d-flex gap-2">
  <a class="btn btn-primary" href="aspirasi.php">Kelola Aspirasi</a>
  <a class="btn btn-outline-primary" href="kelola_admin.php">Kelola Admin</a>
</div>

<!-- Grafik & List Menunggu -->
<div class="row g-4 mt-1">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">Grafik Penambahan Aspirasi Harian (<?= $days ?> hari)</h5>
        </div>
        <div style="position: relative; height: 300px; max-height: 300px;">
          <canvas id="dailyAspirasiChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">Aspirasi Menunggu</h5>
          <a href="aspirasi.php" class="btn btn-sm btn-primary" data-no-transition>Kelola</a>
        </div>
        <?php if (count($pendingRows) === 0): ?>
          <div class="text-muted">Tidak ada aspirasi menunggu.</div>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($pendingRows as $p): ?>
              <a href="detail.php?id=<?= (int)$p['id_pelaporan'] ?>" class="list-group-item list-group-item-action">
                <div class="small text-muted"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></div>
                <div class="fw-semibold"><?= htmlspecialchars($p['ket_kategori']) ?> • <?= htmlspecialchars($p['lokasi']) ?></div>
                <div class="text-truncate small"><?= htmlspecialchars($p['ket']) ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js and initializer -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  // Flag untuk mencegah multiple initialization pada halaman yang sama
  if (window.dailyAspirasiChartInitialized) {
    return;
  }
  
  function initChart() {
    const ctx = document.getElementById('dailyAspirasiChart');
    if (!ctx) return;
    
    // Hancurkan chart sebelumnya jika ada (cek dengan Chart.getChart juga)
    if (window.dailyAspirasiChartInstance) {
      try {
        window.dailyAspirasiChartInstance.destroy();
      } catch(e) {}
      window.dailyAspirasiChartInstance = null;
    }
    
    // Cek apakah Chart.js sudah membuat instance untuk canvas ini
    if (typeof Chart !== 'undefined') {
      const existingChart = Chart.getChart(ctx);
      if (existingChart) {
        try {
          existingChart.destroy();
        } catch(e) {}
      }
    }
    
    const labels = <?= json_encode($dailyLabels, JSON_UNESCAPED_UNICODE) ?>;
    const dataPoints = <?= json_encode(array_map('intval', $dailyCounts), JSON_NUMERIC_CHECK) ?>;
    
    // Pastikan Chart.js sudah di-load, tunggu jika belum
    if (typeof Chart === 'undefined') {
      setTimeout(initChart, 100);
      return;
    }
    
    // Set flag untuk mencegah multiple initialization
    window.dailyAspirasiChartInitialized = true;
    
    // Simpan instance chart ke window untuk bisa di-destroy saat refresh
    window.dailyAspirasiChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Aspirasi',
          data: dataPoints,
          tension: 0.3,
          fill: true,
          borderColor: 'rgba(255,99,132,1)',
          backgroundColor: 'rgba(255,99,132,0.15)',
          pointBackgroundColor: 'rgba(255,99,132,1)',
          pointRadius: 3,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 10,
            bottom: 10,
            left: 10,
            right: 10
          }
        },
        scales: {
          y: { 
            beginAtZero: true, 
            ticks: { precision: 0 },
            grid: {
              display: true
            }
          },
          x: { 
            grid: { display: false }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: { mode: 'index', intersect: false }
        }
      }
    });
  }
  
  // Reset flag saat halaman di-unload (untuk refresh berikutnya)
  window.addEventListener('beforeunload', function() {
    window.dailyAspirasiChartInitialized = false;
    if (window.dailyAspirasiChartInstance) {
      try {
        window.dailyAspirasiChartInstance.destroy();
      } catch(e) {}
      window.dailyAspirasiChartInstance = null;
    }
  });
  
  // Pastikan script hanya dijalankan sekali setelah DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChart);
  } else {
    initChart();
  }
})();
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
