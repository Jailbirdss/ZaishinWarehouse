<?php

function getNotifMetadata(string $type, string $message): array {
    $severity = 'info';
    $severityLabel = 'Info';
    $severityBg = '#f1f5f9';
    $severityColor = 'var(--text-muted)';

    $typeLabel = 'Sistem';
    $typeBorder = 'rgba(30, 64, 175, 0.15)';
    $typeBg = 'rgba(30, 64, 175, 0.05)';
    $typeColor = 'var(--primary)';
    $typeGlow = 'rgba(30, 64, 175, 0.12)';

    if ($type === 'low_stock') {
        $typeLabel = 'Alarm Stok';
        $typeBorder = 'rgba(220, 38, 38, 0.15)';
        $typeBg = 'rgba(220, 38, 38, 0.05)';
        $typeColor = 'var(--danger)';
        $typeGlow = 'rgba(220, 38, 38, 0.15)';
    } elseif (strpos($type, 'restock') !== false) {
        $typeLabel = 'Permintaan Restock';
        $typeBorder = 'rgba(217, 119, 6, 0.15)';
        $typeBg = 'rgba(217, 119, 6, 0.05)';
        $typeColor = 'var(--warning)';
        $typeGlow = 'rgba(217, 119, 6, 0.15)';
    } elseif (strpos($type, 'opname') !== false) {
        $typeLabel = 'Stock Opname';
        $typeBorder = 'rgba(2, 132, 199, 0.15)';
        $typeBg = 'rgba(2, 132, 199, 0.05)';
        $typeColor = '#0284c7';
        $typeGlow = 'rgba(2, 132, 199, 0.15)';
    }

    if ($type === 'low_stock' && (strpos($message, 'bersisa 0') !== false || strpos($message, 'menipis menjadi 0') !== false || strpos($message, 'KOSONG') !== false)) {
        $severity = 'high';
        $severityLabel = 'Tinggi';
        $severityBg = 'var(--danger-soft)';
        $severityColor = 'var(--danger)';
    } elseif ($type === 'low_stock' || $type === 'restock_rejected') {
        $severity = 'high';
        $severityLabel = 'Tinggi';
        $severityBg = 'var(--danger-soft)';
        $severityColor = 'var(--danger)';
    } elseif ($type === 'restock_submitted' || $type === 'opname_initiated') {
        $severity = 'medium';
        $severityLabel = 'Sedang';
        $severityBg = 'var(--warning-soft)';
        $severityColor = 'var(--warning)';
    } else {
        $severity = 'info';
        $severityLabel = 'Rendah';
        $severityBg = 'var(--success-soft)';
        $severityColor = 'var(--success)';
    }

    return [
        'severity' => $severity,
        'severity_label' => $severityLabel,
        'severity_bg' => $severityBg,
        'severity_color' => $severityColor,
        'type_label' => $typeLabel,
        'type_border' => $typeBorder,
        'type_bg' => $typeBg,
        'type_color' => $typeColor,
        'type_glow' => $typeGlow
    ];
}

$unreadReorder = 0;
$unreadOpname = 0;
$unreadRestock = 0;

foreach ($notifications as $n) {
    if (!$n['is_read']) {
        if ($n['type'] === 'low_stock') {
            $unreadReorder++;
        } elseif (strpos($n['type'], 'opname') !== false) {
            $unreadOpname++;
        } elseif (strpos($n['type'], 'restock') !== false) {
            $unreadRestock++;
        }
    }
}
$hasUnread = ($unreadReorder > 0 || $unreadOpname > 0 || $unreadRestock > 0);
?>

<div class="card p-20 mb-24">
  <div class="filter-row">
    <div class="filter-group">
      <div class="filter-label">Tipe Notifikasi</div>
      <select id="filter-type" class="form-control" onchange="applyNotifFilters()">
        <option value="all">Semua Tipe</option>
        <option value="low_stock">Alarm Stok</option>
        <option value="restock">Pemesanan Ulang (Restock)</option>
        <option value="opname">Stock Opname</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Kerawanan</div>
      <select id="filter-severity" class="form-control" onchange="applyNotifFilters()">
        <option value="all">Semua Tingkat</option>
        <option value="high">Tinggi</option>
        <option value="medium">Sedang</option>
        <option value="info">Rendah</option>
      </select>
    </div>
    <div class="filter-group">
      <div class="filter-label">Status Baca</div>
      <select id="filter-status" class="form-control" onchange="applyNotifFilters()">
        <option value="all">Semua Status</option>
        <option value="unread">Belum Dibaca</option>
        <option value="read">Dibaca</option>
      </select>
    </div>
    <button class="btn btn-secondary p-9-16" onclick="resetNotifFilters()">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
      Reset Filter
    </button>
    <button id="mark-all-read-btn" onclick="markAllNotifsAsRead()" class="btn btn-outline p-9-16 ml-auto d-inline-flex align-center gap-6 <?= $hasUnread ? '' : 'd-none' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      Tandai Semua Dibaca
    </button>
  </div>

  <div class="unread-badges-summary">
    <div class="summary-badge summary-badge-reorder">
      Alarm Stok Belum Dibaca: <span id="badge-count-reorder" class="ml-5 font-800"><?= $unreadReorder ?></span>
    </div>
    <div class="summary-badge summary-badge-opname">
      Selisih Opname Belum Dibaca: <span id="badge-count-opname" class="ml-5 font-800"><?= $unreadOpname ?></span>
    </div>
    <div class="summary-badge summary-badge-restock">
      Permintaan Restock Belum Dibaca: <span id="badge-count-restock" class="ml-5 font-800"><?= $unreadRestock ?></span>
    </div>
  </div>
</div>

<div class="alert-feed-title-area">
  <div class="feed-sub">Aliran Peringatan</div>
  <div class="d-flex justify-between align-center flex-wrap gap-8">
    <h2 class="feed-main-title">Notifikasi Operasional</h2>
    <span class="fs-12 text-muted font-700" id="notif-count-text">Menampilkan: <?= count($notifications) ?> Notifikasi</span>
  </div>
</div>

<div class="notif-card-container" id="notif-feed-list">
  <?php if (empty($notifications)): ?>
    <div class="card notif-empty-card" id="feed-empty-placeholder">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-56-center-mb-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      <div class="card-title-fs-16 text-main">Tidak Ada Notifikasi Terkini</div>
      <p class="fs-13 text-muted mt-6">Seluruh log aktivitas operational pergudangan aman.</p>
    </div>
  <?php else: ?>
    <?php
    $index = 0;
    foreach ($notifications as $n):
      $meta = getNotifMetadata($n['type'], $n['message']);
      $unreadClass = $n['is_read'] ? '' : 'unread';
      $timeStr = date('d M Y, H:i', strtotime($n['created_at']));
      $delayMs = $index * 40;
      $index++;

      $createdBy = 'Sistem WMS';
      if ($n['type'] === 'restock_submitted') {
          $createdBy = 'Dani Prasetyo';
      } elseif (strpos($n['type'], 'approved') !== false || strpos($n['type'], 'rejected') !== false) {
          $createdBy = 'Admin Gudang';
      }

      $targetRole = 'Semua Staf';
      if ($n['role'] === 'admin_gudang') {
          $targetRole = 'Admin Gudang';
      } elseif ($n['role'] === 'petugas_gudang') {
          $targetRole = 'Petugas Gudang';
      } elseif ($n['role'] === 'divisi_pembelian') {
          $targetRole = 'Divisi Pembelian';
      } elseif ($n['role'] === 'manajemen') {
          $targetRole = 'Manajemen';
      }
    ?>
      <div class="notif-feed-card <?= $unreadClass ?>"
           data-id="<?= $n['id'] ?>"
           data-read="<?= $n['is_read'] ? 'true' : 'false' ?>"
           data-type="<?= htmlspecialchars($n['type']) ?>"
           data-severity="<?= $meta['severity'] ?>"
           style="--notif-accent-color: <?= $meta['type_color'] ?>; --notif-accent-glow: <?= $meta['type_glow'] ?>; animation-delay: <?= $delayMs ?>ms;">

        <div class="notif-left-pane">
          <div class="notif-badge-row">
            <span class="pill-badge severity" style="--sev-bg: <?= $meta['severity_bg'] ?>; --sev-color: <?= $meta['severity_color'] ?>;"><?= $meta['severity_label'] ?></span>
            <span class="pill-badge type" style="--type-border: <?= $meta['type_border'] ?>; --type-bg: <?= $meta['type_bg'] ?>; --type-color: <?= $meta['type_color'] ?>;"><?= $meta['type_label'] ?></span>
          </div>

          <h3 class="notif-feed-title"><?= htmlspecialchars($n['title']) ?></h3>
          <p class="notif-feed-desc"><?= htmlspecialchars($n['message']) ?></p>

          <div class="notif-feed-footer">
            <span class="text-muted fs-11-5"><?= $timeStr ?></span>

            <?php if ($n['type'] === 'low_stock' && hasPermission('restock.create')): ?>
              <span class="notif-feed-footer-dot"></span>
              <?php
                $cleanedName = str_replace(['Alarm Stok Rendah: ', 'Alarm Stok Rendah : '], '', $n['title']);
                $itemId = (int)$n['related_id'];
                $requests = $restockRequestsByItemId[$itemId] ?? [];
                $latestStatus = null;
                $notifTime = strtotime($n['created_at']);
                foreach ($requests as $req) {
                    $reqTime = strtotime($req['created_at']);
                    if ($reqTime >= $notifTime - 5) {
                        $latestStatus = $req['status'];
                        break;
                    }
                }
              ?>
              <?php if (in_array($latestStatus, ['pending', 'approved', 'completed'], true)): ?>
                <span class="notif-success-badge">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  Restock Diajukan
                </span>
              <?php elseif ($latestStatus === 'rejected'): ?>
                <span class="notif-danger-badge">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Restock Ditolak
                </span>
              <?php else: ?>
                <button onclick="autoSubmitRestock(<?= $n['id'] ?>, <?= (int)$n['related_id'] ?>, '<?= htmlspecialchars($cleanedName, ENT_QUOTES, 'UTF-8') ?>', this)" class="btn-action-restock">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13-mr-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                  Ajukan Restock
                </button>
              <?php endif; ?>
            <?php elseif ($n['type'] === 'restock_submitted' && hasPermission('restock.approve')): ?>
              <span class="notif-feed-footer-dot"></span>
              <?php
                $reqStatus = isset($restockStatusByReqId[(int)$n['related_id']]) ? $restockStatusByReqId[(int)$n['related_id']] : 'approved';
              ?>
              <?php if ($reqStatus === 'pending'): ?>
                <a href="index.php?page=restock" class="btn-action-approve">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13-mr-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                  Buka Persetujuan
                </a>
              <?php else: ?>
                <span class="read-status-indicator" style="background: #e2e8f0; color: #475569; font-weight: 600; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  Selesai Diproses
                </span>
              <?php endif; ?>
            <?php elseif ($n['type'] === 'opname_initiated' && hasPermission('opname.scan')): ?>
              <span class="notif-feed-footer-dot"></span>
              <?php
                $opnameStatus = $opnameStatusBySessionId[(int)$n['related_id']] ?? 'completed';
              ?>
              <?php if ($opnameStatus === 'initiated'): ?>
                <a href="index.php?page=opname&action=scan" class="btn-action-scan">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13-mr-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Masuk Pemindai Fisik
                </a>
              <?php else: ?>
                <span class="notif-success-badge">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  Selesai &amp; Diterapkan
                </span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="notif-right-pane">
          <div class="meta-row">
            <span class="meta-title">Peran Target</span>
            <span class="meta-value"><?= $targetRole ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-title">Dibuat Oleh</span>
            <span class="meta-value"><?= htmlspecialchars($createdBy) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-title">Tanggal Dibuat</span>
            <span class="meta-value"><?= date('d M Y', strtotime($n['created_at'])) ?></span>
          </div>

          <?php
            $rstStatus = null;
            if ($n['type'] === 'low_stock' && !empty($n['related_id'])) {
                $itemId = (int)$n['related_id'];
                $requests = $restockRequestsByItemId[$itemId] ?? [];
                $notifTime = strtotime($n['created_at']);
                foreach ($requests as $req) {
                    $reqTime = strtotime($req['created_at']);
                    if ($reqTime >= $notifTime - 5) {
                        $rstStatus = $req['status'];
                        break;
                    }
                }
            } elseif ($n['type'] === 'restock_submitted' && !empty($n['related_id'])) {
                $rstStatus = isset($restockStatusByReqId[(int)$n['related_id']]) ? $restockStatusByReqId[(int)$n['related_id']] : 'approved';
            }

            if ($rstStatus !== null) {
                if ($rstStatus === 'pending') {
                    $rstLabel = 'Menunggu';
                    $rstBg = 'var(--warning-soft)';
                    $rstColor = 'var(--warning)';
                    $rstIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                } elseif ($rstStatus === 'approved') {
                    $rstLabel = 'Disetujui';
                    $rstBg = 'var(--success-soft)';
                    $rstColor = 'var(--success)';
                    $rstIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
                } elseif ($rstStatus === 'completed') {
                    $rstLabel = 'Selesai';
                    $rstBg = 'var(--success-soft)';
                    $rstColor = 'var(--success)';
                    $rstIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
                } elseif ($rstStatus === 'rejected') {
                    $rstLabel = 'Ditolak';
                    $rstBg = 'var(--danger-soft)';
                    $rstColor = 'var(--danger)';
                    $rstIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
                } else {
                    $rstLabel = ucfirst($rstStatus);
                    $rstBg = '#f1f5f9';
                    $rstColor = 'var(--text-muted)';
                    $rstIcon = '';
                }
                echo "<div class='meta-row mt-2'>";
                echo "<span class='meta-title'>Status Restock</span>";
                echo "<span class='restock-status-badge' style='background:{$rstBg}; color:{$rstColor};'>";
                echo "<svg fill='none' stroke='currentColor' viewBox='0 0 24 24' class='icon-11'>{$rstIcon}</svg>";
                echo htmlspecialchars($rstLabel);
                echo "</span>";
                echo "</div>";
            }
          ?>

          <div class="notif-feed-actions">
            <?php if (!$n['is_read']): ?>
              <button onclick="markSingleFeedAsRead(<?= $n['id'] ?>, this)" class="unread-action-btn-pill">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Tandai Dibaca
              </button>
            <?php else: ?>
              <span class="read-status-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Dibaca
              </span>
            <?php endif; ?>
          </div>
        </div>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="modal-overlay z-1000" id="custom-restock-prompt-modal">
  <div class="modal-box restock-prompt-modal-box">
    <div class="modal-header border-bottom-none pb-0 mb-12">
      <div class="modal-title d-flex align-center gap-8 card-title-fs-16 text-main">
        <svg fill="none" stroke="var(--danger)" viewBox="0 0 24 24" class="icon-20 text-danger"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        Ajukan Restock Otomatis
      </div>
    </div>
    <div class="modal-body p-0 mb-20">
      <p class="fs-13 text-secondary mb-16 lh-1-5">
        Masukkan jumlah pembelian untuk barang:<br>
        <span id="prompt-item-name" class="text-main font-800 d-inline-block mt-4 fs-14"></span>
      </p>
      <div class="form-group mb-0">
        <label class="form-label restock-prompt-label">Jumlah Restock (Rim/Botol/Unit)</label>
        <input type="number" id="prompt-restock-qty" class="form-control restock-prompt-input" min="1" value="50">
      </div>
    </div>
    <div class="modal-footer restock-prompt-footer">
      <button class="btn btn-secondary flex-1 justify-center p-10-14 font-700" onclick="closeCustomRestockModal()">Batal</button>
      <button class="btn btn-primary btn-danger flex-1 justify-center p-10-14 font-700" id="btn-confirm-restock">Kirim</button>
    </div>
  </div>
</div>

<script>
let currentRestockContext = null;

function autoSubmitRestock(notifId, itemId, itemName, btn) {
  currentRestockContext = { notifId, itemId, itemName, btn };

  document.getElementById('prompt-item-name').textContent = itemName;
  document.getElementById('prompt-restock-qty').value = "50";

  document.getElementById('custom-restock-prompt-modal').classList.add('open');

  const qtyInput = document.getElementById('prompt-restock-qty');
  setTimeout(() => {
    qtyInput.focus();
    qtyInput.select();
  }, 100);
}

function closeCustomRestockModal() {
  document.getElementById('custom-restock-prompt-modal').classList.remove('open');
  currentRestockContext = null;
}

document.addEventListener('DOMContentLoaded', () => {
  const qtyInput = document.getElementById('prompt-restock-qty');
  if (qtyInput) {
    qtyInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        document.getElementById('btn-confirm-restock').click();
      }
    });
  }

  const confirmBtn = document.getElementById('btn-confirm-restock');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', async () => {
      if (!currentRestockContext) return;
      const { notifId, itemId, itemName, btn } = currentRestockContext;

      const qty = parseInt(qtyInput.value);
      if (isNaN(qty) || qty <= 0) {
        showToast("Jumlah tidak valid", "error");
        return;
      }

      closeCustomRestockModal();
      await executeAutoRestock(notifId, itemId, itemName, qty, btn);
    });
  }
});

async function executeAutoRestock(notifId, itemId, itemName, qty, btn) {
  try {
    btn.disabled = true;
    const originalContent = btn.innerHTML;
    btn.innerHTML = `<span class="btn-spinner"></span> Memproses...`;

    const restockRes = await fetch(BASE_URL + '/api/restock.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'create',
        item_id: itemId,
        requested_qty: qty,
        notes: 'Pengadaan otomatis via alarm stok di Pusat Notifikasi'
      })
    }).then(r => r.json());

    if (restockRes.success) {
      showToast(`Permintaan restock untuk ${itemName} sebanyak ${qty} berhasil diajukan!`, "success");

      const markRes = await fetch(BASE_URL + '/api/notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_read', id: notifId })
      }).then(r => r.json());

      if (markRes.success) {
        const card = btn.closest('.notif-feed-card');
        if (card) {
          card.classList.remove('unread');
          card.dataset.read = 'true';

          decrementSummaryCapsule(card.dataset.type);

          const actionsWrapper = card.querySelector('.notif-feed-actions');
          if (actionsWrapper) {
            actionsWrapper.innerHTML = `
              <span class="read-status-indicator animate-tab-fade-in">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Dibaca
              </span>
            `;
          }
        }

        const hasUnreadRemaining = document.querySelectorAll('.notif-feed-card.unread').length > 0;
        if (!hasUnreadRemaining) {
          const markAllBtn = document.getElementById('mark-all-read-btn');
          if (markAllBtn) markAllBtn.classList.add('d-none');
        }

        if (typeof checkNotifications === 'function') {
          checkNotifications();
        }

        applyNotifFilters();
      }

      btn.outerHTML = `<span class="notif-success-badge"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Restock Diajukan</span>`;
    } else {
      showToast(restockRes.error || "Gagal mengajukan restock", "error");
      btn.disabled = false;
      btn.innerHTML = originalContent;
    }
  } catch (err) {
    console.error(err);
    showToast("Kesalahan saat menghubungi server", "error");
    btn.disabled = false;
    btn.innerHTML = originalContent;
  }
}

function applyNotifFilters() {
  const typeVal = document.getElementById('filter-type').value;
  const severityVal = document.getElementById('filter-severity').value;
  const statusVal = document.getElementById('filter-status').value;

  const cards = document.querySelectorAll('.notif-feed-card');
  let visibleCount = 0;

  cards.forEach(card => {
    const type = card.dataset.type;
    const severity = card.dataset.severity;
    const isRead = card.dataset.read === 'true';

    let typeMatch = true;
    if (typeVal !== 'all') {
      if (typeVal === 'restock') {
        typeMatch = type.indexOf('restock') !== -1;
      } else if (typeVal === 'opname') {
        typeMatch = type.indexOf('opname') !== -1;
      } else {
        typeMatch = type === typeVal;
      }
    }

    let severityMatch = severityVal === 'all' || severity === severityVal;

    let statusMatch = true;
    if (statusVal === 'unread') {
      statusMatch = !isRead;
    } else if (statusVal === 'read') {
      statusMatch = isRead;
    }

    if (typeMatch && severityMatch && statusMatch) {
      card.classList.remove('d-none');
      visibleCount++;
    } else {
      card.classList.add('d-none');
    }
  });

  document.getElementById('notif-count-text').textContent = `Menampilkan: ${visibleCount} Notifikasi`;
}

function resetNotifFilters() {
  document.getElementById('filter-type').value = 'all';
  document.getElementById('filter-severity').value = 'all';
  document.getElementById('filter-status').value = 'all';

  applyNotifFilters();
}

async function markSingleFeedAsRead(id, btn) {
  try {
    const res = await fetch(BASE_URL + '/api/notifications.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'mark_read', id: id })
    }).then(r => r.json());

    if (res.success) {
      const card = btn.closest('.notif-feed-card');
      if (card) {
        card.classList.remove('unread');
        card.dataset.read = 'true';

        decrementSummaryCapsule(card.dataset.type);

        const actionsWrapper = card.querySelector('.notif-feed-actions');
        if (actionsWrapper) {
          actionsWrapper.innerHTML = `
            <span class="read-status-indicator animate-tab-fade-in">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              Dibaca
            </span>
          `;
        }
      }

      const hasUnreadRemaining = document.querySelectorAll('.notif-feed-card.unread').length > 0;
      if (!hasUnreadRemaining) {
        const markAllBtn = document.getElementById('mark-all-read-btn');
        if (markAllBtn) markAllBtn.classList.add('d-none');
      }

      if (typeof checkNotifications === 'function') {
        checkNotifications();
      }

      showToast("Notifikasi telah ditandai dibaca", "success");
      applyNotifFilters();
    } else {
      showToast(res.error || "Gagal menandai notifikasi", "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Kesalahan saat menghubungi server", "error");
  }
}

async function markAllNotifsAsRead() {
  try {
    const res = await fetch(BASE_URL + '/api/notifications.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'mark_all_read' })
    }).then(r => r.json());

    if (res.success) {
      document.querySelectorAll('.notif-feed-card.unread').forEach(card => {
        card.classList.remove('unread');
        card.dataset.read = 'true';

        const actionsWrapper = card.querySelector('.notif-feed-actions');
        if (actionsWrapper) {
          actionsWrapper.innerHTML = `
            <span class="read-status-indicator animate-tab-fade-in">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              Dibaca
            </span>
          `;
        }
      });

      document.getElementById('badge-count-reorder').textContent = '0';
      document.getElementById('badge-count-opname').textContent = '0';
      document.getElementById('badge-count-restock').textContent = '0';

      const markAllBtn = document.getElementById('mark-all-read-btn');
      if (markAllBtn) markAllBtn.classList.add('d-none');

      if (typeof checkNotifications === 'function') {
        checkNotifications();
      }

      showToast("Seluruh notifikasi telah ditandai dibaca", "success");
      applyNotifFilters();
    } else {
      showToast(res.error || "Gagal menandai seluruh notifikasi", "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Kesalahan saat menghubungi server", "error");
  }
}

function decrementSummaryCapsule(type) {
  let targetId = '';
  if (type === 'low_stock') {
    targetId = 'badge-count-reorder';
  } else if (type.indexOf('opname') !== -1) {
    targetId = 'badge-count-opname';
  } else if (type.indexOf('restock') !== -1) {
    targetId = 'badge-count-restock';
  }

  if (targetId) {
    const el = document.getElementById(targetId);
    if (el) {
      const current = parseInt(el.textContent) || 0;
      el.textContent = Math.max(0, current - 1);
    }
  }
}
</script>
