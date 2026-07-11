<?php

?>

<div class="form-card">

  <div class="so-back-container mb-20">
    <a href="index.php?page=sales-orders" class="btn so-back-btn">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14 text-muted"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Kembali ke Daftar SO
    </a>
  </div>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger animate-in mb-20">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
  <?php endif; ?>

  <form method="POST" action="index.php?page=sales-orders&action=create" onsubmit="return validateForm()">
    <div class="card card-so-form">

      <div class="card-header card-header-so">
        <div class="card-title card-title-so">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18 text-primary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          Detail Pelanggan / Pemesan
        </div>
      </div>

      <div class="card-body p-24">
        <div class="form-group mb-0">
          <label class="form-label form-label-customer" for="customer">Nama Pelanggan / Perusahaan</label>
          <div class="position-relative">
            <span class="customer-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
            <input type="text" id="customer" name="customer" class="form-control customer-input" placeholder="Masukkan nama PT, CV, atau toko pemesan..." required>
          </div>
        </div>
      </div>

      <div class="card-header card-header-so-items">
        <div class="card-title card-title-so">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18 text-primary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          Daftar Bahan Cetak Dipesan
        </div>
      </div>

      <div class="card-body p-24">

        <div class="items-header">
          <div class="items-header-label">Nama Bahan Cetak</div>
          <div class="items-header-label text-center">Jumlah Kuantitas</div>
          <div class="items-header-label text-center">Satuan</div>
          <div></div>
        </div>

        <div id="items-container">

          <div class="item-row" id="row-0">
            <div>
              <select name="items[0][item_id]" class="form-control select-input-icon item-select" onchange="updateRowUnit(this, 0)" required>
                <option value="" disabled selected>-- Pilih Bahan Cetak --</option>
                <?php foreach ($itemsList as $item): ?>
                  <option value="<?= $item['id'] ?>" data-unit="<?= htmlspecialchars($item['unit']) ?>">
                    <?= htmlspecialchars($item['name']) ?> [<?= htmlspecialchars($item['sku']) ?>]
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <input type="number" name="items[0][quantity]" class="form-control num-input-bold" min="1" placeholder="0" required>
            </div>

            <div class="d-flex justify-center">
              <span class="unit-badge" id="unit-label-0">-</span>
            </div>

            <div class="d-flex justify-center">
              <button type="button" class="btn-remove-row invisible" onclick="removeRow(0)">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-secondary btn-sm btn-add-row" onclick="addRow()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Tambah Baris Bahan
        </button>
      </div>

      <div class="card-footer-so">
        <a href="index.php?page=sales-orders" class="btn btn-outline btn-cancel-so">Batal</a>
        <button type="submit" class="btn btn-primary btn-save-so">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          Simpan Sales Order
        </button>
      </div>

    </div>
  </form>
</div>

<script>
let rowCount = 1;
const itemsList = <?= json_encode($itemsList) ?>;

function addRow() {
  const container = document.getElementById('items-container');
  const index = rowCount;
  rowCount++;

  let optionsHtml = '<option value="" disabled selected>-- Pilih Bahan Cetak --</option>';
  itemsList.forEach(item => {
    optionsHtml += `<option value="${item.id}" data-unit="${escHtml(item.unit)}">${escHtml(item.name)} [${escHtml(item.sku)}]</option>`;
  });

  const rowHtml = `
    <div class="item-row" id="row-${index}">
      <div>
        <select name="items[${index}][item_id]" class="form-control select-input-icon item-select" onchange="updateRowUnit(this, ${index})" required>
          ${optionsHtml}
        </select>
      </div>

      <div>
        <input type="number" name="items[${index}][quantity]" class="form-control num-input-bold" min="1" placeholder="0" required>
      </div>

      <div class="d-flex justify-center">
        <span class="unit-badge" id="unit-label-${index}">-</span>
      </div>

      <div class="d-flex justify-center">
        <button type="button" class="btn-remove-row" onclick="removeRow(${index})">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>
  `;

  container.insertAdjacentHTML('beforeend', rowHtml);

  if (typeof convertNativeSelects === 'function') {
    convertNativeSelects();
  }

  updateRemoveButtonsVisibility();
  showToast("Baris bahan cetak ditambahkan.", "info");
}

function removeRow(index) {
  const row = document.getElementById('row-' + index);
  if (row) {
    row.style.animation = "rowFadeIn 0.2s ease reverse forwards";
    setTimeout(() => {
      row.remove();
      updateRemoveButtonsVisibility();
    }, 200);
  }
}

function updateRowUnit(selectEl, index) {
  const selectedOption = selectEl.options[selectEl.selectedIndex];
  const unit = selectedOption.getAttribute('data-unit') || '-';
  const label = document.getElementById('unit-label-' + index);
  if (label) {
    label.textContent = unit;
  }
}

function updateRemoveButtonsVisibility() {
  const rows = document.querySelectorAll('.item-row');
  rows.forEach((row, idx) => {
    const btn = row.querySelector('.btn-remove-row');
    if (btn) {
      btn.style.visibility = rows.length > 1 ? 'visible' : 'hidden';
    }
  });
}

function validateForm() {
  const selects = document.querySelectorAll('.item-select');
  const selectedValues = [];
  let hasDuplicates = false;

  selects.forEach(select => {
    const val = select.value;
    if (val) {
      if (selectedValues.includes(val)) {
        hasDuplicates = true;
      }
      selectedValues.push(val);
    }
  });

  if (hasDuplicates) {
    showToast("Ada bahan cetak yang duplikat dipilih. Silakan gabungkan baris yang sama.", "error");
    return false;
  }

  return true;
}

function escHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
