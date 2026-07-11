

<?php if (!empty($_GET['success'])): ?>
<div class="alert alert-success alert-mb-20 animate-in">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <div>Proses picking selesai! Barang berhasil dikeluarkan. Referensi: <strong><?= htmlspecialchars($_GET['ref'] ?? '') ?></strong> (SO: <strong><?= htmlspecialchars($_GET['so'] ?? '') ?></strong>)</div>
</div>
<?php endif; ?>

<?php if (!empty($_GET['low_items'])):
    $lowItems = json_decode($_GET['low_items'], true);
    if (!empty($lowItems)):
?>
<div class="alert alert-warning alert-w-border-warning alert-mb-20 animate-in d-flex flex-column gap-4">
  <div class="d-flex align-center gap-8 fw-800 text-warning-darker">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 text-warning"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    STOK MENCAPAI BATAS MINIMUM (REORDER POINT)
  </div>
  <p class="fs-12-5 text-warning-darker ml-28 lh-1-4">
    Sisa stok untuk barang berikut berada di bawah batas minimum. Notifikasi pemesanan ulang (reorder) otomatis telah dikirimkan ke Divisi Pembelian.
  </p>
  <div class="ml-28 mt-8 w-full">
    <table class="w-full max-w-500px fs-12 border-collapse">
      <thead>
        <tr class="border-bottom-warning text-left text-warning-darker">
          <th class="py-4 fw-700">Barang</th>
          <th class="py-4 fw-700 text-right">Sisa Stok</th>
          <th class="py-4 fw-700 text-right">Batas Minimum</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lowItems as $low): ?>
          <tr class="border-bottom-warning-dashed text-warning-darker">
            <td class="py-6 fw-600"><?= htmlspecialchars($low['name']) ?> <span class="font-monospace opacity-8">[<?= htmlspecialchars($low['sku']) ?>]</span></td>
            <td class="py-6 text-right fw-800 text-danger"><?= htmlspecialchars($low['stock']) ?> unit</td>
            <td class="py-6 text-right fw-600"><?= htmlspecialchars($low['min']) ?> unit</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-mb-20 animate-in">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <div><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<div class="grid-2">

  <div class="card">
    <div class="card-header p-header">
      <div class="card-title fs-18 fw-800">Proses Pengeluaran Outbound (Picking)</div>
    </div>

    <div class="card-body p-body">

      <div class="wizard-steps">
        <div class="w-step active" id="step-ind-1">
          <span class="step-num">1</span>
          <span class="step-label">Pilih SO</span>
        </div>
        <div class="w-step-line" id="step-line-1"></div>
        <div class="w-step" id="step-ind-2">
          <span class="step-num">2</span>
          <span class="step-label">Picking List</span>
        </div>
        <div class="w-step-line" id="step-line-2"></div>
        <div class="w-step" id="step-ind-3">
          <span class="step-num">3</span>
          <span class="step-label">Konfirmasi</span>
        </div>
      </div>

      <form method="POST" action="index.php?page=outbound" id="form-outbound">

        <input type="hidden" name="so_number" id="hidden-so-number" />

        <div class="wizard-step-panel" id="panel-step-1">
          <div class="verify-title mb-14">Langkah 1: Pilih Sales Order (SO)</div>

          <div class="d-flex flex-column gap-16">
            <?php foreach ($processedSOs as $so): ?>
              <div class="so-card <?= $so['is_stock_sufficient'] ? '' : 'so-card-disabled' ?>" id="so-card-<?= htmlspecialchars($so['so_number']) ?>">
                <div class="d-flex justify-between align-start mb-12">
                  <div>
                    <div class="fs-16 fw-800 text-main"><?= htmlspecialchars($so['so_number']) ?></div>
                    <div class="fs-12-5 text-secondary mt-2">Pelanggan: <strong><?= htmlspecialchars($so['customer']) ?></strong></div>
                    <div class="fs-11-5 text-muted mt-1">Tanggal: <?= htmlspecialchars($so['date']) ?></div>
                  </div>
                  <div>
                    <?php if ($so['is_stock_sufficient']): ?>
                      <span class="badge badge-success d-inline-flex align-center gap-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Stok Cukup
                      </span>
                    <?php else: ?>
                      <span class="badge badge-danger d-inline-flex align-center gap-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Stok Kurang
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="bg-f8fafc border-radius-8 p-10-14 mb-16 border-1-solid">
                  <div class="fs-10-5 fw-700 text-muted text-uppercase letter-spacing-02 mb-8">Bahan Cetak yang Dipesan:</div>
                  <table class="w-full border-collapse fs-12-5">
                    <thead>
                      <tr class="border-bottom-solid text-left text-muted fs-11">
                        <th class="py-4 fw-600">Barang</th>
                        <th class="py-4 fw-600 text-right">Jumlah</th>
                        <th class="py-4 fw-600 text-right">Sisa Stok</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($so['items'] as $item): ?>
                        <tr class="border-bottom-dashed-f1">
                          <td class="py-6 text-main fw-500">
                            <?= htmlspecialchars($item['name']) ?> <span class="font-monospace fs-11 text-light">[<?= htmlspecialchars($item['sku']) ?>]</span>
                          </td>
                          <td class="py-6 text-right fw-700 text-secondary"><?= $item['qty_ordered'] ?> <?= htmlspecialchars($item['unit']) ?></td>
                          <?php
                            if ($item['total_stock'] < $item['qty_ordered']) {
                                $stockClass = 'stock-insufficient';
                            } elseif ($item['total_stock'] <= $item['min_stock']) {
                                $stockClass = 'stock-sufficient-low';
                            } else {
                                $stockClass = 'stock-sufficient-plenty';
                            }
                          ?>
                          <td class="py-6 text-right fw-700 <?= $stockClass ?>">
                            <?= $item['total_stock'] ?> <?= htmlspecialchars($item['unit']) ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <?php if ($so['is_stock_sufficient']): ?>
                  <button type="button" class="btn btn-primary btn-sm w-full justify-center" onclick="startPicking(<?= htmlspecialchars(json_encode($so)) ?>)">
                    Mulai Picking Barang
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                  </button>
                <?php else: ?>
                  <button type="button" class="btn btn-secondary btn-sm w-full justify-center" disabled>Stok Gudang Tidak Cukup</button>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="wizard-step-panel" id="panel-step-2" style="display:none;">
          <div class="picking-info-banner">
            <div class="verify-title">Langkah 2: Picking List & Verifikasi Ganda</div>
            <div class="fs-13 text-secondary font-600" id="picking-so-title">SO: -</div>
            <p class="fs-13 text-muted lh-1-5 mt-2">
              Silakan ambil barang dari rak gudang. Anda wajib melakukan scan QR Code rak (konfirmasi lokasi) dan scan QR Code barang (konfirmasi barang) untuk setiap baris picking list.
            </p>
          </div>

          <div id="picking-rows-container" class="d-flex flex-column gap-12">

          </div>

          <div class="wizard-nav-btns">
            <button type="button" class="btn btn-outline btn-nav-half" onclick="goToStep(1)">Kembali</button>
            <button type="button" id="btn-picking-continue" class="btn btn-primary btn-nav-main" onclick="validateAndGoToStep3()" disabled>
              Lanjutkan
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </div>

        <div class="wizard-step-panel" id="panel-step-3" style="display:none;">
          <div class="verify-title mb-12">Langkah 3: Tinjau & Selesaikan Picking</div>

          <div class="verification-panel verify-panel-success">
            <div class="verify-meta-label-success">Ringkasan Picking List Terverifikasi</div>

            <table class="w-full border-collapse fs-13" id="summary-picking-table">
              <thead>
                <tr class="border-bottom-solid text-left text-muted fs-11">
                  <th class="py-6 fw-700">Barang</th>
                  <th class="py-6 fw-700">Lokasi Rak</th>
                  <th class="py-6 fw-700 text-right">Jumlah Diambil</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>

          <div class="form-group mt-16">
            <label class="form-label fw-700">Catatan Pengeluaran</label>
            <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Catatan opsional (plat mobil supir, detail packaging, dll)"></textarea>
          </div>

          <div class="wizard-nav-btns">
            <button type="button" class="btn btn-outline btn-nav-half" onclick="goToStep(2)">Kembali</button>
            <button type="submit" class="btn btn-success btn-nav-main fw-700">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Selesaikan & Update Stok
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header p-header">
      <div class="card-title fs-18 fw-800">Riwayat Pengeluaran Terakhir</div>
    </div>
    <div class="card-body today-inbound-card-body" id="today-outbound">

    </div>
  </div>
</div>

<div id="picking-scanner-modal" class="modal-overlay z-9999">
  <div class="modal-box w-full max-w-440px overflow-hidden">

    <div class="card-header p-16-20 d-flex justify-between align-center border-bottom-solid">
      <div>
        <div class="card-title fs-15 fw-800" id="picking-modal-title">Pindai QR Code</div>
        <div class="card-sub fs-11-5 text-muted mt-2" id="picking-modal-subtitle">Konfirmasi Lokasi Rak</div>
      </div>
      <button type="button" class="modal-close border-none bg-none cursor-pointer text-muted fs-20 d-flex align-center justify-center" onclick="closePickingScanner()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="card-body p-16-20">

      <div class="qr-method-tabs mb-12">
        <button type="button" class="tab-btn active" id="modal-tab-camera" onclick="switchModalQRMethod('camera')">Kamera Live</button>
        <button type="button" class="tab-btn" id="modal-tab-file" onclick="switchModalQRMethod('file')">Unggah File</button>
      </div>

      <div id="modal-method-camera" class="tab-content-fade">

        <div id="modal-camera-insecure-warning" style="display:none;" class="mb-10">
          <div class="bg-danger-soft border-danger border-radius-8 p-10 fs-12 text-danger lh-1-4 text-center">
            Akses kamera diblokir browser karena koneksi tidak aman (non-localhost/non-HTTPS). Silakan gunakan tab <strong>Unggah File</strong> di sebelah kanan.
          </div>
        </div>

        <div id="modal-camera-active-wrapper">
          <div class="qr-camera-zone p-20 min-h-100px gap-8" id="modal-camera-placeholder" onclick="startModalCamera()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
            <div class="zone-text fs-13">Klik untuk Aktifkan Kamera</div>
          </div>
          <div id="modal-qr-reader" style="display:none;" class="qr-reader-inbound h-180px mb-10"></div>
        </div>
      </div>

      <div id="modal-method-file" class="tab-content-fade" style="display:none;">
        <div class="qr-drag-drop-zone p-24" id="modal-drop-zone">
          <input type="file" id="modal-file-input" accept="image/*" style="display:none;"/>
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          <div class="zone-text fs-13">Seret file ke sini atau klik pilih gambar</div>
        </div>
      </div>

      <div id="modal-qr-reader-temp" style="display:none;"></div>

      <div class="mt-14 text-center">
        <div class="fs-11 fw-700 text-muted text-uppercase mb-4" id="modal-target-code-label">Target QR yang dicari:</div>
        <div id="modal-target-code-display" class="font-monospace fw-700 bg-f1f5f9 text-primary p-6-12 border-radius-6 fs-12-5 d-inline-block word-break-all">-</div>
      </div>

    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>

if (typeof playSuccessFeedback !== 'function') {
  window.playSuccessFeedback = function() { console.log("Success audio-haptic feedback mock"); };
}
if (typeof playErrorFeedback !== 'function') {
  window.playErrorFeedback = function() { console.log("Error audio-haptic feedback mock"); };
}

let selectedSO = null;
let pickingProgress = [];
let activeRowIdx = -1;
let activeScanType = '';
let activeTargetCode = '';
let modalScannerOpen = false;
let modalScanMethod = 'camera';

document.addEventListener('DOMContentLoaded', () => {

  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    document.getElementById('modal-camera-insecure-warning').style.display = 'block';
    document.getElementById('modal-camera-active-wrapper').style.display = 'none';
  }
});

function goToStep(step) {

  document.querySelectorAll('.wizard-step-panel').forEach(p => p.style.display = 'none');

  const activePanel = document.getElementById('panel-step-' + step);
  activePanel.style.display = 'block';
  activePanel.classList.remove('panel-fade-in');
  void activePanel.offsetWidth;
  activePanel.classList.add('panel-fade-in');

  for (let i = 1; i <= 3; i++) {
    const ind = document.getElementById('step-ind-' + i);
    const line = document.getElementById('step-line-' + i);

    ind.classList.remove('active', 'completed');
    if (line) line.classList.remove('active');

    if (i < step) {
      ind.classList.add('completed');
      if (line) line.classList.add('active');
    } else if (i === step) {
      ind.classList.add('active');
    }
  }
}

function startPicking(so) {
  selectedSO = so;
  document.getElementById('hidden-so-number').value = so.so_number;
  document.getElementById('picking-so-title').textContent = `${so.so_number} · Pelanggan: ${so.customer}`;

  pickingProgress = so.picking_list.map(() => ({
    rackVerified: false,
    itemVerified: false
  }));

  renderPickingRows();
  checkPickingCompletion();
  goToStep(2);
}
function renderPickingRows() {
  const container = document.getElementById('picking-rows-container');
  container.innerHTML = '';

  selectedSO.picking_list.forEach((item, index) => {
    container.innerHTML += `
      <div class="picking-list-row" id="picking-row-${index}">
        <div class="d-flex justify-between align-start flex-wrap gap-12">
          <div>
            <div class="fs-15 fw-700 text-main">${escHtml(item.item_name)}</div>
            <div class="fs-12 text-muted mt-2">SKU: <strong class="font-monospace">${escHtml(item.sku)}</strong> · Jumlah: <strong>${item.qty_to_pick} ${escHtml(item.unit)}</strong></div>
            <div class="fs-12 text-primary-dark fw-600 mt-4 d-inline-flex align-center gap-4">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Lokasi: ${escHtml(item.zone_name)} · Rak ${escHtml(item.rack_code)} · Slot ${item.slot_number}
            </div>
          </div>

          <div class="d-flex flex-column gap-8 align-end">
            <div class="d-flex gap-10">

              <div class="d-flex flex-column align-center gap-4">
                <button type="button" id="btn-scan-rack-${index}" class="btn btn-secondary btn-sm p-6-10 fs-11" onclick="openPickingScanner(${index}, 'rack')">
                  Pindai Rak
                </button>
                <span id="status-rack-${index}" class="text-danger d-inline-flex align-center gap-3 fs-10-5 fw-700">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12 text-danger"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Belum Scan
                </span>
              </div>

              <div class="d-flex flex-column align-center gap-4">
                <button type="button" id="btn-scan-item-${index}" class="btn btn-secondary btn-sm p-6-10 fs-11" onclick="openPickingScanner(${index}, 'item')">
                  Pindai Barang
                </button>
                <span id="status-item-${index}" class="text-danger d-inline-flex align-center gap-3 fs-10-5 fw-700">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12 text-danger"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  Belum Scan
                </span>
              </div>
            </div>

            <span id="row-badge-${index}" class="badge badge-muted p-2-8 fs-10">BELUM SIAP</span>
          </div>
        </div>
      </div>
    `;
  });
}

function openPickingScanner(rowIdx, type) {
  activeRowIdx = rowIdx;
  activeScanType = type;

  const item = selectedSO.picking_list[rowIdx];
  const modal = document.getElementById('picking-scanner-modal');
  const modalTitle = document.getElementById('picking-modal-title');
  const modalSubtitle = document.getElementById('picking-modal-subtitle');
  const displayCode = document.getElementById('modal-target-code-display');
  const displayLabel = document.getElementById('modal-target-code-label');

  if (type === 'rack') {
    activeTargetCode = item.rack_qr_code;
    modalTitle.textContent = "Pindai QR Rak Gudang";
    modalSubtitle.textContent = `Posisikan kamera di depan barcode Rak ${item.rack_code} · Slot ${item.slot_number}`;
    displayLabel.textContent = "Target Barcode Rak:";
    displayCode.textContent = item.rack_qr_code;
  } else {
    activeTargetCode = item.item_qr_code;
    modalTitle.textContent = "Pindai QR Barang / Item";
    modalSubtitle.textContent = `Posisikan kamera di depan barcode Item: ${item.item_name}`;
    displayLabel.textContent = "Target Barcode Barang (SKU):";
    displayCode.textContent = item.item_qr_code;
  }

  modal.classList.add('open');

  switchModalQRMethod('camera');
}

function closePickingScanner() {
  stopQRScanner();
  modalScannerOpen = false;
  document.getElementById('modal-qr-reader').style.display = 'none';
  document.getElementById('modal-camera-placeholder').style.display = 'flex';

  const modal = document.getElementById('picking-scanner-modal');
  modal.classList.remove('open');
}

function switchModalQRMethod(method) {
  stopQRScanner();
  modalScannerOpen = false;
  document.getElementById('modal-qr-reader').style.display = 'none';
  document.getElementById('modal-camera-placeholder').style.display = 'flex';
  modalScanMethod = method;

  if (method === 'camera') {
    document.getElementById('modal-tab-camera').classList.add('active');
    document.getElementById('modal-tab-file').classList.remove('active');
    document.getElementById('modal-method-camera').style.display = 'block';
    document.getElementById('modal-method-file').style.display = 'none';
  } else {
    document.getElementById('modal-tab-camera').classList.remove('active');
    document.getElementById('modal-tab-file').classList.add('active');
    document.getElementById('modal-method-camera').style.display = 'none';
    document.getElementById('modal-method-file').style.display = 'block';
  }
}

function startModalCamera() {
  const reader = document.getElementById('modal-qr-reader');
  const placeholder = document.getElementById('modal-camera-placeholder');

  if (!modalScannerOpen) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showToast("Akses kamera ditolak oleh browser (insecure context).", "error");
      return;
    }

    placeholder.style.display = 'none';
    reader.style.display = 'block';

    startQRScanner('modal-qr-reader', (code) => {
      verifyPickingQR(code);
      reader.style.display = 'none';
      placeholder.style.display = 'flex';
      modalScannerOpen = false;
    }, (err) => {
      reader.style.display = 'none';
      placeholder.style.display = 'flex';
      modalScannerOpen = false;
    });
    modalScannerOpen = true;
  }
}

function verifyPickingQR(scannedCode) {
  let codeStr = scannedCode.trim();

  try {
    const parsed = JSON.parse(scannedCode);
    if (parsed && parsed.code) {
      codeStr = parsed.code.trim();
    }
  } catch (e) {

  }

  if (codeStr === activeTargetCode) {
    showToast("Pindai Terverifikasi! Barcode cocok.", "success");
    playSuccessFeedback();

    if (activeScanType === 'rack') {
      pickingProgress[activeRowIdx].rackVerified = true;
    } else {
      pickingProgress[activeRowIdx].itemVerified = true;
    }

    updateRowUI(activeRowIdx);
    checkPickingCompletion();
    closePickingScanner();
  } else {
    showToast(`Scan Gagal! Barcode tidak cocok. Target: ${activeTargetCode}, Terbaca: ${codeStr}`, "error");
    playErrorFeedback();
  }
}

function updateRowUI(rowIdx) {
  const progress = pickingProgress[rowIdx];
  const rowEl = document.getElementById('picking-row-' + rowIdx);

  const rackStatusEl = document.getElementById('status-rack-' + rowIdx);
  if (progress.rackVerified) {
    rackStatusEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12 text-success"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      Terverifikasi
    `;
    rackStatusEl.className = 'text-success d-inline-flex align-center gap-3 fs-10-5 fw-700';
    document.getElementById('btn-scan-rack-' + rowIdx).setAttribute('disabled', 'true');
  }

  const itemStatusEl = document.getElementById('status-item-' + rowIdx);
  if (progress.itemVerified) {
    itemStatusEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12 text-success"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      Terverifikasi
    `;
    itemStatusEl.className = 'text-success d-inline-flex align-center gap-3 fs-10-5 fw-700';
    document.getElementById('btn-scan-item-' + rowIdx).setAttribute('disabled', 'true');
  }

  const badge = document.getElementById('row-badge-' + rowIdx);
  if (progress.rackVerified && progress.itemVerified) {
    rowEl.classList.add('verified-success');
    badge.textContent = "SIAP PACKING";
    badge.className = "badge badge-success p-2-8 fs-10";
  }
}

function checkPickingCompletion() {
  const allCompleted = pickingProgress.every(p => p.rackVerified && p.itemVerified);
  const continueBtn = document.getElementById('btn-picking-continue');

  if (allCompleted) {
    continueBtn.removeAttribute('disabled');
    showToast("Semua item picking list selesai terverifikasi!", "success");
  } else {
    continueBtn.setAttribute('disabled', 'true');
  }
}

function validateAndGoToStep3() {
  const tbody = document.querySelector('#summary-picking-table tbody');
  tbody.innerHTML = '';

  selectedSO.picking_list.forEach(item => {
    tbody.innerHTML += `
      <tr class="border-bottom-dashed-e2">
        <td class="py-10-0 fw-600 text-main">${escHtml(item.item_name)} <span class="font-monospace fs-11 text-muted">[${escHtml(item.sku)}]</span></td>
        <td class="py-10-0 text-primary-dark fw-700">${escHtml(item.zone_name)} · Rak ${escHtml(item.rack_code)} · Slot ${item.slot_number}</td>
        <td class="py-10-0 text-right fw-700 text-secondary">${item.qty_to_pick} ${escHtml(item.unit)}</td>
      </tr>
    `;
  });

  goToStep(3);
}

document.addEventListener('DOMContentLoaded', () => {
  const modalDropZone = document.getElementById('modal-drop-zone');
  const modalFileInput = document.getElementById('modal-file-input');

  if (modalDropZone) {
    modalDropZone.addEventListener('click', () => modalFileInput.click());

    modalDropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      modalDropZone.classList.add('dragover');
    });

    ['dragleave', 'dragend'].forEach(type => {
      modalDropZone.addEventListener(type, () => modalDropZone.classList.remove('dragover'));
    });

    modalDropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      modalDropZone.classList.remove('dragover');
      if (e.dataTransfer.files.length > 0) {
        scanUploadedModalFile(e.dataTransfer.files[0]);
      }
    });
  }

  if (modalFileInput) {
    modalFileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        scanUploadedModalFile(e.target.files[0]);
      }
    });
  }
});

function scanUploadedModalFile(file) {
  if (!file.type.startsWith('image/')) {
    showToast("File harus berupa gambar!", "error");
    return;
  }

  const html5Qr = new Html5Qrcode("modal-qr-reader-temp");
  showToast("Membaca gambar QR...", "info");

  html5Qr.scanFile(file, true)
    .then(decodedText => {
      verifyPickingQR(decodedText);
      showToast("QR Code berhasil dibaca!", "success");
      html5Qr.clear();
    })
    .catch(err => {
      console.warn(err);
      showToast("Gagal membaca QR. Pastikan gambar jelas & berisi QR Code.", "error");
      playErrorFeedback();
      html5Qr.clear();
    });
}

function escHtml(str) {
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

fetch(BASE_URL + '/api/dashboard.php?type=recent').then(r => r.json()).then(txs => {
  const el = document.getElementById('today-outbound');
  const out = txs.filter(t => t.type === 'outbound');
  if (!out.length) {
    el.innerHTML = '<div class="text-muted fs-13 text-center p-24">Belum ada transaksi keluar</div>';
    return;
  }

  out.forEach(tx => {
    el.innerHTML += `
    <div class="recent-tx-row">
      <div class="recent-tx-icon-wrap outbound">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
      </div>
      <div class="flex-1-min-w-0">
        <div class="tx-item-name">${tx.item_name}</div>
        <div class="tx-meta-row">
          <span class="badge-ref-no">${tx.reference_no}</span>
          <span>·</span>
          <span class="fw-600 text-danger">${tx.rack_code ?? 'Retur'}</span>
        </div>
      </div>
      <div class="tx-qty-outbound">-${tx.quantity} ${tx.unit}</div>
    </div>`;
  });
});
</script>
