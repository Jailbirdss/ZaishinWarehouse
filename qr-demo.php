<?php
// ============================================================
// QR Demo Tool — Standalone page (no login required)
// Connects to DB to load real item data
// ============================================================
require_once __DIR__ . '/config/database.php';

// Fetch all items with QR codes from database
$items = [];
$slots = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT i.id, i.name, i.sku, i.unit, i.qr_code, c.name AS cat_name,
                        COALESCE(SUM(s.quantity),0) AS total_stock
                        FROM items i
                        JOIN categories c ON i.category_id = c.id
                        LEFT JOIN stock s ON i.id = s.item_id
                        GROUP BY i.id
                        ORDER BY i.name");
    $items = $stmt->fetchAll();

    // Fetch all rack slots
    $slotsStmt = $db->query("SELECT rs.id, rs.slot_number, r.rack_code, z.name AS zone_name
                            FROM rack_slots rs
                            JOIN racks r ON rs.rack_id = r.id
                            JOIN zones z ON r.zone_id = z.id
                            ORDER BY z.name, r.rack_code, rs.slot_number");
    $slots = $slotsStmt->fetchAll();
} catch (Exception $e) {
    // DB not available — demo still works without real data
}
$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);
$slotsJson = json_encode($slots, JSON_UNESCAPED_UNICODE);
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>WMS QR Generator — Demo Tool</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<!-- QR Generation: qrcodejs (reliable, canvas-based) -->
<script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>
<!-- QR Scanning: html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  *::-webkit-scrollbar { display: none; }
  * { -ms-overflow-style: none; scrollbar-width: none; }

  :root {
    --primary:       #1e40af;
    --primary-light: #3b82f6;
    --primary-soft:  #eff6ff;
    --primary-dark:  #1e3a8a;
    --surface:       #ffffff;
    --bg:            #f0f4ff;
    --border:        #e2e8f0;
    --text-main:     #0f172a;
    --text-muted:    #64748b;
    --success:       #16a34a;
    --success-soft:  #f0fdf4;
    --danger:        #dc2626;
    --danger-soft:   #fef2f2;
    --warning:       #d97706;
    --radius:        12px;
    --radius-lg:     16px;
    --shadow-sm:     0 1px 3px rgba(0,0,0,0.08);
    --shadow:        0 4px 16px rgba(0,0,0,0.10);
    --shadow-lg:     0 8px 32px rgba(0,0,0,0.14);
    --transition:    all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); min-height: 100vh; -webkit-font-smoothing: antialiased; }

  /* === HEADER === */
  .header { background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); padding: 0 40px; height: 68px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 12px rgba(30,64,175,0.3); position: sticky; top: 0; z-index: 100; }
  .header-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
  .header-icon { width: 38px; height: 38px; background: rgba(255,255,255,0.18); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(6px); }
  .header-icon svg { width: 20px; height: 20px; color: #fff; }
  .header-title { color: #fff; font-size: 17px; font-weight: 800; }
  .header-sub   { color: rgba(255,255,255,0.55); font-size: 11px; }
  .header-badge { margin-left: auto; background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.2); letter-spacing: 0.05em; text-transform: uppercase; }
  .header-back  { color: rgba(255,255,255,0.8); font-size: 13px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.1); transition: var(--transition); }
  .header-back:hover { background: rgba(255,255,255,0.2); color: #fff; }
  .header-back svg { width: 14px; height: 14px; }

  /* === LAYOUT === */
  .page { max-width: 1140px; margin: 0 auto; padding: 36px 28px; }
  .page-title { font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
  .page-sub   { font-size: 13.5px; color: var(--text-muted); margin-bottom: 32px; }
  .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }

  /* === CARD === */
  .card { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border); overflow: hidden; }
  .card-header { padding: 20px 24px 14px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
  .card-header-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .card-header-icon svg { width: 18px; height: 18px; }
  .card-title { font-size: 15px; font-weight: 800; color: var(--text-main); }
  .card-sub   { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
  .card-body  { padding: 20px 24px; }

  /* === FORM === */
  .form-group { margin-bottom: 16px; }
  .form-label { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
  .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: var(--radius); font-size: 13.5px; font-family: inherit; color: var(--text-main); background: var(--surface); transition: var(--transition); outline: none; }
  .form-control:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
  .form-control::placeholder { color: #b0bec5; }
  .form-control[readonly] { background: #f8fafc; cursor: default; }

  /* === SEARCH DROPDOWN === */
  .item-search-wrapper { position: relative; }
  .item-search-results {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: var(--surface); border: 1.5px solid var(--primary-light);
    border-radius: var(--radius); z-index: 50; max-height: 220px; overflow-y: auto;
    box-shadow: var(--shadow-lg); display: none;
  }
  .item-search-results.open { display: block; }
  .item-option { padding: 10px 14px; cursor: pointer; font-size: 13px; transition: var(--transition); display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f1f5f9; }
  .item-option:last-child { border-bottom: none; }
  .item-option:hover { background: var(--primary-soft); }
  .item-option-name { font-weight: 700; color: var(--text-main); }
  .item-option-sku  { font-family: monospace; font-size: 11px; color: var(--text-muted); }
  .item-option-dot  { width: 8px; height: 8px; border-radius: 50%; background: var(--primary-light); flex-shrink: 0; }
  .item-option-stock{ margin-left: auto; font-size: 11px; font-weight: 700; color: var(--success); white-space: nowrap; }

  /* === BUTTONS === */
  .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: var(--radius); font-size: 13.5px; font-weight: 700; font-family: inherit; border: none; cursor: pointer; transition: var(--transition); }
  .btn svg { width: 16px; height: 16px; }
  .btn-primary  { background: var(--primary); color: #fff; }
  .btn-primary:hover  { background: #1d3fa3; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(30,64,175,0.3); }
  .btn-success  { background: var(--success); color: #fff; }
  .btn-success:hover  { background: #15803d; transform: translateY(-1px); }
  .btn-danger   { background: var(--danger); color: #fff; }
  .btn-danger:hover   { background: #b91c1c; transform: translateY(-1px); }
  .btn-secondary{ background: var(--bg); color: var(--text-muted); border: 1.5px solid var(--border); }
  .btn-secondary:hover{ background: #e8eef8; color: var(--text-main); }
  .btn:disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
  .btn-full { width: 100%; justify-content: center; padding: 12px; font-size: 14px; }
  .btn-sm   { padding: 6px 14px; font-size: 12px; }

  /* === QR PREVIEW === */
  .qr-preview-wrapper {
    display: flex; flex-direction: column; align-items: center;
    padding: 28px 20px; background: linear-gradient(145deg, #f8fafc, var(--primary-soft));
    border: 2px dashed var(--border); border-radius: var(--radius-lg);
    gap: 16px; min-height: 260px; justify-content: center; transition: var(--transition);
  }
  .qr-preview-wrapper.has-qr { border-color: rgba(59,130,246,0.35); border-style: solid; background: #f8faff; }
  #qr-canvas-container { display: none; flex-direction: column; align-items: center; gap: 14px; }
  #qr-canvas-container img, #qr-canvas-container canvas { border-radius: 12px; box-shadow: 0 8px 24px rgba(30,64,175,0.15); }
  #qrcode-render { display: flex; justify-content: center; }
  .qr-preview-placeholder { display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--text-muted); }
  .qr-preview-placeholder svg { width: 56px; height: 56px; color: #cbd5e1; }
  .qr-preview-placeholder span { font-size: 13px; font-weight: 500; }
  .qr-code-text { font-family: monospace; font-size: 12px; font-weight: 700; color: var(--primary); background: var(--primary-soft); padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(59,130,246,0.2); word-break: break-all; text-align: center; max-width: 280px; }

  /* === HISTORY === */
  .qr-history-list { display: flex; flex-direction: column; gap: 8px; }
  .qr-history-item { display: flex; align-items: center; gap: 12px; padding: 11px 14px; background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer; transition: var(--transition); }
  .qr-history-item:hover { background: var(--primary-soft); border-color: rgba(59,130,246,0.25); transform: translateY(-1px); box-shadow: var(--shadow-sm); }
  .qr-history-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--primary-light); flex-shrink: 0; }
  .qr-history-name { font-size: 13px; font-weight: 700; color: var(--text-main); }
  .qr-history-code { font-family: monospace; font-size: 11px; color: var(--text-muted); }
  .qr-history-actions { margin-left: auto; display: flex; gap: 6px; }

  /* === SCANNER === */
  .qr-camera-zone { border: 2px dashed rgba(59,130,246,0.4); border-radius: var(--radius-lg); padding: 32px 20px; text-align: center; background: var(--primary-soft); cursor: pointer; transition: var(--transition); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; }
  .qr-camera-zone:hover { background: #e0ebff; border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
  .qr-camera-zone svg { width: 44px; height: 44px; color: var(--primary-light); transition: var(--transition); }
  .qr-camera-zone:hover svg { color: var(--primary); transform: scale(1.1); }
  .zone-text { font-size: 13.5px; font-weight: 700; color: #334155; }
  .zone-sub  { font-size: 11.5px; color: var(--text-muted); }

  /* === SCAN RESULT === */
  .scan-result { padding: 16px; border-radius: var(--radius); display: flex; align-items: flex-start; gap: 14px; font-size: 13.5px; animation: fadeInUp 0.3s ease; }
  .scan-result.success { background: var(--success-soft); border: 1.5px solid #bbf7d0; color: #14532d; }
  .scan-result.error   { background: var(--danger-soft);  border: 1.5px solid #fecaca; color: #7f1d1d; }
  .scan-result svg { width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px; }
  .scan-result-name { font-size: 15px; font-weight: 800; margin-bottom: 4px; }
  .scan-result-meta { font-size: 12px; opacity: 0.75; }
  .scan-result-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 8px; }
  .scan-result-grid-item { background: rgba(255,255,255,0.5); border-radius: 6px; padding: 6px 10px; }
  .scan-result-grid-label { font-size: 10px; font-weight: 700; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.04em; }
  .scan-result-grid-value { font-size: 13px; font-weight: 700; margin-top: 2px; }

  /* === DRAG DROP === */
  .drag-drop-zone { border: 2px dashed var(--border); border-radius: var(--radius); padding: 24px; text-align: center; cursor: pointer; transition: var(--transition); background: #fafbfc; }
  .drag-drop-zone:hover, .drag-drop-zone.dragover { border-color: var(--primary-light); background: var(--primary-soft); }

  /* === MODE TABS === */
  .mode-tabs { display: flex; gap: 0; background: #f1f5f9; border-radius: 10px; padding: 4px; margin-bottom: 18px; }
  .mode-tab { flex: 1; text-align: center; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: var(--transition); color: var(--text-muted); border: none; background: transparent; font-family: inherit; }
  .mode-tab.active { background: var(--surface); color: var(--primary); box-shadow: 0 1px 4px rgba(0,0,0,0.1); }

  /* === DIVIDER === */
  .divider { display: flex; align-items: center; gap: 12px; color: var(--text-muted); font-size: 12px; font-weight: 600; margin: 16px 0; }
  .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  /* === TOAST === */
  #toast-container { position: fixed; bottom: 28px; right: 28px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
  .toast { padding: 12px 18px; border-radius: 10px; color: #fff; font-size: 13.5px; font-weight: 600; box-shadow: 0 4px 16px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; animation: toastIn 0.3s ease; max-width: 340px; }
  .toast svg { width: 18px; height: 18px; flex-shrink: 0; }
  .toast.success { background: var(--success); }
  .toast.error   { background: var(--danger); }
  .toast.info    { background: var(--primary); }

  /* === ANIMATIONS === */
  @keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
  @keyframes toastIn  { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
  @keyframes toastOut { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(20px)} }
  .animate-in { animation: fadeInUp 0.35s ease; }
  .section-gap { margin-top: 24px; }

  /* === RESPONSIVE === */
  @media (max-width: 768px) { .two-col{grid-template-columns:1fr} .page{padding:20px 14px} .header{padding:0 20px} }
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="header-brand">
    <div class="header-icon">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4v13H3V7z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21V12h6v9"/>
      </svg>
    </div>
    <div>
      <div class="header-title">Zaishin Warehouse</div>
      <div class="header-sub">QR Code Generator &amp; Demo Tool</div>
    </div>
  </div>
  <a href="index.php" class="header-back">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Kembali ke WMS
  </a>
  <div class="header-badge">Demo Tool</div>
</header>

<div class="page">
  <div class="page-title">QR Code Generator</div>
  <div class="page-sub">Generate dan scan QR Code untuk mendemonstrasikan fitur barcode tracking. Pilih barang dari database atau buat kode custom.</div>

  <div class="two-col">

    <!-- LEFT: GENERATOR -->
    <div>
      <div class="card">
        <div class="card-header">
          <div class="card-header-icon" style="background:#eff6ff;">
            <svg fill="none" stroke="#1e40af" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
          </div>
          <div>
            <div class="card-title">Generate QR Code</div>
            <div class="card-sub">Pilih barang dari database atau buat kode custom</div>
          </div>
        </div>
        <div class="card-body">

          <!-- Mode Tabs -->
          <div class="mode-tabs">
            <button class="mode-tab active" id="tab-db"     onclick="setMode('db')" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              Barang
            </button>
            <button class="mode-tab"         id="tab-rack"   onclick="setMode('rack')" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM4 21a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2z"/></svg>
              Rak Gudang
            </button>
            <button class="mode-tab"         id="tab-custom" onclick="setMode('custom')" style="display:inline-flex; align-items:center; justify-content:center; gap:6px;">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              Kode Custom
            </button>
          </div>

          <!-- RACK MODE -->
          <div id="mode-rack" style="display:none;">
            <div class="form-group">
              <label class="form-label">Pilih Lokasi Rak Gudang</label>
              <select id="rack-select" class="form-control" onchange="onRackSelectChange(this)">
                <option value="">-- Pilih Slot Rak --</option>
                <?php foreach ($slots as $slot): ?>
                <option value="<?= $slot['id'] ?>" data-code="SLOT-<?= htmlspecialchars($slot['rack_code']) ?>-S<?= $slot['slot_number'] ?>" data-name="Rak <?= htmlspecialchars($slot['rack_code']) ?> · Slot <?= $slot['slot_number'] ?> (<?= htmlspecialchars($slot['zone_name']) ?>)">
                  <?= htmlspecialchars($slot['zone_name']) ?> · <?= htmlspecialchars($slot['rack_code']) ?> · Slot <?= $slot['slot_number'] ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div id="rack-details-panel" style="display:none;background:var(--primary-soft);border:1.5px solid rgba(59,130,246,0.18);border-radius:var(--radius);padding:16px;margin-bottom:16px;">
              <div style="font-size:11.5px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Detail Lokasi Rak
              </div>
              <div class="form-group" style="margin-bottom:10px;">
                <label class="form-label" style="font-size:10px;">Nama Lokasi</label>
                <input type="text" id="rack-name-display" class="form-control" style="font-size:12px;padding:6px 10px;" readonly />
              </div>
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-size:10px;">Teks QR yang di-encode</label>
                <input type="text" id="rack-qr-text" class="form-control" style="font-family:monospace;font-size:12px;padding:6px 10px;font-weight:700;color:var(--primary);" readonly />
              </div>
            </div>
          </div>

          <!-- DB MODE -->
          <div id="mode-db">
            <div class="form-group">
              <label class="form-label">Cari Barang dari Database</label>
              <div class="item-search-wrapper">
                <input type="text" id="item-search" class="form-control" placeholder="Ketik nama barang atau SKU..." autocomplete="off" oninput="filterItems(this.value)" onfocus="openDropdown()" onblur="closeDropdownDelayed()"/>
                <div class="item-search-results" id="item-dropdown"></div>
              </div>
            </div>

            <!-- Editable fields — pre-filled saat pilih barang, bisa diubah -->
            <div id="db-edit-fields" style="display:none;background:#f8faff;border:1.5px solid rgba(59,130,246,0.18);border-radius:var(--radius);padding:16px;margin-bottom:16px;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div style="font-size:11.5px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.04em;display:flex;align-items:center;gap:6px;">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                  Detail QR — bisa kamu ubah sebelum generate
                </div>
                <button class="btn btn-sm btn-secondary" onclick="clearSelected()" style="padding:4px 10px;font-size:11px;">Ganti Barang</button>
              </div>
              <div class="form-group" style="margin-bottom:12px;">
                <label class="form-label">Nama Barang</label>
                <input type="text" id="db-nama" class="form-control" placeholder="Nama barang"/>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px;">
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label">SKU</label>
                  <input type="text" id="db-sku" class="form-control" placeholder="SKU"/>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label">Satuan</label>
                  <input type="text" id="db-unit" class="form-control" placeholder="rim, pcs, ..."/>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label">Stok</label>
                  <input type="number" id="db-stock" class="form-control" placeholder="0" min="0"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:12px;">
                <label class="form-label" style="display:flex;align-items:center;justify-content:space-between;">
                  <span>Kode QR yang akan di-encode</span>
                  <button type="button" onclick="resetQrCode()" style="font-size:10px;font-weight:700;color:var(--primary);background:none;border:none;cursor:pointer;padding:0;">↺ Reset ke asli</button>
                </label>
                <input type="text" id="db-qrcode" class="form-control" placeholder="Kode QR" style="font-family:monospace;font-weight:700;color:var(--primary);"/>
                <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">💡 Kode QR ini yang akan di-encode ke gambar. Ubah jika perlu, lalu klik Generate.</div>
              </div>

              <!-- METADATA DEMO INBOUND -->
              <div style="background:var(--primary-soft); border: 1.5px dashed var(--primary-light); border-radius: 8px; padding: 12px; margin-top: 12px;">
                <div style="font-size: 11px; font-weight: 700; color: var(--primary-dark); margin-bottom: 6px; letter-spacing: 0.02em; display:flex; align-items:center; gap:6px;">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                  METADATA DEMO INBOUND (OPSIONAL)
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                  <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 9.5px; margin-bottom: 4px;">Nomor PO Demo</label>
                    <input type="text" id="demo-po" class="form-control" style="font-size: 12px; padding: 6px 10px; height: 32px;" placeholder="Contoh: PO-9988"/>
                  </div>
                  <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 9.5px; margin-bottom: 4px;">Qty Inbound Demo</label>
                    <input type="number" id="demo-qty" class="form-control" style="font-size: 12px; padding: 6px 10px; height: 32px;" placeholder="Contoh: 10" min="1"/>
                  </div>
                </div>
                <div style="font-size: 10.5px; color: var(--text-muted); margin-top: 6px; line-height: 1.3;">Jika diisi, data PO dan Qty ini akan menyusup ke QR Code agar terisi otomatis saat di-scan di halaman Inbound.</div>
              </div>
            </div>

            <?php if (empty($items)): ?>
            <div style="color:var(--warning);font-size:12.5px;background:#fff7ed;padding:12px;border-radius:8px;border:1px solid #fed7aa;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;color:var(--warning);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
              <span>Database tidak tersedia atau belum ada barang. Gunakan mode <strong>Kode Custom</strong>.</span>
            </div>
            <?php else: ?>
            <div id="db-item-count" style="color:var(--text-muted);font-size:12px;margin-bottom:16px;">
              <?= count($items) ?> barang ditemukan di database — pilih untuk mengisi form.
            </div>
            <?php endif; ?>
          </div>

          <!-- CUSTOM MODE -->
          <div id="mode-custom" style="display:none;">
            <div class="form-group">
              <label class="form-label">Teks / Kode QR</label>
              <input type="text" id="qr-custom-text" class="form-control" placeholder="Masukkan teks atau kode QR..."/>
            </div>
          </div>

          <!-- Ukuran + Warna (shared) -->
          <div class="form-group">
            <label class="form-label">Ukuran QR</label>
            <select class="form-control" id="qr-size" onchange="triggerGenerate()">
              <option value="160">160 × 160 (Kecil)</option>
              <option value="200" selected>200 × 200 (Sedang)</option>
              <option value="256">256 × 256 (Besar)</option>
              <option value="320">320 × 320 (Extra Besar)</option>
            </select>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;" class="form-group">
            <div>
              <label class="form-label">Warna QR</label>
              <div style="display:flex;align-items:center;gap:8px;">
                <input type="color" id="qr-dark" value="#0f172a" onchange="triggerGenerate()" style="width:40px;height:36px;border:1.5px solid var(--border);border-radius:8px;padding:2px;cursor:pointer;"/>
                <span style="font-size:12px;color:var(--text-muted);">Foreground</span>
              </div>
            </div>
            <div>
              <label class="form-label">Warna Latar</label>
              <div style="display:flex;align-items:center;gap:8px;">
                <input type="color" id="qr-light" value="#ffffff" onchange="triggerGenerate()" style="width:40px;height:36px;border:1.5px solid var(--border);border-radius:8px;padding:2px;cursor:pointer;"/>
                <span style="font-size:12px;color:var(--text-muted);">Background</span>
              </div>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:18px;">
            <label class="form-label">Preset Warna</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
              <button class="btn btn-sm btn-secondary" onclick="applyPreset('#0f172a','#ffffff')">Hitam</button>
              <button class="btn btn-sm btn-secondary" onclick="applyPreset('#1e40af','#eff6ff')">Biru WMS</button>
              <button class="btn btn-sm btn-secondary" onclick="applyPreset('#16a34a','#f0fdf4')">Hijau</button>
              <button class="btn btn-sm btn-secondary" onclick="applyPreset('#7c3aed','#f5f3ff')">Ungu</button>
              <button class="btn btn-sm btn-secondary" onclick="applyPreset('#dc2626','#fef2f2')">Merah</button>
            </div>
          </div>

          <div style="display:flex;gap:10px;margin-bottom:10px;">
            <button class="btn btn-primary" style="flex:1;justify-content:center;" onclick="triggerGenerate()">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              Generate
            </button>
            <button class="btn btn-success" id="btn-download" style="flex:1;justify-content:center;" onclick="downloadQR()" disabled>
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              Unduh PNG
            </button>
          </div>
          <button class="btn btn-secondary btn-full" id="btn-save" onclick="saveToHistory()" disabled>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Simpan ke Koleksi
          </button>
        </div>
      </div>

      <!-- QR PREVIEW -->
      <div class="card section-gap">
        <div class="card-header">
          <div class="card-header-icon" style="background:#f0fdf4;">
            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
          </div>
          <div><div class="card-title">Preview QR Code</div><div class="card-sub">Hasil generate akan muncul di sini</div></div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;gap:16px;">
          <div class="qr-preview-wrapper" id="qr-preview-wrapper">
            <div class="qr-preview-placeholder" id="qr-placeholder">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
              <span>Pilih barang atau isi kode, lalu klik Generate</span>
            </div>
            <div id="qr-canvas-container">
              <div id="qrcode-render"></div>
              <div class="qr-code-text" id="qr-code-display" style="display:none;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div style="display:flex;flex-direction:column;gap:24px;">

      <!-- QR SCANNER -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-icon" style="background:#fff7ed;">
            <svg fill="none" stroke="#d97706" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
          </div>
          <div><div class="card-title">Scan QR Code</div><div class="card-sub">Scan dengan kamera atau unggah gambar</div></div>
        </div>
        <div class="card-body">
          <!-- Camera Zone -->
          <div class="qr-camera-zone" id="cam-placeholder" onclick="toggleCamera()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            </svg>
            <div class="zone-text">Klik untuk Membuka Kamera</div>
            <div class="zone-sub">Arahkan ke QR Code untuk scan otomatis</div>
          </div>
          <div id="cam-reader" style="display:none; border:2px solid var(--primary); border-radius:var(--radius-lg); overflow:hidden; margin-top:-2px;"></div>
          <button id="btn-stop-cam" class="btn btn-danger btn-full" onclick="toggleCamera()" style="display:none;margin-top:10px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Matikan Kamera
          </button>

          <div class="divider">ATAU UNGGAH GAMBAR</div>

          <div class="drag-drop-zone" id="scan-drop-zone" onclick="document.getElementById('scan-file-input').click()">
            <input type="file" id="scan-file-input" accept="image/*" style="display:none;" onchange="scanFromFile(event)"/>
            <svg fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="width:36px;height:36px;margin-bottom:8px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <div style="font-size:13px;font-weight:600;color:#475569;">Seret gambar QR ke sini</div>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">atau klik untuk memilih file</div>
          </div>
          <!-- Hidden reader for file scanning -->
          <div id="scan-temp" style="display:none;position:absolute;"></div>

          <div id="scan-result" style="display:none;margin-top:16px;"></div>
        </div>
      </div>

      <!-- SAVED COLLECTION -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-icon" style="background:#f5f3ff;">
            <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
          </div>
          <div style="flex:1;"><div class="card-title">Koleksi QR Tersimpan</div><div class="card-sub">Klik untuk re-generate</div></div>
          <button class="btn btn-secondary btn-sm" onclick="clearHistory()">Hapus Semua</button>
        </div>
        <div class="card-body">
          <div class="qr-history-list" id="history-list">
            <div style="color:var(--text-muted);font-size:13px;text-align:center;padding:24px;">Belum ada QR yang disimpan</div>
          </div>
        </div>
      </div>

      <!-- GUIDE -->
      <div class="card">
        <div class="card-header">
          <div class="card-header-icon" style="background:#f0fdf4;">
            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div><div class="card-title">Cara Demo Fitur QR</div></div>
        </div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <div style="width:26px;height:26px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:800;color:#fff;">1</div>
              <div><div style="font-size:13px;font-weight:700;margin-bottom:2px;">Pilih barang dari database</div><div style="font-size:12px;color:var(--text-muted);">Cari barang yang sudah ada di sistem WMS. Kode QR-nya sudah ter-assign otomatis.</div></div>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <div style="width:26px;height:26px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:800;color:#fff;">2</div>
              <div><div style="font-size:13px;font-weight:700;margin-bottom:2px;">Generate &amp; Unduh PNG</div><div style="font-size:12px;color:var(--text-muted);">Klik Generate lalu Unduh PNG. File QR siap diprint atau digunakan untuk demo.</div></div>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <div style="width:26px;height:26px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:800;color:#fff;">3</div>
              <div><div style="font-size:13px;font-weight:700;margin-bottom:2px;">Scan di Inbound / Outbound WMS</div><div style="font-size:12px;color:var(--text-muted);">Buka halaman <a href="index.php?page=inbound" style="color:var(--primary);font-weight:700;text-decoration:underline;">Barang Masuk</a> atau <a href="index.php?page=outbound" style="color:var(--primary);font-weight:700;text-decoration:underline;">Barang Keluar</a>, unggah gambar QR tersebut — data barang otomatis terisi.</div></div>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <div style="width:26px;height:26px;background:var(--success);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:800;color:#fff;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;color:#fff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div><div style="font-size:13px;font-weight:700;color:var(--success);margin-bottom:2px;">Data barang langsung terisi!</div><div style="font-size:12px;color:var(--text-muted);">Nama, SKU, stok, dan zona otomatis muncul dari database.</div></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast-container"></div>

<script>
// ============================================================
// DATA FROM PHP / DATABASE
// ============================================================
const DB_ITEMS = <?= $itemsJson ?>;

// ============================================================
// STATE
// ============================================================
let currentMode = 'db';
let currentQRText = '';
let currentItemName = '';
let selectedItem = null;
let camOpen = false;
let html5QrCode = null;
let qrInstance = null;
let historyItems = JSON.parse(localStorage.getItem('wms_qr_history') || '[]');

// ============================================================
// MODE SWITCHING
// ============================================================
function setMode(mode) {
  currentMode = mode;
  document.getElementById('mode-db').style.display     = mode === 'db' ? 'block' : 'none';
  document.getElementById('mode-rack').style.display   = mode === 'rack' ? 'block' : 'none';
  document.getElementById('mode-custom').style.display = mode === 'custom' ? 'block' : 'none';
  
  document.getElementById('tab-db').className     = 'mode-tab' + (mode === 'db' ? ' active' : '');
  document.getElementById('tab-rack').className   = 'mode-tab' + (mode === 'rack' ? ' active' : '');
  document.getElementById('tab-custom').className = 'mode-tab' + (mode === 'custom' ? ' active' : '');
  
  // Clear other selections
  if (mode !== 'rack') {
    document.getElementById('rack-select').value = '';
    document.getElementById('rack-details-panel').style.display = 'none';
  }
  
  resetPreview();
}

function onRackSelectChange(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (opt && opt.value) {
    document.getElementById('rack-details-panel').style.display = 'block';
    document.getElementById('rack-name-display').value = opt.dataset.name;
    document.getElementById('rack-qr-text').value = opt.dataset.code;
    
    currentItemName = opt.dataset.name;
    currentQRText = opt.dataset.code;
    
    generateQR(opt.dataset.code);
    showToast('Rak terpilih: ' + opt.dataset.name, 'success');
  } else {
    document.getElementById('rack-details-panel').style.display = 'none';
    resetPreview();
  }
}

// ============================================================
// ITEM SEARCH / DROPDOWN
// ============================================================
function filterItems(query) {
  const q = query.toLowerCase().trim();
  const dropdown = document.getElementById('item-dropdown');
  const filtered = q
    ? DB_ITEMS.filter(i => i.name.toLowerCase().includes(q) || i.sku.toLowerCase().includes(q) || (i.qr_code && i.qr_code.toLowerCase().includes(q)))
    : DB_ITEMS;

  renderDropdown(filtered.slice(0, 10));
  dropdown.classList.add('open');
}

function renderDropdown(items) {
  const dropdown = document.getElementById('item-dropdown');
  if (!items.length) {
    dropdown.innerHTML = '<div style="padding:14px;color:var(--text-muted);font-size:13px;text-align:center;">Tidak ada barang ditemukan</div>';
    return;
  }
  dropdown.innerHTML = items.map(item => `
    <div class="item-option" onmousedown="selectItem(${item.id})">
      <div class="item-option-dot"></div>
      <div style="flex:1;min-width:0;">
        <div class="item-option-name">${escHtml(item.name)}</div>
        <div class="item-option-sku">${escHtml(item.sku)} · ${escHtml(item.unit)} · ${escHtml(item.cat_name)}</div>
      </div>
      <div class="item-option-stock">${escHtml(String(item.total_stock))} unit</div>
    </div>
  `).join('');
}

function openDropdown() {
  filterItems(document.getElementById('item-search').value);
}

function closeDropdownDelayed() {
  setTimeout(() => document.getElementById('item-dropdown').classList.remove('open'), 200);
}

function selectItem(id) {
  const item = DB_ITEMS.find(i => i.id == id);
  if (!item) return;
  selectedItem = item;

  document.getElementById('item-search').value = item.name;
  document.getElementById('item-dropdown').classList.remove('open');

  // Show editable fields, pre-filled with item data
  document.getElementById('db-edit-fields').style.display = 'block';
  document.getElementById('db-item-count') && (document.getElementById('db-item-count').style.display = 'none');
  document.getElementById('db-nama').value   = item.name;
  document.getElementById('db-sku').value    = item.sku;
  document.getElementById('db-unit').value   = item.unit;
  document.getElementById('db-stock').value  = item.total_stock;
  document.getElementById('db-qrcode').value = item.qr_code || '';

  // Store original QR code for reset
  document.getElementById('db-qrcode').dataset.original = item.qr_code || '';

  currentItemName = item.name;
  currentQRText   = ''; // reset — user must click Generate
  resetPreview();

  if (!item.qr_code) {
    showToast('Barang ini belum memiliki kode QR. Edit kode QR di bawah lalu Generate.', 'error');
  } else {
    showToast('Data "' + item.name + '" terisi! Edit jika perlu lalu klik Generate.', 'success');
  }
}

function resetQrCode() {
  const input = document.getElementById('db-qrcode');
  input.value = input.dataset.original || '';
}

function clearSelected() {
  selectedItem = null;
  document.getElementById('item-search').value = '';
  document.getElementById('db-edit-fields').style.display = 'none';
  const countEl = document.getElementById('db-item-count');
  if (countEl) countEl.style.display = 'block';
  resetPreview();
}

// ============================================================
// QR GENERATION (using davidshimjs qrcodejs)
// ============================================================
function triggerGenerate() {
  let text = '';
  if (currentMode === 'custom') {
    text = document.getElementById('qr-custom-text').value.trim();
    currentItemName = text.substring(0, 30);
  } else if (currentMode === 'rack') {
    text = document.getElementById('rack-qr-text').value.trim();
    currentItemName = document.getElementById('rack-name-display').value.trim() || text;
  } else if (currentMode === 'db') {
    const baseCode = document.getElementById('db-qrcode').value.trim();
    const demoPo = document.getElementById('demo-po').value.trim();
    const demoQty = parseInt(document.getElementById('demo-qty').value) || 0;
    
    currentItemName = document.getElementById('db-nama').value.trim() || baseCode.substring(0, 30);
    
    if (demoPo || demoQty > 0) {
      // Encode as a compact JSON object
      text = JSON.stringify({
        code: baseCode,
        qty: demoQty > 0 ? demoQty : undefined,
        po: demoPo ? demoPo : undefined
      });
    } else {
      text = baseCode;
    }
  }
  if (!text) {
    showToast('Isi kode QR terlebih dahulu', 'error');
    resetPreview();
    return;
  }
  currentQRText = text;
  generateQR(text);
}

function generateQR(text) {
  if (!text) return;
  const size  = parseInt(document.getElementById('qr-size').value);
  const dark  = document.getElementById('qr-dark').value;
  const light = document.getElementById('qr-light').value;

  const container = document.getElementById('qrcode-render');
  container.innerHTML = ''; // clear previous

  try {
    qrInstance = new QRCode(container, {
      text: text,
      width: size,
      height: size,
      colorDark:  dark,
      colorLight: light,
      correctLevel: QRCode.CorrectLevel.H
    });

    // Show preview
    document.getElementById('qr-placeholder').style.display  = 'none';
    document.getElementById('qr-canvas-container').style.display = 'flex';
    document.getElementById('qr-preview-wrapper').classList.add('has-qr');
    const codeDisplay = document.getElementById('qr-code-display');
    codeDisplay.textContent = text;
    codeDisplay.style.display = 'block';

    document.getElementById('btn-download').disabled = false;
    document.getElementById('btn-save').disabled = false;
    currentQRText = text;
  } catch(e) {
    showToast('Gagal generate QR: ' + e.message, 'error');
    console.error(e);
  }
}

function resetPreview() {
  currentQRText = '';
  document.getElementById('qr-placeholder').style.display = 'flex';
  document.getElementById('qr-canvas-container').style.display = 'none';
  document.getElementById('qr-preview-wrapper').classList.remove('has-qr');
  document.getElementById('qr-code-display').style.display = 'none';
  document.getElementById('qrcode-render').innerHTML = '';
  document.getElementById('btn-download').disabled = true;
  document.getElementById('btn-save').disabled = true;
  qrInstance = null;
}

function downloadQR() {
  const img = document.querySelector('#qrcode-render img');
  const canvas = document.querySelector('#qrcode-render canvas');
  let dataUrl = '';
  if (img) {
    dataUrl = img.src;
  } else if (canvas) {
    dataUrl = canvas.toDataURL('image/png');
  }
  if (!dataUrl || !currentQRText) return;
  const a = document.createElement('a');
  a.download = 'WMS-QR-' + currentQRText.replace(/[^a-zA-Z0-9]/g, '_') + '.png';
  a.href = dataUrl;
  a.click();
  showToast('QR Code berhasil diunduh!', 'success');
}

function applyPreset(dark, light) {
  document.getElementById('qr-dark').value  = dark;
  document.getElementById('qr-light').value = light;
  if (currentQRText) triggerGenerate();
}

// ============================================================
// HISTORY
// ============================================================
function saveToHistory() {
  if (!currentQRText) return;
  const label = currentMode === 'db' && selectedItem ? selectedItem.name : (currentItemName || currentQRText.substring(0,30));
  if (historyItems.find(i => i.code === currentQRText)) { showToast('Sudah ada di koleksi', 'info'); return; }
  historyItems.unshift({ name: label, code: currentQRText, time: Date.now() });
  if (historyItems.length > 20) historyItems.pop();
  localStorage.setItem('wms_qr_history', JSON.stringify(historyItems));
  renderHistory();
  showToast('Disimpan ke koleksi!', 'success');
}

function renderHistory() {
  const list = document.getElementById('history-list');
  if (!historyItems.length) {
    list.innerHTML = '<div style="color:var(--text-muted);font-size:13px;text-align:center;padding:24px;">Belum ada QR yang disimpan</div>';
    return;
  }
  list.innerHTML = historyItems.map((item, idx) => `
    <div class="qr-history-item" onclick="loadFromHistory(${idx})">
      <div class="qr-history-dot"></div>
      <div style="flex:1;min-width:0;">
        <div class="qr-history-name">${escHtml(item.name)}</div>
        <div class="qr-history-code">${escHtml(item.code)}</div>
      </div>
      <div class="qr-history-actions">
        <button class="btn btn-sm btn-secondary" style="padding:4px 10px;" onclick="event.stopPropagation();copyCode('${escHtml(item.code)}')" title="Salin">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </button>
        <button class="btn btn-sm" style="padding:4px 10px;background:var(--danger-soft);color:var(--danger);" onclick="event.stopPropagation();deleteHistory(${idx})" title="Hapus">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
  `).join('');
}

function loadFromHistory(idx) {
  const item = historyItems[idx];
  setMode('custom');
  document.getElementById('qr-custom-text').value = item.code;
  currentQRText = item.code;
  currentItemName = item.name;
  generateQR(item.code);
  showToast(item.name + ' dimuat!', 'info');
}

function deleteHistory(idx) {
  historyItems.splice(idx, 1);
  localStorage.setItem('wms_qr_history', JSON.stringify(historyItems));
  renderHistory();
}

function clearHistory() {
  if (!historyItems.length) return;
  if (confirm('Hapus semua dari koleksi?')) {
    historyItems = [];
    localStorage.removeItem('wms_qr_history');
    renderHistory();
  }
}

function copyCode(code) {
  navigator.clipboard.writeText(code)
    .then(() => showToast('Kode disalin!', 'success'))
    .catch(() => showToast('Gagal menyalin', 'error'));
}

// ============================================================
// SCANNER
// ============================================================
function toggleCamera() {
  const placeholder = document.getElementById('cam-placeholder');
  const reader      = document.getElementById('cam-reader');
  const stopBtn     = document.getElementById('btn-stop-cam');

  if (!camOpen) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showToast('Kamera tidak tersedia (butuh HTTPS atau localhost). Gunakan fitur unggah gambar.', 'error');
      return;
    }
    placeholder.style.display = 'none';
    reader.style.display = 'block';
    stopBtn.style.display = 'flex';

    html5QrCode = new Html5Qrcode('cam-reader');
    Html5Qrcode.getCameras()
      .then(devices => {
        let cameraId = devices[0]?.id;
        for (const d of devices) {
          if (/back|environment|rear/i.test(d.label)) { cameraId = d.id; break; }
        }
        return html5QrCode.start(
          cameraId || { facingMode: 'environment' },
          { fps: 12, qrbox: (w, h) => { const s = Math.floor(Math.min(w,h)*0.7); return {width:s,height:s}; } },
          (code) => { stopCamera(); handleScanResult(code); },
          () => {}
        );
      })
      .catch(() => showToast('Gagal membuka kamera. Pastikan izin kamera sudah diberikan.', 'error'));
    camOpen = true;
  } else {
    stopCamera();
  }
}

function stopCamera() {
  if (html5QrCode) {
    html5QrCode.stop().catch(() => {}).finally(() => { html5QrCode = null; });
  }
  document.getElementById('cam-placeholder').style.display = 'flex';
  document.getElementById('cam-reader').style.display      = 'none';
  document.getElementById('btn-stop-cam').style.display    = 'none';
  camOpen = false;
}

function scanFromFile(e) {
  const file = e.target.files[0];
  if (!file) return;
  showToast('Memproses gambar...', 'info');
  const scanner = new Html5Qrcode('scan-temp');
  scanner.scanFile(file, true)
    .then(code => { handleScanResult(code); scanner.clear(); })
    .catch(() => { showToast('QR tidak terdeteksi. Pastikan gambar jelas.', 'error'); scanner.clear(); });
  e.target.value = '';
}

const scanDropZone = document.getElementById('scan-drop-zone');
scanDropZone.addEventListener('dragover',  e => { e.preventDefault(); scanDropZone.classList.add('dragover'); });
['dragleave','dragend'].forEach(t => scanDropZone.addEventListener(t, () => scanDropZone.classList.remove('dragover')));
scanDropZone.addEventListener('drop', e => {
  e.preventDefault();
  scanDropZone.classList.remove('dragover');
  const file = e.dataTransfer.files[0];
  if (!file || !file.type.startsWith('image/')) return;
  showToast('Memproses gambar...', 'info');
  const scanner = new Html5Qrcode('scan-temp');
  scanner.scanFile(file, true)
    .then(code => { handleScanResult(code); scanner.clear(); })
    .catch(() => { showToast('QR tidak terdeteksi.', 'error'); scanner.clear(); });
});

function handleScanResult(code) {
  const resultEl = document.getElementById('scan-result');
  resultEl.style.display = 'block';
  resultEl.className = 'scan-result animate-in';

  // Parse JSON if possible
  let displayCode = code;
  let demoQty = null;
  let demoPo = null;
  try {
    const parsed = JSON.parse(code);
    if (parsed && parsed.code) {
      displayCode = parsed.code;
      demoQty = parsed.qty;
      demoPo = parsed.po;
    }
  } catch(e) {
    // Not JSON, treat as plain QR
  }

  // Try to match with DB items using the item code
  const matchedItem = DB_ITEMS.find(i => i.qr_code === displayCode);
  const isWMSCode   = /^QR-[A-Z0-9]+$/i.test(displayCode);

  if (matchedItem) {
    resultEl.classList.add('success');
    let metaHtml = `Kode: <strong style="font-family:monospace;">${escHtml(displayCode)}</strong>`;
    if (demoPo) metaHtml += ` · <span style="background:var(--primary-soft);color:var(--primary-dark);font-weight:700;padding:2px 6px;border-radius:4px;font-size:10px;">PO: ${escHtml(demoPo)}</span>`;
    if (demoQty) metaHtml += ` · <span style="background:var(--success-soft);color:var(--success);font-weight:700;padding:2px 6px;border-radius:4px;font-size:10px;">Demo Qty: ${escHtml(String(demoQty))}</span>`;

    resultEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div style="flex:1;">
        <div class="scan-result-name">${escHtml(matchedItem.name)}</div>
        <div class="scan-result-meta">${metaHtml}</div>
        <div class="scan-result-grid">
          <div class="scan-result-grid-item"><div class="scan-result-grid-label">SKU</div><div class="scan-result-grid-value" style="font-family:monospace;">${escHtml(matchedItem.sku)}</div></div>
          <div class="scan-result-grid-item"><div class="scan-result-grid-label">Stok Sistem</div><div class="scan-result-grid-value">${escHtml(String(matchedItem.total_stock))} ${escHtml(matchedItem.unit)}</div></div>
          <div class="scan-result-grid-item"><div class="scan-result-grid-label">Kategori</div><div class="scan-result-grid-value">${escHtml(matchedItem.cat_name)}</div></div>
          <div class="scan-result-grid-item"><div class="scan-result-grid-label">Satuan</div><div class="scan-result-grid-value">${escHtml(matchedItem.unit)}</div></div>
        </div>
      </div>
    `;
    showToast('Barang ditemukan: ' + matchedItem.name, 'success');
  } else if (isWMSCode) {
    resultEl.classList.add('success');
    resultEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div><div class="scan-result-name">QR WMS Terdeteksi</div><div class="scan-result-meta">Kode: <strong style="font-family:monospace;">${escHtml(displayCode)}</strong></div><div class="scan-result-meta" style="margin-top:6px;">Barang tidak ditemukan di database halaman ini, tapi kode WMS valid.</div></div>
    `;
    showToast('QR WMS valid', 'success');
  } else {
    resultEl.classList.add('success');
    resultEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div><div class="scan-result-name">QR Terbaca</div><div class="scan-result-meta">Isi: <strong>${escHtml(code)}</strong></div></div>
    `;
    showToast('QR berhasil di-scan!', 'success');
  }

  // Load into generator
  setMode('custom');
  document.getElementById('qr-custom-text').value = code;
  currentQRText  = code;
  currentItemName = matchedItem ? matchedItem.name : displayCode.substring(0, 30);
  generateQR(code);
}

// ============================================================
// UTILS
// ============================================================
function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showToast(msg, type = 'info') {
  const c = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  const icons = {
    success: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    error:   '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    info:    '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  };
  t.innerHTML = (icons[type] || icons.info) + escHtml(msg);
  c.appendChild(t);
  setTimeout(() => { t.style.animation = 'toastOut 0.3s ease forwards'; setTimeout(() => t.remove(), 300); }, 3500);
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  renderHistory();
  // Show all items immediately on focus
  filterItems('');
});
</script>
</body>
</html>
