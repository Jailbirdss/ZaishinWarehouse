<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Zaishin Warehouse</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= time() ?>"/>
<?php if (hasPermission('dashboard.view')): ?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/ai-chat.css?v=<?= time() ?>"/>
<?php endif; ?>
<script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>
<div class="app-layout">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-icon">
        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="wireframe-grad" x1="5" y1="5" x2="27" y2="27" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.95" />
              <stop offset="100%" stop-color="#60A5FA" stop-opacity="0.75" />
            </linearGradient>
            <linearGradient id="z-grad" x1="11" y1="11" x2="21" y2="21" gradientUnits="userSpaceOnUse">
              <stop offset="0%" stop-color="#FFFFFF" />
              <stop offset="100%" stop-color="#93C5FD" />
            </linearGradient>
          </defs>
          <path d="M16 4.5L27.5 11.14V24.43L16 31.07L4.5 24.43V11.14L16 4.5Z" 
                stroke="url(#wireframe-grad)" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round" />
          <path d="M4.5 11.14L16 17.8L27.5 11.14M16 17.8V31.07" 
                stroke="url(#wireframe-grad)" stroke-width="1.2" stroke-opacity="0.5" stroke-linejoin="round" stroke-linecap="round" />
          <path d="M11 11H21L11 21H21" 
                stroke="#3B82F6" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.3" />
          <path d="M11 11H21L11 21H21" 
                stroke="url(#z-grad)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>
      <div>
        <div class="sidebar-brand-text">Zaishin Warehouse</div>
        <div class="sidebar-brand-sub">Manajemen Gudang</div>
      </div>
    </div>

    <nav class="sidebar-nav">

      <?php if (hasPermission('dashboard.view') || hasPermission('zones.view') || hasPermission('stock.view') || hasPermission('notifications.view')): ?>
        <div class="nav-section-label">Utama</div>
        
        <?php if (hasPermission('dashboard.view')): ?>
        <a href="index.php?page=dashboard" class="nav-item" data-page="dashboard">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
          </svg>
          Dashboard
        </a>
        <?php endif; ?>

        <?php if (hasPermission('zones.view')): ?>
        <a href="index.php?page=zones" class="nav-item" data-page="zones">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
          </svg>
          Peta Gudang
        </a>
        <?php endif; ?>

        <?php if (hasPermission('stock.view')): ?>
        <a href="index.php?page=stock" class="nav-item" data-page="stock">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
          </svg>
          Ketersediaan Stok
        </a>
        <?php endif; ?>

        <?php if (hasPermission('notifications.view')): ?>
        <a href="index.php?page=notifications" class="nav-item" data-page="notifications">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          Notifikasi
          <span class="nav-badge d-none" id="sidebar-notif-badge">0</span>
        </a>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (hasPermission('inbound.view') || hasPermission('outbound.view') || hasPermission('relocation.view') || hasPermission('restock.view') || hasPermission('opname.view')): ?>
        <div class="nav-section-label">Operasional</div>

        <?php if (hasPermission('inbound.view')): ?>
        <a href="index.php?page=inbound" class="nav-item" data-page="inbound">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
          </svg>
          Barang Masuk
        </a>
        <?php endif; ?>

        <?php if (hasPermission('outbound.view')): ?>
        <a href="index.php?page=outbound" class="nav-item" data-page="outbound">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Barang Keluar
        </a>
        <?php endif; ?>

        <?php if (hasPermission('relocation.view')): ?>
        <a href="index.php?page=relocation" class="nav-item" data-page="relocation">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
          </svg>
          Mutasi Stok
        </a>
        <?php endif; ?>

        <?php if (hasPermission('restock.view')): ?>
        <a href="index.php?page=restock" class="nav-item" data-page="restock" id="nav-restock">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Permintaan Restock
          <span class="nav-badge d-none" id="restock-badge">0</span>
        </a>
        <?php endif; ?>

        <?php if (hasPermission('opname.view')): ?>
        <a href="index.php?page=opname" class="nav-item" data-page="opname">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          Stock Opname
        </a>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (hasPermission('sales_orders.view') || hasPermission('items.view') || hasPermission('reports.view')): ?>
        <div class="nav-section-label">Inventaris</div>

        <?php if (hasPermission('sales_orders.view')): ?>
        <a href="index.php?page=sales-orders" class="nav-item" data-page="sales-orders">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          Sales Order (SO)
        </a>
        <?php endif; ?>

        <?php if (hasPermission('items.view')): ?>
        <a href="index.php?page=items" class="nav-item" data-page="items">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
          </svg>
          Master Barang
        </a>
        <?php endif; ?>

        <?php if (hasPermission('reports.view')): ?>
        <a href="index.php?page=reports" class="nav-item" data-page="reports">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Laporan
        </a>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (hasPermission('users.view') || hasPermission('roles.manage')): ?>
        <div class="nav-section-label">Admin</div>

        <?php if (hasPermission('users.view')): ?>
        <a href="index.php?page=users" class="nav-item" data-page="users">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Manajemen User
        </a>
        <?php endif; ?>

        <?php if (hasPermission('roles.manage')): ?>
        <a href="index.php?page=roles" class="nav-item" data-page="roles">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          Peran & Hak Akses
        </a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <a href="index.php?page=logout" class="sidebar-user">
        <div class="user-avatar"><?= htmlspecialchars($_SESSION['user_avatar'] ?? 'U') ?></div>
        <div>
          <div class="user-info-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
          <div class="user-info-role"><?= roleLabel($_SESSION['user_role'] ?? '') ?></div>
        </div>
        <svg class="sidebar-logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </aside>
  <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

  <div class="main-wrapper">

    <header class="topbar">
      <div class="topbar-left-area">
        <button id="sidebar-toggle-btn" class="topbar-hamburger-btn" title="Menu Utama" aria-label="Menu Utama">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <?php
        $currentPage = $_GET['page'] ?? 'dashboard';
        $topbarIcon = match($currentPage) {
            'dashboard' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>',
            'zones' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
            'stock' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>',
            'notifications' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
            'inbound' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>',
            'outbound' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>',
            'relocation' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>',
            'restock' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
            'opname' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'sales-orders' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'items' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>',
            'reports' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'users' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            default => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4l3 3" stroke-width="2"/></svg>',
        };
        ?>
        <div class="topbar-icon-wrapper">
          <?= $topbarIcon ?>
        </div>
        <div class="topbar-title-group">
          <div class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></div>
          <?php if (!empty($pageSubtitle)): ?>
            <div class="topbar-sub"><?= htmlspecialchars($pageSubtitle) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="topbar-actions">

        <?php $isMac = strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'Mac') !== false; ?>
        <button id="cmd-palette-btn" onclick="openCommandPalette()" title="Pencarian Cepat (Ctrl+K)">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
          </svg>
          Cari
          <span class="cp-kbd ml-2"><?= $isMac ? '⌘K' : 'Ctrl+K' ?></span>
        </button>

        <button class="notif-btn" title="Notifikasi" onclick="openModal('notif-modal')">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <span class="notif-dot d-none" id="notif-dot"></span>
        </button>

        <div class="topbar-divider"></div>

        <div class="topbar-user-profile">
          <div class="flex-column-align-end">
            <div class="fs-13 font-700 text-main"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
            <div class="topbar-user-role">
              <?= roleLabel($_SESSION['user_role'] ?? '') ?>
            </div>
          </div>
          <div class="topbar-user-avatar">
            <?= htmlspecialchars($_SESSION['user_avatar'] ?? 'U') ?>
          </div>
        </div>
      </div>
    </header>

    <main class="page-content">
      <?= $content ?? '' ?>
    </main>
  </div>
</div>

<div class="modal-overlay" id="notif-modal">
  <div class="modal-box max-w-480px">
    <div class="modal-header">
      <div class="modal-title d-flex align-center gap-8">
        <svg fill="none" stroke="var(--warning)" viewBox="0 0 24 24" class="icon-20 text-warning"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        Pusat Notifikasi &amp; Alarm Stok
      </div>
    </div>
    <div class="modal-body p-16-20">
      <div id="notif-loading" class="p-24 text-center">
        <div class="notif-spinner"></div>
        <div class="mt-8 fs-12 text-muted">Memuat notifikasi...</div>
      </div>
      <div id="notif-list" class="d-flex flex-column gap-10">

      </div>
    </div>
    <div class="modal-footer p-12-20">
      <button class="btn btn-secondary btn-sm" onclick="closeModal('notif-modal')">Tutup</button>
    </div>
  </div>
</div>

<div class="modal-overlay z-9999" id="custom-confirm-modal">
  <div class="modal-box confirm-modal-box">
    <div class="modal-header confirm-modal-header">
      <div class="modal-title confirm-modal-title" id="confirm-modal-title">
        Konfirmasi Tindakan
      </div>
    </div>
    <div class="modal-body confirm-modal-body">
      <p id="confirm-modal-message" class="confirm-modal-message"></p>
    </div>
    <div class="modal-footer confirm-modal-footer">
      <button class="btn btn-secondary flex-1 justify-center p-10-14 font-700" onclick="closeConfirmModal(false)">Batal</button>
      <button class="btn btn-primary flex-1 justify-center p-10-14 font-700" id="btn-confirm-yes">Ya, Lanjutkan</button>
    </div>
  </div>
</div>

<div id="grid-tooltip" class="grid-tooltip-box d-none"></div>

<script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= time() ?>"></script>
<?php

$cpAllowed = [];
$role = $_SESSION['user_role'] ?? '';
if ($role === 'divisi_penjualan') {
    $cpAllowed = ['stock','sales-orders'];
} elseif ($role === 'divisi_pembelian') {
    $cpAllowed = ['stock','notifications','restock','reports'];
} elseif ($role === 'petugas_gudang') {
    $cpAllowed = ['dashboard','zones','stock','notifications','inbound','outbound','relocation','opname'];
} elseif ($role === 'manajemen') {
    $cpAllowed = ['dashboard','zones','stock','notifications','opname','sales-orders','reports'];
} else {
    $cpAllowed = ['dashboard','zones','stock','notifications','inbound','outbound','relocation','restock','opname','sales-orders','items','reports','users'];
}
?>
<script>const CP_ALLOWED_PAGES = <?= json_encode($cpAllowed) ?>;</script>
<script src="<?= BASE_URL ?>/assets/js/command-palette.js?v=<?= time() ?>"></script>
<script>

const CURRENT_ROLE = '<?= htmlspecialchars($_SESSION['user_role'] ?? '') ?>';

function checkNotifications() {

  if (CURRENT_ROLE === 'divisi_penjualan') return;

  const dot = document.getElementById('notif-dot');
  const sidebarBadge = document.getElementById('sidebar-notif-badge');

  fetch(BASE_URL + '/api/notifications.php?type=unread_count')
    .then(r => r.json())
    .then(data => {
      const count = parseInt(data.count) || 0;
      if (count > 0) {
        if (dot) {
          dot.textContent = count;
          dot.classList.remove('d-none');
        }
        if (sidebarBadge) {
          sidebarBadge.textContent = count;
          sidebarBadge.classList.remove('d-none');
        }
      } else {
        if (dot) dot.classList.add('d-none');
        if (sidebarBadge) sidebarBadge.classList.add('d-none');
      }
      return fetch(BASE_URL + '/api/dashboard.php?type=restock_pending');
    })
    .then(r => r.json())
    .then(d => {
      if (d && d.count > 0) {
        const badge = document.getElementById('restock-badge');
        if (badge) {
          badge.textContent = d.count;
          badge.classList.remove('d-none');
        }
      }
    })
    .catch(() => {});
}
document.addEventListener('DOMContentLoaded', () => {
  checkNotifications();

  const sidebarNav = document.querySelector('.sidebar-nav');
  if (sidebarNav) {
    const savedScroll = sessionStorage.getItem('sidebarScroll');
    if (savedScroll) {
      sidebarNav.scrollTop = parseInt(savedScroll, 10);
    }
    sidebarNav.addEventListener('scroll', () => {
      sessionStorage.setItem('sidebarScroll', sidebarNav.scrollTop);
    });
  }
});
</script>
<?php if (hasPermission('dashboard.view')): ?>

<?php include __DIR__ . '/ai_chatbot.php'; ?>

<script>
  const USER_NAME = <?= json_encode($_SESSION['user_name'] ?? 'User') ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/ai-chat.js?v=<?= time() ?>"></script>
<?php endif; ?>
</body>
</html>
<?php
function roleLabel(string $role): string {
    return match($role) {
        'admin_gudang'     => 'Admin Gudang',
        'petugas_gudang'   => 'Petugas Gudang',
        'divisi_penjualan' => 'Divisi Penjualan',
        'divisi_pembelian' => 'Divisi Pembelian',
        'manajemen'        => 'Manajemen',
        default            => $role,
    };
}
?>