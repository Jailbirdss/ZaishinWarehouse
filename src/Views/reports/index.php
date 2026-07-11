<?php

$exportData = [];
foreach ($transactions as $tx) {
    $exportData[] = [
        'ref'       => $tx['reference_no'],
        'po'        => $tx['po_number'] ?? '',
        'item'      => $tx['item_name'],
        'sku'       => $tx['sku'],
        'category'  => $tx['category_name'],
        'type'      => $tx['type'] === 'inbound' ? 'Barang Masuk' : 'Barang Keluar',
        'qty'       => ($tx['type'] === 'inbound' ? '+' : '-') . $tx['quantity'],
        'unit'      => $tx['unit'],
        'condition' => $tx['condition'] ?? '',
        'location'  => ($tx['rack_code'] ?? '-') . ' · ' . ($tx['zone_name'] ?? '-'),
        'officer'   => $tx['user_name'],
        'date'      => date('d M Y H:i', strtotime($tx['created_at'])),
    ];
}
?>

<div class="modal-overlay print-preview-modal-overlay" id="print-preview-modal">
  <div class="w-full h-full d-flex flex-column bg-bg">

    <div class="print-preview-header">
      <div class="d-flex align-center gap-10 flex-1">
        <div class="print-preview-icon-container">
          <svg fill="none" viewBox="0 0 24 24" class="icon-18-primary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        </div>
        <div>
          <div class="fs-14 fw-700 text-main">Print Preview — Laporan Transaksi</div>
          <div class="fs-11 text-muted" id="preview-meta-label">Memuat data...</div>
        </div>
      </div>
      <div class="d-flex gap-8 align-center">
        <button class="btn btn-secondary btn-sm d-flex align-center gap-6" onclick="closePrintPreview()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          Tutup
        </button>
        <button class="btn btn-primary btn-sm d-flex align-center gap-6" onclick="executePrint()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Cetak / PDF
        </button>
      </div>
    </div>

    <div class="print-preview-body">
      <div id="print-canvas" class="print-preview-canvas">

        <div class="print-preview-canvas-header">
          <div class="d-flex align-center gap-14">
            <div class="print-preview-canvas-logo">
              <svg fill="none" viewBox="0 0 24 24" class="icon-26-white"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4v13H3V7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21V12h6v9"/></svg>
            </div>
            <div>
              <div class="text-white fs-20 fw-800 letter-spacing-neg-02">Zaishin Warehouse</div>
              <div class="text-white-70 fs-12 fw-500 mt-2">Sistem Manajemen Gudang Terintegrasi</div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-white fs-16 fw-700">Laporan Transaksi</div>
            <div class="text-white-70 fs-11 mt-4" id="preview-date-range-header"></div>
            <div class="text-white-55 fs-10 mt-2" id="preview-generated-at"></div>
          </div>
        </div>

        <div class="grid-3-col border-bottom-solid-e2" id="preview-kpi-strip">

        </div>

        <div class="p-24-36-8">
          <div class="fs-12 fw-700 text-muted text-uppercase letter-spacing-06 mb-12">Rincian Transaksi</div>
          <table id="preview-table" class="w-full border-collapse fs-12">
            <thead>
              <tr class="bg-f8fafc-border-bottom-2-e2">
                <th class="p-10-12 text-left fw-700 text-main fs-11 text-uppercase letter-spacing-04">No. Ref</th>
                <th class="p-10-12 text-left fw-700 text-main fs-11 text-uppercase letter-spacing-04">Barang</th>
                <th class="p-10-12 text-left fw-700 text-main fs-11 text-uppercase letter-spacing-04">Kategori</th>
                <th class="p-10-12 text-center fw-700 text-main fs-11 text-uppercase letter-spacing-04">Tipe</th>
                <th class="p-10-12 text-right fw-700 text-main fs-11 text-uppercase letter-spacing-04">Jumlah</th>
                <th class="p-10-12 text-left fw-700 text-main fs-11 text-uppercase letter-spacing-04">Lokasi</th>
                <th class="p-10-12 text-left fw-700 text-main fs-11 text-uppercase letter-spacing-04">Petugas</th>
                <th class="p-10-12 text-right fw-700 text-main fs-11 text-uppercase letter-spacing-04">Tanggal</th>
              </tr>
            </thead>
            <tbody id="preview-tbody">

            </tbody>
          </table>
          <div id="preview-empty" class="d-none p-32 text-center text-muted fs-13">Tidak ada transaksi dalam rentang tanggal ini.</div>
        </div>

        <div class="print-preview-footer">
          <div class="fs-10 text-light">Dicetak oleh: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></strong> &mdash; <?= date('d M Y, H:i') ?> WIB</div>
          <div class="fs-10 text-light">Zaishin Warehouse Management System &copy; <?= date('Y') ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="kpi-grid mb-6 <?= $_SESSION['user_role'] === 'divisi_pembelian' ? 'kpi-grid-2' : 'kpi-grid-3' ?>">
  <div class="kpi-card blue">
    <div class="kpi-icon bg-primary-soft">
      <svg fill="none" stroke="#1e40af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <div class="kpi-value"><?= count($transactions) ?></div>
    <div class="kpi-label"><?= $_SESSION['user_role'] === 'divisi_pembelian' ? 'Total Penerimaan' : 'Total Transaksi' ?></div>
  </div>
  <div class="kpi-card green">
    <div class="kpi-icon bg-success-soft">
      <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
    </div>
    <div class="kpi-value"><?= number_format($totIn) ?></div>
    <div class="kpi-label">Total Barang Masuk</div>
  </div>
  <?php if ($_SESSION['user_role'] !== 'divisi_pembelian'): ?>
  <div class="kpi-card danger">
    <div class="kpi-icon bg-danger-soft">
      <svg fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    </div>
    <div class="kpi-value"><?= number_format($totOut) ?></div>
    <div class="kpi-label">Total Barang Keluar</div>
  </div>
  <?php endif; ?>
</div>
<div class="card mb-6 no-print">
  <div class="card-body p-16-20">
    <form method="GET" action="index.php" id="report-filter-form" class="d-flex align-end flex-wrap gap-12">
      <input type="hidden" name="page" value="reports"/>
      <div>
        <label class="form-label mb-4">Dari Tanggal</label>
        <input type="date" name="from" class="form-control w-160px" value="<?= htmlspecialchars($from) ?>"/>
      </div>
      <div>
        <label class="form-label mb-4">Sampai Tanggal</label>
        <input type="date" name="to" class="form-control w-160px" value="<?= htmlspecialchars($to) ?>"/>
      </div>
      <?php if ($_SESSION['user_role'] !== 'divisi_pembelian'): ?>
      <div>
        <label class="form-label mb-4">Tipe Transaksi</label>
        <select name="type" class="form-control w-160px">
          <option value="all" <?= $type==='all'?'selected':'' ?>>Semua</option>
          <option value="inbound" <?= $type==='inbound'?'selected':'' ?>>Barang Masuk</option>
          <option value="outbound" <?= $type==='outbound'?'selected':'' ?>>Barang Keluar</option>
        </select>
      </div>
      <?php else: ?>
        <input type="hidden" name="type" value="inbound"/>
      <?php endif; ?>
      <button type="submit" class="btn btn-primary d-flex align-center gap-6">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
        Terapkan Filter
      </button>

      <div class="position-relative ml-auto" id="export-dropdown-wrapper">
        <button type="button" class="btn btn-secondary d-flex align-center gap-6 pr-10" id="export-toggle-btn"
          onclick="toggleExportDropdown()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          Export &amp; Cetak
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12 mt-1 transition-transform" id="export-chevron"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div id="export-dropdown-menu" class="export-dropdown-menu">
          <button type="button" onclick="openPrintPreview()" class="export-menu-item">
            <div class="export-menu-icon-wrap blue">
              <svg fill="none" stroke="#1e40af" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div>
              <div class="fs-13 fw-600 text-main">Print Preview</div>
              <div class="fs-11 text-muted">Pratinjau sebelum cetak</div>
            </div>
          </button>
          <button type="button" onclick="exportToPDF()" class="export-menu-item">
            <div class="export-menu-icon-wrap red">
              <svg fill="none" stroke="#dc2626" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <div class="fs-13 fw-600 text-main">Export PDF</div>
              <div class="fs-11 text-muted">Cetak sebagai PDF</div>
            </div>
          </button>
          <div class="dropdown-divider"></div>
          <button type="button" onclick="exportToCSV()" class="export-menu-item">
            <div class="export-menu-icon-wrap green">
              <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
              <div class="fs-13 fw-600 text-main">Export CSV</div>
              <div class="fs-11 text-muted">Unduh sebagai .csv</div>
            </div>
          </button>
          <button type="button" onclick="exportToExcel()" class="export-menu-item">
            <div class="export-menu-icon-wrap green">
              <svg fill="none" stroke="#15803d" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <div class="fs-13 fw-600 text-main">Export Excel</div>
              <div class="fs-11 text-muted">Unduh sebagai .xlsx</div>
            </div>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Referensi</th><th>Barang</th><th>Kategori</th>
          <th>Tipe</th><th>Jumlah</th><th>Lokasi</th><th>Petugas</th><th>Tanggal &amp; Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($transactions)): ?>
        <tr><td colspan="8" class="text-center p-40 text-muted">Tidak ada transaksi dalam rentang tanggal ini</td></tr>
        <?php else: foreach ($transactions as $tx): ?>
        <tr>
          <td>
            <span class="font-monospace fs-12 bg-f1f5f9 p-2-7 border-radius-4"><?= htmlspecialchars($tx['reference_no']) ?></span>
            <?php if (!empty($tx['po_number'])): ?>
              <div class="fs-11 text-secondary mt-4 fw-600">PO: <?= htmlspecialchars($tx['po_number']) ?></div>
            <?php endif; ?>
          </td>
          <td class="fw-500 text-main">
            <?= htmlspecialchars($tx['item_name']) ?>
            <div class="fs-11 text-muted"><?= htmlspecialchars($tx['sku']) ?></div>
          </td>
          <td><span class="badge badge-primary"><?= htmlspecialchars($tx['category_name']) ?></span></td>
          <td>
            <?= $tx['type'] === 'inbound'
              ? '<span class="badge badge-success">Masuk</span>'
              : '<span class="badge badge-danger">Keluar</span>' ?>
            <?php if ($tx['type'] === 'inbound' && !empty($tx['condition'])): ?>
              <?php
                $cMap = [
                  'baik' => ['badge-cond-baik', 'Baik'],
                  'sebagian_rusak' => ['badge-cond-sebagian', 'Sebagian Cacat'],
                  'rusak' => ['badge-cond-rusak', 'Rusak']
                ];
                $c = $cMap[$tx['condition']] ?? ['badge-cond-baik', 'Baik'];
              ?>
              <div class="badge-condition <?= $c[0] ?> mt-4"><?= $c[1] ?></div>
            <?php endif; ?>
          </td>
          <td class="fw-700 <?= $tx['type']==='inbound' ? 'text-success' : 'text-danger' ?>">
            <?= $tx['type']==='inbound' ? '+' : '-' ?><?= number_format($tx['quantity']) ?> <?= htmlspecialchars($tx['unit']) ?>
          </td>
          <td class="fs-12 text-muted"><?= htmlspecialchars($tx['rack_code'] ?? '-') ?> · <?= htmlspecialchars($tx['zone_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($tx['user_name']) ?></td>
          <td class="fs-12 text-muted"><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script>

const REPORT_DATA = <?= json_encode($exportData, JSON_UNESCAPED_UNICODE) ?>;
const REPORT_FROM = '<?= htmlspecialchars($from) ?>';
const REPORT_TO   = '<?= htmlspecialchars($to) ?>';
const REPORT_TYPE = '<?= htmlspecialchars($type) ?>';
const REPORT_TOTAL_IN  = <?= intval($totIn) ?>;
const REPORT_TOTAL_OUT = <?= intval($totOut) ?>;

function toggleExportDropdown() {
  const menu    = document.getElementById('export-dropdown-menu');
  const chevron = document.getElementById('export-chevron');
  const isOpen  = getComputedStyle(menu).display !== 'none';
  menu.style.display = isOpen ? 'none' : 'block';
  chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}
document.addEventListener('click', (e) => {
  const wrapper = document.getElementById('export-dropdown-wrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    document.getElementById('export-dropdown-menu').style.display = 'none';
    document.getElementById('export-chevron').style.transform = 'rotate(0deg)';
  }
});

function openPrintPreview() {
  document.getElementById('export-dropdown-menu').style.display = 'none';

  const dateLabel = `${REPORT_FROM} s/d ${REPORT_TO}` + (REPORT_TYPE !== 'all' ? ` — ${REPORT_TYPE === 'inbound' ? 'Barang Masuk' : 'Barang Keluar'}` : '');
  const now = new Date().toLocaleString('id-ID', {day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'});

  document.getElementById('preview-meta-label').textContent  = `${REPORT_DATA.length} transaksi · ${dateLabel}`;
  document.getElementById('preview-date-range-header').textContent = dateLabel;
  document.getElementById('preview-generated-at').textContent = `Digenerate: ${now}`;

  const typeLabel = REPORT_TYPE === 'inbound' ? 'Barang Masuk' : REPORT_TYPE === 'outbound' ? 'Barang Keluar' : 'Semua Tipe';
  const kpiStrip  = document.getElementById('preview-kpi-strip');
  kpiStrip.innerHTML = `
    <div class="print-preview-kpi-box border-right">
      <div class="fs-10 fw-700 text-light text-uppercase letter-spacing-05">Total Transaksi</div>
      <div class="fs-28 fw-800 text-main mt-4">${REPORT_DATA.length}</div>
      <div class="fs-11 text-muted mt-2">${typeLabel}</div>
    </div>
    <div class="print-preview-kpi-box border-right">
      <div class="fs-10 fw-700 text-light text-uppercase letter-spacing-05">Total Masuk</div>
      <div class="fs-28 fw-800 text-success mt-4">${REPORT_TOTAL_IN.toLocaleString('id-ID')}</div>
      <div class="fs-11 text-muted mt-2">unit barang</div>
    </div>
    <div class="print-preview-kpi-box">
      <div class="fs-10 fw-700 text-light text-uppercase letter-spacing-05">Total Keluar</div>
      <div class="fs-28 fw-800 text-danger mt-4">${REPORT_TOTAL_OUT.toLocaleString('id-ID')}</div>
      <div class="fs-11 text-muted mt-2">unit barang</div>
    </div>
  `;

  const tbody = document.getElementById('preview-tbody');
  const empty = document.getElementById('preview-empty');
  tbody.innerHTML = '';
  if (REPORT_DATA.length === 0) {
    empty.classList.add('d-block');
    empty.classList.remove('d-none');
  } else {
    empty.classList.add('d-none');
    empty.classList.remove('d-block');
    REPORT_DATA.forEach((row, i) => {
      const isInbound = row.type === 'Barang Masuk';
      const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
      tbody.innerHTML += `
        <tr style="background:${bg};" class="border-bottom-f1">
          <td class="p-9-12 font-monospace fs-11 text-secondary-dark">${row.ref}</td>
          <td class="p-9-12 fw-600 text-main">${row.item}<br><span class="fw-500 text-light fs-10">${row.sku}</span></td>
          <td class="p-9-12 text-secondary-dark">${row.category}</td>
          <td class="p-9-12 text-center">
            <span class="p-2-8 rounded-full fs-10 fw-700 ${isInbound?'badge-inbound-preview':'badge-outbound-preview'}">${row.type}</span>
          </td>
          <td class="p-9-12 text-right fw-700 ${isInbound?'text-success':'text-danger'}">${row.qty} ${row.unit}</td>
          <td class="p-9-12 text-muted fs-11">${row.location}</td>
          <td class="p-9-12 text-secondary-dark">${row.officer}</td>
          <td class="p-9-12 text-right fs-11 text-muted">${row.date}</td>
        </tr>
      `;
    });
  }

  document.getElementById('print-preview-modal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closePrintPreview() {
  document.getElementById('print-preview-modal').classList.remove('open');
  document.body.style.overflow = '';
}

function executePrint() {
  window.print();
}

function exportToPDF() {
  document.getElementById('export-dropdown-menu').style.display = 'none';
  window.print();
}

function exportToCSV() {
  document.getElementById('export-dropdown-menu').style.display = 'none';
  if (REPORT_DATA.length === 0) {
    showToast('Tidak ada data untuk diekspor.', 'warning');
    return;
  }
  const headers = ['No. Referensi','No. PO','Nama Barang','SKU','Kategori','Tipe','Jumlah','Satuan','Kondisi','Lokasi','Petugas','Tanggal'];
  const rows = REPORT_DATA.map(r => [
    r.ref, r.po, r.item, r.sku, r.category, r.type, r.qty.replace(/[+\-]/g,''), r.unit, r.condition, r.location, r.officer, r.date
  ]);
  const csvContent = [headers, ...rows]
    .map(row => row.map(cell => `"${String(cell).replace(/"/g,'""')}"`).join(','))
    .join('\r\n');
  const bom = '\uFEFF';
  const blob = new Blob([bom + csvContent], { type: 'text/csv;charset=utf-8;' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `laporan-transaksi_${REPORT_FROM}_sd_${REPORT_TO}.csv`;
  a.click();
  URL.revokeObjectURL(url);
  showToast('File CSV berhasil diunduh.', 'success');
}

function exportToExcel() {
  document.getElementById('export-dropdown-menu').style.display = 'none';
  if (REPORT_DATA.length === 0) {
    showToast('Tidak ada data untuk diekspor.', 'warning');
    return;
  }

  if (typeof XLSX === 'undefined') {
    const script  = document.createElement('script');
    script.src    = 'https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js';
    script.onload = () => doExcelExport();
    script.onerror = () => showToast('Gagal memuat library Excel. Periksa koneksi internet.', 'error');
    document.head.appendChild(script);
    showToast('Menyiapkan library Excel...', 'info', 2000);
  } else {
    doExcelExport();
  }
}

function doExcelExport() {
  const headers = ['No. Referensi','No. PO','Nama Barang','SKU','Kategori','Tipe','Jumlah','Satuan','Kondisi','Lokasi','Petugas','Tanggal'];
  const rows = REPORT_DATA.map(r => [
    r.ref, r.po, r.item, r.sku, r.category, r.type,
    parseFloat(r.qty.replace(/[^0-9.]/g,'')), r.unit, r.condition, r.location, r.officer, r.date
  ]);

  const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);

  ws['!cols'] = [16,14,24,14,14,12,10,10,14,18,16,18].map(w => ({ wch: w }));

  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Transaksi');

  const filename = `laporan-transaksi_${REPORT_FROM}_sd_${REPORT_TO}.xlsx`;
  XLSX.writeFile(wb, filename);
  showToast('File Excel (.xlsx) berhasil diunduh.', 'success');
}
</script>
