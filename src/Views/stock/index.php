<?php

?>

<?php
$totalItems   = count($stockItems);
$lowItems     = array_filter($stockItems, fn($i) => (int)$i['total_stock'] <= (int)$i['min_stock']);
$critItems    = array_filter($stockItems, fn($i) => (int)$i['total_stock'] === 0);
$healthyItems = $totalItems - count($lowItems);
?>

<div class="stock-view-header justify-start mb-24">
  <div class="stock-search-bar">
    <div class="stock-search-wrap">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
      <input type="text" class="stock-search-input" id="stock-search" placeholder="Cari nama / kode bahan…" oninput="filterStockTable(this.value)">
    </div>
    <select class="form-control so-filter-select" id="stock-filter-status" onchange="filterStockTable()">
      <option value="all">Semua Status</option>
      <option value="ok">Stok Aman</option>
      <option value="low">Stok Menipis</option>
      <option value="empty">Stok Kosong</option>
    </select>
  </div>
</div>

<div class="stock-summary-row">
  <div class="stock-summary-card border-top-primary">
    <div class="stock-summary-label">Total Jenis Bahan</div>
    <div class="stock-summary-value text-primary"><?= $totalItems ?></div>
    <div class="stock-summary-sub">Bahan cetak aktif</div>
  </div>
  <div class="stock-summary-card border-top-success">
    <div class="stock-summary-label">Stok Aman</div>
    <div class="stock-summary-value text-success"><?= $healthyItems ?></div>
    <div class="stock-summary-sub">Stok mencukupi</div>
  </div>
  <div class="stock-summary-card border-top-warning">
    <div class="stock-summary-label">Stok Menipis</div>
    <div class="stock-summary-value text-warning"><?= count($lowItems) ?></div>
    <div class="stock-summary-sub">Perlu dipesan ulang</div>
  </div>
  <div class="stock-summary-card border-top-danger">
    <div class="stock-summary-label">Stok Kosong</div>
    <div class="stock-summary-value text-danger"><?= count($critItems) ?></div>
    <div class="stock-summary-sub">Stok habis total</div>
  </div>
</div>

<div class="stock-table-wrap">
  <?php if (empty($stockItems)): ?>
    <div class="empty-state">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-state-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
      <div class="empty-state-title">Tidak ada data barang</div>
    </div>
  <?php else: ?>
  <table class="stock-table" id="stock-table">
    <thead>
      <tr>
        <th>Nama Bahan Cetak</th>
        <th>Kategori</th>
        <th>Jumlah Stok</th>
        <th>Batas Reorder</th>
        <th>Satuan Ukur</th>
        <th>Status Stok</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stockItems as $idx => $item):
        $stock    = (int)$item['total_stock'];
        $min      = (int)$item['min_stock'];
        $pct      = $min > 0 ? min(100, round(($stock / max($min * 2, 1)) * 100)) : ($stock > 0 ? 100 : 0);

        if ($stock === 0) {
            $statusClass = 'low';
            $statusLabel = 'Kosong';
            $statusIcon  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            $barColor    = 'var(--danger)';
        } elseif ($stock <= $min) {
            $statusClass = 'warn';
            $statusLabel = 'Menipis';
            $statusIcon  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>';
            $barColor    = 'var(--warning)';
        } else {
            $statusClass = 'ok';
            $statusLabel = 'Aman';
            $statusIcon  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>';
            $barColor    = 'var(--success)';
        }

        $catColor = $item['cat_color'] ?? '#6366f1';
        $catBg    = $catColor . '22';
      ?>
      <tr style="animation-delay: <?= $idx * 25 ?>ms;"
          data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>"
          data-sku="<?= strtolower(htmlspecialchars($item['sku'])) ?>"
          data-status="<?= $statusClass ?>">
        <td>
          <div class="stock-name-cell">
            <span class="stock-item-name"><?= htmlspecialchars($item['name']) ?></span>
            <span class="stock-item-sku"><?= htmlspecialchars($item['sku']) ?></span>
          </div>
        </td>
        <td>
          <span class="cat-pill" style="background:<?= $catBg ?>; color:<?= $catColor ?>; border: 1px solid <?= $catColor ?>33;">
            <?= htmlspecialchars($item['cat_name']) ?>
          </span>
        </td>
        <td>
          <div class="stock-level-cell">
            <span class="stock-qty" style="color:<?= $barColor ?>;"><?= number_format($stock) ?></span>
            <div class="stock-bar-wrap">
              <div class="stock-bar-fill" style="width:<?= $pct ?>%; background:<?= $barColor ?>;"></div>
            </div>
          </div>
        </td>
        <td class="text-muted font-600"><?= number_format($min) ?></td>
        <td class="text-muted"><?= htmlspecialchars($item['unit']) ?></td>
        <td>
          <span class="stock-status-badge <?= $statusClass ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-11"><?= $statusIcon ?></svg>
            <?= $statusLabel ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div id="stock-empty-filter" class="so-empty-filter-card d-none">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-state-icon-sm"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
  <div class="so-empty-filter-title">Tidak ada hasil untuk pencarian ini</div>
</div>

<script>
function filterStockTable(query) {
  const searchVal = (typeof query === 'string' ? query : document.getElementById('stock-search').value).toLowerCase();
  const statusVal = document.getElementById('stock-filter-status').value;
  const rows = document.querySelectorAll('#stock-table tbody tr');
  let visible = 0;

  rows.forEach(row => {
    const name   = row.dataset.name   || '';
    const sku    = row.dataset.sku    || '';
    const status = row.dataset.status || '';

    const matchSearch = !searchVal || name.includes(searchVal) || sku.includes(searchVal);
    let matchStatus = true;
    if (statusVal === 'ok')    matchStatus = status === 'ok';
    if (statusVal === 'low')   matchStatus = status === 'warn';
    if (statusVal === 'empty') matchStatus = status === 'low';

    if (matchSearch && matchStatus) {
      row.classList.remove('d-none');
      visible++;
    } else {
      row.classList.add('d-none');
    }
  });

  const emptyEl = document.getElementById('stock-empty-filter');
  if (emptyEl) {
    if (visible === 0) {
      emptyEl.classList.remove('d-none');
    } else {
      emptyEl.classList.add('d-none');
    }
  }
}
</script>
