
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="kpi-grid mb-6" id="kpi-grid">
  <div class="kpi-card kpi-primary">
    <div class="kpi-icon bg-primary-soft">
      <svg fill="none" stroke="#1e40af" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
      </svg>
    </div>
    <div class="kpi-value" data-countup="0" id="kpi-total-stok">—</div>
    <div class="kpi-label">Total Stok Aktif</div>
    <div class="kpi-change text-muted text-sm" id="kpi-stok-unit">unit tersimpan</div>
  </div>

  <div class="kpi-card kpi-success">
    <div class="kpi-icon bg-success-soft">
      <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
    </div>
    <div class="kpi-value" id="kpi-kapasitas">—</div>
    <div class="kpi-label">Kapasitas Terpakai</div>
    <div class="kpi-change text-muted fs-xs" id="kpi-kapasitas-sub">dari total slot gudang</div>
  </div>

  <div class="kpi-card kpi-purple">
    <div class="kpi-icon bg-purple-soft">
      <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
    </div>
    <div class="kpi-value" data-countup="0" id="kpi-tx">—</div>
    <div class="kpi-label">Transaksi Hari Ini</div>
    <div class="kpi-change text-muted text-sm">inbound + outbound</div>
  </div>

  <div class="kpi-card kpi-danger">
    <div class="kpi-icon bg-danger-soft">
      <svg fill="none" stroke="#dc2626" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
    </div>
    <div class="kpi-value" data-countup="0" id="kpi-alert">—</div>
    <div class="kpi-label">Alert Stok Rendah</div>
    <div class="kpi-change kpi-down text-sm" id="kpi-alert-sub">item perlu restock</div>
  </div>
</div>

<?php
$role = $_SESSION['user_role'] ?? '';
$showCharts = in_array($role, ['admin_gudang', 'manajemen', 'divisi_pembelian', 'divisi_penjualan']);
?>

<?php if ($showCharts): ?>

<div class="grid-2 mb-6">

  <div class="card dashboard-grid-card">
    <div class="card-header">
      <div>
        <div class="card-title">Arus Barang 7 Hari Terakhir</div>
        <div class="fs-sm text-muted mt-1">Kuantitas Masuk vs Keluar</div>
      </div>
      <div class="d-flex gap-3 fs-sm">
        <span class="d-flex align-center gap-1">
          <span class="chart-legend-color blue"></span> Masuk
        </span>
        <span class="d-flex align-center gap-1">
          <span class="chart-legend-color red"></span> Keluar
        </span>
      </div>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-flow"></canvas>
      </div>
    </div>
  </div>

  <div class="card dashboard-grid-card">
    <div class="card-header">
      <div>
        <div class="card-title">Tren Volume Stok Gudang</div>
        <div class="fs-sm text-muted mt-1">Total akumulasi unit stok 7 hari terakhir</div>
      </div>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-trend"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="grid-2 mb-6">

  <div class="card dashboard-grid-card">
    <div class="card-header">
      <div>
        <div class="card-title">Distribusi Barang per Zona Gudang</div>
        <div class="fs-sm text-muted mt-1">Proporsi jumlah barang fisik tersimpan</div>
      </div>
    </div>
    <div class="card-body d-flex flex-col gap-4">
      <div class="d-flex align-center gap-6">
        <div class="pie-chart-wrap">
          <canvas id="chart-zone" width="160" height="160"></canvas>
        </div>
        <div id="zone-legend" class="flex-1 d-flex flex-col gap-2"></div>
      </div>
      <div class="analysis-divider">
        <div class="fs-xs fw-700 text-muted text-uppercase letter-spacing-lg mb-3">
          Analisis Ruang &amp; Kategori Gudang
        </div>

        <div class="grid-2 gap-4">

          <div>
            <div class="fs-md fw-700 text-secondary mb-2 text-uppercase letter-spacing-sm d-flex align-center gap-2">
              <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="icon-14 text-muted"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              Okupansi Slot Fisik
            </div>
            <div class="d-grid gap-2">

              <div class="stat-card-row">
                <div class="stat-card-icon blue">
                  <svg fill="none" stroke="var(--primary)" stroke-width="2.5" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
                </div>
                <div>
                  <div class="fs-xxs text-muted fw-600 text-uppercase">Slot Terisi</div>
                  <div id="slot-stats-occupied" class="fs-xl fw-800 text-primary mt-1">Memuat...</div>
                  <div id="slot-stats-occupied-pct" class="fs-xxs text-muted mt-1">Memuat %...</div>
                </div>
              </div>

              <div class="stat-card-row">
                <div class="stat-card-icon green">
                  <svg fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                  <div class="fs-xxs text-muted fw-600 text-uppercase">Slot Tersedia</div>
                  <div id="slot-stats-free" class="fs-xl fw-800 text-success mt-1">Memuat...</div>
                  <div id="slot-stats-free-pct" class="fs-xxs text-muted mt-1">Memuat %...</div>
                </div>
              </div>

            </div>
          </div>

          <div>
            <div class="fs-md fw-700 text-secondary mb-2 text-uppercase letter-spacing-sm d-flex align-center gap-2">
              <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="icon-14 text-muted"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Kategori Barang Utama
            </div>
            <div class="d-grid gap-2">

              <div class="stat-card-row">
                <div class="stat-card-icon purple">
                  <svg fill="none" stroke="#7c3aed" stroke-width="2.5" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="fs-xxs text-muted fw-600 text-uppercase">Volume Terbanyak</div>
                  <div id="cat-stats-highest" class="stat-card-value-ellipsis">Memuat...</div>
                  <div class="fs-xxs text-muted mt-1">Kategori dominan</div>
                </div>
              </div>

              <div class="stat-card-row">
                <div class="stat-card-icon orange">
                  <svg fill="none" stroke="#d97706" stroke-width="2.5" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="fs-xxs text-muted fw-600 text-uppercase">Volume Terendah</div>
                  <div id="cat-stats-lowest" class="stat-card-value-ellipsis">Memuat...</div>
                  <div class="fs-xxs text-muted mt-1">Kategori minoritas</div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="card dashboard-grid-card">
    <div class="card-header">
      <div class="card-title">Top Barang Fast-Moving</div>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="chart-fast"></canvas>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="grid-2 mb-6">

  <div class="card">
    <div class="card-header flex-between">
      <div class="card-title">Utilisasi Slot Rak per Zona Gudang</div>
      <a href="index.php?page=zones" class="fs-sm text-primary fw-600">Lihat Peta</a>
    </div>
    <div class="card-body d-flex flex-col gap-4" id="zone-progress-list"></div>
  </div>

  <div class="card">
    <div class="card-header flex-between mb-0">
      <div class="card-title d-flex align-center gap-2">
        <span class="danger-pulse-dot"></span>
        Stok Perlu Perhatian (di Bawah Reorder Point)
      </div>
      <?php if (in_array($role, ['admin_gudang', 'divisi_pembelian'])): ?>
        <a href="index.php?page=restock" class="fs-sm text-primary fw-600">Ajukan Restock</a>
      <?php endif; ?>
    </div>
    <div class="card-body pt-3">
      <div id="low-stock-list"></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header flex-between mb-0">
    <div class="card-title">Log Transaksi Terbaru (Hari Ini & Kemarin)</div>
    <a href="index.php?page=reports" class="fs-sm text-primary fw-600">Lihat semua laporan</a>
  </div>
  <div class="card-body pt-3">
    <div id="recent-list"></div>
  </div>
</div>

<script>
const COLORS = ['#1e40af','#3b82f6','#16a34a','#d97706','#7c3aed','#dc2626','#0891b2'];
const SHOW_CHARTS = <?= $showCharts ? 'true' : 'false' ?>;

async function loadDashboard() {

  const kpi = await fetch(BASE_URL + '/api/dashboard.php?type=kpi').then(r=>r.json());
  animateKPI('kpi-total-stok', kpi.total_stok);
  animateKPI('kpi-tx', kpi.tx_hari_ini);
  animateKPI('kpi-alert', kpi.alert_count);
  document.getElementById('kpi-kapasitas').textContent = kpi.kapasitas_pct + '%';
  document.getElementById('kpi-kapasitas-sub').textContent = 'kapasitas gudang terpakai';
  if (kpi.alert_count === 0) {
    document.getElementById('kpi-alert-sub').textContent = 'semua stok aman';
    document.getElementById('kpi-alert-sub').className = 'kpi-change kpi-up text-sm';
  }

  const zone = await fetch(BASE_URL + '/api/dashboard.php?type=chart_zone').then(r=>r.json());

  if (zone && zone.pct && zone.pct.length > 0) {

    if (zone.loaded && zone.free) {
      const totalOccupied = zone.loaded.reduce((a, b) => a + b, 0);
      const totalFree = zone.free.reduce((a, b) => a + b, 0);
      const totalSlots = totalOccupied + totalFree;
      const occupiedPct = totalSlots > 0 ? ((totalOccupied / totalSlots) * 100).toFixed(1) : 0;
      const freePct = totalSlots > 0 ? ((totalFree / totalSlots) * 100).toFixed(1) : 0;

      const occEl = document.getElementById('slot-stats-occupied');
      const freeEl = document.getElementById('slot-stats-free');
      const occPctEl = document.getElementById('slot-stats-occupied-pct');
      const freePctEl = document.getElementById('slot-stats-free-pct');

      if (occEl) occEl.textContent = `${totalOccupied} Slot`;
      if (freeEl) freeEl.textContent = `${totalFree} Slot`;
      if (occPctEl) occPctEl.textContent = `${occupiedPct}% terpakai`;
      if (freePctEl) freePctEl.textContent = `${freePct}% kosong`;
    }

    const catHighestEl = document.getElementById('cat-stats-highest');
    const catLowestEl = document.getElementById('cat-stats-lowest');

    if (catHighestEl && zone.top_category_name !== undefined) {
      const highestText = `${zone.top_category_name} (${zone.top_category_qty.toLocaleString('id-ID')} unit)`;
      catHighestEl.textContent = highestText;
      catHighestEl.title = highestText;
    }
    if (catLowestEl && zone.bottom_category_name !== undefined) {
      const lowestText = `${zone.bottom_category_name} (${zone.bottom_category_qty.toLocaleString('id-ID')} unit)`;
      catLowestEl.textContent = lowestText;
      catLowestEl.title = lowestText;
    }
  }

  const progEl = document.getElementById('zone-progress-list');
  zone.labels.forEach((label, i) => {
    const color = zone.pct[i] >= 80 ? 'var(--danger)' : zone.pct[i] >= 60 ? 'var(--warning)' : '#16a34a';
    progEl.innerHTML += `
      <div>
        <div class="d-flex justify-between fs-md mb-2">
          <span class="fw-600 text-secondary">${label}</span>
          <span class="fw-700 text-main">${zone.pct[i]}%</span>
        </div>
        <div class="progress-track">
          <div class="progress-fill" data-width="${zone.pct[i]}" style="background:${color}"></div>
        </div>
        <div class="fs-xs text-muted mt-1">${zone.loaded[i]} slot terisi · ${zone.free[i]} slot kosong</div>
      </div>`;
  });

  setTimeout(() => initProgressBars(), 50);

  if (SHOW_CHARTS) {

    const flow = await fetch(BASE_URL + '/api/dashboard.php?type=chart_flow').then(r=>r.json());
    new Chart(document.getElementById('chart-flow'), {
      type: 'bar',
      data: {
        labels: flow.labels,
        datasets: [
          {
            label: 'Masuk',
            data: flow.inbound,
            backgroundColor: '#1e40af',
            borderRadius: 4,
          },
          {
            label: 'Keluar',
            data: flow.outbound,
            backgroundColor: '#dc2626',
            borderRadius: 4,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
          y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' }, beginAtZero: true }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
      }
    });

    const trend = await fetch(BASE_URL + '/api/dashboard.php?type=chart_trend').then(r=>r.json());
    new Chart(document.getElementById('chart-trend'), {
      type: 'line',
      data: {
        labels: trend.labels,
        datasets: [{
          label: 'Total Unit Stok',
          data: trend.values,
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,0.08)',
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#16a34a',
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
          y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' } }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
      }
    });

    new Chart(document.getElementById('chart-zone'), {
      type: 'pie',
      data: {
        labels: zone.labels,
        datasets: [{
          data: zone.qtys,
          backgroundColor: COLORS,
          borderWidth: 2,
          borderColor: '#fff',
          hoverOffset: 6,
        }]
      },
      options: {
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} unit (${zone.qty_pct[ctx.dataIndex]}%)` } }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
      }
    });

    const legendEl = document.getElementById('zone-legend');
    zone.labels.forEach((label, i) => {
      legendEl.innerHTML += `
        <div class="d-flex align-center justify-between fs-sm">
          <div class="d-flex align-center gap-2">
            <span class="legend-color-dot" style="background:${COLORS[i]}"></span>
            <span class="text-secondary">${label.split(' - ')[0]}</span>
          </div>
          <span class="fw-700 text-main">${zone.qtys[i].toLocaleString('id-ID')} unit</span>
        </div>`;
    });

    const fast = await fetch(BASE_URL + '/api/dashboard.php?type=chart_fast').then(r=>r.json());
    new Chart(document.getElementById('chart-fast'), {
      type: 'bar',
      data: {
        labels: fast.labels,
        datasets: [{
          data: fast.values,
          backgroundColor: fast.values.map((_,i) =>
            i === 0 ? '#1e40af' : i === 1 ? '#2563eb' : i === 2 ? '#3b82f6' : '#93c5fd'
          ),
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' }, beginAtZero: true },
          y: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#334155' } }
        },
        animation: { duration: 1000, easing: 'easeInOutQuart' }
      }
    });
  }

  const recent = await fetch(BASE_URL + '/api/dashboard.php?type=recent').then(r=>r.json());
  const recEl  = document.getElementById('recent-list');
  recent.forEach(tx => {
    const isIn    = tx.type === 'inbound';
    const isAdj   = tx.notes && tx.notes.includes('STOCK OPNAME ADJ');
    const timeStr = new Date(tx.created_at).toLocaleString('id-ID', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' });

    const iconBg = isAdj ? '#fffbeb' : (isIn ? '#eff6ff' : '#fef2f2');
    const strokeColor = isAdj ? 'var(--warning)' : (isIn ? '#1e40af' : '#dc2626');
    const pathMarkup = isAdj
      ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>'
      : (isIn
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>'
      );

    recEl.innerHTML += `
      <div class="tx-row">
        <div class="tx-icon-wrap" style="background:${iconBg}">
          <svg fill="none" stroke="${strokeColor}" viewBox="0 0 24 24" class="icon-16">
            ${pathMarkup}
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="fs-base fw-600 text-main text-ellipsis">${tx.item_name}</div>
          <div class="fs-xs text-muted text-ellipsis">
            ${tx.reference_no} · ${tx.rack_code ?? 'Slot Lepas'} · oleh ${tx.user_name}
            ${tx.po_number ? ' · PO: ' + tx.po_number : ''}
          </div>
          ${isAdj ? `<div class="fs-xs text-warning-dark text-italic mt-1">${tx.notes}</div>` : ''}
        </div>
        <div class="text-right flex-shrink-0">
          <div class="fs-base fw-700" style="color:${isAdj ? 'var(--warning-dark, #b45309)' : (isIn ? '#16a34a' : '#dc2626')}">
            ${isAdj ? (isIn ? '+' : '-') : (isIn ? '+' : '-')}${tx.quantity} ${tx.unit}
          </div>
          <div class="fs-xs text-muted">${timeStr}</div>
        </div>
      </div>`;
  });

  const low    = await fetch(BASE_URL + '/api/dashboard.php?type=low_stock').then(r=>r.json());
  const lowEl  = document.getElementById('low-stock-list');
  if (low.length === 0) {
    lowEl.innerHTML = `<div class="alert alert-success fs-base align-center gap-2">
      <svg fill="none" stroke="var(--success)" viewBox="0 0 24 24" class="icon-20 text-success"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span>Semua stok dalam kondisi aman</span>
    </div>`;
  } else {
    low.forEach(item => {
      const isEmpty = item.stock_status === 'empty';
      lowEl.innerHTML += `
        <div class="tx-row pb-2 pt-2">
          <div class="status-dot ${isEmpty ? 'bg-danger' : 'bg-warning'}"></div>
          <div class="flex-1 min-w-0">
            <div class="fs-base fw-600 text-main text-ellipsis">${item.item_name}</div>
            <div class="fs-xs text-muted">${item.category_name} · Min: ${item.min_stock} ${item.unit}</div>
          </div>
          <div class="text-right flex-shrink-0">
            <span class="badge ${isEmpty ? 'badge-danger' : 'badge-warning'}">${item.total_stock} ${item.unit}</span>
          </div>
        </div>`;
    });
  }
}

function animateKPI(id, target) {
  const el = document.getElementById(id);
  if (!el) return;
  let start = null;
  const step = (ts) => {
    if (!start) start = ts;
    const p = Math.min((ts - start) / 1200, 1);
    const ease = 1 - Math.pow(1 - p, 3);
    el.textContent = Math.round(target * ease).toLocaleString('id-ID');
    if (p < 1) requestAnimationFrame(step);
  };
  el.textContent = '0';
  requestAnimationFrame(step);
}

loadDashboard();
</script>
