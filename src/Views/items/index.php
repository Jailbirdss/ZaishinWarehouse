<?php if (!empty($_GET['saved'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Data barang berhasil disimpan.
</div>
<?php endif; ?>

<?php
$totalCount = count($items);
$totalCategories = count($categories);

$units = array_unique(array_filter(array_map(fn($i) => trim($i['unit']), $items)));
$totalUnits = count($units);
$unitsList = implode(', ', array_slice($units, 0, 4));
?>

<div class="kpi-grid mb-24 kpi-grid-3">
  <div class="kpi-card primary kpi-card-row">
    <div class="kpi-icon kpi-icon-items primary">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $totalCount ?></div>
      <div class="kpi-label kpi-label-sm">Total Bahan Cetak</div>
    </div>
  </div>
  <div class="kpi-card warning kpi-card-row">
    <div class="kpi-icon kpi-icon-items warning">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $totalCategories ?></div>
      <div class="kpi-label kpi-label-sm">Kategori Terdaftar</div>
    </div>
  </div>
  <div class="kpi-card success kpi-card-row">
    <div class="kpi-icon kpi-icon-items success">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2 2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $totalUnits ?></div>
      <div class="kpi-label kpi-label-sm">Satuan: <?= htmlspecialchars($unitsList) ?></div>
    </div>
  </div>
</div>

<div class="flex-between mb-6">
  <form method="GET" action="index.php" class="items-filter-form">
    <input type="hidden" name="page" value="items"/>
    <div class="position-relative flex-1">
      <svg class="filter-search-icon"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
      </svg>
      <input name="q" type="text" class="form-control pl-40" placeholder="Cari nama atau SKU..."
        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
    </div>
    <select name="cat" class="form-control w-180px" onchange="this.form.submit()">
      <option value="">Semua Kategori</option>
      <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>" <?= (($_GET['cat'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
  </form>
  <button class="btn btn-primary" onclick="openModal('modal-item')">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Tambah Barang
  </button>
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>SKU</th><th>Nama Barang</th><th>Kategori</th>
          <th>Satuan</th><th>Stok</th><th>Min. Stok</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
        <tr><td colspan="8" class="text-center text-muted p-40">Tidak ada barang ditemukan</td></tr>
        <?php else: foreach ($items as $item): ?>
        <?php
          $status = 'normal';
          if ($item['total_stock'] == 0) $status = 'empty';
          elseif ($item['total_stock'] <= $item['min_stock']) $status = 'low';
        ?>
        <tr>
          <td><span class="sku-badge"><?= htmlspecialchars($item['sku']) ?></span></td>
          <td class="font-500-main">
            <?= htmlspecialchars($item['name']) ?>
            <?php
              $slots = Item::getStorageSlots($db, $item['id']);
              if (!empty($slots)):
            ?>
            <div class="item-loc-info">
              Lokasi:
              <?php
                $locs = [];
                foreach ($slots as $sl) {
                    $locs[] = htmlspecialchars($sl['zone_name'] . ' - ' . $sl['rack_code'] . '-' . $sl['slot_number'] . ' (' . $sl['quantity'] . ')');
                }
                echo implode(', ', $locs);
              ?>
            </div>
            <?php else: ?>
            <div class="item-no-loc">
              Belum ditempatkan
            </div>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge" style="background:<?= htmlspecialchars($item['cat_color']) ?>22;color:<?= htmlspecialchars($item['cat_color']) ?>">
              <?= htmlspecialchars($item['cat_name']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($item['unit']) ?></td>
          <td class="font-bold-main"><?= number_format($item['total_stock']) ?></td>
          <td class="text-muted"><?= $item['min_stock'] ?></td>
          <td>
            <?php if ($status === 'empty'): ?>
            <span class="badge badge-danger">Habis</span>
            <?php elseif ($status === 'low'): ?>
            <span class="badge badge-warning">Rendah</span>
            <?php else: ?>
            <span class="badge badge-success">Normal</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="d-flex gap-6">
              <button class="btn btn-secondary btn-sm" onclick='editItem(<?= json_encode($item) ?>)'>Edit</button>
              <a href="index.php?page=items&action=delete&id=<?= $item['id'] ?>"
                class="btn btn-sm btn-danger-soft"
                onclick="deleteItem(event, this.href, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>')">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="modal-item">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title" id="modal-item-title">Tambah Barang</div>
    </div>
    <form method="POST" action="index.php?page=items" id="form-item">
      <div class="modal-body">
        <input type="hidden" name="id" id="item-id"/>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">SKU *</label>
            <input name="sku" id="item-sku" class="form-control" placeholder="KRT-001" required/>
          </div>
          <div class="form-group">
            <label class="form-label">Satuan *</label>
            <input name="unit" id="item-unit" class="form-control" placeholder="rim, botol, pcs..." required/>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Barang *</label>
          <input name="name" id="item-name" class="form-control" placeholder="Nama lengkap barang" required/>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Kategori *</label>
            <select name="category_id" id="item-cat" class="form-control" required>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Stok Minimum *</label>
            <input name="min_stock" id="item-min" type="number" class="form-control" min="1" value="10" required/>
          </div>
        </div>
        <div id="initial-stock-section" class="grid-2">
          <div class="form-group">
            <label class="form-label">Stok Awal (Opsional)</label>
            <input name="initial_stock" id="item-initial-stock" type="number" class="form-control" min="0" placeholder="0"/>
          </div>
          <div class="form-group">
            <label class="form-label">Slot Rak Tujuan</label>
            <select name="initial_slot_id" id="item-initial-slot" class="form-control">
              <option value="">-- Pilih Slot Rak --</option>
              <?php foreach ($freeSlots as $slot): ?>
              <option value="<?= $slot['id'] ?>">
                <?= htmlspecialchars($slot['zone_name'] . ' - ' . $slot['rack_code'] . ' - Slot ' . $slot['slot_number']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" id="item-desc" class="form-control" rows="3" placeholder="Deskripsi barang (opsional)"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-item');resetForm()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Barang</button>
      </div>
    </form>
  </div>
</div>

<?php if ($editItem): ?>
<script>
window.addEventListener('DOMContentLoaded', () => editItem(<?= json_encode($editItem) ?>));
</script>
<?php endif; ?>

<script>
function editItem(item) {
  document.getElementById('modal-item-title').textContent = 'Edit Barang';
  document.getElementById('item-id').value   = item.id;
  document.getElementById('item-sku').value  = item.sku;
  document.getElementById('item-name').value = item.name;
  document.getElementById('item-cat').value  = item.category_id;
  document.getElementById('item-unit').value = item.unit;
  document.getElementById('item-min').value  = item.min_stock;
  document.getElementById('item-desc').value = item.description || '';
  document.getElementById('initial-stock-section').classList.add('d-none');
  openModal('modal-item');
}
function resetForm() {
  document.getElementById('form-item').reset();
  document.getElementById('item-id').value = '';
  document.getElementById('modal-item-title').textContent = 'Tambah Barang';
  document.getElementById('initial-stock-section').classList.remove('d-none');
}
async function deleteItem(event, url, name) {
  event.preventDefault();
  const confirmed = await showConfirm(
    `Apakah Anda yakin ingin menghapus barang "${name}"? Tindakan ini tidak dapat dibatalkan.`,
    'Hapus Barang',
    'Ya, Hapus',
    true
  );
  if (confirmed) {
    window.location.href = url;
  }
}
</script>
