<?php

?>

<div class="so-header">
  <div class="so-header-inner">
    <div class="so-search-bar">
      <div class="so-search-wrap">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
        <input type="text" class="so-search-input" id="so-search" placeholder="Cari SO / pelanggan…" oninput="filterSoTable(this.value)">
      </div>
      <select class="form-control so-filter-select" id="so-filter-status" onchange="filterSoTable()">
        <option value="all">Semua Status</option>
        <option value="pending">Menunggu Picking</option>
        <option value="shortage">Stok Kurang</option>
        <option value="completed">Selesai</option>
      </select>
    </div>

    <?php if (hasPermission('sales_orders.create')): ?>
      <a href="index.php?page=sales-orders&action=create" class="btn btn-primary btn-so-create">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Buat SO Baru
      </a>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($success)): ?>
<div class="alert alert-success animate-in mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <div><?= $success ?></div>
</div>
<?php endif; ?>

<div class="so-table-wrap">
  <?php if (empty($salesOrders)): ?>
    <div class="empty-state">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-state-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
      <div class="empty-state-title">Belum ada data Sales Order</div>
      <p class="empty-state-desc">Silakan buat Sales Order baru melalui tombol di kanan atas.</p>
    </div>
  <?php else: ?>
  <table class="so-table" id="so-table">
    <thead>
      <tr>
        <th>No. Sales Order</th>
        <th>Pelanggan</th>
        <th>Tanggal Pembuatan</th>
        <th>Bahan & Kuantitas</th>
        <th>Dibuat Oleh</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($salesOrders as $so):
        $status = $so['status'];
        if ($status === 'pending') {
            if (!$so['is_stock_sufficient']) {
                $badgeClass = 'danger';
                $statusLabel = 'Stok Kurang';
                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>';
            } else {
                $badgeClass = 'pending';
                $statusLabel = 'Menunggu Picking';
                $statusIcon = '<circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 6v6l3 3" stroke-width="2"/>';
            }
        } else {
            $badgeClass = 'completed';
            $statusLabel = 'Selesai';
            $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
        }
      ?>
      <tr data-number="<?= strtolower(htmlspecialchars($so['so_number'])) ?>"
          data-customer="<?= strtolower(htmlspecialchars($so['customer'])) ?>"
          data-status="<?= $status === 'pending' && !$so['is_stock_sufficient'] ? 'shortage' : $status ?>">
        <td class="so-number-cell"><?= htmlspecialchars($so['so_number']) ?></td>
        <td class="so-customer"><?= htmlspecialchars($so['customer']) ?></td>
        <td><?= date('d M Y, H:i', strtotime($so['created_at'])) ?></td>
        <td class="so-items-cell">
          <?php foreach ($so['items'] as $item): ?>
            <span class="item-badge" title="<?= htmlspecialchars($item['name']) ?>">
              <?= htmlspecialchars($item['name']) ?>: <strong><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?></strong>
            </span>
          <?php endforeach; ?>
        </td>
        <td><?= htmlspecialchars($so['creator_name']) ?></td>
        <td>
          <span class="so-status-badge <?= $badgeClass ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-11"><?= $statusIcon ?></svg>
            <?= $statusLabel ?>
          </span>
          <?php if (!empty($so['completion_info'])): ?>
            <div class="so-completion-info">
              <strong>Diproses:</strong> <?= htmlspecialchars($so['completion_info']['picker_name']) ?><br/>
              <strong>Waktu:</strong> <?= date('d M, H:i', strtotime($so['completion_info']['completed_at'])) ?><br/>
              <span class="so-ref-badge">Ref: <?= htmlspecialchars($so['completion_info']['reference_no']) ?></span>
            </div>
          <?php endif; ?>
        </td>

      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div id="so-empty-filter" class="so-empty-filter-card d-none">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-state-icon-sm"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
  <div class="so-empty-filter-title">Tidak ada hasil untuk pencarian ini</div>
</div>

<script>
function filterSoTable(query) {
  const searchVal = (typeof query === 'string' ? query : document.getElementById('so-search').value).toLowerCase();
  const statusVal = document.getElementById('so-filter-status').value;
  const rows = document.querySelectorAll('#so-table tbody tr');
  let visible = 0;

  rows.forEach(row => {
    const num = row.dataset.number || '';
    const cust = row.dataset.customer || '';
    const status = row.dataset.status || '';

    const matchSearch = !searchVal || num.includes(searchVal) || cust.includes(searchVal);
    const matchStatus = statusVal === 'all' || status === statusVal;

    if (matchSearch && matchStatus) {
      row.classList.remove('d-none');
      visible++;
    } else {
      row.classList.add('d-none');
    }
  });

  const emptyEl = document.getElementById('so-empty-filter');
  if (emptyEl) {
    if (visible === 0) {
      emptyEl.classList.remove('d-none');
    } else {
      emptyEl.classList.add('d-none');
    }
  }
}
</script>
