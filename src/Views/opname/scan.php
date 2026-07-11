<?php

?>

<div class="mb-20">
  <a href="index.php?page=opname" class="btn btn-secondary btn-sm d-inline-flex">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali ke List Opname
  </a>
</div>

<div class="grid-2">

  <div class="scan-card">
    <div class="scan-header-title">Operasional Audit Fisik Sesi Opname</div>

    <div class="scan-progress-steps">
      <div class="scan-step-indicator active" id="ind-op-1">1. Scan Rak</div>
      <div class="scan-step-indicator" id="ind-op-2">2. Scan Barang</div>
      <div class="scan-step-indicator" id="ind-op-3">3. Input Qty</div>
    </div>

    <div class="scanner-instruction" id="op-instruction-text">
      <span class="step-bubble">1</span>
      Pindai/Scan QR Code Rak Gudang Target
    </div>

    <div id="opname-scan-container">
      <div class="qr-method-tabs mb-12">
        <button type="button" class="tab-btn active" id="tab-opname-camera" onclick="switchOpnameScanMethod('camera')">Kamera Live</button>
        <button type="button" class="tab-btn" id="tab-opname-file" onclick="switchOpnameScanMethod('file')">Unggah Gambar QR</button>
      </div>

      <div id="opname-camera-container" class="tab-content-fade">
        <div class="qr-camera-zone p-24" id="opname-camera-placeholder" onclick="toggleOpnameScanner()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-36"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
          <div class="zone-text zone-text-opname">Aktifkan Kamera Pemindai</div>
        </div>
        <div id="qr-reader-opname" class="qr-reader-opname-box d-none"></div>
        <button type="button" class="btn btn-danger btn-sm d-none w-100 justify-center mb-12" id="btn-stop-opname-camera" onclick="toggleOpnameScanner()">Matikan Kamera</button>
      </div>

      <div id="opname-file-container" class="tab-content-fade d-none">
        <div class="qr-drag-drop-zone p-24" id="opname-drop-zone">
          <input type="file" id="opname-file-input" accept="image/*" class="d-none"/>
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-36"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          <div class="zone-text zone-text-opname">Pilih File Gambar QR</div>
        </div>
      </div>

      <div id="qr-reader-temp" class="d-none"></div>
    </div>

    <div id="opname-input-panel" class="opname-input-panel-box d-none panel-fade-in">
      <div class="opname-input-label">Input Hasil Perhitungan</div>
      <div class="opname-input-item-display" id="input-item-display">Nama Barang</div>

      <div class="opname-input-slot-display">
        Lokasi Slot Rak: <strong id="input-slot-display" class="font-monospace">ZA-R01-S3</strong>
      </div>

      <div class="form-group">
        <label class="form-label font-700">Jumlah Fisik yang Ditemukan</label>
        <input type="number" id="physical-qty-input" class="form-control" placeholder="Ketik jumlah fisik barang aktual di rak" min="0" />
      </div>

      <div class="d-flex gap-10">
        <button type="button" class="btn btn-secondary btn-sm flex-1 justify-center" onclick="resetOpnameWorkflow()">Batal</button>
        <button type="button" class="btn btn-success btn-sm flex-1-5 justify-center" onclick="submitOpnameItem()">Simpan Hasil</button>
      </div>
    </div>

    <div class="opname-active-loc-footer">
      <div>Sesi: <span class="font-monospace font-700"><?= htmlspecialchars($activeOpname['opname_no']) ?></span></div>
      <div>Zona / Rak Aktif: <span id="opname-active-loc" class="font-600 text-secondary">-</span></div>
      <div>Item Terpilih: <span id="opname-active-item" class="font-600 text-secondary">-</span></div>
    </div>

  </div>

  <div class="card opname-checklist-card">
    <div class="card-header p-18-20-0">
      <div class="card-title opname-checklist-title">
        <span>Checklist Slot untuk Diaudit</span>
        <span class="badge badge-primary" id="pending-count-badge">0 pending</span>
      </div>
    </div>
    <div class="card-body opname-checklist-body">
      <div id="pending-audit-list">
        <?php foreach ($details as $det): ?>
          <div class="pending-audit-item" id="pending-item-row-<?= $det['rack_slot_id'] ?>" data-slot-code="SLOT-<?= $det['rack_code'] ?>-S<?= $det['slot_number'] ?>" data-sku="<?= $det['sku'] ?>" data-verified="<?= $det['status'] === 'verified' ? 'true' : 'false' ?>">
            <div>
              <div class="font-700 text-main"><?= htmlspecialchars($det['rack_code']) ?> · Slot <?= $det['slot_number'] ?></div>
              <div class="fs-11-5 text-muted"><?= htmlspecialchars($det['item_name']) ?> [<?= htmlspecialchars($det['sku']) ?>]</div>
            </div>
            <div>
              <?php if ($det['status'] === 'verified'): ?>
                <span class="badge badge-success badge-pill-padded">Cocok: <?= $det['physical_quantity'] ?></span>
              <?php else: ?>
                <span class="badge badge-muted badge-pill-padded">Pending</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const OPNAME_ID = <?= $activeOpname['id'] ?>;
const USER_ROLE = '<?= $_SESSION['user_role'] ?? '' ?>';

if (typeof playSuccessFeedback !== 'function') {
  window.playSuccessFeedback = function() { console.log("Success audio-haptic feedback mock"); };
}
if (typeof playErrorFeedback !== 'function') {
  window.playErrorFeedback = function() { console.log("Error audio-haptic feedback mock"); };
}

let opnameScannerOpen = false;
let opnameScanMethod = 'camera';

let currentStep = 1;
let selectedSlotId = null;
let selectedSlotCode = null;
let selectedItemId = null;
let selectedItemName = null;
let selectedItemSku = null;

document.addEventListener('DOMContentLoaded', () => {
  updatePendingBadge();

  const opDropZone = document.getElementById('opname-drop-zone');
  const opFileInput = document.getElementById('opname-file-input');

  if (opDropZone) {
    opDropZone.addEventListener('click', () => opFileInput.click());

    opDropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      opDropZone.classList.add('dragover');
    });

    ['dragleave', 'dragend'].forEach(type => {
      opDropZone.addEventListener(type, () => opDropZone.classList.remove('dragover'));
    });

    opDropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      opDropZone.classList.remove('dragover');
      if (e.dataTransfer.files.length > 0) {
        scanUploadedOpnameFile(e.dataTransfer.files[0]);
      }
    });
  }

  if (opFileInput) {
    opFileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        scanUploadedOpnameFile(e.target.files[0]);
      }
    });
  }
});

function updatePendingBadge() {
  const pendingRows = document.querySelectorAll('.pending-audit-item[data-verified="false"]').length;
  const badge = document.getElementById('pending-count-badge');
  if (badge) {
    badge.textContent = pendingRows + ' pending';
    if (pendingRows === 0) {
      badge.className = 'badge badge-success';
    } else {
      badge.className = 'badge badge-primary';
    }
  }
}

function switchOpnameScanMethod(method) {
  stopQRScanner();
  opnameScannerOpen = false;
  document.getElementById('qr-reader-opname').classList.add('d-none');
  document.getElementById('btn-stop-opname-camera').classList.add('d-none');
  document.getElementById('opname-camera-placeholder').classList.remove('d-none');

  opnameScanMethod = method;
  if (method === 'camera') {
    document.getElementById('tab-opname-camera').classList.add('active');
    document.getElementById('tab-opname-file').classList.remove('active');
    document.getElementById('opname-camera-container').classList.remove('d-none');
    document.getElementById('opname-file-container').classList.add('d-none');
  } else {
    document.getElementById('tab-opname-camera').classList.remove('active');
    document.getElementById('tab-opname-file').classList.add('active');
    document.getElementById('opname-camera-container').classList.add('d-none');
    document.getElementById('opname-file-container').classList.remove('d-none');
  }
}

function toggleOpnameScanner() {
  const reader = document.getElementById('qr-reader-opname');
  const placeholder = document.getElementById('opname-camera-placeholder');
  const stopBtn = document.getElementById('btn-stop-opname-camera');

  if (!opnameScannerOpen) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showToast("Akses kamera ditolak (insecure context).", "error");
      return;
    }

    placeholder.classList.add('d-none');
    reader.classList.remove('d-none');
    stopBtn.classList.remove('d-none');

    startQRScanner('qr-reader-opname', (code) => {
      processOpnameScanResult(code);

      reader.classList.add('d-none');
      stopBtn.classList.add('d-none');
      placeholder.classList.remove('d-none');
      opnameScannerOpen = false;
    }, (err) => {
      reader.classList.add('d-none');
      stopBtn.classList.add('d-none');
      placeholder.classList.remove('d-none');
      opnameScannerOpen = false;
    });
    opnameScannerOpen = true;
  } else {
    stopQRScanner();
    reader.classList.add('d-none');
    stopBtn.classList.add('d-none');
    placeholder.classList.remove('d-none');
    opnameScannerOpen = false;
  }
}

function scanUploadedOpnameFile(file) {
  if (!file.type.startsWith('image/')) {
    showToast("File harus berupa gambar!", "error");
    return;
  }

  const html5Qr = new Html5Qrcode("qr-reader-temp");
  showToast("Membaca gambar QR...", "info");

  html5Qr.scanFile(file, true)
    .then(decodedText => {
      processOpnameScanResult(decodedText);
      showToast("QR Code berhasil dibaca!", "success");
      html5Qr.clear();
    })
    .catch(err => {
      console.warn(err);
      showToast("Gagal membaca QR. Pastikan gambar jelas.", "error");
      playErrorFeedback();
      html5Qr.clear();
    });
}

function processOpnameScanResult(scannedText) {
  let codeStr = scannedText.trim();

  try {
    const parsed = JSON.parse(scannedText);
    if (parsed && parsed.code) {
      codeStr = parsed.code.trim();
    }
  } catch (e) {}

  if (currentStep === 1) {
    verifyScannedRack(codeStr);
  } else if (currentStep === 2) {
    verifyScannedItem(codeStr);
  }
}

function verifyScannedRack(codeStr) {
  fetch(BASE_URL + `/api/opname.php?opname_id=${OPNAME_ID}&type=verify_rack&scanned_code=${encodeURIComponent(codeStr)}`)
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        showToast(data.error, "error");
        playErrorFeedback();
      } else {
        selectedSlotId = data.slot_id;
        selectedSlotCode = data.rack_code + ' · Slot ' + data.slot_number;

        document.getElementById('ind-op-1').className = 'scan-step-indicator completed';
        document.getElementById('ind-op-2').className = 'scan-step-indicator active';
        document.getElementById('opname-active-loc').textContent = selectedSlotCode;

        currentStep = 2;
        document.getElementById('op-instruction-text').innerHTML = `
          <span class="step-bubble">2</span>
          Pindai/Scan QR Code Barang di dalam Slot ${selectedSlotCode}
        `;
        showToast("Rak terverifikasi! Silakan scan barang.", "success");
        playSuccessFeedback();
      }
    })
    .catch(() => {
      showToast("Koneksi gagal saat memverifikasi rak", "error");
      playErrorFeedback();
    });
}

function verifyScannedItem(codeStr) {
  fetch(BASE_URL + `/api/opname.php?opname_id=${OPNAME_ID}&type=verify_item&scanned_code=${encodeURIComponent(codeStr)}&slot_id=${selectedSlotId}`)
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        showToast(data.error, "error");
        playErrorFeedback();
      } else {
        selectedItemId = data.item_id;
        selectedItemName = data.name;
        selectedItemSku = data.sku;

        document.getElementById('ind-op-2').className = 'scan-step-indicator completed';
        document.getElementById('ind-op-3').className = 'scan-step-indicator active';
        document.getElementById('opname-active-item').textContent = `${selectedItemName} [${selectedItemSku}]`;

        document.getElementById('opname-scan-container').classList.add('d-none');
        document.getElementById('opname-input-panel').classList.remove('d-none');

        document.getElementById('input-item-display').textContent = selectedItemName + ' [' + selectedItemSku + ']';
        document.getElementById('input-slot-display').textContent = selectedSlotCode;
        if (USER_ROLE === 'petugas_gudang') {
            document.getElementById('physical-qty-input').value = '';
        } else {
            document.getElementById('physical-qty-input').value = data.system_qty || '0';
        }

        currentStep = 3;
        document.getElementById('op-instruction-text').innerHTML = `
          <span class="step-bubble">3</span>
          Ketik kuantitas fisik barang aktual yang ditemukan di rak
        `;

        showToast("Barang terverifikasi!", "success");
        playSuccessFeedback();

        setTimeout(() => document.getElementById('physical-qty-input').focus(), 100);
      }
    })
    .catch(() => {
      showToast("Koneksi gagal saat memverifikasi barang", "error");
      playErrorFeedback();
    });
}

function submitOpnameItem() {
  const qtyInput = document.getElementById('physical-qty-input');
  const qty = parseInt(qtyInput.value);
  if (isNaN(qty) || qty < 0) {
    showToast("Jumlah barang harus valid dan minimal 0!", "error");
    qtyInput.focus();
    return;
  }

  fetch(BASE_URL + `/api/opname.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `opname_id=${OPNAME_ID}&type=submit_qty&slot_id=${selectedSlotId}&item_id=${selectedItemId}&physical_qty=${qty}`
  })
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        showToast(data.error, "error");
      } else {
        showToast("Perhitungan item berhasil disimpan!", "success");

        const row = document.getElementById('pending-item-row-' + selectedSlotId);
        if (row) {
          row.dataset.verified = 'true';
          row.querySelector('div:last-child').innerHTML = `<span class="badge badge-success badge-pill-padded">Cocok: ${qty}</span>`;
        }

        updatePendingBadge();
        resetOpnameWorkflow();
      }
    })
    .catch(() => showToast("Koneksi gagal saat menyimpan perhitungan fisik", "error"));
}

function resetOpnameWorkflow() {
  currentStep = 1;
  selectedSlotId = null;
  selectedSlotCode = null;
  selectedItemId = null;
  selectedItemName = null;
  selectedItemSku = null;

  document.getElementById('opname-active-loc').textContent = '-';
  document.getElementById('opname-active-item').textContent = '-';

  document.getElementById('ind-op-1').className = 'scan-step-indicator active';
  document.getElementById('ind-op-2').className = 'scan-step-indicator';
  document.getElementById('ind-op-3').className = 'scan-step-indicator';

  document.getElementById('opname-scan-container').classList.remove('d-none');
  document.getElementById('opname-input-panel').classList.add('d-none');

  document.getElementById('op-instruction-text').innerHTML = `
    <span class="step-bubble">1</span>
    Pindai/Scan QR Code Rak Gudang Target
  `;
}
</script>
