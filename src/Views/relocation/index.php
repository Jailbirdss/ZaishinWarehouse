<?php

?>
<div class="container-960">

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger mb-4">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div><?= $error ?></div>
  </div>
  <?php endif; ?>

  <?php if (!empty($successMsg)): ?>
  <div class="alert alert-success mb-4">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><?= $successMsg ?></div>
  </div>
  <?php endif; ?>

  <div class="grid-2">

    <div class="card">
      <div class="card-header">
        <div class="card-title">Formulir Mutasi Stok</div>
      </div>
      <div class="card-body">
        <form method="POST" action="index.php?page=relocation" onsubmit="return validateForm()">
          <div class="form-group">
            <label class="form-label">Pilih Barang *</label>
            <select id="item_id" name="item_id" class="form-control" onchange="onItemChange()" required>
              <option value="">-- Pilih Barang --</option>
              <?php foreach ($items as $item): ?>
                <option value="<?= $item['id'] ?>" data-unit="<?= htmlspecialchars($item['unit']) ?>">
                  <?= htmlspecialchars($item['name']) ?> [<?= htmlspecialchars($item['sku']) ?>]
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Pilih Slot Asal *</label>
            <select id="src_slot_id" name="src_slot_id" class="form-control" onchange="onSourceSlotChange()" required disabled>
              <option value="">-- Pilih Barang Terlebih Dahulu --</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Pilih Slot Tujuan *</label>
            <select id="dst_slot_id" name="dst_slot_id" class="form-control" required>
              <option value="">-- Pilih Slot Tujuan --</option>
              <?php foreach ($freeSlots as $slot): ?>
                <option value="<?= $slot['id'] ?>">
                  <?= htmlspecialchars($slot['zone_name']) ?> &middot; Rak <?= htmlspecialchars($slot['rack_code']) ?> &middot; Slot <?= htmlspecialchars($slot['slot_number']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <p class="fs-11 text-muted mt-4">Hanya menampilkan slot rak yang berstatus kosong (Free).</p>
          </div>

          <div class="form-group">
            <label class="form-label">Jumlah Pindah *</label>
            <div class="d-flex align-center gap-8">
              <input type="number" id="quantity" name="quantity" class="form-control" min="1" placeholder="Masukkan kuantitas" required disabled/>
              <span id="unit-label" class="relocation-unit-label">—</span>
            </div>
            <p id="max-qty-label" class="fs-11 text-muted mt-4 d-none">Maksimum yang dapat dipindahkan: <strong id="max-qty-val">0</strong></p>
          </div>

          <div class="form-group">
            <label class="form-label">Catatan Mutasi</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Konsolidasi rak, merapikan letak barang..."></textarea>
          </div>

          <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-primary flex-1 justify-center">Kirim Mutasi</button>
            <a href="index.php?page=stock" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>

    <div class="d-flex flex-column gap-20">
      <div class="card card-relocation-guide">
        <div class="card-header border-bottom-none">
          <div class="card-title text-primary d-flex align-center gap-8">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Panduan Mutasi Internal
          </div>
        </div>
        <div class="card-body card-body-relocation-guide">
          <p class="mb-12">Fitur ini digunakan oleh petugas gudang untuk memindahkan inventaris antar-slot secara teratur demi menjaga efisiensi ruang rak.</p>
          <ul class="relocation-guide-list">
            <li>Sistem akan mendebit stok di slot asal dan mengkreditnya ke slot tujuan.</li>
            <li>Jika kuantitas di slot asal habis dipindahkan, status slot asal otomatis kembali menjadi <strong>Kosong (Free)</strong>.</li>
            <li>Log perpindahan akan dicatat berpasangan (Outbound &amp; Inbound) di laporan aktivitas demi integritas riwayat mutasi.</li>
          </ul>
        </div>
      </div>

      <div class="card d-none" id="preview-card">
        <div class="card-header">
          <div class="card-title">Posisi Stok Saat Ini</div>
        </div>
        <div class="card-body p-0">
          <div class="table-wrapper table-wrapper-plain">
            <table class="w-100">
              <thead>
                <tr>
                  <th class="th-relocation">Slot Rak</th>
                  <th class="th-relocation-right">Kuantitas</th>
                </tr>
              </thead>
              <tbody id="preview-tbody">

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

const stocks = <?= json_encode($stocks) ?>;

function onItemChange() {
  const itemIdSelect = document.getElementById('item_id');
  const itemId = parseInt(itemIdSelect.value);
  const srcSlotSelect = document.getElementById('src_slot_id');
  const qtyInput = document.getElementById('quantity');
  const unitLabel = document.getElementById('unit-label');
  const maxLabel = document.getElementById('max-qty-label');
  const previewCard = document.getElementById('preview-card');
  const previewTbody = document.getElementById('preview-tbody');

  srcSlotSelect.innerHTML = '<option value="">-- Pilih Slot Asal --</option>';
  qtyInput.value = '';
  qtyInput.disabled = true;
  maxLabel.classList.add('d-none');
  previewCard.classList.add('d-none');
  previewTbody.innerHTML = '';

  if (!itemId) {
    srcSlotSelect.disabled = true;
    unitLabel.textContent = '—';
    return;
  }

  const selectedOpt = itemIdSelect.options[itemIdSelect.selectedIndex];
  const unit = selectedOpt.dataset.unit || '';
  unitLabel.textContent = unit;

  const matchingStocks = stocks.filter(s => parseInt(s.item_id) === itemId);

  if (matchingStocks.length > 0) {
    srcSlotSelect.disabled = false;
    previewCard.classList.remove('d-none');

    matchingStocks.forEach(s => {

      const opt = document.createElement('option');
      opt.value = s.slot_id;
      opt.dataset.qty = s.quantity;
      opt.textContent = `${s.rack_code}-S${s.slot_number} (Tersedia: ${s.quantity} ${unit})`;
      srcSlotSelect.appendChild(opt);

      previewTbody.innerHTML += `
        <tr>
          <td class="td-relocation">
            <span class="font-bold-main-mono">${s.rack_code}-S${s.slot_number}</span> &middot; ${s.zone_name}
          </td>
          <td class="td-relocation-qty">
            ${s.quantity} ${unit}
          </td>
        </tr>`;
    });
  } else {
    srcSlotSelect.disabled = true;
    srcSlotSelect.innerHTML = '<option value="">-- Stok Habis / Kosong --</option>';
  }
}

function onSourceSlotChange() {
  const srcSlotSelect = document.getElementById('src_slot_id');
  const qtyInput = document.getElementById('quantity');
  const maxLabel = document.getElementById('max-qty-label');
  const maxValSpan = document.getElementById('max-qty-val');

  const opt = srcSlotSelect.options[srcSlotSelect.selectedIndex];
  if (!opt || !opt.value) {
    qtyInput.value = '';
    qtyInput.disabled = true;
    maxLabel.classList.add('d-none');
    return;
  }

  const qty = parseInt(opt.dataset.qty) || 0;
  qtyInput.disabled = false;
  qtyInput.value = qty;
  qtyInput.max = qty;

  maxValSpan.textContent = qty;
  maxLabel.classList.remove('d-none');
}

function validateForm() {
  const src = document.getElementById('src_slot_id').value;
  const dst = document.getElementById('dst_slot_id').value;
  const qtyInput = document.getElementById('quantity');
  const qty = parseInt(qtyInput.value) || 0;
  const maxQty = parseInt(document.getElementById('max-qty-val').textContent) || 0;

  if (src === dst) {
    showToast('Slot asal dan tujuan tidak boleh sama!', 'error');
    return false;
  }

  if (qty <= 0) {
    showToast('Jumlah pindah harus lebih dari 0!', 'error');
    return false;
  }

  if (qty > maxQty) {
    showToast(`Jumlah pindah melebihi stok tersedia (${maxQty})!`, 'error');
    return false;
  }

  return true;
}
</script>
